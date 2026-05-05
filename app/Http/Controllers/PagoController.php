<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use App\Models\Cuota;
use Illuminate\Http\Request;
use App\Traits\BitacoraTrait;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Models\Multa;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PagoController extends Controller
{
    use BitacoraTrait;

    public function index(Request $request)
    {
        $pagos = $this->filtrarPagos($request);
        return view('pagos.index', compact('pagos'));
    }

    private function filtrarPagos(Request $request)
    {
        $query = \App\Models\Pago::with(['cuota.residente', 'user']);

        // Se eliminó el filtro por usuario autenticado

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->whereHas('cuota.residente', function ($res) use ($search) {
                    $res->where('nombre', 'like', "%$search%")
                        ->orWhere('apellido', 'like', "%$search%")
                        ->orWhere('unidad', 'like', "%$search%");
                })->orWhereHas('cuota', function ($cu) use ($search) {
                    $cu->where('id', 'like', "%$search%");
                });
            });
        }

        if ($request->filled('metodo')) {
            $query->where('metodo', $request->metodo);
        }

        switch ($request->filtro_tiempo) {
            case 'fecha':
                if ($request->filled('fecha_desde') && $request->filled('fecha_hasta')) {
                    $query->whereBetween('fecha_pago', [$request->fecha_desde, $request->fecha_hasta]);
                }
                break;

            case 'mes':
                if ($request->filled('mes')) {
                    $query->whereMonth('fecha_pago', date('m', strtotime($request->mes)))
                        ->whereYear('fecha_pago', date('Y', strtotime($request->mes)));
                }
                break;

            case 'semana':
                if ($request->filled('semana')) {
                    $week = explode('-W', $request->semana);
                    $startOfWeek = \Carbon\Carbon::now()->setISODate($week[0], $week[1])->startOfWeek();
                    $endOfWeek = \Carbon\Carbon::now()->setISODate($week[0], $week[1])->endOfWeek();
                    $query->whereBetween('fecha_pago', [$startOfWeek->toDateString(), $endOfWeek->toDateString()]);
                }
                break;

            case 'anio':
                if ($request->filled('anio')) {
                    $query->whereYear('fecha_pago', $request->anio);
                }
                break;
        }

        return $query->orderByDesc('fecha_pago')->paginate(10);
    }

    public function misCuotas()
    {
        $residente = auth()->user()->residente;

        if (!$residente) {
            abort(403, 'Solo los residentes pueden acceder a sus cuotas.');
        }

        $cuotas = \App\Models\Cuota::with('pagos')
            ->where('residente_id', $residente->id)
            ->orderByDesc('fecha_vencimiento')
            ->paginate(10);

        return view('pagos.mis_cuotas', compact('cuotas'));
    }

    public function createCuota($cuotaId)
    {
        $cuota = Cuota::with('pagos')->findOrFail($cuotaId);

        // Solo el residente propietario puede pagar
        if (auth()->user()->residente_id !== $cuota->residente_id) {
            abort(403);
        }

        // Generar contenido QR
        $contenidoQr = urlencode("Pago de cuota\nMonto: Bs {$cuota->monto}\nConcepto: {$cuota->concepto}");
        $qrBase64    = "https://api.qrserver.com/v1/create-qr-code/?data={$contenidoQr}&size=200x200";

        return view('pagos.opciones_pago', [
            'entidad' => $cuota,
            'tipo' => 'cuota',
            'qrBase64' => $qrBase64
        ]);
    }





    public function pagoQR(Request $request)
    {
        $request->validate([
            'cuota_id' => 'required|exists:cuotas,id',
            'comprobante' => 'required|image|max:2048',
        ]);

        $cuota = Cuota::findOrFail($request->cuota_id);

        if (auth()->user()->residente_id !== $cuota->residente_id) {
            abort(403, 'No autorizado.');
        }

        $ruta = $request->file('comprobante')->store('comprobantes', 'public');

        Pago::create([
            'cuota_id' => $cuota->id,
            'user_id' => auth()->id(),
            'monto_pagado' => $cuota->monto,
            'fecha_pago' => now(),
            'metodo' => 'QR',
            'estado' => 'pendiente',
            'comprobante' => $ruta,
        ]);

        return redirect()->route('pagos.mis_cuotas')->with('success', 'Comprobante enviado. Esperando validación.');
    }

    public function pagoStripe(Request $request)
    {
        $cuota = Cuota::findOrFail($request->cuota_id);

        if (auth()->user()->residente_id !== $cuota->residente_id) {
            abort(403);
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'bob', // o 'usd' si lo manejarás así
                    'product_data' => [
                        'name' => 'Pago de cuota: ' . $cuota->concepto,
                    ],
                    'unit_amount' => $cuota->monto * 100, // Stripe usa centavos
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('pagos.stripe.success', ['cuota' => $cuota->id]),
            'cancel_url' => route('pagos.stripe.cancel'),
        ]);

        return redirect($session->url);
    }

    public function stripeSuccess($cuotaId)
    {
        Pago::create([
            'cuota_id' => $cuotaId,
            'user_id' => auth()->id(),
            'monto_pagado' => Cuota::find($cuotaId)->monto,
            'fecha_pago' => now(),
            'metodo' => 'Stripe',
            'estado' => 'aprobado',
        ]);

        return redirect()->route('pagos.mis_cuotas')->with('success', 'Pago realizado exitosamente con Stripe.');
    }

    public function createMulta($multaId)
    {
        $multa = Multa::with('pagos')->findOrFail($multaId);

        // Solo el residente o empleado al que corresponde puede pagar
        $user = auth()->user();
        $esResidente = $user->residente_id && $user->residente_id === $multa->residente_id;
        $esEmpleado  = $user->empleado_id  && $user->empleado_id  === $multa->empleado_id;

        if (! ($esResidente || $esEmpleado)) {
            abort(403);
        }

        // Generar contenido QR
        $contenidoQr = urlencode("Pago de multa\nMotivo: {$multa->motivo}\nMonto: Bs {$multa->monto}");
        $qrBase64    = "https://api.qrserver.com/v1/create-qr-code/?data={$contenidoQr}&size=200x200";

        return view('pagos.opciones_pago', [
            'entidad' => $multa,
            'tipo' => 'multa',
            'qrBase64' => $qrBase64
        ]);
    }

    public function pagoQRMulta(Request $request)
    {
        $request->validate([
            'multa_id'    => 'required|exists:multas,id',
            'comprobante' => 'required|image|max:2048',
        ]);

        $multa = Multa::findOrFail($request->multa_id);
        $user  = auth()->user();

        // Sólo el residente o empleado al que se la multa pertenece puede subir comprobante
        $esResidente = $user->residente_id && $user->residente_id === $multa->residente_id;
        $esEmpleado  = $user->empleado_id  && $user->empleado_id  === $multa->empleado_id;
        if (! ($esResidente || $esEmpleado) ) {
            abort(403, 'No autorizado.');
        }

        // Guardar archivo
        $ruta = $request->file('comprobante')->store('comprobantes/multas', 'public');

        // Crear el pago
        Pago::create([
            'multa_id'     => $multa->id,
            'user_id'      => $user->id,
            'monto_pagado' => $multa->monto,
            'fecha_pago'   => now(),
            'metodo'       => 'QR',
            'estado'       => 'pendiente',
            'comprobante'  => $ruta,
        ]);

        $multa->estado = 'pagada';
        $multa->save();
        $this->registrarEnBitacora('Pago de multa registrado', $multa->id);

        return redirect()
            ->route('multas.index')
            ->with('success', 'Comprobante de multa enviado. Esperando validación.');
    }

    public function pagoStripeMulta(Request $request)
    {
        // Validar que venga la multa
        $request->validate([
            'multa_id' => 'required|exists:multas,id',
        ]);

        $multa = Multa::findOrFail($request->multa_id);
        $user  = auth()->user();

        // Sólo su propio residente o empleado (o admin) puede pagar
        $esResidente = $user->residente_id && $user->residente_id === $multa->residente_id;
        $esEmpleado  = $user->empleado_id  && $user->empleado_id  === $multa->empleado_id;
        if (! $user->hasRole('ADMINISTRADOR') && ! ($esResidente || $esEmpleado)) {
            abort(403);
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'bob',
                    'product_data' => [
                        'name' => 'Pago de multa: ' . $multa->motivo,
                    ],
                    'unit_amount' => $multa->monto * 100,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('pagos.stripe.success.multa', ['multa' => $multa->id]),
            'cancel_url'  => route('pagos.stripe.cancel'),
        ]);

        return redirect($session->url);
    }

    /**
     * Callback de Stripe exitoso para multa.
     */
    public function stripeSuccessMulta($multaId)
    {
        $multa = Multa::findOrFail($multaId);
        $user  = auth()->user();

        // Creo el registro de pago
        $pago = Pago::create([
            'multa_id'     => $multa->id,
            'user_id'      => $user->id,
            'monto_pagado' => $multa->monto,
            'fecha_pago'   => now(),
            'metodo'       => 'Stripe',
            'estado'       => 'aprobado',
        ]);

        // Marco la multa como pagada
        $multa->estado = 'pagada';
        $multa->save();

        $this->registrarEnBitacora('Pago de multa registrado', $multa->id);

        return redirect()
            ->route('pagos.mis_multa')  // o donde muestres “mis multas”
            ->with('success', 'Pago de multa exitoso con Stripe.');
    }

    public function comprobante(Pago $pago)
    {
        // Validación de seguridad
        if (auth()->id() !== $pago->user_id && !auth()->user()->hasRole('admin')) {
            abort(403);
        }

        return view('pagos.comprobante_html', compact('pago'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cuota_id' => 'required|exists:cuotas,id',
            'monto_pagado' => 'required|numeric|min:1',
            'fecha_pago' => 'required|date',
            'metodo' => 'nullable|string',
            'observacion' => 'nullable|string',
        ]);

        $cuota = Cuota::findOrFail($request->cuota_id);

        // Crear el pago
        $pago = Pago::create([
            'cuota_id' => $cuota->id,
            'monto_pagado' => $request->monto_pagado,
            'fecha_pago' => $request->fecha_pago,
            'metodo' => $request->metodo,
            'observacion' => $request->observacion,
            'user_id' => auth()->id(),
        ]);

        // Si el pago cubre el monto total de la cuota, actualizar el estado
        if ($request->monto_pagado >= $cuota->monto) {
            $cuota->estado = 'pagado';
            $cuota->save();
        }
        $this->registrarEnBitacora('Pago registrado', $pago->id);

        return redirect()->route('pagos.index')->with('success', 'Pago registrado exitosamente.');
    }
}
