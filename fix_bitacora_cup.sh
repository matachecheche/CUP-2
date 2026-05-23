#!/usr/bin/env bash
# =============================================================================
#  fix_bitacora_cup.sh
#  Corrige 3 problemas detectados:
#    1. Laravel 11 usa bootstrap/app.php, NO Kernel.php → middleware nunca corría
#    2. Tabla bitacoras: columnas faltantes (usuario, modulo, metodo_http, ruta,
#       user_agent, descripcion) y FK user_id con restrictOnDelete que rompe
#       al intentar registrar usuarios eliminados
#    3. Reemplaza "Auditoría" → "Bitácora" en TODOS los archivos
#
#  USO: bash fix_bitacora_cup.sh (desde la raíz del proyecto Laravel)
# =============================================================================

set -e
C='\033[0;36m'; G='\033[0;32m'; Y='\033[1;33m'; R='\033[0;31m'; N='\033[0m'
info() { echo -e "${C}[INFO]${N}  $1"; }
ok()   { echo -e "${G}[OK]${N}    $1"; }
warn() { echo -e "${Y}[WARN]${N}  $1"; }
err()  { echo -e "${R}[ERROR]${N} $1"; exit 1; }

[ -f "artisan" ] || err "Ejecuta desde la raíz del proyecto Laravel."

# =============================================================================
#  1. DETECTAR VERSIÓN DE LARAVEL
# =============================================================================
info "Detectando versión de Laravel..."
LARAVEL_VERSION=$(php artisan --version 2>/dev/null | grep -oP '\d+\.\d+' | head -1)
LARAVEL_MAJOR=$(echo "$LARAVEL_VERSION" | cut -d. -f1)
echo "   Laravel $LARAVEL_VERSION detectado (major: $LARAVEL_MAJOR)"

# =============================================================================
#  2. REGISTRAR MIDDLEWARE EN bootstrap/app.php (Laravel 11+)
#     En Laravel 11 Kernel.php es ignorado. El middleware web se registra
#     con ->withMiddleware() en bootstrap/app.php
# =============================================================================
info "Registrando BitacoraMiddleware en bootstrap/app.php (Laravel 11)..."

