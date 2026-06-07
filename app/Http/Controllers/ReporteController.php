<?php
namespace App\Http\Controllers;
use App\Models\{Carrera, Gestion};
use App\Traits\BitacoraTrait;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * CU-19: Reportes y estadísticas. Una sola fuente de datos (datos()) alimenta
 * tres salidas: vista dinámica (filtros + DataTable + Chart.js), PDF estático
 * con membrete (dompdf) y CSV/Excel (BOM UTF-8, separador ';').
 */
class ReporteController extends Controller
{
    use BitacoraTrait;

    public const TIPOS = ['general','aprobados','reprobados','promedios','grupos','materias','docentes','top-grupos'];
    private const APROBADO_EN = ['aprobado','admitido','admitido_segunda_opcion'];

    public function __construct()
    {
        $this->middleware(['auth', 'permission:ver reportes']);
    }

    /** Hub de reportes: tarjetas + filtro global de gestión. */
    public function index(Request $r)
    {
        $gestiones = Gestion::orderByDesc('fecha_inicio')->get();
        $gestionId = (int) ($r->query('gestion_id') ?: ($gestiones->firstWhere('estado','en_curso')->id ?? $gestiones->first()?->id));
        $catalogo = [
            ['tipo'=>'general',    'icono'=>'fa-users',          'nombre'=>'Lista general de postulantes', 'desc'=>'Todos los postulantes con filtros por carrera y estado'],
            ['tipo'=>'aprobados',  'icono'=>'fa-user-check',     'nombre'=>'Postulantes aprobados',        'desc'=>'Quienes alcanzaron promedio ≥ 60 en el CUP'],
            ['tipo'=>'reprobados', 'icono'=>'fa-user-times',     'nombre'=>'Postulantes reprobados',       'desc'=>'Quienes no alcanzaron la nota mínima'],
            ['tipo'=>'promedios',  'icono'=>'fa-percentage',     'nombre'=>'Promedios generales',          'desc'=>'Ranking de promedios y promedio por carrera'],
            ['tipo'=>'grupos',     'icono'=>'fa-layer-group',    'nombre'=>'Grupos habilitados',           'desc'=>'Ocupación y capacidad de cada grupo'],
            ['tipo'=>'materias',   'icono'=>'fa-book-open',      'nombre'=>'Estadísticas por materia',     'desc'=>'Promedio y aprobación por materia'],
            ['tipo'=>'docentes',   'icono'=>'fa-chalkboard-teacher','nombre'=>'Docentes por grupos',       'desc'=>'Carga horaria: docente, materia, grupo y horario'],
            ['tipo'=>'top-grupos', 'icono'=>'fa-trophy',         'nombre'=>'Grupos con más aprobados',     'desc'=>'Ranking de grupos por cantidad de aprobados'],
        ];
        return view('reportes.index', compact('catalogo','gestiones','gestionId'));
    }

    /** Reporte dinámico en pantalla. */
    public function show(Request $r, string $tipo)
    {
        abort_unless(in_array($tipo, self::TIPOS), 404);
        $rep = $this->datos($tipo, $r);
        $this->registrarEnBitacora("Generó reporte: {$rep['titulo']}", null, 'Reportes');
        return view('reportes.show', $rep + [
            'tipo'      => $tipo,
            'gestiones' => Gestion::orderByDesc('fecha_inicio')->get(),
            'carreras'  => Carrera::where('estado', true)->orderBy('nombre')->get(),
        ]);
    }

