<?php
namespace App\Http\Controllers;

use App\Models\{Grupo, Asignacion, Docente, Materia, Gestion, Postulante};
use App\Traits\BitacoraTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * CU-11: Generar grupos automáticamente   (CEIL inscritos/70)
 * CU-12: Asignar docente a grupo y materia
 * CU-12: Validar cruces de horario         (integrado en asignarDocente)
 * CU-12: Asignar horarios y modalidad      (integrado en update)
 * CU-11: Inscribir postulantes a grupos
 */
class GrupoController extends Controller
{
    use BitacoraTrait;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:ver grupos')->only('index','show');
        $this->middleware('permission:crear grupos')->only('create','store','generar');
        $this->middleware('permission:editar grupos')->only('edit','update','asignarDocente','inscribirPostulantes');
        $this->middleware('permission:eliminar grupos')->only('destroy');
    }

    // ─── CU-11 ───────────────────────────────────────────────────────────────
    public function index()
    {
        $gestion  = Gestion::where('estado','en_curso')->first();
        $grupos   = $gestion
            ? Grupo::where('gestion_id', $gestion->id)
                ->withCount('postulantes')
                ->with(['asignaciones.docente','asignaciones.materia'])
                ->get()
            : collect();

        $totalInscritos   = $gestion ? Postulante::where('gestion_id',$gestion->id)->count() : 0;
        $gruposNecesarios = $totalInscritos > 0 ? (int) ceil($totalInscritos / 70) : 0;

        return view('grupos.index', compact('gestion','grupos','totalInscritos','gruposNecesarios'));
    }

    /** CU-11: Genera automáticamente los grupos faltantes (CEIL(inscritos/70)) */
    public function generar()
    {
        $gestion = Gestion::where('estado','en_curso')->firstOrFail();
        $total   = Postulante::where('gestion_id', $gestion->id)->count();

        if ($total === 0) {
            return redirect()->route('grupos.index')
                ->with('error','No hay postulantes inscritos en la gestión activa.');
        }

        $necesarios = (int) ceil($total / 70);
        $existentes = Grupo::where('gestion_id', $gestion->id)->count();
        $turnos     = ['mañana','tarde','noche'];
        $modalidades= ['presencial','presencial','virtual'];
        $creados    = 0;

        for ($i = $existentes; $i < $necesarios; $i++) {
            $letra = chr(65 + $i);  // A, B, C …
            Grupo::create([
                'gestion_id'      => $gestion->id,
                'codigo'          => "GRP-{$letra}",
                'turno'           => $turnos[$i % 3],
                'modalidad'       => $modalidades[$i % 3],
                'capacidad_maxima'=> 70,
                'estado'          => true,
            ]);
            $creados++;
        }
        $this->registrarEnBitacora("Generó {$creados} grupo(s) automáticamente para {$gestion->descripcion}", null, 'Grupos');
        return redirect()->route('grupos.index')->with('success',
            $creados > 0
                ? "✔ Se generaron {$creados} grupo(s) (total inscritos: {$total}, fórmula: ⌈{$total}/70⌉ = {$necesarios})."
                : "Ya existen los {$necesarios} grupo(s) necesarios para {$total} inscritos."
        );
    }

    // ─── CU-11: detalle del grupo ─────────────────────────────────────────────
    public function show(Grupo $grupo)
    {
        $grupo->load(['gestion','asignaciones.docente','asignaciones.materia','postulantes']);
        $docentes = Docente::where('estado',true)->orderBy('apellidos')->get();
        $materias = Materia::where('estado',true)->orderBy('orden')->get();
        // Postulantes de la gestión aún sin grupo
        $gestion = $grupo->gestion;
        $sinGrupo = $gestion
            ? Postulante::where('gestion_id', $gestion->id)
                ->whereNotIn('id', DB::table('grupo_postulante')->pluck('postulante_id'))
                ->orderBy('apellidos')->get()
            : collect();

        return view('grupos.show', compact('grupo','docentes','materias','sinGrupo'));
    }

    public function edit(Grupo $grupo)
    {
        return view('grupos.edit', compact('grupo'));
    }

    public function update(Request $r, Grupo $grupo)
    {
        $d = $r->validate([
            'turno'            => 'required|in:mañana,tarde,noche',
            'modalidad'        => 'required|in:presencial,virtual',
            'capacidad_maxima' => 'required|integer|min:1|max:200',
        ]);
        $grupo->update($d);
        $this->registrarEnBitacora("Editó horario/modalidad del grupo {$grupo->codigo}", $grupo->id, 'Grupos');
        return redirect()->route('grupos.show',$grupo)->with('success','Grupo actualizado.');
    }

    /** CU-12: Asignar docente validando cruces de horario y afinidad de área */
    public function asignarDocente(Request $r, Grupo $grupo)
    {
        $d = $r->validate([
            'docente_id'  => 'required|exists:docentes,id',
            'materia_id'  => 'required|exists:materias,id',
            'dia'         => 'required|in:lunes,martes,miercoles,jueves,viernes,sabado',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin'    => 'required|date_format:H:i|after:hora_inicio',
            'aula'        => 'nullable|string|max:30|regex:/^[A-Za-z0-9\-]+$/',
        ], [
            'hora_fin.after'  => 'La hora de fin debe ser posterior a la hora de inicio.',
            'aula.regex'      => 'El aula solo puede contener letras, números y guiones.',
        ]);

        // Afinidad de área: el docente debe ser del área de la materia
        $docente = Docente::find($d['docente_id']);
        $materia = Materia::find($d['materia_id']);
        if (!empty($docente->area_formacion) && !empty($materia->area_formacion)
            && $docente->area_formacion !== $materia->area_formacion) {
            return back()->withErrors(['docente_id' =>
                "⚠ Afinidad de área incorrecta: el docente «{$docente->nombre_completo}» es de «{$docente->area_formacion}» y no puede dictar «{$materia->nombre}» («{$materia->area_formacion}»)."
            ])->withInput();
        }

        // CU-12 — validar cruce de horario del docente
        $cruce = Asignacion::where('docente_id', $d['docente_id'])
            ->where('dia', $d['dia'])
            ->where(function($q) use ($d) {
                $q->where(function($q2) use ($d) {
                    $q2->where('hora_inicio', '<', $d['hora_fin'])
                       ->where('hora_fin',    '>',  $d['hora_inicio']);
                });
            })
            ->where('grupo_id', '!=', $grupo->id)
            ->first();

        if ($cruce) {
            $doc = Docente::find($d['docente_id']);
            return back()->withErrors([
                'docente_id' =>
                    "⚠ Cruce de horario: {$doc->nombre_completo} ya tiene clase el día ".
                    ucfirst($d['dia'])." de {$cruce->hora_inicio} a {$cruce->hora_fin} en el grupo {$cruce->grupo->codigo}."
            ])->withInput();
        }

        // CU-12 — máx. 4 grupos por docente
        $gruposDocente = Asignacion::where('docente_id', $d['docente_id'])
            ->whereHas('grupo', fn($q)=>$q->where('gestion_id',$grupo->gestion_id))
            ->distinct('grupo_id')->count('grupo_id');

        $yaAsignadoAEsteGrupo = Asignacion::where('docente_id',$d['docente_id'])
            ->where('grupo_id', $grupo->id)->exists();

        if ($gruposDocente >= 4 && !$yaAsignadoAEsteGrupo) {
            return back()->withErrors(['docente_id'=>'El docente ya tiene 4 grupos asignados (máximo permitido).'])->withInput();
        }

        Asignacion::updateOrCreate(
            ['grupo_id' => $grupo->id, 'materia_id' => $d['materia_id']],
            ['docente_id'  => $d['docente_id'],
             'dia'         => $d['dia'],
             'hora_inicio' => $d['hora_inicio'],
             'hora_fin'    => $d['hora_fin'],
             'aula'        => $d['aula'] ?? null]
        );
        $doc = Docente::find($d['docente_id']);
        $mat = Materia::find($d['materia_id']);
        $this->registrarEnBitacora(
            "Asignó a {$doc->nombre_completo} para {$mat->nombre} en {$grupo->codigo}",
            $grupo->id, 'Grupos'
        );
        return redirect()->route('grupos.show',$grupo)
            ->with('success',"✔ {$doc->nombre_completo} asignado a {$mat->nombre} en {$grupo->codigo}.");
    }

    /** CU-11: Inscribir postulantes al grupo respetando capacidad máxima */
    public function inscribirPostulantes(Request $r, Grupo $grupo)
    {
        $r->validate(['postulante_ids' => 'required|array|min:1',
                      'postulante_ids.*' => 'exists:postulantes,id']);

        $yaInscritos = DB::table('grupo_postulante')->where('grupo_id',$grupo->id)->count();
        $disponible  = $grupo->capacidad_maxima - $yaInscritos;

        if ($disponible <= 0) {
            return back()->withErrors(['postulante_ids' => 'El grupo ya está lleno (capacidad máxima alcanzada).']);
        }

        $nuevos = array_slice($r->postulante_ids, 0, $disponible);
        foreach ($nuevos as $pid) {
            DB::table('grupo_postulante')->updateOrInsert(
                ['grupo_id' => $grupo->id, 'postulante_id' => $pid],
                ['created_at' => now(), 'updated_at' => now()]
            );
            Postulante::where('id',$pid)->where('estado','inscrito')
                ->update(['estado'=>'en_curso','updated_at'=>now()]);
        }

        $omitidos = count($r->postulante_ids) - count($nuevos);
        $msg = "✔ ".count($nuevos)." postulante(s) inscritos en {$grupo->codigo}.";
        if ($omitidos > 0) $msg .= " ({$omitidos} omitidos por falta de espacio)";

        $this->registrarEnBitacora("Inscribió ".count($nuevos)." postulante(s) en {$grupo->codigo}", $grupo->id,'Grupos');
        return redirect()->route('grupos.show',$grupo)->with('success',$msg);
    }

    public function destroy(Grupo $grupo)
    {
        $cod = $grupo->codigo;
        $grupo->delete();
        $this->registrarEnBitacora("Eliminó grupo {$cod}", null,'Grupos');
        return redirect()->route('grupos.index')->with('success',"Grupo «{$cod}» eliminado.");
    }
}