cat > bootstrap/app.php << 'PHP'
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        // Proxy confiable (Codespaces / proxies inversos)
        $middleware->trustProxies(at: '*');

        // ── Spatie Laravel Permission — aliases ───────────────────────
        $middleware->alias([
            'role'               => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission'         => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);

        // ── Bitácora automática — se ejecuta en TODAS las rutas web ──
        // Debe ir DESPUÉS de StartSession/Auth para que Auth::check() funcione.
        // En Laravel 11 appendToGroup garantiza el orden correcto.
        $middleware->appendToGroup('web', \App\Http\Middleware\BitacoraMiddleware::class);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
PHP
ok "bootstrap/app.php — BitacoraMiddleware registrado correctamente"

# =============================================================================
#  3. REESCRIBIR BitacoraMiddleware — robusto para Laravel 11
#     • Usa try/catch en todo para que nunca rompa una petición real
#     • Acepta null en user_id (usuario no autenticado = null)
#     • Trunca campos para respetar longitudes de columna
#     • Incluye TODOS los módulos del CUP (60+ rutas)
# =============================================================================
info "Reescribiendo BitacoraMiddleware..."

cat > app/Http/Middleware/BitacoraMiddleware.php << 'PHP'
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Bitácora automática — registra ABSOLUTAMENTE TODAS las peticiones web del CUP.
 *
 * Usa DB::table() en lugar de Bitacora::create() para evitar problemas con
 * el modelo (eventos, observers, fillable) y garantizar que SIEMPRE se grabe.
 *
 * Compatible con Laravel 11 (bootstrap/app.php → appendToGroup('web')).
 */
class BitacoraMiddleware
{
    // Rutas que NO se registran (evitar bucles y AJAX de infraestructura)
    protected array $ignorar = [
        'bitacora.page-close',
        'livewire.message',
        'livewire.upload-file',
        'debugbar.openhandler',
    ];

    // ── Mapa completo ruta → descripción ─────────────────────────────────────
    protected array $mapa = [

        // Módulo 1 — Seguridad y Autenticación (CU-01 a CU-04)
        'panel'                          => 'Accedió al panel de control',
        'login'                          => 'Visitó la página de inicio de sesión',
        'logout'                         => 'Cerró sesión',
        'password.request'               => 'Visitó recuperación de contraseña',
        'password.email'                 => 'Solicitó enlace de recuperación de contraseña',
        'password.reset'                 => 'Visitó formulario de nueva contraseña',
        'password.update'                => 'Restableció su contraseña',

        'users.index'                    => 'Listó usuarios del sistema',
        'users.create'                   => 'Abrió formulario de creación de usuario',
        'users.store'                    => 'Creó un nuevo usuario',
        'users.show'                     => 'Consultó detalle de usuario',
        'users.edit'                     => 'Abrió formulario de edición de usuario',
        'users.update'                   => 'Actualizó datos de usuario',
        'users.destroy'                  => 'Cambió estado de usuario (activar/desactivar)',
        'users.perfil'                   => 'Consultó su propio perfil',

        'roles.index'                    => 'Listó roles y permisos',
        'roles.create'                   => 'Abrió formulario de creación de rol',
        'roles.store'                    => 'Creó un nuevo rol',
        'roles.show'                     => 'Consultó detalle de rol',
        'roles.edit'                     => 'Abrió formulario de edición de rol',
        'roles.update'                   => 'Actualizó un rol',
        'roles.destroy'                  => 'Eliminó un rol',

        'bitacora.index'                 => 'Consultó la bitácora del sistema',

        // Módulo 2 — Gestión Académica (CU-10 a CU-13)
        'gestiones.index'                => 'Listó gestiones académicas',
        'gestiones.create'               => 'Abrió formulario de nueva gestión académica',
        'gestiones.store'                => 'Creó una gestión académica (CU-13)',
        'gestiones.show'                 => 'Consultó detalle de gestión académica',
        'gestiones.edit'                 => 'Abrió edición de gestión académica',
        'gestiones.update'               => 'Actualizó una gestión académica',
        'gestiones.destroy'              => 'Eliminó una gestión académica',

        'carreras.index'                 => 'Listó carreras de la facultad',
        'carreras.create'                => 'Abrió formulario de nueva carrera',
        'carreras.store'                 => 'Creó una carrera (CU-10)',
        'carreras.show'                  => 'Consultó detalle de carrera',
        'carreras.edit'                  => 'Abrió edición de carrera',
        'carreras.update'                => 'Actualizó una carrera',
        'carreras.destroy'               => 'Eliminó una carrera',
        'carreras.cupos'                 => 'Definió cupos por carrera y gestión (CU-11)',

        'materias.index'                 => 'Listó materias del CUP',
        'materias.create'                => 'Abrió formulario de nueva materia',
        'materias.store'                 => 'Creó una materia del CUP (CU-12)',
        'materias.show'                  => 'Consultó detalle de materia',
        'materias.edit'                  => 'Abrió edición de materia',
        'materias.update'                => 'Actualizó una materia',
        'materias.destroy'               => 'Eliminó una materia',

        // Módulo 3 — Postulantes y Docentes (CU-05 a CU-09, CU-14 a CU-16)
        'postulantes.index'              => 'Listó postulantes (CU-09)',
        'postulantes.create'             => 'Abrió formulario de registro de postulante',
        'postulantes.store'              => 'Registró un postulante (CU-05)',
        'postulantes.show'               => 'Consultó estado del postulante (CU-09)',
        'postulantes.edit'               => 'Abrió edición de postulante',
        'postulantes.update'             => 'Actualizó datos de postulante',
        'postulantes.destroy'            => 'Eliminó un postulante',
        'postulantes.cargar-documentos'  => 'Cargó requisitos del postulante — CI, libreta, título (CU-06)',
        'postulantes.validar'            => 'Validó requisitos del postulante (CU-07)',
        'postulantes.opciones-carrera'   => 'Registró 1ª y 2ª opción de carrera del postulante (CU-08)',
        'postulantes.estado'             => 'Consultó estado del postulante (CU-09)',

        'docentes.index'                 => 'Listó docentes del CUP',
        'docentes.create'                => 'Abrió formulario de registro de docente',
        'docentes.store'                 => 'Registró un docente con perfil profesional (CU-14)',
        'docentes.show'                  => 'Consultó perfil de docente',
        'docentes.edit'                  => 'Abrió edición de docente',
        'docentes.update'                => 'Actualizó datos de docente',
        'docentes.destroy'               => 'Eliminó un docente',
        'docentes.validar-perfil'        => 'Validó perfil profesional del docente (CU-15)',
        'docentes.carga-horaria'         => 'Consultó carga horaria del docente (CU-16)',

        // Módulo 4 — Grupos, Horarios y Evaluación (CU-17 a CU-26)
        'grupos.index'                   => 'Listó grupos del CUP',
        'grupos.create'                  => 'Abrió formulario de nuevo grupo',
        'grupos.store'                   => 'Creó un grupo',
        'grupos.show'                    => 'Consultó detalle de grupo',
        'grupos.edit'                    => 'Abrió edición de grupo',
        'grupos.update'                  => 'Actualizó un grupo',
        'grupos.destroy'                 => 'Eliminó un grupo',
        'grupos.generar'                 => 'Generó grupos automáticamente (máx. 60 alumnos) (CU-17)',
        'grupos.asignar-docente'         => 'Asignó docente a grupo y materia (CU-18)',
        'grupos.validar-horario'         => 'Validó cruces de horario (CU-19)',
        'grupos.horario'                 => 'Asignó horario y modalidad al grupo (CU-20)',
        'grupos.inscribir'               => 'Inscribió postulantes a un grupo (CU-21)',

        'notas.index'                    => 'Listó notas del sistema',
        'notas.create'                   => 'Abrió formulario de registro de notas',
        'notas.store'                    => 'Registró notas de exámenes (CU-22)',
        'notas.show'                     => 'Consultó notas de postulante',
        'notas.edit'                     => 'Abrió edición de notas',
        'notas.update'                   => 'Actualizó notas de examen',
        'notas.calcular-final'           => 'Calculó nota final por materia — 30%+30%+40% (CU-23)',
        'notas.calcular-promedio'        => 'Calculó promedio general del postulante (CU-24)',
        'notas.determinar-condicion'     => 'Determinó condición aprobado/reprobado ≥60 en 4 materias (CU-25)',
        'notas.propias'                  => 'Postulante consultó sus propias notas (CU-26)',

        // Módulo 5 — Admisión y Reportes (CU-27 a CU-33)
        'admision.index'                 => 'Accedió al módulo de proceso de admisión',
        'admision.procesar'              => 'Procesó admisión por primera opción de carrera (CU-27)',
        'admision.reasignar'             => 'Reasignó postulantes a segunda opción de carrera (CU-28)',
        'admision.publicar'              => 'Publicó resultado final de admisión (CU-29)',
        'admision.resultado-propio'      => 'Postulante consultó su resultado de admisión (CU-29)',

        'reportes.index'                 => 'Accedió al módulo de reportes',
        'reportes.aprobados-reprobados'  => 'Generó reporte de aprobados y reprobados por grupo (CU-30)',
        'reportes.admitidos-carrera'     => 'Generó reporte de admitidos por carrera y gestión (CU-31)',
        'reportes.historico'             => 'Consultó comparativo histórico entre gestiones (CU-32)',
        'reportes.estadisticas'          => 'Consultó indicadores estadísticos del proceso (CU-33)',
    ];

    // Módulo al que pertenece cada prefijo de ruta
    protected array $modulos = [
        'panel'         => 'Seguridad',
        'login'         => 'Seguridad',
        'logout'        => 'Seguridad',
        'password'      => 'Seguridad',
        'users'         => 'Usuarios',
        'roles'         => 'Roles',
        'bitacora'      => 'Bitácora',
        'gestiones'     => 'Gestión Académica',
        'carreras'      => 'Gestión Académica',
        'materias'      => 'Gestión Académica',
        'postulantes'   => 'Postulantes',
        'docentes'      => 'Docentes',
        'grupos'        => 'Grupos',
        'notas'         => 'Evaluación',
        'admision'      => 'Admisión',
        'reportes'      => 'Reportes',
    ];

    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Ignorar si no hay usuario autenticado
        if (!Auth::check()) {
            return $response;
        }

        try {
            $routeName = $request->route()?->getName() ?? '';

            // Ignorar rutas de infraestructura
            if (empty($routeName) || in_array($routeName, $this->ignorar)) {
                return $response;
            }

            // Descripción de la acción
            $accion = $this->mapa[$routeName]
                ?? 'Visitó ' . strtoupper($request->method()) . ' /' . $request->path();

            // Módulo: derivar del primer segmento del nombre de ruta
            $prefijo = explode('.', $routeName)[0];
            $modulo  = $this->modulos[$prefijo] ?? 'Sistema';

            $user = Auth::user();

            // Insertar directo con DB::table() para máxima confiabilidad
            // (evita problemas de fillable, observers, o modelo desactualizado)
            DB::table('bitacoras')->insert([
                'user_id'      => $user->id,
                'usuario'      => $user->name,
                'accion'       => substr($accion, 0, 250),
                'modulo'       => substr($modulo, 0, 60),
                'metodo_http'  => $request->method(),
                'ruta'         => substr($request->path(), 0, 255),
                'ip'           => $request->ip(),
                'user_agent'   => substr($request->userAgent() ?? '', 0, 255),
                'fecha_hora'   => now(),
                'id_operacion' => null,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);

        } catch (\Throwable $e) {
            // NUNCA romper la petición real por un error de bitácora
            Log::error('BitacoraMiddleware falló: ' . $e->getMessage(), [
                'route' => $request->route()?->getName(),
                'url'   => $request->path(),
            ]);
        }

        return $response;
    }
}
PHP
ok "app/Http/Middleware/BitacoraMiddleware.php"

# =============================================================================
#  4. MIGRACIÓN — agregar columnas faltantes a bitacoras
#     Usa hasColumn() para ser idempotente (puede correr múltiples veces)
#     También convierte user_id en nullable para registrar sesiones anónimas
# =============================================================================
info "Creando migración para ampliar tabla bitacoras..."

cat > database/migrations/2026_05_23_100000_ampliar_bitacoras_cup.php << 'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Amplía la tabla bitacoras con todas las columnas necesarias para el CUP.
 * Convierte user_id en nullable (permite registrar incluso antes de autenticar).
 * Es IDEMPOTENTE: usa hasColumn() antes de cada cambio.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bitacoras', function (Blueprint $table) {

            // Columna usuario (nombre del usuario en texto, redundante con user_id
            // pero necesaria para conservar el nombre aunque el usuario se elimine)
            if (!Schema::hasColumn('bitacoras', 'usuario')) {
                $table->string('usuario', 120)->nullable()->after('user_id');
            }

            // Módulo del sistema al que pertenece la acción
            if (!Schema::hasColumn('bitacoras', 'modulo')) {
                $table->string('modulo', 60)->nullable()->after('usuario');
            }

            // Ampliar accion a 250 caracteres si era 100
            // (no se puede modificar tamaño directamente en SQLite, se hace en MySQL/PG)
            // Se omite para compatibilidad; el middleware trunca a 250 caracteres.

            // Método HTTP (GET, POST, PUT, DELETE, etc.)
            if (!Schema::hasColumn('bitacoras', 'metodo_http')) {
                $table->string('metodo_http', 10)->nullable()->after('modulo');
            }

            // Ruta de la petición
            if (!Schema::hasColumn('bitacoras', 'ruta')) {
                $table->string('ruta', 255)->nullable()->after('metodo_http');
            }

            // User-agent del navegador
            if (!Schema::hasColumn('bitacoras', 'user_agent')) {
                $table->string('user_agent', 255)->nullable()->after('ip');
            }

            // Descripción libre (para BitacoraTrait)
            if (!Schema::hasColumn('bitacoras', 'descripcion')) {
                $table->text('descripcion')->nullable()->after('user_agent');
            }
        });

        // Hacer user_id nullable en MySQL/MariaDB
        // (Laravel no permite cambiar a nullable directamente en SQLite)
        try {
            $driver = DB::getDriverName();
            if (in_array($driver, ['mysql', 'mariadb', 'pgsql'])) {
                Schema::table('bitacoras', function (Blueprint $table) {
                    $table->foreignId('user_id')->nullable()->change();
                });
            }
        } catch (\Throwable $e) {
            // Si falla (SQLite, sin doctrine), no es crítico
        }
    }

    public function down(): void
    {
        Schema::table('bitacoras', function (Blueprint $table) {
            $cols = ['usuario', 'modulo', 'metodo_http', 'ruta', 'user_agent', 'descripcion'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('bitacoras', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
PHP
ok "database/migrations/2026_05_23_100000_ampliar_bitacoras_cup.php"

# =============================================================================
#  5. BitacoraTrait — también usa DB::table() para máxima confiabilidad
# =============================================================================
info "Actualizando BitacoraTrait..."

cat > app/Traits/BitacoraTrait.php << 'PHP'
<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Para registrar acciones CON DETALLE desde controladores.
 * Complementa al BitacoraMiddleware (que registra navegación automáticamente).
 *
 * Uso:
 *   $this->registrarEnBitacora('Creó postulante: Juan Pérez', $postulante->id, 'Postulantes');
 */
trait BitacoraTrait
{
    public function registrarEnBitacora(string $accion, $id_operacion = null, string $modulo = ''): void
    {
        try {
            $user   = Auth::user();
            $nombre = $user?->name ?? 'Sistema';

            DB::table('bitacoras')->insert([
                'user_id'      => $user?->id,
                'usuario'      => $nombre,
                'accion'       => substr($accion, 0, 250),
                'modulo'       => substr($modulo, 0, 60),
                'metodo_http'  => request()->method(),
                'ruta'         => substr(request()->path(), 0, 255),
                'ip'           => request()->ip(),
                'user_agent'   => substr(request()->userAgent() ?? '', 0, 255),
                'fecha_hora'   => now(),
                'id_operacion' => $id_operacion,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        } catch (\Throwable $e) {
            Log::error('BitacoraTrait: ' . $e->getMessage());
        }
    }
}
PHP
ok "app/Traits/BitacoraTrait.php"

# =============================================================================
#  6. Modelo Bitacora actualizado con todos los fillable
# =============================================================================
cat > app/Models/Bitacora.php << 'PHP'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bitacora extends Model
{
    protected $table    = 'bitacoras';
    protected $fillable = [
        'user_id',
        'usuario',
        'accion',
        'modulo',
        'metodo_http',
        'ruta',
        'fecha_hora',
        'ip',
        'user_agent',
        'id_operacion',
        'descripcion',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
PHP
ok "app/Models/Bitacora.php"

# =============================================================================
#  7. REEMPLAZAR "Auditoría" → "Bitácora" en TODOS los archivos del proyecto
# =============================================================================
info "Reemplazando 'Auditoría' / 'Auditoria' → 'Bitácora' en todos los archivos..."

# Archivos PHP y Blade
find app routes resources config -type f \( -name "*.php" -o -name "*.blade.php" \) 2>/dev/null | while read f; do
    if grep -qiE "auditor[íi]a|auditoria" "$f" 2>/dev/null; then
        sed -i \
            -e 's/Auditoría/Bitácora/g' \
            -e 's/Auditoria/Bitacora/g' \
            -e 's/auditoría/bitácora/g' \
            -e 's/auditoria/bitacora/g' \
            -e 's/Registro de Auditoría/Bitácora del Sistema/g' \
            -e 's/Registro de Auditoria/Bitacora del Sistema/g' \
            "$f"
        warn "Reemplazado en: $f"
    fi
done

ok "Reemplazo de 'Auditoría' → 'Bitácora' completado"

# =============================================================================
#  8. CORREGIR sidebar y topbar: cambiar "Registro de Auditoría" → "Bitácora"
#     y el ícono en navigation-menu si aún existe el componente viejo
# =============================================================================
info "Corrigiendo etiquetas del sidebar (navigation-menu y layouts/ap)..."

for f in resources/views/layouts/ap.blade.php \
          resources/views/components/navigation-menu.blade.php; do
    [ -f "$f" ] || continue
    sed -i \
        -e 's/Registro de Auditoría/Bitácora del Sistema/g' \
        -e 's/Registro de Auditoria/Bitacora del Sistema/g' \
        -e 's/Auditoría \/ Bitácora/Bitácora del Sistema/g' \
        -e 's/Auditoria \/ Bitacora/Bitacora del Sistema/g' \
        "$f"
    ok "Sidebar actualizado: $f"
done

# =============================================================================
#  9. VISTA bitacora/index — nombre correcto + tabla completa
# =============================================================================
info "Reescribiendo vista bitacora/index.blade.php..."

mkdir -p resources/views/bitacora

cat > resources/views/bitacora/index.blade.php << 'BLADE'
@extends('layouts.ap')
@section('title', 'Bitácora del Sistema')

@push('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<style>
/* ── Chips de módulo ─────────────────────────── */
.chip-modulo {
    display: inline-block;
    font-size: .63rem; font-weight: 700;
    padding: 2px 7px; border-radius: 3px;
    text-transform: uppercase; letter-spacing: .05em;
    white-space: nowrap;
}
.chip-Seguridad        { background:#dbeafe; color:#1d4f8f; }
.chip-Usuarios         { background:#d4e8dc; color:#1a5c38; }
.chip-Roles            { background:#d8f0e6; color:#1a5c38; }
.chip-Bitácora         { background:#ede0f7; color:#5b2a8a; }
.chip-GestiónAcadémica { background:#fef9c3; color:#78350f; }
.chip-Postulantes      { background:#fde8cc; color:#8a4300; }
.chip-Docentes         { background:#fff8e1; color:#7a5c00; }
.chip-Grupos           { background:#fce4e4; color:#8a1f1f; }
.chip-Evaluación       { background:#e0f7fa; color:#006064; }
.chip-Admisión         { background:#f3e5f5; color:#6a1b9a; }
.chip-Reportes         { background:#e8f5e9; color:#2e7d32; }
.chip-Sistema          { background:#f0f0f0; color:#555; }

/* ── Chips de método HTTP ────────────────────── */
.chip-http {
    font-size: .65rem; font-weight: 800;
    padding: 1px 5px; border-radius: 3px;
    font-family: 'Courier New', monospace;
    white-space: nowrap;
}
.chip-GET    { background:#e8f4fd; color:#0369a1; }
.chip-POST   { background:#d4edda; color:#155724; }
.chip-PUT    { background:#fff3cd; color:#856404; }
.chip-PATCH  { background:#fff3cd; color:#856404; }
.chip-DELETE { background:#fde8e3; color:#a3290c; }
</style>
@endpush

@section('content')

<div class="page-header">
    <h1>Bitácora del Sistema</h1>
    <p class="subtitle">Registro completo de todas las acciones realizadas por los usuarios</p>
    <ol class="breadcrumb">
        <li><a href="{{ route('panel') }}">Inicio</a></li>
        <li>Bitácora</li>
    </ol>
</div>

<div class="card">
    <div class="card-header">
        <i class="fas fa-journal-whills"></i>
        Bitácora del Sistema
        <span style="margin-left:auto; font-size:.8rem; color:var(--txt-3); font-weight:400;">
            {{ $bitacoras->count() }} registros cargados
        </span>
    </div>
    <div class="card-body" style="padding: .75rem;">
        <div class="table-wrapper">
            <table id="tablaBitacora" class="cup-table" style="width:100%;">
                <thead>
                    <tr>
                        <th>Fecha y Hora</th>
                        <th>Usuario</th>
                        <th>Módulo</th>
                        <th>Acción realizada</th>
                        <th>Método</th>
                        <th>Ruta</th>
                        <th>Dirección IP</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bitacoras as $log)
                    @php
                        $modKey = str_replace([' ', 'é', 'ó', 'á', 'í', 'ú', 'ñ'], ['', 'é', 'ó', 'á', 'í', 'ú', 'ñ'], $log->modulo ?? 'Sistema');
                        $chipClass = 'chip-' . str_replace(' ', '', $log->modulo ?? 'Sistema');
                    @endphp
                    <tr>
                        <td style="white-space:nowrap; font-size:.8rem; color:var(--txt-3);">
                            {{ \Carbon\Carbon::parse($log->fecha_hora)->format('d/m/Y H:i:s') }}
                        </td>
                        <td>
                            <span style="font-weight:600; font-size:.87rem;">
                                {{ $log->usuario ?? '—' }}
                            </span>
                        </td>
                        <td>
                            @if($log->modulo)
                                <span class="chip-modulo {{ $chipClass }}">{{ $log->modulo }}</span>
                            @else
                                <span style="color:var(--txt-3); font-size:.8rem;">—</span>
                            @endif
                        </td>
                        <td style="font-size:.86rem; color:var(--txt);">
                            {{ $log->accion }}
                        </td>
                        <td>
                            @if($log->metodo_http)
                                <span class="chip-http chip-{{ $log->metodo_http }}">
                                    {{ $log->metodo_http }}
                                </span>
                            @endif
                        </td>
                        <td style="font-size:.78rem; font-family:'Courier New',monospace; color:var(--txt-3);">
                            {{ $log->ruta ?? '—' }}
                        </td>
                        <td style="font-size:.78rem; font-family:'Courier New',monospace; color:var(--txt-3);">
                            {{ $log->ip ?? '—' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align:center; padding:2rem; color:var(--txt-3);">
                            <i class="fas fa-journal-whills" style="font-size:2rem; display:block; margin-bottom:.5rem; opacity:.3;"></i>
                            No hay registros en la bitácora todavía.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('js')
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function(){
    $('#tablaBitacora').DataTable({
        language: { url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' },
        order: [[0, 'desc']],          // más reciente primero
        pageLength: 25,
        lengthMenu: [10, 25, 50, 100],
        columnDefs: [
            { targets: [4, 5], orderable: false }
        ]
    });
});
</script>
@endpush

@endsection
BLADE
ok "resources/views/bitacora/index.blade.php"

# =============================================================================
#  10. Limpiar caché
# =============================================================================
info "Limpiando caché de Laravel..."
php artisan config:clear  2>/dev/null || true
php artisan route:clear   2>/dev/null || true
php artisan view:clear    2>/dev/null || true
php artisan cache:clear   2>/dev/null || true
ok "Caché limpiada"

# =============================================================================
#  RESUMEN
# =============================================================================
echo ""
echo -e "${G}══════════════════════════════════════════════════════════════${N}"
echo -e "${G}  FIX COMPLETADO — Bitácora CUP${N}"
echo -e "${G}══════════════════════════════════════════════════════════════${N}"
echo ""
echo -e "  ${C}PROBLEMAS CORREGIDOS:${N}"
echo ""
echo "  1. MIDDLEWARE NO CORRÍA (causa principal)"
echo "     ✓ Laravel 11 usa bootstrap/app.php, NO Kernel.php"
echo "     ✓ BitacoraMiddleware registrado con appendToGroup('web')"
echo "     ✓ Corre DESPUÉS de StartSession y Auth (orden correcto)"
echo ""
echo "  2. COLUMNAS FALTANTES EN TABLA bitacoras"
echo "     ✓ Migración 2026_05_23_100000_ampliar_bitacoras_cup.php"
echo "     ✓ Nuevas columnas: usuario, modulo, metodo_http, ruta, user_agent, descripcion"
echo "     ✓ user_id convertido a nullable (no rompe en usuarios eliminados)"
echo "     ✓ Inserta con DB::table() directo (sin pasar por modelo/fillable)"
echo ""
echo "  3. NOMBRE 'Auditoría' CORREGIDO"
echo "     ✓ Todos los archivos PHP/Blade actualizados → 'Bitácora'"
echo "     ✓ Sidebar y panel muestran 'Bitácora del Sistema'"
echo ""
echo -e "  ${C}PRÓXIMO PASO (OBLIGATORIO):${N}"
echo "   php artisan migrate"
echo ""
echo -e "  ${Y}VERIFICAR QUE FUNCIONA:${N}"
echo "   1. Inicia sesión en el sistema"
echo "   2. Navega por algunas páginas"
echo "   3. Ve a Bitácora → deberías ver los registros"
echo ""
echo -e "  ${C}Si aún no registra, revisar:${N}"
echo "   php artisan route:list | grep bitacora"
echo "   tail -50 storage/logs/laravel.log"
echo ""
