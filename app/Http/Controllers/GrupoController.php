<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGrupoRequest;
use App\Http\Requests\UpdateGrupoRequest;
use App\Models\Gestion;
use App\Models\Grupo;
use App\Models\Postulante;
use App\Services\GrupoService;
use App\Traits\BitacoraTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * CU-11 · Gestionar grupos (la entidad grupo): CRUD, generación automática
 * (⌈inscritos/capacidad⌉) e inscripción/visualización de estudiantes por grupo.
 *
 * NO asigna docentes: eso es CU-12 (AsignacionDocenteController / /asignaciones).
 */
class GrupoController extends Controller
{
    use BitacoraTrait;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:ver grupos')->only('index', 'show', 'estudiantes');
        $this->middleware('permission:crear grupos')->only('create', 'store', 'generar');
        $this->middleware('permission:editar grupos')->only('edit', 'update', 'inscribirPostulantes');
        $this->middleware('permission:eliminar grupos')->only('destroy');
    }

    public function index(GrupoService $svc)
    {
        $gestion = Gestion::where('estado', 'en_curso')->first();
        $grupos = $gestion
            ? Grupo::where('gestion_id', $gestion->id)
                ->withCount('postulantes')
                ->with(['asignaciones.docente', 'asignaciones.materia'])
                ->orderBy('codigo')->get()
            : collect();

        $totalInscritos = $gestion ? $svc->totalInscritos($gestion) : 0;
        $gruposNecesarios = $svc->calcularCantidadGrupos($totalInscritos);

        return view('grupos.index', compact('gestion', 'grupos', 'totalInscritos', 'gruposNecesarios'));
    }

    public function create()
    {
        $gestion = Gestion::where('estado', 'en_curso')->first();

        return view('grupos.create', compact('gestion'));
    }

    public function store(StoreGrupoRequest $r)
    {
        $gestion = Gestion::where('estado', 'en_curso')->firstOrFail();
        $grupo = Grupo::create($r->validated() + ['gestion_id' => $gestion->id, 'estado' => true]);
        $this->registrarEnBitacora("Creó el grupo {$grupo->codigo}", $grupo->id, 'Grupos');

        return redirect()->route('grupos.show', $grupo)->with('success', "Grupo «{$grupo->codigo}» creado.");
    }

    /** CU-11: genera ⌈inscritos/capacidad⌉ grupos faltantes y distribuye estudiantes sin grupo. */
    public function generar(GrupoService $svc)
    {
        $gestion = Gestion::where('estado', 'en_curso')->firstOrFail();
        $total = $svc->totalInscritos($gestion);

        if ($total === 0) {
            return redirect()->route('grupos.index')
                ->with('error', 'No hay postulantes inscritos en la gestión activa.');
        }

        $cap = GrupoService::CAPACIDAD_DEFAULT;
        $necesarios = $svc->calcularCantidadGrupos($total, $cap);
        $creados = $svc->generarGruposAutomaticos($gestion, $cap);
        $this->registrarEnBitacora("Generó {$creados} grupo(s) automáticamente para {$gestion->descripcion}", null, 'Grupos');

        return redirect()->route('grupos.index')->with('success',
            $creados > 0
                ? "✔ Se generaron {$creados} grupo(s) (fórmula ⌈{$total}/{$cap}⌉ = {$necesarios}) y se distribuyeron los estudiantes sin grupo."
                : "Ya existen los {$necesarios} grupo(s) necesarios para {$total} inscritos; se distribuyeron los estudiantes sin grupo."
        );
    }

    public function show(Grupo $grupo)
    {
        $grupo->load(['gestion', 'asignaciones.docente', 'asignaciones.materia', 'postulantes']);
        $gestion = $grupo->gestion;
        $sinGrupo = $gestion
            ? Postulante::where('gestion_id', $gestion->id)
                ->whereNotIn('id', DB::table('grupo_postulante')->pluck('postulante_id'))
                ->orderBy('apellidos')->get()
            : collect();

        return view('grupos.show', compact('grupo', 'sinGrupo'));
    }

    /** CU-11: lista de estudiantes inscritos en el grupo. */
    public function estudiantes(Grupo $grupo)
    {
        $grupo->load('gestion');
        $estudiantes = $grupo->postulantes()->with('primeraOpcion')->orderBy('apellidos')->get();

        return view('grupos.estudiantes', compact('grupo', 'estudiantes'));
    }

    public function edit(Grupo $grupo)
    {
        return view('grupos.edit', compact('grupo'));
    }

    public function update(UpdateGrupoRequest $r, Grupo $grupo)
    {
        $grupo->update($r->validated());
        $this->registrarEnBitacora("Editó el grupo {$grupo->codigo}", $grupo->id, 'Grupos');

        return redirect()->route('grupos.show', $grupo)->with('success', 'Grupo actualizado.');
    }

    /** CU-11: inscribir postulantes al grupo respetando la capacidad máxima. */
    public function inscribirPostulantes(Request $r, Grupo $grupo)
    {
        $r->validate(['postulante_ids' => 'required|array|min:1',
            'postulante_ids.*' => 'exists:postulantes,id']);

        $yaInscritos = DB::table('grupo_postulante')->where('grupo_id', $grupo->id)->count();
        $disponible = $grupo->capacidad_maxima - $yaInscritos;

        if ($disponible <= 0) {
            return back()->withErrors(['postulante_ids' => 'El grupo ya está lleno (capacidad máxima alcanzada).']);
        }

        $nuevos = array_slice($r->postulante_ids, 0, $disponible);
        foreach ($nuevos as $pid) {
            DB::table('grupo_postulante')->updateOrInsert(
                ['grupo_id' => $grupo->id, 'postulante_id' => $pid],
                ['created_at' => now(), 'updated_at' => now()]
            );
            Postulante::where('id', $pid)->where('estado', 'inscrito')
                ->update(['estado' => 'en_curso', 'updated_at' => now()]);
        }

        $omitidos = count($r->postulante_ids) - count($nuevos);
        $msg = '✔ '.count($nuevos)." postulante(s) inscritos en {$grupo->codigo}.";
        if ($omitidos > 0) {
            $msg .= " ({$omitidos} omitidos por falta de espacio)";
        }

        $this->registrarEnBitacora('Inscribió '.count($nuevos)." postulante(s) en {$grupo->codigo}", $grupo->id, 'Grupos');

        return redirect()->route('grupos.show', $grupo)->with('success', $msg);
    }

    public function destroy(Grupo $grupo)
    {
        $cod = $grupo->codigo;
        $grupo->delete();
        $this->registrarEnBitacora("Eliminó el grupo {$cod}", null, 'Grupos');

        return redirect()->route('grupos.index')->with('success', "Grupo «{$cod}» eliminado.");
    }
}
