<?php
namespace App\Http\Controllers;
use App\Models\Materia;
use App\Traits\BitacoraTrait;
use Illuminate\Http\Request;
class MateriaController extends Controller {
    use BitacoraTrait;
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('permission:ver materias')->only('index','show');
        $this->middleware('permission:crear materias')->only('create','store');
        $this->middleware('permission:editar materias')->only('edit','update');
        $this->middleware('permission:eliminar materias')->only('destroy');
    }
    public function index() { return view('materias.index',['materias'=>Materia::orderBy('orden')->orderBy('nombre')->get()]); }
    public function create() { return view('materias.create'); }
    public function store(Request $r) {
        $d=$r->validate([
            'nombre'                => 'required|string|max:100|unique:materias,nombre',
            'area_formacion'        => 'required|in:Computación,Matemáticas,Física,Inglés',
            'descripcion'           => 'nullable|string',
            'pond_examen1'          => 'required|integer|min:1|max:98',
            'pond_examen2'          => 'required|integer|min:1|max:98',
            'pond_examen3'          => 'required|integer|min:1|max:98',
            'nota_minima_aprobacion'=> 'required|integer|min:1|max:100',
            'orden'                 => 'nullable|integer|min:0',
            'estado'                => 'boolean',
        ], $this->messages());
        if(($d['pond_examen1']+$d['pond_examen2']+$d['pond_examen3'])!==100)
            return back()->withErrors(['pond_total'=>'La suma de las ponderaciones debe ser exactamente 100%.'])->withInput();
        $d['estado']=$r->boolean('estado',true); $d['orden']=$d['orden']??0;
        $m=Materia::create($d);
        $this->registrarEnBitacora("Creó materia: {$m->nombre}",$m->id,'Materias');
        return redirect()->route('materias.index')->with('success',"Materia «{$m->nombre}» creada.");
    }
    public function show(Materia $materia) { return view('materias.show',compact('materia')); }
    public function edit(Materia $materia) { return view('materias.edit',compact('materia')); }
    public function update(Request $r, Materia $materia) {
        $d=$r->validate([
            'nombre'                => "required|string|max:100|unique:materias,nombre,{$materia->id}",
            'area_formacion'        => 'required|in:Computación,Matemáticas,Física,Inglés',
            'descripcion'           => 'nullable|string',
            'pond_examen1'          => 'required|integer|min:1|max:98',
            'pond_examen2'          => 'required|integer|min:1|max:98',
            'pond_examen3'          => 'required|integer|min:1|max:98',
            'nota_minima_aprobacion'=> 'required|integer|min:1|max:100',
            'orden'                 => 'nullable|integer|min:0',
            'estado'                => 'boolean',
        ], $this->messages());
        if(($d['pond_examen1']+$d['pond_examen2']+$d['pond_examen3'])!==100)
            return back()->withErrors(['pond_total'=>'La suma de las ponderaciones debe ser exactamente 100%.'])->withInput();
        $d['estado']=$r->boolean('estado',true); $materia->update($d);
        $this->registrarEnBitacora("Actualizó materia: {$materia->nombre}",$materia->id,'Materias');
        return redirect()->route('materias.index')->with('success',"Materia actualizada.");
    }
    private function messages(): array {
        return [
            'nombre.unique'           => 'Ya existe una materia con ese nombre.',
            'area_formacion.in'       => 'El área debe ser Computación, Matemáticas, Física o Inglés.',
            'pond_examen1.required'   => 'La ponderación del examen 1 es obligatoria.',
            'pond_examen2.required'   => 'La ponderación del examen 2 es obligatoria.',
            'pond_examen3.required'   => 'La ponderación del examen 3 es obligatoria.',
        ];
    }
    public function destroy(Materia $materia) {
        $n=$materia->nombre; $materia->delete();
        $this->registrarEnBitacora("Eliminó materia: {$n}",null,'Materias');
        return redirect()->route('materias.index')->with('success',"Materia «{$n}» eliminada.");
    }
}
