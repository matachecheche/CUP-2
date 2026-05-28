<?php
namespace App\Http\Controllers;

use App\Models\{Nota, Postulante, Materia, Grupo, Gestion};
use App\Traits\BitacoraTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotaController extends Controller
{
    use BitacoraTrait;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:ver notas')->only('index','show');
        $this->middleware('permission:crear notas')->only('create','store');
        $this->middleware('permission:editar notas')->only('edit','update');
    }

    /** CU-13/CU-15: Lista notas del grupo (docente ve solo sus grupos) */
    public function index(Request $r)
    {
        $gestion = Gestion::where('estado','en_curso')->first();
        $grupos  = Grupo::when($gestion, fn($q)=>$q->where('gestion_id',$gestion->id))
            ->with('asignaciones.docente')
            ->get();
        $grupoSel = $r->grupo_id ? Grupo::find($r->grupo_id) : $grupos->first();
        $materias = $grupoSel ? Materia::where('estado',true)->orderBy('orden')->get() : collect();
        $matSel   = $r->materia_id ? Materia::find($r->materia_id) : $materias->first();

        $notas = ($grupoSel && $matSel)
            ? Nota::where('grupo_id',$grupoSel->id)->where('materia_id',$matSel->id)
                ->with('postulante')->orderBy('created_at')->get()
            : collect();

        // Postulantes del grupo sin nota aún
        $inscritos = $grupoSel
            ? DB::table('grupo_postulante')->where('grupo_id',$grupoSel->id)->pluck('postulante_id')
            : collect();
        $sinNota = $matSel
            ? Postulante::whereIn('id',$inscritos)
                ->whereNotIn('id', Nota::where('grupo_id',$grupoSel->id)->where('materia_id',$matSel->id)->pluck('postulante_id'))
                ->get()
            : collect();

        return view('notas.index', compact('grupos','grupoSel','materias','matSel','notas','sinNota','gestion'));
    }

    /** CU-13: Formulario para registrar nota de un postulante */
    public function create(Request $r)
    {
        $r->validate(['postulante_id'=>'required|exists:postulantes,id','grupo_id'=>'required|exists:grupos,id','materia_id'=>'required|exists:materias,id']);
        $postulante = Postulante::findOrFail($r->postulante_id);
        $grupo      = Grupo::findOrFail($r->grupo_id);
        $materia    = Materia::findOrFail($r->materia_id);
        return view('notas.create', compact('postulante','grupo','materia'));
    }

    /** CU-13/CU-14: Guarda nota y recalcula estado del postulante */
    public function store(Request $r)
    {
        $d = $r->validate([
            'postulante_id' => 'required|exists:postulantes,id',
            'materia_id'    => 'required|exists:materias,id',
            'grupo_id'      => 'required|exists:grupos,id',
            'examen1'       => 'required|numeric|min:0|max:100',
            'examen2'       => 'required|numeric|min:0|max:100',
            'examen3'       => 'required|numeric|min:0|max:100',
        ], [
            'examen1.max' => 'La nota del examen 1 debe estar entre 0 y 100.',
            'examen2.max' => 'La nota del examen 2 debe estar entre 0 y 100.',
            'examen3.max' => 'La nota del examen 3 debe estar entre 0 y 100.',
            'examen1.min' => 'La nota del examen 1 debe estar entre 0 y 100.',
            'examen2.min' => 'La nota del examen 2 debe estar entre 0 y 100.',
            'examen3.min' => 'La nota del examen 3 debe estar entre 0 y 100.',
        ]);

        // Validar que el postulante esté inscrito en el grupo
        $inscrito = DB::table('grupo_postulante')
            ->where(['grupo_id'=>$d['grupo_id'],'postulante_id'=>$d['postulante_id']])->exists();
        if (!$inscrito) {
            return back()->withErrors(['postulante_id'=>'El postulante no está inscrito en este grupo.'])->withInput();
        }

        $nota = Nota::updateOrCreate(
            ['postulante_id'=>$d['postulante_id'],'materia_id'=>$d['materia_id'],'grupo_id'=>$d['grupo_id']],
            ['examen1'=>$d['examen1'],'examen2'=>$d['examen2'],'examen3'=>$d['examen3']]
        );
        $nota->calcularNotaFinal();   // CU-14

        $this->_actualizarEstadoPostulante($d['postulante_id']); // CU-14
        $this->registrarEnBitacora("Registró nota para postulante ID:{$d['postulante_id']}", $nota->id, 'Notas');

        return redirect()->route('notas.index', ['grupo_id'=>$d['grupo_id'],'materia_id'=>$d['materia_id']])
            ->with('success','Nota registrada y promedio actualizado.');
    }

    public function edit(Nota $nota)
    {
        $nota->load('postulante','materia','grupo');
        return view('notas.edit', compact('nota'));
    }

    public function update(Request $r, Nota $nota)
    {
        $d = $r->validate([
            'examen1'=>'required|numeric|min:0|max:100',
            'examen2'=>'required|numeric|min:0|max:100',
            'examen3'=>'required|numeric|min:0|max:100',
        ], [
            'examen1.max' => 'La nota del examen 1 debe estar entre 0 y 100.',
            'examen2.max' => 'La nota del examen 2 debe estar entre 0 y 100.',
            'examen3.max' => 'La nota del examen 3 debe estar entre 0 y 100.',
        ]);
        $nota->update($d);
        $nota->calcularNotaFinal();
        $this->_actualizarEstadoPostulante($nota->postulante_id);
        $this->registrarEnBitacora("Editó nota ID:{$nota->id}", $nota->id, 'Notas');
        return redirect()->route('notas.index',['grupo_id'=>$nota->grupo_id,'materia_id'=>$nota->materia_id])
            ->with('success','Nota actualizada.');
    }

    /** CU-14: Recalcula promedio general y estado aprobado/no_aprobado */
    private function _actualizarEstadoPostulante(int $postulanteId): void
    {
        $notas = Nota::where('postulante_id', $postulanteId)->get();
        if ($notas->count() < 4) return; // esperar a tener las 4 materias

        $promedio = round($notas->avg('nota_final'), 2);
        $aprobado = $notas->every(fn($n) => $n->nota_final >= 60);

        Postulante::find($postulanteId)?->update([
            'promedio_general' => $promedio,
            'estado'           => $aprobado ? 'aprobado' : 'no_aprobado',
        ]);
    }
}
