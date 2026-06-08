<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAsignacionRequest;
use App\Http\Requests\UpdateAsignacionRequest;
use App\Models\Asignacion;
use App\Models\Docente;
use App\Models\Gestion;
use App\Models\Grupo;
use App\Models\Materia;
use App\Traits\BitacoraTrait;
use Illuminate\Http\Request;

/**
 * CU-12 · Asignar docente a grupos y materias.
 * CRUD de asignaciones (docente↔grupo↔materia + horario). Solo REFERENCIA grupos;
 * no los crea ni edita (eso es CU-11 / GrupoController). La validación de negocio
 * (afinidad de área, máx. 4 grupos, cruce de horario, materia única) vive en
 * AsignacionService a través de los Form Requests.
 */
class AsignacionDocenteController extends Controller
{
    use BitacoraTrait;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:ver grupos')->only('index');
        $this->middleware('permission:crear grupos')->only('create', 'store');
        $this->middleware('permission:editar grupos')->only('edit', 'update');
        $this->middleware('permission:eliminar grupos')->only('destroy');
    }

    /** Lista de asignaciones de la gestión activa, con filtros por grupo/docente/materia. */
    public function index(Request $r)
    {
        $gestion = Gestion::where('estado', 'en_curso')->first();
        $grupoIds = $gestion ? Grupo::where('gestion_id', $gestion->id)->pluck('id') : collect();

        $asignaciones = Asignacion::with(['docente', 'grupo', 'materia'])
            ->whereIn('grupo_id', $grupoIds)
            ->when($r->filled('grupo_id'), fn ($q) => $q->where('grupo_id', $r->grupo_id))
            ->when($r->filled('docente_id'), fn ($q) => $q->where('docente_id', $r->docente_id))
            ->when($r->filled('materia_id'), fn ($q) => $q->where('materia_id', $r->materia_id))
            ->orderBy('grupo_id')->orderBy('dia')->orderBy('hora_inicio')->get();

        [$grupos, $docentes, $materias] = $this->catalogos($gestion);

        return view('asignaciones.index', compact('gestion', 'asignaciones', 'grupos', 'docentes', 'materias'));
    }

    public function create(Request $r)
    {
        $gestion = Gestion::where('estado', 'en_curso')->first();
        [$grupos, $docentes, $materias] = $this->catalogos($gestion);
        $grupoSel = $r->grupo_id; // preselección opcional al venir desde CU-11

        return view('asignaciones.create', compact('grupos', 'docentes', 'materias', 'grupoSel'));
    }

    public function store(StoreAsignacionRequest $r)
    {
        $a = Asignacion::create($r->validated());
        $a->load(['docente', 'materia', 'grupo']);
        $this->registrarEnBitacora(
            "CU-12: asignó a {$a->docente?->nombre_completo} para {$a->materia?->nombre} en {$a->grupo?->codigo}",
            $a->id, 'Asignaciones'
        );

        return redirect()->route('asignaciones.index')
            ->with('success', "✔ {$a->docente?->nombre_completo} asignado a {$a->materia?->nombre} en {$a->grupo?->codigo}.");
    }

    public function edit(Asignacion $asignacion)
    {
        $gestion = Gestion::where('estado', 'en_curso')->first();
        [$grupos, $docentes, $materias] = $this->catalogos($gestion);

        return view('asignaciones.edit', compact('asignacion', 'grupos', 'docentes', 'materias'));
    }

    public function update(UpdateAsignacionRequest $r, Asignacion $asignacion)
    {
        $asignacion->update($r->validated());
        $this->registrarEnBitacora("CU-12: actualizó la asignación ID:{$asignacion->id}", $asignacion->id, 'Asignaciones');

        return redirect()->route('asignaciones.index')->with('success', 'Asignación actualizada.');
    }

    public function destroy(Asignacion $asignacion)
    {
        $info = "ID:{$asignacion->id}";
        $asignacion->delete();
        $this->registrarEnBitacora("CU-12: eliminó la asignación {$info}", null, 'Asignaciones');

        return redirect()->route('asignaciones.index')->with('success', 'Asignación eliminada.');
    }

    /** Catálogos para los selects (grupos de la gestión activa, docentes y materias activas). */
    private function catalogos(?Gestion $gestion): array
    {
        return [
            Grupo::when($gestion, fn ($q) => $q->where('gestion_id', $gestion->id))->orderBy('codigo')->get(),
            Docente::where('estado', true)->orderBy('apellidos')->get(),
            Materia::where('estado', true)->orderBy('orden')->get(),
        ];
    }
}
