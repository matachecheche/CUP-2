<?php
namespace App\Http\Controllers;
use App\Models\{Carrera,CupoCarrera,Gestion};
use App\Traits\BitacoraTrait;
use Illuminate\Http\Request;
class CarreraController extends Controller {
    use BitacoraTrait;
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('permission:ver carreras')->only('index','show');
        $this->middleware('permission:crear carreras')->only('create','store');
        $this->middleware('permission:editar carreras')->only('edit','update');
        $this->middleware('permission:eliminar carreras')->only('destroy');
    }
    public function index() { return view('carreras.index',['carreras'=>Carrera::orderBy('nombre')->get()]); }
    public function create() { return view('carreras.create'); }
    public function store(Request $r) {
        $d=$r->validate([
            'nombre'      => 'required|string|min:3|max:100|unique:carreras,nombre',
            'sigla'       => 'nullable|string|min:2|max:5|regex:/^[A-Za-z]{2,5}$/',
            'descripcion' => 'nullable|string',
            'estado'      => 'boolean',
        ], [
            'nombre.unique' => 'Ya existe una carrera con ese nombre.',
            'sigla.regex'   => 'La sigla debe tener entre 2 y 5 letras (sin números ni símbolos).',
        ]);
        if (!empty($d['sigla'])) $d['sigla'] = strtoupper($d['sigla']);
        $d['estado']=$r->boolean('estado',true);
        $c=Carrera::create($d);
        $this->registrarEnBitacora("Creó carrera: {$c->nombre}",$c->id,'Carreras');
        return redirect()->route('carreras.index')->with('success',"Carrera «{$c->nombre}» creada.");
    }
    public function show(Carrera $carrera) {
        $gestiones=Gestion::orderByDesc('fecha_inicio')->get();
        $cupos=CupoCarrera::where('carrera_id',$carrera->id)->with('gestion')->orderByDesc('id')->get();
        return view('carreras.show',compact('carrera','gestiones','cupos'));
    }
    public function edit(Carrera $carrera) { return view('carreras.edit',compact('carrera')); }
    public function update(Request $r, Carrera $carrera) {
        $d=$r->validate([
            'nombre'      => "required|string|min:3|max:100|unique:carreras,nombre,{$carrera->id}",
            'sigla'       => 'nullable|string|min:2|max:5|regex:/^[A-Za-z]{2,5}$/',
            'descripcion' => 'nullable|string',
            'estado'      => 'boolean',
        ], [
            'nombre.unique' => 'Ya existe una carrera con ese nombre.',
            'sigla.regex'   => 'La sigla debe tener entre 2 y 5 letras (sin números ni símbolos).',
        ]);
        if (!empty($d['sigla'])) $d['sigla'] = strtoupper($d['sigla']);
        $d['estado']=$r->boolean('estado',true); $carrera->update($d);
        $this->registrarEnBitacora("Actualizó carrera: {$carrera->nombre}",$carrera->id,'Carreras');
        return redirect()->route('carreras.index')->with('success',"Carrera actualizada.");
    }
    public function destroy(Carrera $carrera) {
        $n=$carrera->nombre; $carrera->delete();
        $this->registrarEnBitacora("Eliminó carrera: {$n}",null,'Carreras');
        return redirect()->route('carreras.index')->with('success',"Carrera «{$n}» eliminada.");
    }
    public function storeCupo(Request $r, Carrera $carrera) {
        $d=$r->validate([
            'gestion_id'      => 'required|exists:gestiones,id',
            'cantidad_maxima' => 'required|integer|min:1|max:9999',
        ], [
            'cantidad_maxima.min' => 'El cupo debe ser un número mayor a 0.',
        ]);
        $c=CupoCarrera::updateOrCreate(['carrera_id'=>$carrera->id,'gestion_id'=>$d['gestion_id']],['cantidad_maxima'=>$d['cantidad_maxima']]);
        $this->registrarEnBitacora("Definió cupo {$c->cantidad_maxima} para {$carrera->nombre}",$carrera->id,'Carreras');
        return redirect()->route('carreras.show',$carrera)->with('success','Cupo definido correctamente.');
    }
}
