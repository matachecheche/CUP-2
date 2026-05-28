<?php

namespace App\Http\Controllers;

use App\Models\{Carrera, CupoCarrera, Gestion};
use App\Traits\BitacoraTrait;
use Illuminate\Http\Request;

/**
 * CU-08: Definir y consultar cupos por carrera y gestión.
 * Página dedicada /cupos — tabla cruzada carrera × gestión.
 */
class CupoController extends Controller
{
    use BitacoraTrait;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:ver carreras');
    }

    /** Lista todos los cupos en una tabla carrera × gestión */
    public function index()
    {
        $carreras  = Carrera::where('estado', true)->orderBy('nombre')->get();
        $gestiones = Gestion::orderByDesc('fecha_inicio')->get();

        // Matriz [carrera_id][gestion_id] = cantidad_maxima
        $matriz = [];
        CupoCarrera::with(['carrera', 'gestion'])->get()->each(function ($c) use (&$matriz) {
            $matriz[$c->carrera_id][$c->gestion_id] = $c->cantidad_maxima;
        });

        return view('cupos.index', compact('carreras', 'gestiones', 'matriz'));
    }

    /** Crear o actualizar cupo para una combinación carrera + gestión */
    public function store(Request $r)
    {
        $data = $r->validate([
            'carrera_id'      => 'required|exists:carreras,id',
            'gestion_id'      => 'required|exists:gestiones,id',
            'cantidad_maxima' => 'required|integer|min:1|max:9999',
        ]);

        $cupo    = CupoCarrera::updateOrCreate(
            ['carrera_id' => $data['carrera_id'], 'gestion_id' => $data['gestion_id']],
            ['cantidad_maxima' => $data['cantidad_maxima']]
        );
        $carrera = Carrera::find($data['carrera_id']);
        $gestion = Gestion::find($data['gestion_id']);

        $this->registrarEnBitacora(
            "Definió cupo {$cupo->cantidad_maxima} para {$carrera->nombre} — {$gestion->descripcion}",
            $cupo->id, 'Cupos'
        );

        return redirect()->route('cupos.index')
            ->with('success', "Cupo de {$cupo->cantidad_maxima} guardado para «{$carrera->nombre}» en {$gestion->descripcion}.");
    }
}
