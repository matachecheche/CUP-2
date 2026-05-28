<?php
namespace App\Http\Controllers;
use App\Models\Docente;
use App\Traits\BitacoraTrait;
use Illuminate\Http\Request;
class DocenteController extends Controller {
    use BitacoraTrait;
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('permission:ver docentes')->only('index','show');
        $this->middleware('permission:crear docentes')->only('create','store');
        $this->middleware('permission:editar docentes')->only('edit','update');
        $this->middleware('permission:eliminar docentes')->only('destroy');
    }
    public function index() { return view('docentes.index',['docentes'=>Docente::orderBy('apellidos')->get()]); }
    public function create() { return view('docentes.create'); }
    public function store(Request $r) {
        $d=$r->validate([
            'ci'                          => 'required|string|max:20|regex:/^[0-9]{6,10}(-[A-Z]{1,2})?$/|unique:docentes,ci',
            'nombres'                     => 'required|string|max:100|regex:/^[\pL\s\.\-]+$/u',
            'apellidos'                   => 'required|string|max:100|regex:/^[\pL\s\.\-]+$/u',
            'telefono'                    => 'nullable|regex:/^[67][0-9]{7}$/',
            'email'                       => 'required|email|max:100|unique:docentes,email',
            'titulo_profesional'          => 'required|string|max:150',
            'maestria'                    => 'required|string|max:150',
            'diplomado_educacion_superior'=> 'required|string|max:150',
            'certificacion_ingles'        => 'nullable|string|max:100',
            'otras_certificaciones'       => 'nullable|string',
            'area_formacion'              => 'required|in:Computación,Matemáticas,Física,Inglés',
            'estado'                      => 'boolean',
        ], $this->messages());
        $d['estado']=$r->boolean('estado',true);
        $dc=Docente::create($d);
        $this->registrarEnBitacora("Registró docente: {$dc->nombre_completo}",$dc->id,'Docentes');
        return redirect()->route('docentes.index')->with('success',"Docente «{$dc->nombre_completo}» registrado.");
    }
    public function show(Docente $docente) { return view('docentes.show',compact('docente')); }
    public function edit(Docente $docente) { return view('docentes.edit',compact('docente')); }
    public function update(Request $r, Docente $docente) {
        $d=$r->validate([
            'ci'                          => "required|string|max:20|regex:/^[0-9]{6,10}(-[A-Z]{1,2})?$/|unique:docentes,ci,{$docente->id}",
            'nombres'                     => 'required|string|max:100|regex:/^[\pL\s\.\-]+$/u',
            'apellidos'                   => 'required|string|max:100|regex:/^[\pL\s\.\-]+$/u',
            'telefono'                    => 'nullable|regex:/^[67][0-9]{7}$/',
            'email'                       => "required|email|max:100|unique:docentes,email,{$docente->id}",
            'titulo_profesional'          => 'required|string|max:150',
            'maestria'                    => 'required|string|max:150',
            'diplomado_educacion_superior'=> 'required|string|max:150',
            'certificacion_ingles'        => 'nullable|string|max:100',
            'otras_certificaciones'       => 'nullable|string',
            'area_formacion'              => 'required|in:Computación,Matemáticas,Física,Inglés',
            'estado'                      => 'boolean',
        ], $this->messages());
        $d['estado']=$r->boolean('estado',true); $docente->update($d);
        $this->registrarEnBitacora("Actualizó docente: {$docente->nombre_completo}",$docente->id,'Docentes');
        return redirect()->route('docentes.index')->with('success',"Docente actualizado.");
    }
    private function messages(): array {
        return [
            'ci.regex'      => 'CI inválido (formato: 6-10 dígitos, opcional sufijo -LP, -SC, etc.).',
            'ci.unique'     => 'Ya existe un docente con ese CI.',
            'email.unique'  => 'Ya existe un docente con ese email.',
            'email.required'=> 'El correo electrónico es obligatorio.',
            'nombres.regex' => 'Los nombres solo pueden contener letras, espacios, punto y guion.',
            'apellidos.regex'=>'Los apellidos solo pueden contener letras, espacios, punto y guion.',
            'telefono.regex'=> 'El teléfono debe iniciar con 6 o 7 y tener 8 dígitos.',
            'titulo_profesional.required' => 'El título profesional es obligatorio (requisito CUP).',
            'maestria.required'           => 'La maestría es obligatoria (requisito CUP).',
            'diplomado_educacion_superior.required' => 'El diplomado en educación superior es obligatorio (requisito CUP).',
            'area_formacion.in'           => 'El área debe ser Computación, Matemáticas, Física o Inglés.',
        ];
    }
    public function destroy(Docente $docente) {
        $n=$docente->nombre_completo; $docente->update(['estado'=>false]);
        $this->registrarEnBitacora("Desactivó docente: {$n}",$docente->id,'Docentes');
        return redirect()->route('docentes.index')->with('success',"Docente «{$n}» desactivado.");
    }
}
