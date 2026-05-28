<?php
namespace App\Http\Controllers;

use App\Models\{Admision, Postulante, Carrera, Gestion, CupoCarrera};
use App\Traits\BitacoraTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdmisionController extends Controller
{
    use BitacoraTrait;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:procesar admision');
    }

    /** CU-16/CU-17: Vista del proceso de admisión */
    public function index()
    {
        $gestion    = Gestion::where('estado','en_curso')->first();
        $admisiones = $gestion
            ? Admision::where('gestion_id',$gestion->id)
                ->with(['postulante','carreraAsignada'])
                ->orderByDesc('promedio_general')->get()
            : collect();

        $resumen = $gestion ? [
            'total'            => Postulante::where('gestion_id',$gestion->id)->count(),
            'aprobados'        => Postulante::where('gestion_id',$gestion->id)->where('estado','aprobado')->count(),
            'admitidos_1'      => $admisiones->where('resultado','admitido_primera')->count(),
            'admitidos_2'      => $admisiones->where('resultado','admitido_segunda')->count(),
            'no_admitidos'     => $admisiones->where('resultado','no_admitido')->count(),
        ] : [];

        return view('admision.index', compact('gestion','admisiones','resumen'));
    }

    /** CU-16/CU-17: Procesa la admisión para la gestión activa */
    public function procesar()
    {
        $gestion = Gestion::where('estado','en_curso')->firstOrFail();

        // Limpiar admisiones previas de esta gestión
        Admision::where('gestion_id',$gestion->id)->delete();

        $cupos      = CupoCarrera::where('gestion_id',$gestion->id)->pluck('cantidad_maxima','carrera_id')->toArray();
        $contadores = array_fill_keys(array_keys($cupos), 0);

        $aprobados = Postulante::where('gestion_id',$gestion->id)
            ->where('estado','aprobado')
            ->orderByDesc('promedio_general')
            ->get();

        $pendientes2 = [];
        foreach ($aprobados as $p) {
            $c1 = $p->primera_opcion_id;
            if (($contadores[$c1] ?? 0) < ($cupos[$c1] ?? 0)) {
                Admision::create(['postulante_id'=>$p->id,'gestion_id'=>$gestion->id,'promedio_general'=>$p->promedio_general,'carrera_asignada_id'=>$c1,'resultado'=>'admitido_primera','publicado'=>false]);
                $contadores[$c1]++;
                $p->update(['estado'=>'admitido']);
            } else {
                $pendientes2[] = $p;
            }
        }
        foreach ($pendientes2 as $p) {
            $c2 = $p->segunda_opcion_id;
            if (($contadores[$c2] ?? 0) < ($cupos[$c2] ?? 0)) {
                Admision::create(['postulante_id'=>$p->id,'gestion_id'=>$gestion->id,'promedio_general'=>$p->promedio_general,'carrera_asignada_id'=>$c2,'resultado'=>'admitido_segunda','publicado'=>false]);
                $contadores[$c2]++;
                $p->update(['estado'=>'admitido_segunda_opcion']);
            } else {
                Admision::create(['postulante_id'=>$p->id,'gestion_id'=>$gestion->id,'promedio_general'=>$p->promedio_general,'carrera_asignada_id'=>null,'resultado'=>'no_admitido','publicado'=>false]);
                $p->update(['estado'=>'no_admitido']);
            }
        }

        $total = Admision::where('gestion_id',$gestion->id)->count();
        $this->registrarEnBitacora("Procesó admisión gestión {$gestion->descripcion}: {$total} registros", null, 'Admisión');
        return redirect()->route('admision.index')->with('success',"Admisión procesada: {$total} postulantes evaluados.");
    }

    /** CU-18: Publicar resultados */
    public function publicar()
    {
        $gestion = Gestion::where('estado','en_curso')->firstOrFail();
        $n = Admision::where('gestion_id',$gestion->id)->update(['publicado'=>true]);
        $this->registrarEnBitacora("Publicó resultados de admisión gestión {$gestion->descripcion}", null, 'Admisión');
        return redirect()->route('admision.index')->with('success',"{$n} resultado(s) publicados.");
    }
}
