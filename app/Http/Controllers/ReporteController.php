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

    public const TIPOS = ['general','aprobados','reprobados','promedios','grupos','materias','docentes','top-grupos','personalizado'];
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

        // Constructor del reporte personalizado: sin 'tabla' todavía no hay nada que generar
        if ($tipo === 'personalizado' && ! $r->filled('tabla')) {
            return view('reportes.personalizado', [
                'catalogoCampos' => $this->camposPersonalizado(),
                'tabla'          => $r->query('tabla_sel', 'postulantes'),
                'gestiones'      => Gestion::orderByDesc('fecha_inicio')->get(),
                'gestionId'      => (int) ($r->query('gestion_id') ?: Gestion::where('estado','en_curso')->value('id') ?? 0),
            ]);
        }

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

            case 'personalizado':
                $cat   = $this->camposPersonalizado();
                $tabla = $r->query('tabla');
                abort_unless(isset($cat[$tabla]), 404);
                $disp = $cat[$tabla]['campos'];
                $sel  = array_values(array_intersect(array_keys($disp), (array) $r->query('campos', [])));
                if (! $sel) $sel = array_slice(array_keys($disp), 0, 4); // por defecto, los primeros 4

                $columnas = []; $selects = [];
                foreach ($sel as $i => $k) {
                    $columnas[] = $disp[$k][0];
                    $selects[]  = DB::raw($disp[$k][1].' as c'.$i);
                }
                $filas = $this->queryPersonalizado($tabla, $gid)->select($selects)->get()
                    ->map(fn ($x) => array_map(
                        fn ($v) => is_bool($v) ? ($v ? 'Sí' : 'No') : ($v ?? '—'),
                        array_values((array) $x)))->all();

                return ['titulo'=>'Reporte personalizado: '.$cat[$tabla]['titulo'],'subtitulo'=>$sub,
                        'columnas'=>$columnas,'filas'=>$filas,'grafico'=>null];
        }
        abort(404);
    }

    /**
     * Whitelist del reporte personalizado: tablas y campos permitidos.
     * Cada campo: clave => [Etiqueta visible, expresión SQL segura].
     * Nada que no esté aquí puede llegar a la consulta.
     */
    private function camposPersonalizado(): array
    {
        return [
            'postulantes' => ['titulo' => 'Postulantes', 'campos' => [
                'ci'        => ['CI', 'p.ci'],
                'apellidos' => ['Apellidos', 'p.apellidos'],
                'nombres'   => ['Nombres', 'p.nombres'],
                'nacimiento'=> ['Fecha de nacimiento', 'p.fecha_nacimiento'],
                'sexo'      => ['Sexo', 'p.sexo'],
                'telefono'  => ['Teléfono', 'p.telefono'],
                'email'     => ['Correo', 'p.email'],
                'colegio'   => ['Colegio', 'p.colegio_procedencia'],
                'ciudad'    => ['Ciudad', 'p.ciudad'],
                'op1'       => ['1ª Opción', 'c1.nombre'],
                'op2'       => ['2ª Opción', 'c2.nombre'],
                'estado'    => ['Estado', 'p.estado'],
                'promedio'  => ['Promedio', 'p.promedio_general'],
            ]],
            'docentes' => ['titulo' => 'Docentes', 'campos' => [
                'ci'        => ['CI', 'd.ci'],
                'apellidos' => ['Apellidos', 'd.apellidos'],
                'nombres'   => ['Nombres', 'd.nombres'],
                'email'     => ['Correo', 'd.email'],
                'telefono'  => ['Teléfono', 'd.telefono'],
                'titulo'    => ['Título profesional', 'd.titulo_profesional'],
                'maestria'  => ['Maestría', 'd.maestria'],
                'diplomado' => ['Dipl. Educación Superior', 'd.diplomado_educacion_superior'],
                'area'      => ['Área de formación', 'd.area_formacion'],
            ]],
            'grupos' => ['titulo' => 'Grupos', 'campos' => [
                'codigo'    => ['Grupo', 'g.codigo'],
                'turno'     => ['Turno', 'g.turno'],
                'modalidad' => ['Modalidad', 'g.modalidad'],
                'capacidad' => ['Capacidad', 'g.capacidad_maxima'],
                'gestion'   => ['Gestión', 'ge.descripcion'],
            ]],
            'notas' => ['titulo' => 'Notas', 'campos' => [
                'postulante'=> ['Postulante', "p.apellidos || ', ' || p.nombres"],
                'ci'        => ['CI', 'p.ci'],
                'materia'   => ['Materia', 'm.nombre'],
                'grupo'     => ['Grupo', 'g.codigo'],
                'examen1'   => ['Examen 1', 'n.examen1'],
                'examen2'   => ['Examen 2', 'n.examen2'],
                'examen3'   => ['Examen 3', 'n.examen3'],
                'final'     => ['Nota final', 'n.nota_final'],
                'aprobado'  => ['Aprobado', 'n.aprobado'],
            ]],
            'pagos' => ['titulo' => 'Pagos', 'campos' => [
                'comprobante'=> ['Comprobante', 'pa.comprobante'],
                'postulante' => ['Postulante', "p.apellidos || ', ' || p.nombres"],
                'ci'         => ['CI', 'p.ci'],
                'monto'      => ['Monto (Bs)', 'pa.monto'],
                'metodo'     => ['Método', 'pa.metodo'],
                'estado'     => ['Estado', 'pa.estado'],
                'fecha'      => ['Fecha de pago', 'pa.fecha_pago'],
            ]],
        ];
    }

    /** Consulta base (joins y filtro de gestión) por tabla del reporte personalizado. */
    private function queryPersonalizado(string $tabla, int $gid)
    {
        return match ($tabla) {
            'postulantes' => DB::table('postulantes as p')
                ->leftJoin('carreras as c1', 'c1.id', '=', 'p.primera_opcion_id')
                ->leftJoin('carreras as c2', 'c2.id', '=', 'p.segunda_opcion_id')
                ->where('p.gestion_id', $gid)->orderBy('p.apellidos'),
            'docentes' => DB::table('docentes as d')->orderBy('d.apellidos'),
            'grupos' => DB::table('grupos as g')
                ->leftJoin('gestiones as ge', 'ge.id', '=', 'g.gestion_id')
                ->where('g.gestion_id', $gid)->orderBy('g.codigo'),
            'notas' => DB::table('notas as n')
                ->join('postulantes as p', 'p.id', '=', 'n.postulante_id')
                ->join('materias as m', 'm.id', '=', 'n.materia_id')
                ->join('grupos as g', 'g.id', '=', 'n.grupo_id')
                ->where('g.gestion_id', $gid)->orderBy('p.apellidos'),
            'pagos' => DB::table('pagos as pa')
                ->join('postulantes as p', 'p.id', '=', 'pa.postulante_id')
                ->where('pa.gestion_id', $gid)->orderByDesc('pa.created_at'),
        };
    }
}
