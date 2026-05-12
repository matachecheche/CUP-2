<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ResidenteController;
use App\Http\Controllers\BitacoraController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\CargoEmpleadoController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\CuotaController;
use App\Http\Controllers\TipoCuotaController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\MultaController;
use App\Http\Controllers\EmpresaExternaController;
use App\Http\Controllers\MantenimientoController;
use App\Http\Controllers\AreaComunController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\VisitaController;
use App\Http\Controllers\ComunicadoController;
use App\Models\Bitacora;

// ── Recuperación de contraseña ────────────────────────────────────────────────
Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

// ── Cargos de empleados ───────────────────────────────────────────────────────
Route::prefix('empleados/cargo')->group(function () {
    Route::get('/', [CargoEmpleadoController::class, 'index'])->name('cargos.index');
    Route::get('/crear', [CargoEmpleadoController::class, 'create'])->name('cargos.create');
    Route::post('/', [CargoEmpleadoController::class, 'store'])->name('cargos.store');
    Route::get('/{id}/editar', [CargoEmpleadoController::class, 'edit'])->name('cargos.edit');
    Route::put('/{id}', [CargoEmpleadoController::class, 'update'])->name('cargos.update');
    Route::delete('/{id}', [CargoEmpleadoController::class, 'destroy'])->name('cargos.destroy');
});

// ── Panel y navegación ────────────────────────────────────────────────────────
Route::get('/', [HomeController::class, 'index'])->name('panel');
Route::get('/panel', [HomeController::class, 'index']);

// ── CRUD básicos del ciclo 1 ──────────────────────────────────────────────────
Route::resource('bitacora', BitacoraController::class);
Route::resource('roles', RoleController::class)->middleware('auth');
Route::resources([
    'users'      => UsuarioController::class,
    'residentes' => ResidenteController::class,
]);
Route::resource('empleados', App\Http\Controllers\EmpleadoController::class);

// ── Autenticación ─────────────────────────────────────────────────────────────
Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::get('/logout', [LogoutController::class, 'logout'])->name('logout');

// ── Páginas de error ──────────────────────────────────────────────────────────
Route::get('/401', fn() => view('pages.401'));
Route::get('/404', fn() => view('pages.404'));
Route::get('/500', fn() => view('pages.500'));
Route::get('/admin', fn() => null)->middleware('role:ADMINISTRADOR');
Route::get('/prueba-permiso', fn() => 'Tienes permiso')->middleware(['auth', 'permission:ver-role']);

// ── CU7 — Cuotas, Multas y Pagos ─────────────────────────────────────────────
Route::resource('tipos-cuotas', TipoCuotaController::class);
Route::resource('cuotas', CuotaController::class);
Route::middleware(['auth'])->group(function () {
    Route::resource('pagos', PagoController::class)->only(['index', 'store']);
    Route::get('/mis-cuotas', [PagoController::class, 'misCuotas'])->name('pagos.mis_cuotas');
    Route::post('/pagos/qr', [PagoController::class, 'pagoQR'])->name('pagos.qr');
    Route::get('/pagos/create/cuota/{cuota}', [PagoController::class, 'createCuota'])->name('pagos.create.cuota');
    Route::get('/pagos/comprobante/{pago}', [PagoController::class, 'comprobante'])->name('pagos.comprobante');
    Route::resource('multas', MultaController::class)->parameters(['multas' => 'multa']);
    Route::get('/pagos/create/multa/{multa}', [PagoController::class, 'createMulta'])->name('pagos.create.multa');
    Route::post('/pagos/qr-multa', [PagoController::class, 'pagoQRMulta'])->name('pagos.qr.multa');
    Route::post('/pagos/stripe/multa', [PagoController::class, 'pagoStripeMulta'])->name('pagos.stripe.multa');
    Route::get('/stripe/success/multa/{multa}', [PagoController::class, 'stripeSuccessMulta'])->name('pagos.stripe.success.multa');
    Route::post('/pagos/stripe', [PagoController::class, 'pagoStripe'])->name('pagos.stripe');
    Route::get('/stripe/success/{cuota}', [PagoController::class, 'stripeSuccess'])->name('pagos.stripe.success');
    Route::get('/stripe/cancel', fn() => redirect()->route('pagos.mis_cuotas')->with('error', 'Pago cancelado.'))->name('pagos.stripe.cancel');
});

// ── CU8 — Áreas comunes y Reservas ───────────────────────────────────────────
Route::middleware(['auth'])->group(function () {
    Route::resource('areas-comunes', AreaComunController::class)->parameters(['areas-comunes' => 'areaComun']);
    Route::resource('reservas', ReservaController::class)->parameters(['reservas' => 'reserva']);
    Route::get('reservas/{reserva}/verificar-inventario', [ReservaController::class, 'verificarInventario'])->name('reservas.verificar-inventario');
    Route::post('/reservas/{reserva}/verificar-inventario', [ReservaController::class, 'guardarVerificacion'])->name('reservas.guardar-verificacion');
});
Route::get('/api/horas-libres', [ReservaController::class, 'horasLibres']);

// ── CU9 — Mantenimientos y Empresas externas ─────────────────────────────────
Route::resource('mantenimientos', App\Http\Controllers\MantenimientoController::class);
Route::resource('empresas', EmpresaExternaController::class);

// ── CU10 — Visitas ────────────────────────────────────────────────────────────
Route::middleware(['auth'])->group(function () {
    Route::resource('visitas', VisitaController::class);
    Route::get('/validar-codigo', [VisitaController::class, 'mostrarValidarCodigo'])->name('visitas.mostrar-validar-codigo');
    Route::post('/visitas/validar-codigo', [VisitaController::class, 'validarCodigo'])->name('visitas.validar-codigo');
    Route::post('/visitas/{visita}/entrada', [VisitaController::class, 'registrarEntrada'])->name('visitas.entrada');
    Route::post('/visitas/{visita}/salida', [VisitaController::class, 'registrarSalida'])->name('visitas.salida');
    Route::get('/panel-guardia', [VisitaController::class, 'panelGuardia'])->name('visitas.panel-guardia');
    Route::get('/buscar-codigo', [VisitaController::class, 'buscarPorCodigo'])->name('visitas.buscar-codigo');
});

// ── CU11 — Comunicados ────────────────────────────────────────────────────────
Route::resource('comunicados', ComunicadoController::class);

// ── Bitácora: cierre de página (beforeunload vía sendBeacon) ──────────────────
// Esta ruta recibe un POST silencioso desde el JS cuando el usuario cierra/abandona la página.
Route::post('/bitacora/page-close', function () {
    if (Auth::check()) {
        Bitacora::create([
            'user_id'    => Auth::id(),
            'usuario'    => Auth::user()->name,
            'accion'     => 'Cerró o abandonó la página del sistema',
            'fecha_hora' => now(),
            'ip'         => request()->ip(),
        ]);
    }
    return response()->noContent();
})->middleware('web')->name('bitacora.page-close');