    /** Exportación estática: pdf | csv (Excel). */
    public function exportar(Request $r, string $tipo, string $formato)
    {
        abort_unless(in_array($tipo, self::TIPOS) && in_array($formato, ['pdf','csv']), 404);
        $rep = $this->datos($tipo, $r);
        $nombre = 'reporte-'.$tipo.'-'.now()->format('Y-m-d_Hi');
        $this->registrarEnBitacora("Exportó reporte {$tipo} a ".strtoupper($formato), null, 'Reportes');

        if ($formato === 'pdf') {
            return Pdf::loadView('reportes.pdf', $rep)
                ->setPaper('letter', count($rep['columnas']) > 5 ? 'landscape' : 'portrait')
                ->download($nombre.'.pdf');
        }
        return response()->streamDownload(function () use ($rep) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // BOM: tildes correctas en Excel
            fputcsv($out, [$rep['titulo'].' — '.$rep['subtitulo']], ';');
            fputcsv($out, $rep['columnas'], ';');
            foreach ($rep['filas'] as $f) fputcsv($out, $f, ';');
            fclose($out);
        }, $nombre.'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /** Única fuente de datos de los 8 reportes. Devuelve titulo, subtitulo, columnas, filas, grafico. */
    private function datos(string $tipo, Request $r): array
    {
        $gestion = Gestion::find($r->query('gestion_id'))
                ?? Gestion::where('estado','en_curso')->first()
                ?? Gestion::orderByDesc('fecha_inicio')->first();
        $gid = $gestion?->id ?? 0;
        $sub = 'Gestión: '.($gestion->descripcion ?? '—').' · Generado: '.now()->format('d/m/Y H:i');
        $grafico = null;

        switch ($tipo) {
            case 'general':
                $q = DB::table('postulantes as p')
                    ->leftJoin('carreras as c1','c1.id','=','p.primera_opcion_id')
                    ->leftJoin('carreras as c2','c2.id','=','p.segunda_opcion_id')
                    ->where('p.gestion_id', $gid);
                if ($r->filled('carrera_id')) $q->where('p.primera_opcion_id', $r->query('carrera_id'));
                if ($r->filled('estado'))     $q->where('p.estado', $r->query('estado'));
                $filas = $q->orderBy('p.apellidos')
                    ->get(['p.ci','p.apellidos','p.nombres','c1.nombre as op1','c2.nombre as op2','p.estado','p.promedio_general'])
                    ->map(fn($x) => [$x->ci, "{$x->apellidos}, {$x->nombres}", $x->op1, $x->op2,
                                     ucfirst(str_replace('_',' ',$x->estado)), $x->promedio_general ?? '—'])->all();
                return ['titulo'=>'Lista general de postulantes','subtitulo'=>$sub,
                        'columnas'=>['CI','Apellidos y Nombres','1ª Opción','2ª Opción','Estado','Promedio'],
                        'filas'=>$filas,'grafico'=>null];

            case 'aprobados':
            case 'reprobados':
                $ok = $tipo === 'aprobados';
                $filas = DB::table('postulantes as p')
                    ->leftJoin('carreras as c1','c1.id','=','p.primera_opcion_id')
                    ->leftJoin('admisiones as a','a.postulante_id','=','p.id')
                    ->leftJoin('carreras as ca','ca.id','=','a.carrera_asignada_id')
                    ->where('p.gestion_id', $gid)
                    ->when($ok,  fn($q) => $q->whereIn('p.estado', self::APROBADO_EN),
                                 fn($q) => $q->where('p.estado', 'no_aprobado'))
                    ->orderByDesc('p.promedio_general')
                    ->get(['p.ci','p.apellidos','p.nombres','c1.nombre as op1','p.promedio_general','p.estado','ca.nombre as asignada'])
                    ->map(fn($x) => [$x->ci, "{$x->apellidos}, {$x->nombres}", $x->op1,
                                     $x->promedio_general ?? '—', ucfirst(str_replace('_',' ',$x->estado)), $x->asignada ?? '—'])->all();
                $ap  = DB::table('postulantes')->where('gestion_id',$gid)->whereIn('estado', self::APROBADO_EN)->count();
                $rep = DB::table('postulantes')->where('gestion_id',$gid)->where('estado','no_aprobado')->count();
                $grafico = ['tipo'=>'doughnut','labels'=>['Aprobados','Reprobados'],'data'=>[$ap,$rep],'label'=>'Resultado del CUP'];
                return ['titulo'=>'Postulantes '.$tipo,'subtitulo'=>$sub,
                        'columnas'=>['CI','Apellidos y Nombres','1ª Opción','Promedio','Estado','Carrera asignada'],
                        'filas'=>$filas,'grafico'=>$grafico];

            case 'promedios':
                $filas = DB::table('postulantes as p')
                    ->leftJoin('carreras as c1','c1.id','=','p.primera_opcion_id')
                    ->where('p.gestion_id',$gid)->whereNotNull('p.promedio_general')
                    ->orderByDesc('p.promedio_general')
                    ->get(['p.ci','p.apellidos','p.nombres','c1.nombre as op1','p.promedio_general'])
                    ->map(fn($x,$i) => [$i+1, $x->ci, "{$x->apellidos}, {$x->nombres}", $x->op1, $x->promedio_general])->all();
                $porCarrera = DB::table('postulantes as p')->join('carreras as c','c.id','=','p.primera_opcion_id')
                    ->where('p.gestion_id',$gid)->whereNotNull('p.promedio_general')
                    ->groupBy('c.nombre')->orderBy('c.nombre')
                    ->selectRaw('c.nombre, ROUND(AVG(p.promedio_general),2) prom')->get();
                $grafico = ['tipo'=>'bar','labels'=>$porCarrera->pluck('nombre')->all(),
                            'data'=>$porCarrera->pluck('prom')->map(fn($v)=>(float)$v)->all(),'label'=>'Promedio por carrera (1ª opción)'];
                return ['titulo'=>'Promedios generales','subtitulo'=>$sub,
                        'columnas'=>['#','CI','Apellidos y Nombres','1ª Opción','Promedio'],
                        'filas'=>$filas,'grafico'=>$grafico];

            case 'grupos':
                $g = DB::table('grupos as g')
                    ->leftJoin('grupo_postulante as gp','gp.grupo_id','=','g.id')
                    ->where('g.gestion_id',$gid)->groupBy('g.id','g.codigo','g.turno','g.modalidad','g.capacidad_maxima')
                    ->orderBy('g.codigo')
                    ->selectRaw('g.codigo, g.turno, g.modalidad, g.capacidad_maxima, COUNT(gp.id) inscritos')->get();
                $filas = $g->map(fn($x) => [$x->codigo, ucfirst($x->turno), ucfirst($x->modalidad),
                            $x->inscritos, $x->capacidad_maxima,
                            $x->capacidad_maxima ? round(100*$x->inscritos/$x->capacidad_maxima).' %' : '—'])->all();
                $grafico = ['tipo'=>'bar','labels'=>$g->pluck('codigo')->all(),
                            'data'=>$g->pluck('inscritos')->map(fn($v)=>(int)$v)->all(),'label'=>'Inscritos por grupo'];
                return ['titulo'=>'Grupos habilitados ('.$g->count().')','subtitulo'=>$sub,
                        'columnas'=>['Grupo','Turno','Modalidad','Inscritos','Capacidad','Ocupación'],
                        'filas'=>$filas,'grafico'=>$grafico];

            case 'materias':
                $m = DB::table('notas as n')->join('materias as m','m.id','=','n.materia_id')
                    ->join('grupos as g','g.id','=','n.grupo_id')->where('g.gestion_id',$gid)
                    ->groupBy('m.nombre')->orderBy('m.nombre')
                    ->selectRaw("m.nombre, ROUND(AVG(n.nota_final),2) prom,
                                 COUNT(*) FILTER (WHERE n.aprobado) ap,
                                 COUNT(*) FILTER (WHERE NOT n.aprobado) rep")->get();
                $filas = $m->map(fn($x) => [$x->nombre, $x->prom, $x->ap, $x->rep,
                            ($x->ap+$x->rep) ? round(100*$x->ap/($x->ap+$x->rep)).' %' : '—'])->all();
                $grafico = ['tipo'=>'bar','labels'=>$m->pluck('nombre')->all(),
                            'data'=>$m->pluck('prom')->map(fn($v)=>(float)$v)->all(),'label'=>'Promedio por materia'];
                return ['titulo'=>'Estadísticas por materia','subtitulo'=>$sub,
                        'columnas'=>['Materia','Promedio','Aprobados','Reprobados','% Aprobación'],
                        'filas'=>$filas,'grafico'=>$grafico];

            case 'docentes':
                $filas = DB::table('asignaciones as a')
                    ->join('docentes as d','d.id','=','a.docente_id')
                    ->join('materias as m','m.id','=','a.materia_id')
                    ->join('grupos as g','g.id','=','a.grupo_id')->where('g.gestion_id',$gid)
                    ->orderBy('d.apellidos')->orderBy('g.codigo')
                    ->get(['d.apellidos','d.nombres','m.nombre as materia','g.codigo','a.dia','a.hora_inicio','a.hora_fin','a.aula'])
                    ->map(fn($x) => ["{$x->apellidos}, {$x->nombres}", $x->materia, $x->codigo,
                                     ucfirst($x->dia), substr($x->hora_inicio,0,5).'–'.substr($x->hora_fin,0,5), $x->aula ?? '—'])->all();
                return ['titulo'=>'Docentes por grupos','subtitulo'=>$sub,
                        'columnas'=>['Docente','Materia','Grupo','Día','Horario','Aula'],
                        'filas'=>$filas,'grafico'=>null];

            case 'top-grupos':
                $g = DB::table('grupo_postulante as gp')
                    ->join('grupos as g','g.id','=','gp.grupo_id')
                    ->join('postulantes as p','p.id','=','gp.postulante_id')->where('g.gestion_id',$gid)
                    ->groupBy('g.codigo','g.turno')->orderByDesc(DB::raw('2'))
                    ->selectRaw("g.codigo, COUNT(*) FILTER (WHERE p.estado IN ('aprobado','admitido','admitido_segunda_opcion')) aprobados,
                                 COUNT(*) inscritos, g.turno")->get()->sortByDesc('aprobados')->values();
                $filas = $g->map(fn($x,$i) => [$i+1, $x->codigo, ucfirst($x->turno), $x->aprobados, $x->inscritos,
                            $x->inscritos ? round(100*$x->aprobados/$x->inscritos).' %' : '—'])->all();
                $grafico = ['tipo'=>'bar','labels'=>$g->pluck('codigo')->all(),
                            'data'=>$g->pluck('aprobados')->map(fn($v)=>(int)$v)->all(),'label'=>'Aprobados por grupo'];
                return ['titulo'=>'Grupos con mayor cantidad de aprobados','subtitulo'=>$sub,
                        'columnas'=>['#','Grupo','Turno','Aprobados','Inscritos','% Aprobación'],
                        'filas'=>$filas,'grafico'=>$grafico];
        }
        abort(404);
    }
}
