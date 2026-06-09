<?php

use App\Http\Controllers\AdmisionController;
use App\Http\Controllers\AsignacionDocenteController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\BitacoraController;
use App\Http\Controllers\CarreraController;
use App\Http\Controllers\ComunicadoController;
use App\Http\Controllers\ConsultaVozController;
use App\Http\Controllers\CupoController;
use App\Http\Controllers\DocenteController;
use App\Http\Controllers\GestionController;
use App\Http\Controllers\GrupoController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\MateriaController;
use App\Http\Controllers\NotaController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\PostulanteController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\ResultadoPublicoController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

// Recuperación de contraseña
Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

// Autenticación
Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::get('/logout', [LogoutController::class, 'logout'])->name('logout');

// ── CU-22: Consulta pública de resultados de admisión (sin login) ───────────
Route::get('resultados',  [ResultadoPublicoController::class, 'index'])->name('resultados.publico');
Route::post('resultados', [ResultadoPublicoController::class, 'consultar'])
    ->middleware('throttle:10,1')->name('resultados.consultar'); // 10 consultas/min por IP

Route::middleware('auth')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('panel');
    Route::get('/panel', [HomeController::class, 'index']);
    Route::get('/perfil', [UsuarioController::class, 'miPerfil'])->name('users.perfil');

    // Módulo 1: Autenticación y Seguridad
    Route::resource('users', UsuarioController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('bitacora', BitacoraController::class)->only(['index']);

    // Módulo 2: Registro de Postulantes (CU-05)
    Route::resource('postulantes', PostulanteController::class);

    // Módulo 3: Gestión Académica (CU-06 a CU-09)
    Route::resource('gestiones', GestionController::class);
    Route::resource('carreras', CarreraController::class);
    Route::post('carreras/{carrera}/cupos', [CarreraController::class, 'storeCupo'])->name('carreras.cupos');
    // CU-08: Vista dedicada de cupos (tabla carrera × gestión)
    Route::get('cupos', [CupoController::class, 'index'])->name('cupos.index');
    Route::post('cupos', [CupoController::class, 'store'])->name('cupos.store');
    Route::resource('materias', MateriaController::class);

    // Módulo 4: Asignación de Grupos y Docentes (CU-10 a CU-12)
    Route::resource('docentes', DocenteController::class);

    // Módulo 5: Exámenes y Control Académico (CU-13 a CU-15)
    // ── CU-13/14/15: notas con interfaz propia por caso de uso ─────────────
    Route::get( 'notas/planilla',  [NotaController::class,'planilla'])->name('notas.planilla');
    Route::post('notas/planilla',  [NotaController::class,'guardarPlanilla'])->name('notas.planilla.guardar');
    Route::get( 'notas/calcular',  [NotaController::class,'calcular'])->name('notas.calcular');
    Route::post('notas/calcular',  [NotaController::class,'procesarCalculo'])->name('notas.calcular.procesar');
    Route::get( 'notas/consultar', [NotaController::class,'consultar'])->name('notas.consultar');
    Route::resource('notas', NotaController::class)->except(['destroy','show']);

    // Módulo 6: Panel Administrativo y Reportes (CU-11 grupos, CU-12 asignaciones, CU-16 a CU-18 admisión)
    // CU-11 · Gestionar grupos (la entidad grupo). Rutas literales ANTES del resource.
    Route::get('grupos/generar', [GrupoController::class, 'mostrarGenerador'])->name('grupos.generar.form');
    Route::post('grupos/generar', [GrupoController::class, 'generarAutomatico'])->name('grupos.generar');
    Route::get('grupos/{grupo}/estudiantes', [GrupoController::class, 'estudiantes'])->name('grupos.estudiantes');
    Route::post('grupos/{grupo}/inscribir', [GrupoController::class, 'inscribirPostulantes'])->name('grupos.inscribirPostulantes');
    Route::resource('grupos', GrupoController::class);
    // CU-12 · Asignar docente a grupos y materias (CRUD de asignaciones).
    Route::resource('asignaciones', AsignacionDocenteController::class)
        ->parameters(['asignaciones' => 'asignacion'])->except(['show']);
    // ── CU-16/17/18: proceso de admisión en 3 pasos ────────────────────────
    Route::get( 'admision',             [AdmisionController::class,'index'])->name('admision.index');
    Route::get( 'admision/primera',     [AdmisionController::class,'primera'])->name('admision.primera');
    Route::post('admision/primera',     [AdmisionController::class,'procesarPrimera'])->name('admision.primera.procesar');
    // ── CU-17 MANUAL: asignar/rechazar postulantes individualmente ──────────
    Route::post('admision/segunda/manual/{postulante}',   [AdmisionController::class,'asignarSegundaManual'])->name('admision.segunda.asignar');
    Route::post('admision/segunda/rechazar/{postulante}', [AdmisionController::class,'rechazarSegundaManual'])->name('admision.segunda.rechazar');
    Route::get( 'admision/segunda',     [AdmisionController::class,'segunda'])->name('admision.segunda');
    Route::post('admision/segunda',     [AdmisionController::class,'procesarSegunda'])->name('admision.segunda.procesar');
    Route::post('admision/segunda/{postulante}/asignar', [AdmisionController::class,'asignarSegundaOpcion'])->name('admision.segunda.asignar');
    Route::get( 'admision/publicacion', [AdmisionController::class,'publicacion'])->name('admision.publicacion');
    Route::post('admision/publicar',    [AdmisionController::class,'publicar'])->name('admision.publicar');
    Route::post('admision/reiniciar',   [AdmisionController::class,'reiniciar'])->name('admision.reiniciar');

    // Módulo 7: Pago de inscripción (CU-20) — Stripe Checkout
    Route::get('pagos',                    [PagoController::class, 'index'])->name('pagos.index');
    Route::get('pagos/pagar/{postulante}', [PagoController::class, 'pagar'])->name('pagos.pagar');
    Route::post('pagos/checkout/{postulante}', [PagoController::class, 'checkout'])->name('pagos.checkout');
    Route::get('pagos/exito', [PagoController::class, 'exito'])->name('pagos.exito');
    Route::get('pagos/cancelado/{postulante}', [PagoController::class, 'cancelado'])->name('pagos.cancelado');

    // ── CU-19: Reportes y estadísticas ─────────────────────────────────────
    Route::get('reportes',                              [ReporteController::class,'index'])->name('reportes.index');
    Route::get('reportes/{tipo}',                       [ReporteController::class,'show'])->name('reportes.show');
    Route::get('reportes/{tipo}/exportar/{formato}',    [ReporteController::class,'exportar'])->name('reportes.exportar');

    // ── CU-21: Comunicados ─────────────────────────────────────────────────
    Route::resource('comunicados', ComunicadoController::class)->except(['show']);

    // ── Asistente de Consulta por Voz con IA (Whisper) ──────────────────────
    Route::post('consulta-voz/transcribir', [ConsultaVozController::class, 'transcribir'])
        ->middleware('throttle:20,1')->name('consulta-voz.transcribir');
    Route::post('consulta-voz/responder', [ConsultaVozController::class, 'responder'])
        ->middleware('throttle:30,1')->name('consulta-voz.responder');
    Route::get('consulta-voz/comandos', [ConsultaVozController::class, 'comandos'])
        ->name('consulta-voz.comandos');
});

// Bitácora: cierre de pestaña
Route::post('/bitacora/page-close', function () {
    if (Auth::check()) {
        DB::table('bitacoras')->insert([
            'user_id' => Auth::id(), 'usuario' => Auth::user()->name,
            'accion' => 'Cerró o abandonó la página del sistema',
            'modulo' => 'Seguridad', 'metodo_http' => 'POST', 'ruta' => 'bitacora/page-close',
            'fecha_hora' => now(), 'ip' => request()->ip(),
            'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    return response()->noContent();
})->middleware('web')->name('bitacora.page-close');

// Webhook de Stripe (sin auth ni CSRF — Stripe llama directo)
Route::post('stripe/webhook', [PagoController::class, 'webhook'])->name('stripe.webhook');

Route::get('/401', fn () => view('pages.401'));
Route::get('/404', fn () => view('pages.404'));
Route::get('/500', fn () => view('pages.500'));
