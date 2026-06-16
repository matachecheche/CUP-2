<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\BitacoraTrait;
use App\Models\{Admision, Asignacion, Comunicado, Gestion, Grupo, Nota, Pago, Postulante};
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    use BitacoraTrait;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $this->registrarEnBitacora('Usuario accedió al panel de control');
        $u = auth()->user();

        $comunicados = Comunicado::vigentes()->paraUsuario($u)
            ->orderByDesc('created_at')->limit(5)->get();

        $dash = null;
        if ($u->docente_id) {
            $dash = $this->dashDocente($u);
        } elseif ($u->postulante_id) {
            $dash = $this->dashPostulante($u);
        } elseif ($u->can('ver postulantes')) { // Admin + Coordinador del CUP
            $dash = $this->dashAdmin();
        }

        return view('panel.index', compact('comunicados', 'dash'));
    }

    /** Dashboard del administrador: indicadores del enunciado + gráficas. */
    private function dashAdmin(): array
    {
        $g = Gestion::where('estado', 'en_curso')->first();
        if (! $g) return ['tipo' => 'admin', 'gestion' => null];

        $base = fn () => Postulante::where('gestion_id', $g->id);
        $adm   = $base()->whereIn('estado', ['admitido', 'admitido_segunda_opcion'])->count();
        $sinCupo = $base()->where('estado', 'no_admitido')->count();
        $aprEsp  = $base()->where('estado', 'aprobado')->count();
        $rep     = $base()->where('estado', 'no_aprobado')->count();
        $proc    = $base()->whereIn('estado', ['preinscrito', 'inscrito', 'en_curso'])->count();

        $porCarrera = Postulante::join('carreras', 'carreras.id', '=', 'postulantes.primera_opcion_id')
            ->where('postulantes.gestion_id', $g->id)
            ->groupBy('carreras.nombre')->orderBy('carreras.nombre')
            ->selectRaw('carreras.nombre, COUNT(*) n')->get();

        return [
            'tipo' => 'admin', 'gestion' => $g,
            'kpi' => [
                'inscritos'  => $base()->where('estado', '!=', 'preinscrito')->count(),
                'aprobados'  => $adm + $sinCupo + $aprEsp,
                'reprobados' => $rep,
                'grupos'     => Grupo::where('gestion_id', $g->id)->count(),
                'admitidos'  => $adm,
                'recaudado'  => (float) Pago::where('gestion_id', $g->id)->where('estado', 'pagado')->sum('monto'),
            ],
            'estados'  => ['labels' => ['Admitidos', 'Aprob. sin cupo', 'Aprob. en espera', 'Reprobados', 'En proceso'],
                           'data'   => [$adm, $sinCupo, $aprEsp, $rep, $proc]],
            'carreras' => ['labels' => $porCarrera->pluck('nombre')->all(),
                           'data'   => $porCarrera->pluck('n')->map(fn ($v) => (int) $v)->all()],
        ];
    }

    /** Dashboard del docente: carga horaria y avance de notas. */
    private function dashDocente($u): array
    {
        $asig = Asignacion::with(['grupo', 'materia'])
            ->where('docente_id', $u->docente_id)
            ->orderBy('grupo_id')->orderBy('materia_id')->get();

        $grupoIds = $asig->pluck('grupo_id')->unique();
        $avance = $asig->map(function ($a) {
            $insc = DB::table('grupo_postulante')->where('grupo_id', $a->grupo_id)->count();
            $reg  = Nota::where('grupo_id', $a->grupo_id)->where('materia_id', $a->materia_id)->count();
            $a->insc = $insc; $a->reg = $reg;
            $a->pct  = $insc ? (int) round(100 * $reg / $insc) : 0;
            return $a;
        });

        return [
            'tipo' => 'docente', 'docente' => $u->docente, 'avance' => $avance,
            'kpi' => [
                'grupos'      => $grupoIds->count(),
                'materias'    => $asig->pluck('materia_id')->unique()->count(),
                'estudiantes' => DB::table('grupo_postulante')->whereIn('grupo_id', $grupoIds)
                                    ->distinct('postulante_id')->count('postulante_id'),
                'clases'      => $asig->count(),
            ],
        ];
    }

    /** Dashboard del postulante: su proceso de punta a punta. */
    private function dashPostulante($u): array
    {
        $p = Postulante::with(['gestion', 'primeraOpcion', 'segundaOpcion'])->find($u->postulante_id);
        $pago = Pago::where('postulante_id', $p->id)
            ->orderByRaw("CASE WHEN estado = 'pagado' THEN 0 ELSE 1 END")->orderByDesc('id')->first();

        $grupoId = DB::table('grupo_postulante')->where('postulante_id', $p->id)->value('grupo_id');
        $grupo   = $grupoId ? Grupo::find($grupoId) : null;
        $horario = $grupo
            ? Asignacion::with(['materia', 'docente'])->where('grupo_id', $grupo->id)
                ->orderByRaw("array_position(ARRAY['lunes','martes','miercoles','jueves','viernes','sabado']::text[], dia)")
                ->orderBy('hora_inicio')->get()
            : collect();

        $notas = Nota::with('materia')->where('postulante_id', $p->id)->get()
            ->sortBy(fn ($n) => $n->materia->orden ?? 0)->values();

        $admision = Admision::with('carreraAsignada')->where('postulante_id', $p->id)->first();
        $publicado = (bool) ($admision?->publicado);

        $paso = match (true) {
            $publicado                                          => 5,
            ! is_null($p->promedio_general) && $notas->isNotEmpty() => 4,
            $notas->isNotEmpty()                                => 3,
            in_array($p->estado, ['inscrito', 'en_curso'])      => 2,
            default                                             => 1,
        };

        return ['tipo' => 'postulante', 'p' => $p, 'pago' => $pago, 'grupo' => $grupo,
                'horario' => $horario, 'notas' => $notas,
                'admision' => $publicado ? $admision : null, 'paso' => $paso];
    }

    public function login()
    {
        // Registrar acción en la bitácora
        $this->registrarEnBitacora('Usuario inició sesión');

        // Lógica de inicio de sesión aquí
    }
}
