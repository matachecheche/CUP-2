<?php
namespace App\Http\Controllers;
use App\Models\Gestion;
use App\Traits\BitacoraTrait;
use Illuminate\Http\Request;
class GestionController extends Controller {
    use BitacoraTrait;
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('permission:ver gestiones')->only('index','show');
        $this->middleware('permission:crear gestiones')->only('create','store');
        $this->middleware('permission:editar gestiones')->only('edit','update');
        $this->middleware('permission:eliminar gestiones')->only('destroy');
    }
    public function index() { return view('gestiones.index',['gestiones'=>Gestion::orderByDesc('fecha_inicio')->get()]); }
    public function create() { return view('gestiones.create'); }
    public function store(Request $r) {
        $d = $r->validate([
            'descripcion'  => 'required|string|max:50|unique:gestiones,descripcion',
            'fecha_inicio' => 'required|date',
            'fecha_fin'    => 'required|date|after:fecha_inicio',
            'estado'       => 'required|in:planificacion,inscripcion,en_curso,finalizado',
        ], [
            'descripcion.unique' => 'Ya existe una gestión con esa descripción.',
            'fecha_fin.after'    => 'La fecha de fin debe ser posterior a la fecha de inicio.',
            'estado.required'    => 'El estado de la gestión es obligatorio.',
        ]);
        $g = Gestion::create($d);
        $this->registrarEnBitacora("Creó gestión: {$g->descripcion}",$g->id,'Gestiones');
        return redirect()->route('gestiones.index')->with('success',"Gestión «{$g->descripcion}» creada.");
    }
    public function show(Gestion $gestion) { return view('gestiones.show',compact('gestion')); }
    public function edit(Gestion $gestion) { return view('gestiones.edit',compact('gestion')); }
    public function update(Request $r, Gestion $gestion) {
        $d = $r->validate([
            'descripcion'  => "required|string|max:50|unique:gestiones,descripcion,{$gestion->id}",
            'fecha_inicio' => 'required|date',
            'fecha_fin'    => 'required|date|after:fecha_inicio',
            'estado'       => 'required|in:planificacion,inscripcion,en_curso,finalizado',
        ], [
            'descripcion.unique' => 'Ya existe una gestión con esa descripción.',
            'fecha_fin.after'    => 'La fecha de fin debe ser posterior a la fecha de inicio.',
            'estado.required'    => 'El estado de la gestión es obligatorio.',
        ]);
        $gestion->update($d);
        $this->registrarEnBitacora("Actualizó gestión: {$gestion->descripcion}",$gestion->id,'Gestiones');
        return redirect()->route('gestiones.index')->with('success',"Gestión actualizada.");
    }
    public function destroy(Gestion $gestion) {
        $n=$gestion->descripcion; $gestion->delete();
        $this->registrarEnBitacora("Eliminó gestión: {$n}",null,'Gestiones');
        return redirect()->route('gestiones.index')->with('success',"Gestión «{$n}» eliminada.");
    }
}
