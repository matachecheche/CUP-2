<?php
namespace App\Http\Controllers;
use App\Models\{Postulante,Carrera,Gestion};
use App\Traits\BitacoraTrait;
use Illuminate\Http\Request;
class PostulanteController extends Controller {
    use BitacoraTrait;
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('permission:ver postulantes')->only('index','show');
        $this->middleware('permission:crear postulantes')->only('create','store');
        $this->middleware('permission:editar postulantes')->only('edit','update');
        $this->middleware('permission:eliminar postulantes')->only('destroy');
    }
    public function index() { return view('postulantes.index',['postulantes'=>Postulante::with('primeraOpcion','segundaOpcion','gestion')->orderBy('apellidos')->get()]); }
    public function create() { return view('postulantes.create',['carreras'=>Carrera::where('estado',true)->orderBy('nombre')->get(),'gestiones'=>Gestion::orderByDesc('fecha_inicio')->get()]); }
    public function store(Request $r) {
        $d=$r->validate(['gestion_id'=>'required|exists:gestiones,id','primera_opcion_id'=>'required|exists:carreras,id','segunda_opcion_id'=>'required|exists:carreras,id|different:primera_opcion_id','ci'=>'required|string|max:20|unique:postulantes,ci','nombres'=>'required|string|max:100','apellidos'=>'required|string|max:100','fecha_nacimiento'=>'nullable|date|before:today','sexo'=>'nullable|in:M,F,Otro','direccion'=>'nullable|string|max:200','telefono'=>'nullable|string|max:20','email'=>'nullable|email|max:100','colegio_procedencia'=>'nullable|string|max:150','ciudad'=>'nullable|string|max:80','doc_ci'=>'boolean','doc_libreta_colegio'=>'boolean','doc_titulo_bachiller'=>'boolean']);
        $d['doc_ci']=$r->boolean('doc_ci'); $d['doc_libreta_colegio']=$r->boolean('doc_libreta_colegio'); $d['doc_titulo_bachiller']=$r->boolean('doc_titulo_bachiller');
        // CU-07: los 3 documentos son obligatorios
        if (!$d['doc_ci'] || !$d['doc_libreta_colegio'] || !$d['doc_titulo_bachiller']) {
            return back()
                ->withErrors(['documentos'=>'Para completar la inscripción debes presentar los tres documentos: CI, Libreta de colegio y Título de Bachiller.'])
                ->withInput();
        }
        // CU-07 guard: los 3 documentos son obligatorios para inscribirse
        if (empty($d["doc_ci"]) || empty($d["doc_libreta_colegio"]) || empty($d["doc_titulo_bachiller"])) {
            return back()
                ->withErrors(["documentos" => "⚠ Requisitos incompletos: el postulante debe presentar los 3 documentos (CI, Libreta de colegio y Título de Bachiller) para completar la inscripción."])
                ->withInput();
        }
        $p=Postulante::create($d);
        $this->registrarEnBitacora("Registró postulante: {$p->nombre_completo} CI:{$p->ci}",$p->id,'Postulantes');
        return redirect()->route('postulantes.index')->with('success',"Postulante «{$p->nombre_completo}» registrado.");
    }
    public function show(Postulante $postulante) { $postulante->load('primeraOpcion','segundaOpcion','gestion'); return view('postulantes.show',compact('postulante')); }
    public function edit(Postulante $postulante) { return view('postulantes.edit',['postulante'=>$postulante,'carreras'=>Carrera::where('estado',true)->orderBy('nombre')->get(),'gestiones'=>Gestion::orderByDesc('fecha_inicio')->get()]); }
    public function update(Request $r, Postulante $postulante) {
        $d=$r->validate(['gestion_id'=>'required|exists:gestiones,id','primera_opcion_id'=>'required|exists:carreras,id','segunda_opcion_id'=>"required|exists:carreras,id|different:primera_opcion_id",'ci'=>"required|string|max:20|unique:postulantes,ci,{$postulante->id}",'nombres'=>'required|string|max:100','apellidos'=>'required|string|max:100','fecha_nacimiento'=>'nullable|date|before:today','sexo'=>'nullable|in:M,F,Otro','direccion'=>'nullable|string|max:200','telefono'=>'nullable|string|max:20','email'=>'nullable|email|max:100','colegio_procedencia'=>'nullable|string|max:150','ciudad'=>'nullable|string|max:80','doc_ci'=>'boolean','doc_libreta_colegio'=>'boolean','doc_titulo_bachiller'=>'boolean']);
        $d['doc_ci']=$r->boolean('doc_ci'); $d['doc_libreta_colegio']=$r->boolean('doc_libreta_colegio'); $d['doc_titulo_bachiller']=$r->boolean('doc_titulo_bachiller');
        $postulante->update($d);
        $this->registrarEnBitacora("Actualizó postulante: {$postulante->nombre_completo}",$postulante->id,'Postulantes');
        return redirect()->route('postulantes.index')->with('success',"Postulante actualizado.");
    }
    public function destroy(Postulante $postulante) {
        $n=$postulante->nombre_completo; $postulante->delete();
        $this->registrarEnBitacora("Eliminó postulante: {$n}",null,'Postulantes');
        return redirect()->route('postulantes.index')->with('success',"Postulante «{$n}» eliminado.");
    }

    /** CU-07: Valida/invalida requisitos de un postulante */
    public function validarRequisitos(\Illuminate\Http\Request $r, \App\Models\Postulante $postulante) {
        $d = $r->validate([
            "doc_ci"              => "boolean",
            "doc_libreta_colegio" => "boolean",
            "doc_titulo_bachiller"=> "boolean",
        ]);
        $d["doc_ci"]               = $r->boolean("doc_ci");
        $d["doc_libreta_colegio"]  = $r->boolean("doc_libreta_colegio");
        $d["doc_titulo_bachiller"] = $r->boolean("doc_titulo_bachiller");
        $postulante->update($d);
        $completo = $postulante->tieneDocumentos();
        if ($completo && $postulante->estado === "inscrito") {
            $postulante->update(["estado" => "en_curso"]);
        }
        $this->registrarEnBitacora("Validó requisitos del postulante: {$postulante->nombre_completo}", $postulante->id, "Postulantes");
        return redirect()->route("postulantes.show", $postulante)
            ->with("success", $completo
                ? "✔ Requisitos completos. Postulante habilitado para el CUP."
                : "⚠ Requisitos incompletos. El postulante no puede acceder al CUP hasta completarlos.");
    }
}
