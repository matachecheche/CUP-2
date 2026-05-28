<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BitacoraMiddleware
{
    protected array $ignorar = ['bitacora.page-close','livewire.message','debugbar.openhandler'];

    protected array $mapa = [
        'panel'                         => ['Accedió al panel de control','Seguridad'],
        'login'                         => ['Visitó la página de inicio de sesión','Seguridad'],
        'logout'                        => ['Cerró sesión','Seguridad'],
        'password.request'              => ['Visitó recuperación de contraseña','Seguridad'],
        'password.email'                => ['Solicitó enlace de recuperación','Seguridad'],
        'password.reset'                => ['Visitó formulario nueva contraseña','Seguridad'],
        'password.update'               => ['Restableció su contraseña','Seguridad'],
        'users.index'                   => ['Listó usuarios del sistema','Usuarios'],
        'users.create'                  => ['Abrió formulario crear usuario','Usuarios'],
        'users.store'                   => ['Creó un nuevo usuario','Usuarios'],
        'users.show'                    => ['Vio detalle de usuario','Usuarios'],
        'users.edit'                    => ['Abrió formulario editar usuario','Usuarios'],
        'users.update'                  => ['Actualizó datos de usuario','Usuarios'],
        'users.destroy'                 => ['Cambió estado de usuario','Usuarios'],
        'users.perfil'                  => ['Consultó su perfil','Usuarios'],
        'roles.index'                   => ['Listó roles y permisos','Roles'],
        'roles.create'                  => ['Abrió formulario crear rol','Roles'],
        'roles.store'                   => ['Creó un nuevo rol','Roles'],
        'roles.edit'                    => ['Abrió formulario editar rol','Roles'],
        'roles.update'                  => ['Actualizó un rol','Roles'],
        'roles.destroy'                 => ['Eliminó un rol','Roles'],
        'bitacora.index'                => ['Consultó la bitácora del sistema','Bitácora'],
        'gestiones.index'               => ['Listó gestiones académicas','Gestiones'],
        'gestiones.create'              => ['Abrió formulario nueva gestión','Gestiones'],
        'gestiones.store'               => ['Creó una gestión académica (CU-06)','Gestiones'],
        'gestiones.show'                => ['Consultó detalle de gestión','Gestiones'],
        'gestiones.edit'                => ['Abrió formulario editar gestión','Gestiones'],
        'gestiones.update'              => ['Actualizó una gestión académica','Gestiones'],
        'gestiones.destroy'             => ['Eliminó una gestión académica','Gestiones'],
        'carreras.index'                => ['Listó carreras de la facultad','Carreras'],
        'carreras.create'               => ['Abrió formulario nueva carrera','Carreras'],
        'carreras.store'                => ['Creó una carrera (CU-07)','Carreras'],
        'carreras.show'                 => ['Consultó detalle de carrera','Carreras'],
        'carreras.edit'                 => ['Abrió formulario editar carrera','Carreras'],
        'carreras.update'               => ['Actualizó una carrera','Carreras'],
        'carreras.destroy'              => ['Eliminó una carrera','Carreras'],
        'carreras.cupos'                => ['Definió cupos por carrera y gestión (CU-08)','Carreras'],
        'materias.index'                => ['Listó materias del CUP','Materias'],
        'materias.create'               => ['Abrió formulario nueva materia','Materias'],
        'materias.store'                => ['Creó una materia (CU-09)','Materias'],
        'materias.show'                 => ['Consultó detalle de materia','Materias'],
        'materias.edit'                 => ['Abrió formulario editar materia','Materias'],
        'materias.update'               => ['Actualizó una materia','Materias'],
        'materias.destroy'              => ['Eliminó una materia','Materias'],
        'postulantes.index'             => ['Listó postulantes inscritos','Postulantes'],
        'postulantes.create'            => ['Abrió formulario registrar postulante','Postulantes'],
        'postulantes.store'             => ['Registró un postulante (CU-05)','Postulantes'],
        'postulantes.update'            => ['Actualizó datos de postulante (CU-05)','Postulantes'],
        'postulantes.show'              => ['Consultó estado del postulante (CU-05)','Postulantes'],
        'postulantes.edit'              => ['Abrió formulario editar postulante','Postulantes'],
        'postulantes.update'            => ['Actualizó datos de postulante','Postulantes'],
        'postulantes.destroy'           => ['Eliminó un postulante','Postulantes'],
        'docentes.index'                => ['Listó docentes del CUP','Docentes'],
        'docentes.create'               => ['Abrió formulario registrar docente','Docentes'],
        'docentes.store'                => ['Registró un docente (CU-10)','Docentes'],
        'docentes.show'                 => ['Consultó perfil de docente','Docentes'],
        'docentes.edit'                 => ['Abrió formulario editar docente','Docentes'],
        'docentes.update'               => ['Actualizó datos de docente','Docentes'],
        'docentes.destroy'              => ['Desactivó un docente','Docentes'],
        'grupos.index'                  => ['Listó grupos del CUP','Grupos'],
        'grupos.create'                 => ['Abrió formulario nuevo grupo','Grupos'],
        'grupos.store'                  => ['Creó un grupo','Grupos'],
        'grupos.generar'                => ['Generó grupos automáticamente (CU-11)','Grupos'],
        'notas.index'                   => ['Listó notas del sistema','Evaluación'],
        'notas.store'                   => ['Registró notas de exámenes (CU-13)','Evaluación'],
        'notas.propias'                 => ['Postulante consultó sus notas (CU-15)','Evaluación'],
        'admision.index'                => ['Accedió al módulo de admisión','Admisión'],
        'admision.procesar'             => ['Procesó admisión primera opción (CU-16)','Admisión'],
        'admision.publicar'             => ['Publicó resultado de admisión (CU-18)','Admisión'],
        'reportes.index'                => ['Accedió al módulo de reportes','Reportes'],
    ];

    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        if (!Auth::check()) return $response;
        try {
            $rn = $request->route()?->getName() ?? '';
            if (empty($rn) || in_array($rn, $this->ignorar)) return $response;
            [$accion, $modulo] = $this->mapa[$rn]
                ?? ['Visitó ' . strtoupper($request->method()) . ' /' . $request->path(), 'Sistema'];
            $u = Auth::user();
            DB::table('bitacoras')->insert([
                'user_id'     => $u->id,
                'usuario'     => $u->name,
                'accion'      => substr($accion, 0, 250),
                'modulo'      => substr($modulo, 0, 60),
                'metodo_http' => $request->method(),
                'ruta'        => substr($request->path(), 0, 255),
                'ip'          => $request->ip(),
                'user_agent'  => substr($request->userAgent() ?? '', 0, 255),
                'fecha_hora'  => now(),
                'id_operacion'=> null,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        } catch (\Throwable $e) {
            Log::error('BitacoraMiddleware: ' . $e->getMessage());
        }
        return $response;
    }
}
