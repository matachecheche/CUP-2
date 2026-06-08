<?php

namespace App\Services;

use App\Models\Gestion;
use App\Models\Grupo;
use App\Models\Postulante;
use Illuminate\Support\Facades\DB;

/**
 * CU-11 · Lógica de negocio de la gestión de grupos (entidad grupo).
 * No contiene nada de asignación de docentes (eso es CU-12 / AsignacionService).
 */
class GrupoService
{
    /** Capacidad por defecto. Configurable; única fuente del divisor (ver §7 del enunciado). */
    public const CAPACIDAD_DEFAULT = 70;

    /** CantidadGrupos = ceil(TotalInscritos / capacidad). */
    public function calcularCantidadGrupos(int $totalInscritos, int $capacidad = self::CAPACIDAD_DEFAULT): int
    {
        return $capacidad > 0 ? (int) ceil($totalInscritos / $capacidad) : 0;
    }

    /**
     * Crea los grupos que falten para la gestión y distribuye a los inscritos sin grupo.
     * Devuelve cuántos grupos nuevos creó. Idempotente: no recrea grupos existentes.
     */
    public function generarGruposAutomaticos(Gestion $gestion, int $capacidad = self::CAPACIDAD_DEFAULT): int
    {
        $total = $this->totalInscritos($gestion);
        $necesarios = $this->calcularCantidadGrupos($total, $capacidad);
        $existentes = Grupo::where('gestion_id', $gestion->id)->count();
        $turnos = ['mañana', 'tarde', 'noche'];
        $modalidades = ['presencial', 'presencial', 'virtual'];
        $creados = 0;

        for ($i = $existentes; $i < $necesarios; $i++) {
            $letra = chr(65 + $i); // A, B, C …
            Grupo::create([
                'gestion_id' => $gestion->id,
                'codigo' => "GRP-{$letra}",
                'turno' => $turnos[$i % 3],
                'modalidad' => $modalidades[$i % 3],
                'capacidad_maxima' => $capacidad,
                'estado' => true,
            ]);
            $creados++;
        }

        $this->distribuirEstudiantes($gestion, $capacidad);

        return $creados;
    }

    /**
     * Asigna grupo a los inscritos que aún no tienen ninguno, llenando los grupos
     * por orden de código sin exceder su capacidad. Devuelve cuántos distribuyó.
     */
    public function distribuirEstudiantes(Gestion $gestion, int $capacidad = self::CAPACIDAD_DEFAULT): int
    {
        $grupos = Grupo::where('gestion_id', $gestion->id)->orderBy('codigo')->get();
        if ($grupos->isEmpty()) {
            return 0;
        }

        $yaAsignados = DB::table('grupo_postulante')->pluck('postulante_id')->all();
        $sinGrupo = Postulante::where('gestion_id', $gestion->id)
            ->where('estado', '!=', 'preinscrito')
            ->whereNotIn('id', $yaAsignados ?: [0])
            ->orderBy('apellidos')->pluck('id');

        $ocupacion = [];
        foreach ($grupos as $g) {
            $ocupacion[$g->id] = DB::table('grupo_postulante')->where('grupo_id', $g->id)->count();
        }

        $distribuidos = 0;
        foreach ($sinGrupo as $pid) {
            $destino = $grupos->first(fn ($g) => $ocupacion[$g->id] < ($g->capacidad_maxima ?: $capacidad));
            if (! $destino) {
                break; // todos los grupos están llenos
            }
            DB::table('grupo_postulante')->insert([
                'grupo_id' => $destino->id, 'postulante_id' => $pid,
                'created_at' => now(), 'updated_at' => now(),
            ]);
            Postulante::where('id', $pid)->where('estado', 'inscrito')
                ->update(['estado' => 'en_curso', 'updated_at' => now()]);
            $ocupacion[$destino->id]++;
            $distribuidos++;
        }

        return $distribuidos;
    }

    /** Inscritos de la gestión que cuentan para los grupos (excluye preinscritos sin pago). */
    public function totalInscritos(Gestion $gestion): int
    {
        return Postulante::where('gestion_id', $gestion->id)->where('estado', '!=', 'preinscrito')->count();
    }
}
