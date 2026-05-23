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
