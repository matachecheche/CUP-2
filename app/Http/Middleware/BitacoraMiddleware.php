<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Bitacora;
use Illuminate\Support\Facades\Auth;

class BitacoraMiddleware
{
    /**
     * Mapa de rutas → descripción legible para la bitácora.
     * Agrega aquí cualquier ruta nueva que quieras nombrar explícitamente.
     */
    protected array $rutasDescriptivas = [
        'panel'                        => 'Visitó el panel principal',
        'login'                        => 'Accedió a la página de login',
        'logout'                       => 'Cerró sesión',

        'users.index'                  => 'Listó usuarios',
        'users.create'                 => 'Abrió formulario crear usuario',
        'users.store'                  => 'Creó un usuario',
        'users.edit'                   => 'Abrió formulario editar usuario',
        'users.update'                 => 'Actualizó un usuario',
        'users.destroy'                => 'Eliminó un usuario',

        'residentes.index'             => 'Listó residentes',
        'residentes.create'            => 'Abrió formulario crear residente',
        'residentes.store'             => 'Registró un residente',
        'residentes.edit'              => 'Abrió formulario editar residente',
        'residentes.update'            => 'Actualizó un residente',
        'residentes.destroy'           => 'Eliminó un residente',

        'empleados.index'              => 'Listó empleados',
        'empleados.create'             => 'Abrió formulario crear empleado',
        'empleados.store'              => 'Registró un empleado',
        'empleados.edit'               => 'Abrió formulario editar empleado',
        'empleados.update'             => 'Actualizó un empleado',
        'empleados.destroy'            => 'Eliminó un empleado',

        'cargos.index'                 => 'Listó cargos de empleado',
        'cargos.create'                => 'Abrió formulario crear cargo',
        'cargos.store'                 => 'Creó un cargo de empleado',
        'cargos.edit'                  => 'Abrió formulario editar cargo',
        'cargos.update'                => 'Actualizó un cargo de empleado',
        'cargos.destroy'               => 'Eliminó un cargo de empleado',

        'roles.index'                  => 'Listó roles',
        'roles.create'                 => 'Abrió formulario crear rol',
        'roles.store'                  => 'Creó un rol',
        'roles.edit'                   => 'Abrió formulario editar rol',
        'roles.update'                 => 'Actualizó un rol',
        'roles.destroy'                => 'Eliminó un rol',

        'cuotas.index'                 => 'Listó cuotas',
        'cuotas.create'                => 'Abrió formulario crear cuota',
        'cuotas.store'                 => 'Registró una cuota',
        'cuotas.edit'                  => 'Abrió formulario editar cuota',
        'cuotas.update'                => 'Actualizó una cuota',
        'cuotas.destroy'               => 'Eliminó una cuota',

        'tipos-cuotas.index'           => 'Listó tipos de cuota',
        'tipos-cuotas.create'          => 'Abrió formulario crear tipo cuota',
        'tipos-cuotas.store'           => 'Creó un tipo de cuota',
        'tipos-cuotas.edit'            => 'Abrió formulario editar tipo cuota',
        'tipos-cuotas.update'          => 'Actualizó un tipo de cuota',
        'tipos-cuotas.destroy'         => 'Eliminó un tipo de cuota',

        'pagos.index'                  => 'Listó pagos',
        'pagos.store'                  => 'Registró un pago',
        'pagos.mis_cuotas'             => 'Consultó sus cuotas',
        'pagos.comprobante'            => 'Consultó comprobante de pago',
        'pagos.qr'                     => 'Realizó pago por QR (cuota)',
        'pagos.qr.multa'               => 'Realizó pago por QR (multa)',
        'pagos.stripe'                 => 'Inició pago Stripe (cuota)',
        'pagos.stripe.multa'           => 'Inició pago Stripe (multa)',
        'pagos.stripe.success'         => 'Pago Stripe completado (cuota)',
        'pagos.stripe.success.multa'   => 'Pago Stripe completado (multa)',

        'multas.index'                 => 'Listó multas',
        'multas.create'                => 'Abrió formulario crear multa',
        'multas.store'                 => 'Registró una multa',
        'multas.edit'                  => 'Abrió formulario editar multa',
        'multas.update'                => 'Actualizó una multa',
        'multas.destroy'               => 'Eliminó una multa',

        'areas-comunes.index'          => 'Listó áreas comunes',
        'areas-comunes.create'         => 'Abrió formulario crear área común',
        'areas-comunes.store'          => 'Registró un área común',
        'areas-comunes.edit'           => 'Abrió formulario editar área común',
        'areas-comunes.update'         => 'Actualizó un área común',
        'areas-comunes.destroy'        => 'Eliminó un área común',

        'reservas.index'               => 'Listó reservas',
        'reservas.create'              => 'Abrió formulario crear reserva',
        'reservas.store'               => 'Registró una reserva',
        'reservas.edit'                => 'Abrió formulario editar reserva',
        'reservas.update'              => 'Actualizó una reserva',
        'reservas.destroy'             => 'Eliminó una reserva',
        'reservas.verificar-inventario'=> 'Verificó inventario de reserva',
        'reservas.guardar-verificacion'=> 'Guardó verificación de inventario',

        'mantenimientos.index'         => 'Listó mantenimientos',
        'mantenimientos.create'        => 'Abrió formulario crear mantenimiento',
        'mantenimientos.store'         => 'Registró un mantenimiento',
        'mantenimientos.edit'          => 'Abrió formulario editar mantenimiento',
        'mantenimientos.update'        => 'Actualizó un mantenimiento',
        'mantenimientos.destroy'       => 'Eliminó un mantenimiento',

        'empresas.index'               => 'Listó empresas externas',
        'empresas.create'              => 'Abrió formulario crear empresa',
        'empresas.store'               => 'Registró una empresa externa',
        'empresas.edit'                => 'Abrió formulario editar empresa',
        'empresas.update'              => 'Actualizó una empresa externa',
        'empresas.destroy'             => 'Eliminó una empresa externa',

        'visitas.index'                => 'Listó visitas',
        'visitas.create'               => 'Abrió formulario crear visita',
        'visitas.store'                => 'Registró una visita',
        'visitas.edit'                 => 'Abrió formulario editar visita',
        'visitas.update'               => 'Actualizó una visita',
        'visitas.destroy'              => 'Eliminó una visita',
        'visitas.entrada'              => 'Registró entrada de visita',
        'visitas.salida'               => 'Registró salida de visita',
        'visitas.validar-codigo'       => 'Validó código de visita',
        'visitas.panel-guardia'        => 'Accedió al panel de guardia',
        'visitas.mostrar-validar-codigo'=> 'Abrió validador de código de visita',
        'visitas.buscar-codigo'        => 'Buscó visita por código',

        'comunicados.index'            => 'Listó comunicados',
        'comunicados.create'           => 'Abrió formulario crear comunicado',
        'comunicados.store'            => 'Publicó un comunicado',
        'comunicados.edit'             => 'Abrió formulario editar comunicado',
        'comunicados.update'           => 'Actualizó un comunicado',
        'comunicados.destroy'          => 'Eliminó un comunicado',
        'comunicados.show'             => 'Vio detalle de comunicado',

        'bitacora.index'               => 'Consultó la bitácora del sistema',
    ];

    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Solo registrar si hay usuario autenticado
        if (!Auth::check()) {
            return $response;
        }

        // Obtener nombre de ruta actual
        $routeName = $request->route()?->getName() ?? '';

        // Ignorar rutas internas / AJAX que no necesitan bitácora
        $ignorar = ['bitacora.page-close', 'api.horas-libres', 'pagos.stripe.cancel'];
        if (in_array($routeName, $ignorar)) {
            return $response;
        }

        // Determinar descripción legible
        $accion = $this->rutasDescriptivas[$routeName]
            ?? 'Visitó: ' . $request->path();

        Bitacora::create([
            'user_id'      => Auth::id(),
            'usuario'      => Auth::user()->name,
            'accion'       => $accion,
            'fecha_hora'   => now(),
            'ip'           => $request->ip(),
            'id_operacion' => null,
        ]);

        return $response;
    }
}