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
