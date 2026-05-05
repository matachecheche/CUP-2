<?php

namespace App\Http\Controllers;

use App\Models\Cuota;
use Illuminate\Http\Request;
use App\Models\Residente;
use Spatie\Permission\Models\Role;
use App\Traits\BitacoraTrait;

use Carbon\Carbon;

class CuotaController extends Controller
{
    use BitacoraTrait;

    public function index(Request $request)
    {
        $query = Cuota::with('residente');

        // Filtro por texto (nombre, apellido o unidad)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('residente', function ($q) use ($search) {
                $q->where('nombre', 'like', "%$search%")
                    ->orWhere('apellido', 'like', "%$search%")
                    ->orWhere('unidad', 'like', "%$search%");
            });
        }

        // Filtro por estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // Filtro por tipo de tiempo
        switch ($request->filtro_tiempo) {
            case 'fecha':
                if ($request->filled('fecha_desde') && $request->filled('fecha_hasta')) {
                    $query->whereBetween('fecha_emision', [$request->fecha_desde, $request->fecha_hasta]);
                }
                break;

            case 'mes':
                if ($request->filled('mes')) {
                    $query->whereMonth('fecha_emision', date('m', strtotime($request->mes)))
                        ->whereYear('fecha_emision', date('Y', strtotime($request->mes)));
                }
                break;

            case 'semana':
                if ($request->filled('semana')) {
                    [$year, $week] = explode('-W', $request->semana);
                    $start = Carbon::now()->setISODate($year, $week)->startOfWeek();
                    $end = Carbon::now()->setISODate($year, $week)->endOfWeek();
                    $query->whereBetween('fecha_emision', [$start->toDateString(), $end->toDateString()]);
                }
                break;

            case 'anio':
                if ($request->filled('anio')) {
                    $query->whereYear('fecha_emision', $request->anio);
                }
                break;
        }

        $cuotas = $query->orderByDesc('fecha_emision')->paginate(10);

        return view('cuotas.index', compact('cuotas'));
    }

    public function create()
    {
        $tiposCuotas = \App\Models\TipoCuota::all();
        $residentes = \App\Models\Residente::all();
        $roles = Role::pluck('name'); // 🔁 trae solo los nombres de roles

        $this->registrarEnBitacora('Ingresó al formulario de crear cuota', auth()->id());
        
        return view('cuotas.create', compact('tiposCuotas', 'residentes', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'fecha_emision' => 'required|date',
            'fecha_vencimiento' => 'required|date|after_or_equal:fecha_emision',
            'monto' => 'required|numeric',
            'tipo_cuota_id' => 'required|exists:tipos_cuotas,id',
            'estado' => 'required|in:pendiente,activa,cancelada',
            'observacion' => 'nullable|string',
        ]);

        $destino = $request->destino;

        if ($destino === 'todos') {
            $residentes = Residente::all();
        } elseif ($destino === 'grupo') {
            $residentes = Residente::role($request->grupo_rol)->get(); // Usa spatie
        } elseif ($destino === 'personalizado') {
            $request->validate(['residente_id' => 'required|exists:residentes,id']);
            $residentes = collect([Residente::findOrFail($request->residente_id)]);
        } else {
            return redirect()->back()->withErrors(['destino' => 'Destino inválido']);
        }

        foreach ($residentes as $residente) {
            Cuota::create([
                'titulo' => $request->titulo,
                'descripcion' => $request->descripcion,
                'fecha_emision' => $request->fecha_emision,
                'fecha_vencimiento' => $request->fecha_vencimiento,
                'monto' => $request->monto,
                'estado' => $request->estado,
                'residente_id' => $residente->id,
                'tipo_cuota_id' => $request->tipo_cuota_id,
                'user_id' => auth()->id(),
                'observacion' => $request->observacion,
            ]);
        }

        return redirect()->route('cuotas.index')->with('success', 'Cuota(s) registrada(s) correctamente.');
    }

    public function edit(Cuota $cuota)
    {
        return view('cuotas.edit', compact('cuota'));
    }

    public function update(Request $request, Cuota $cuota)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'fecha_emision' => 'required|date',
            'fecha_vencimiento' => 'required|date|after_or_equal:fecha_emision',
            'monto' => 'required|numeric',
            'estado' => 'required|in:pendiente,activa,cancelada,pagado',
            'observacion' => 'nullable|string',
        ]);

        $cuota->update($request->only([
            'titulo',
            'descripcion',
            'fecha_emision',
            'fecha_vencimiento',
            'monto',
            'estado',
            'observacion'
        ]));
        
        return redirect()->route('cuotas.index')->with('success', 'Cuota actualizada correctamente.');
    }


    public function destroy(Cuota $cuota)
    {
        $cuota->delete();
        return redirect()->route('cuotas.index')->with('success', 'Cuota eliminada correctamente.');
    }


    public function show(Cuota $cuota)
    {
        return view('cuotas.show', compact('cuota'));
    }
}
