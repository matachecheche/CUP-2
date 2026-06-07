<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use App\Models\Postulante;
use App\Traits\BitacoraTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;
use Stripe\Webhook;

/**
 * CU-20: Pago de inscripción al CUP mediante pasarela (Stripe Checkout).
 *
 * Flujo: postulante 'preinscrito' → checkout() crea la Checkout Session →
 * Stripe cobra → webhook() (fuente de verdad) o exito() (verificación al
 * volver) confirman → pago 'pagado' + postulante 'inscrito'. Recién ahí
 * cuenta para CEIL(inscritos/70), grupos, notas y admisión.
 */
class PagoController extends Controller
{
    use BitacoraTrait;

    private function stripe(): StripeClient
    {
        return new StripeClient(config('services.stripe.secret'));
    }

    /** Pantalla de resumen del pago con botón "Pagar con Stripe". */
    public function pagar(Postulante $postulante)
    {
        $this->autorizar($postulante);
        if ($postulante->estado !== 'preinscrito') {
            return redirect()->route('postulantes.show', $postulante)
                ->with('error', 'Este postulante no tiene un pago pendiente.');
        }
        $monto = (float) ($postulante->gestion->costo_inscripcion ?? 850.00);

        return view('pagos.pagar', compact('postulante', 'monto'));
    }

    /** Crea la Checkout Session en Stripe y redirige a la pasarela. */
    public function checkout(Postulante $postulante)
    {
        $this->autorizar($postulante);
        if ($postulante->estado !== 'preinscrito') {
            return redirect()->route('postulantes.show', $postulante)
                ->with('error', 'El pago ya fue realizado o el estado no lo permite.');
        }

        $monto = (float) ($postulante->gestion->costo_inscripcion ?? 850.00);
        // Moneda configurable vía STRIPE_CURRENCY (Stripe soporta BOB; si tu
        // cuenta de prueba la rechazara, usar 'usd' y ajustar el monto).
        $moneda = config('services.stripe.currency', 'bob');

        $session = $this->stripe()->checkout->sessions->create([
            'mode' => 'payment',
            'line_items' => [[
                'quantity' => 1,
                'price_data' => [
                    'currency' => $moneda,
                    'unit_amount' => (int) round($monto * 100), // en centavos
                    'product_data' => [
                        'name' => 'Inscripción CUP FICCT — '.$postulante->gestion->descripcion,
                        'description' => "Postulante: {$postulante->nombre_completo} (CI {$postulante->ci})",
                    ],
                ],
            ]],
            'customer_email' => $postulante->email,
            'metadata' => ['postulante_id' => $postulante->id, 'gestion_id' => $postulante->gestion_id],
            // Stripe reemplaza el placeholder por el id real de la sesión:
            'success_url' => route('pagos.exito').'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('pagos.cancelado', $postulante),
        ]);

        // Queda 'pendiente' hasta que Stripe confirme (webhook o retorno).
        Pago::updateOrCreate(
            ['postulante_id' => $postulante->id, 'gestion_id' => $postulante->gestion_id, 'estado' => 'pendiente'],
            ['monto' => $monto, 'moneda' => strtoupper($moneda), 'metodo' => 'stripe', 'stripe_session_id' => $session->id]
        );

        $this->registrarEnBitacora("Inició pago Stripe de {$postulante->nombre_completo} (Bs {$monto})", $postulante->id, 'Pagos');

        return redirect()->away($session->url);
    }

    /** Retorno desde Stripe tras pago exitoso (verifica directo, además del webhook). */
    public function exito(Request $r)
    {
        $sessionId = $r->query('session_id');
        if (! $sessionId) {
            return redirect()->route('panel');
        }

        $pago = Pago::where('stripe_session_id', $sessionId)->first();
        try {
            $session = $this->stripe()->checkout->sessions->retrieve($sessionId);
            if ($pago && $session->payment_status === 'paid') {
                $this->confirmar($pago, $session->payment_intent);
            }
        } catch (\Throwable $e) {
            Log::warning('No se pudo verificar la sesión de Stripe: '.$e->getMessage());
        }

        return view('pagos.exito', ['pago' => $pago?->fresh(['postulante'])]);
    }

    /** El usuario canceló en la pasarela: vuelve al resumen para reintentar. */
    public function cancelado(Postulante $postulante)
    {
        return redirect()->route('pagos.pagar', $postulante)
            ->with('error', 'Pago cancelado. Puedes intentarlo nuevamente.');
    }

    /**
     * Webhook de Stripe: fuente de verdad del pago.
     * Evento esperado: checkout.session.completed.
     */
    public function webhook(Request $r)
    {
        $payload = $r->getContent();
        $secret = config('services.stripe.webhook_secret');

        try {
            $event = $secret
                ? Webhook::constructEvent($payload, $r->header('Stripe-Signature'), $secret)
                : json_decode($payload); // SOLO desarrollo: sin verificación de firma
        } catch (\Throwable $e) {
            Log::warning('Stripe webhook inválido: '.$e->getMessage());

            return response('Firma inválida', 400);
        }

        if (($event->type ?? null) === 'checkout.session.completed') {
            $sess = $event->data->object ?? null;
            $pago = Pago::where('stripe_session_id', $sess->id ?? null)->first();
            if ($pago && ($sess->payment_status ?? null) === 'paid') {
                $this->confirmar($pago, $sess->payment_intent ?? null);
            }
        }

        return response('ok', 200);
    }

    /** Idempotente: marca el pago como pagado y promueve al postulante a 'inscrito'. */
    private function confirmar(Pago $pago, ?string $paymentIntent): void
    {
        if ($pago->estado === 'pagado') {
            return; // ya confirmado (webhook + retorno)
        }

        $pago->update([
            'estado' => 'pagado',
            'fecha_pago' => now(),
            'stripe_payment_intent_id' => $paymentIntent,
            'comprobante' => 'CUP-'.now()->format('Y').'-'.str_pad($pago->id, 5, '0', STR_PAD_LEFT),
        ]);

        if ($pago->postulante->estado === 'preinscrito') {
            $pago->postulante->update(['estado' => 'inscrito']);
        }

        $this->registrarEnBitacora(
            "Pago confirmado: {$pago->postulante->nombre_completo} Bs {$pago->monto} ({$pago->comprobante})",
            $pago->id, 'Pagos'
        );
    }

    /** Puede pagar: admin con permiso sobre postulantes, o el propio postulante logueado. */
    private function autorizar(Postulante $postulante): void
    {
        $u = auth()->user();
        abort_unless($u && ($u->can('ver postulantes') || $u->postulante_id === $postulante->id), 403);
    }
}
