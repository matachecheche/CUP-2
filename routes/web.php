<?php
use App\Http\Controllers\Auth\{ForgotPasswordController,ResetPasswordController};
use App\Http\Controllers\{AdmisionController,CupoController,BitacoraController,CarreraController,DocenteController,GestionController,GrupoController,HomeController,LoginController,LogoutController,MateriaController,NotaController,PostulanteController,RoleController,UsuarioController};
use Illuminate\Support\Facades\{Auth,DB,Route};

// Recuperación de contraseña
Route::get('password/reset',         [ForgotPasswordController::class,'showLinkRequestForm'])->name('password.request');
Route::post('password/email',        [ForgotPasswordController::class,'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset',        [ResetPasswordController::class, 'reset'])->name('password.update');

// Autenticación
Route::get('/login',  [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::get('/logout', [LogoutController::class,'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/',      [HomeController::class,'index'])->name('panel');
    Route::get('/panel', [HomeController::class,'index']);
    Route::get('/perfil',[UsuarioController::class,'miPerfil'])->name('users.perfil');

    // Módulo 1: Autenticación y Seguridad
    Route::resource('users',    UsuarioController::class);
    Route::resource('roles',    RoleController::class);
    Route::resource('bitacora', BitacoraController::class)->only(['index']);

    // Módulo 2: Registro de Postulantes (CU-05)
    Route::resource('postulantes', PostulanteController::class);

    // Módulo 3: Gestión Académica (CU-06 a CU-09)
    Route::resource('gestiones', GestionController::class);
    Route::resource('carreras',  CarreraController::class);
    Route::post('carreras/{carrera}/cupos',[CarreraController::class,'storeCupo'])->name('carreras.cupos');
    // CU-08: Vista dedicada de cupos (tabla carrera × gestión)
    Route::get('cupos',  [CupoController::class,'index'])->name('cupos.index');
    Route::post('cupos', [CupoController::class,'store'])->name('cupos.store');
    Route::resource('materias',  MateriaController::class);

    // Módulo 4: Asignación de Grupos y Docentes (CU-10 a CU-12)
    Route::resource('docentes', DocenteController::class);

    // Módulo 5: Exámenes y Control Académico (CU-13 a CU-15)
    Route::resource('notas', NotaController::class)->except(['destroy']);

    // Módulo 6: Panel Administrativo y Reportes (CU-11/12 grupos, CU-16 a CU-18 admisión)
    Route::resource('grupos', GrupoController::class);
    Route::post('grupos/generar-automatico',          [GrupoController::class,'generar'])->name('grupos.generar');
    Route::post('grupos/{grupo}/asignar-docente',    [GrupoController::class,'asignarDocente'])->name('grupos.asignarDocente');
    Route::post('grupos/{grupo}/inscribir',          [GrupoController::class,'inscribirPostulantes'])->name('grupos.inscribirPostulantes');
    Route::get( 'admision',        [AdmisionController::class,'index'])->name('admision.index');
    Route::post('admision/procesar',[AdmisionController::class,'procesar'])->name('admision.procesar');
    Route::post('admision/publicar',[AdmisionController::class,'publicar'])->name('admision.publicar');
});

// Bitácora: cierre de pestaña
Route::post('/bitacora/page-close', function () {
    if (Auth::check()) {
        DB::table('bitacoras')->insert([
            'user_id'=>Auth::id(),'usuario'=>Auth::user()->name,
            'accion'=>'Cerró o abandonó la página del sistema',
            'modulo'=>'Seguridad','metodo_http'=>'POST','ruta'=>'bitacora/page-close',
            'fecha_hora'=>now(),'ip'=>request()->ip(),
            'created_at'=>now(),'updated_at'=>now(),
        ]);
    }
    return response()->noContent();
})->middleware('web')->name('bitacora.page-close');

Route::get('/401',fn()=>view('pages.401'));
Route::get('/404',fn()=>view('pages.404'));
Route::get('/500',fn()=>view('pages.500'));
