<?php

namespace App\Services;

use App\Models\Asignacion;
use App\Models\Docente;
use App\Models\Materia;

/**
 * CU-12 · Reglas de negocio de la asignación docente ↔ grupo ↔ materia.
 * No crea ni edita grupos (eso es CU-11 / GrupoService).
 */
class AsignacionService
{
    public const MAX_GRUPOS_POR_DOCENTE = 4;

    /**
     * Requisitos del docente para dictar una materia:
     * afinidad de área + maestría + diplomado en educación superior.
     * (El repo guarda maestría/diplomado como texto; se exige que estén declarados.)
     */
    public function docenteCumpleRequisitos(Docente $d, Materia $m): bool
    {
        $afin = empty($d->area_formacion) || empty($m->area_formacion)
            || $d->area_formacion === $m->area_formacion;

        return $afin
            && ! empty($d->maestria)
            && ! empty($d->diplomado_educacion_superior);
    }

    /** ¿El docente superaría el máximo de grupos DISTINTOS permitidos al añadir este grupo? */
    public function excedeLimiteGrupos(int $docenteId, int $grupoIdNuevo, ?int $excluirAsignacionId = null): bool
    {
        $grupos = Asignacion::where('docente_id', $docenteId)
            ->when($excluirAsignacionId, fn ($q) => $q->where('id', '!=', $excluirAsignacionId))
            ->pluck('grupo_id')->push($grupoIdNuevo)->unique();

        return $grupos->count() > self::MAX_GRUPOS_POR_DOCENTE;
    }

    /** Dos franjas [ini,fin) se cruzan si ini < fin_existente y fin > ini_existente. */
    public function hayCruceHorario(int $docenteId, string $dia, string $ini, string $fin, ?int $excluirId = null): bool
    {
        return Asignacion::where('docente_id', $docenteId)
            ->where('dia', $dia)
            ->when($excluirId, fn ($q) => $q->where('id', '!=', $excluirId))
            ->where('hora_inicio', '<', $fin)
            ->where('hora_fin', '>', $ini)
            ->exists();
    }

    /** ¿Esa materia ya tiene docente en ese grupo? (una materia → un docente por grupo) */
    public function materiaYaCubierta(int $grupoId, int $materiaId, ?int $excluirId = null): bool
    {
        return Asignacion::where('grupo_id', $grupoId)->where('materia_id', $materiaId)
            ->when($excluirId, fn ($q) => $q->where('id', '!=', $excluirId))
            ->exists();
    }
}
