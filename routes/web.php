<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\BitacoraController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Models\Bitacora;

// ── Recuperación de contraseña ────────────────────────────────────────────────
Route::get('password/reset',          [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email',         [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}',  [ResetPasswordController::class,  'showResetForm'])->name('password.reset');
Route::post('password/reset',         [ResetPasswordController::class,  'reset'])->name('password.update');

// ── Autenticación ─────────────────────────────────────────────────────────────
Route::get('/login',  [LoginController::class,  'index'])->name('login');
Route::post('/login', [LoginController::class,  'login']);
Route::get('/logout', [LogoutController::class, 'logout'])->name('logout');

// ── Panel principal ───────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/',       [HomeController::class, 'index'])->name('panel');
    Route::get('/panel',  [HomeController::class, 'index']);

    // Perfil propio
    Route::get('/perfil', [UsuarioController::class, 'miPerfil'])->name('users.perfil');

    // ── MÓDULO DE SEGURIDAD (ya implementado) ─────────────────────────────────
    Route::resource('users',  UsuarioController::class);
    Route::resource('roles',  RoleController::class);
    Route::resource('bitacora', BitacoraController::class)->only(['index']);

    // ── MÓDULO ACADÉMICO (implementar en próximos ciclos) ─────────────────────
    // Route::resource('gestiones', GestionController::class);
    // Route::resource('carreras',  CarreraController::class);
    // Route::resource('materias',  MateriaController::class);
    // Route::resource('cupos',     CupoCarreraController::class);

    // ── MÓDULO DE DOCENTES ────────────────────────────────────────────────────
    // Route::resource('docentes', DocenteController::class);

    // ── MÓDULO DE POSTULANTES ─────────────────────────────────────────────────
    // Route::resource('postulantes', PostulanteController::class);

    // ── MÓDULO DE GRUPOS / AULAS ──────────────────────────────────────────────
    // Route::resource('grupos', GrupoController::class);
    // Route::post('/grupos/generar', [GrupoController::class, 'generarAutomatico'])->name('grupos.generar');

    // ── MÓDULO DE HORARIOS ────────────────────────────────────────────────────
    // Route::resource('horarios', HorarioController::class);

    // ── MÓDULO DE EVALUACIÓN ──────────────────────────────────────────────────
    // Route::resource('notas', NotaController::class);

    // ── MÓDULO DE ADMISIÓN ────────────────────────────────────────────────────
    // Route::resource('admision', AdmisionController::class);

    // ── REPORTES ──────────────────────────────────────────────────────────────
    // Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');
});

// ── Bitácora: cierre de página (sendBeacon) ───────────────────────────────────
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

// ── Páginas de error ──────────────────────────────────────────────────────────
Route::get('/401', fn() => view('pages.401'));
Route::get('/404', fn() => view('pages.404'));
Route::get('/500', fn() => view('pages.500'));
