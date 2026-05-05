<?php

namespace App\Http\Controllers;
use App\Models\VerificacionInventario;
use App\Models\AreaComun;
use App\Models\Reserva;
use Illuminate\Http\Request;
use App\Traits\BitacoraTrait;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Notificacion;


class ReservaController extends Controller
{
    use BitacoraTrait;

    public function index()
    {
        $user = Auth::user();

        if ($user->residente_id) {
            // Residente ve sólo sus reservas
            $reservas = Reserva::where('residente_id', $user->residente_id)
                            ->orderBy('fecha', 'Desc')->get();
        } elseif ($user->empleado_id) {
            // Empleado ve sólo las reservas que él registró
            $reservas = Reserva::where('empleado_id', $user->empleado_id)
                            ->orderBy('fecha', 'Desc')->get();
        } else {
            // Administrador ve todas
            $reservas = Reserva::orderBy('id', 'Desc')->get();
        }

        return view('reservas.index', compact('reservas'));
    }

    public function create()
    {
        $areasComunes = AreaComun::where('estado', 'activo')->get();
        return view('reservas.create', compact('areasComunes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'area_comun_id' => 'required|exists:area_comuns,id',
            'fecha' => 'required|date|after_or_equal:today',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
            'observacion' => 'nullable|string|max:255',
            'monto_total' => 'nullable|numeric|min:0',
        ]);

        $horaInicio = Carbon::createFromFormat('H:i', $request->hora_inicio);
        $horaFin = Carbon::createFromFormat('H:i', $request->hora_fin);

        $conflict = Reserva::where('area_comun_id', $request->area_comun_id)
            ->where('fecha', $request->fecha)
            ->where(function ($query) use ($horaInicio, $horaFin) {
                $query->where(function ($q) use ($horaInicio, $horaFin) {
                    $q->where('hora_inicio', '<', $horaFin->format('H:i:s'))
                        ->where('hora_fin', '>', $horaInicio->format('H:i:s'));
                });
            })->exists();

        if ($conflict) {
            return back()->withErrors(['La reserva seleccionada se solapa con otra ya existente.'])->withInput();
        }

        $areaComun = AreaComun::findOrFail($request->area_comun_id);
        $duracionHoras = $horaFin->diffInMinutes($horaInicio) / 60;
        $montoTotal = $duracionHoras * $areaComun->monto;

        $user = Auth::user();

        if (!$user) {
            return back()->withErrors(['user' => 'No hay usuario autenticado'])->withInput();
        }

        if (!$user->residente_id) {
            return back()->withErrors([
                'residente_id' => 'El usuario no tiene residente asignado. Usuario ID: ' . $user->id
            ])->withInput();
        }

        // Crear reserva
        $reserva = Reserva::create([
            'area_comun_id' => $request->area_comun_id,
            'fecha' => $request->fecha,
            'hora_inicio' => $request->hora_inicio,
            'hora_fin' => $request->hora_fin,
            'observacion' => $request->observacion,
            'estado' => 'pendiente',
            'residente_id' => $user->residente_id,
            'monto_total' => $montoTotal,
        ]);

        // Determinar nombre del usuario afectado
        $nombreUsuario = $reserva->residente
            ? $reserva->residente->nombre_completo
            : ($reserva->empleado->nombre_completo ?? 'N/D');

        $this->registrarEnBitacora( "Usuario {$nombreUsuario} Creo una reserva ID: {$reserva->id}", $reserva->id);

        // Registrar en bitácora
        $this->registrarEnBitacora('Residente agendó un área común', $request->area_comun_id);

        // Crear notificación solo para el residente que hizo la reserva
        Notificacion::create([
            'titulo' => 'Reserva registrada',
            'contenido' => 'Has registrado una reserva para el área "' . $areaComun->nombre . '" el día ' . $request->fecha . ' de ' . $request->hora_inicio . ' a ' . $request->hora_fin . '.',
            'tipo' => 'Informativa',
            'fecha_hora' => now(),
            'residente_id' => $user->residente_id,
            'ruta'=> route('reservas.index'),
        ]);

        return redirect()->route('reservas.index')->with('success', 'Reserva creada correctamente.');
}

    public function show(Reserva $reserva)
    {
        //
    }

    public function edit(Reserva $reserva)
    {
        $areasComunes = AreaComun::where('estado', 'activo')->get();
        return view('reservas.edit', compact('reserva', 'areasComunes'));
    }

    public function update(Request $request, Reserva $reserva)
    {
        $request->validate([
            'area_comun_id' => 'required|exists:area_comuns,id',
            'fecha' => 'required|date|after_or_equal:today',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
            'observacion' => 'nullable|string|max:255',
            'monto_total' => 'nullable|numeric|min:0',
        ]);

        $horaInicio = Carbon::createFromFormat('H:i', $request->hora_inicio);
        $horaFin = Carbon::createFromFormat('H:i', $request->hora_fin);

        // Validar solapamiento pero excluyendo la reserva actual
        $conflict = Reserva::where('area_comun_id', $request->area_comun_id)
            ->where('fecha', $request->fecha)
            ->where('id', '!=', $reserva->id)
            ->where(function ($query) use ($horaInicio, $horaFin) {
                $query->where(function ($q) use ($horaInicio, $horaFin) {
                    $q->where('hora_inicio', '<', $horaFin->format('H:i:s'))
                      ->where('hora_fin', '>', $horaInicio->format('H:i:s'));
                });
            })->exists();

        if ($conflict) {
            return back()->withErrors(['La reserva seleccionada se solapa con otra ya existente.'])->withInput();
        }

        $areaComun = AreaComun::findOrFail($request->area_comun_id);
        $duracionHoras = $horaFin->diffInMinutes($horaInicio) / 60;
        $montoTotal = $duracionHoras * $areaComun->monto;

        $user = Auth::user();
        if (!$user) {
            return back()->withErrors(['user' => 'No hay usuario autenticado'])->withInput();
        }

        if (!$user->residente_id) {
            return back()->withErrors([
                'residente_id' => 'El usuario no tiene residente asignado. Usuario ID: ' . $user->id
            ])->withInput();
        }

        // Actualizar la reserva
        $reserva->update([
            'area_comun_id' => $request->area_comun_id,
            'fecha' => $request->fecha,
            'hora_inicio' => $request->hora_inicio,
            'hora_fin' => $request->hora_fin,
            'observacion' => $request->observacion,
            'monto_total' => $montoTotal,
        ]);

        // Determinar nombre del usuario afectado
        $nombreUsuario = $reserva->residente
            ? $reserva->residente->nombre_completo
            : ($reserva->empleado->nombre_completo ?? 'N/D');

        $this->registrarEnBitacora( "Usuario {$nombreUsuario} Actualizo la reserva ID: {$reserva->id}", $reserva->id);

        return redirect()->route('reservas.index')->with('success', 'Reserva actualizada correctamente.');
    }

    public function destroy(Reserva $reserva)
    {
        try {

            // Determinar nombre del usuario afectado
            $nombreUsuario = $reserva->residente
                ? $reserva->residente->nombre_completo
                : ($reserva->empleado->nombre_completo ?? 'N/D');

            $reserva->delete();

            $this->registrarEnBitacora( "Usuario {$nombreUsuario} Borro su reserva ID: {$reserva->id}", $reserva->id);

            return redirect()->route('reservas.index')
                            ->with('success', 'Reserva eliminada correctamente.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al eliminar la reserva: ' . $e->getMessage()]);
        }
    }

    public function horasLibres(Request $request)
    {
        $area_comun_id = $request->query('area_comun_id');
        $fecha = $request->query('fecha');

        if (!$area_comun_id || !$fecha) {
            return response()->json([], 400);
        }

        // Generar horas posibles en formato Carbon
        $horasPosibles = [];
        for ($h = 8; $h <= 20; $h++) {
            $horasPosibles[] = Carbon::createFromFormat('H:i', sprintf('%02d:00', $h));
            $horasPosibles[] = Carbon::createFromFormat('H:i', sprintf('%02d:30', $h));
        }

        // Obtener reservas para ese día y área
        $reservas = Reserva::where('area_comun_id', $area_comun_id)
                    ->where('fecha', $fecha)
                    ->get();

        foreach ($reservas as $reserva) {
            $horaInicio = Carbon::createFromFormat('H:i:s', $reserva->hora_inicio);
            $horaFin = Carbon::createFromFormat('H:i:s', $reserva->hora_fin);

            // Filtrar horas que no están dentro del rango reservado
            $horasPosibles = array_filter($horasPosibles, function ($hora) use ($horaInicio, $horaFin) {
                return !($hora >= $horaInicio && $hora < $horaFin);
            });
        }

        // Convertir objetos Carbon de vuelta a strings 'H:i'
        $horasPosiblesStrings = array_map(fn($hora) => $hora->format('H:i'), $horasPosibles);

        // Reindexar array
        $horasPosiblesStrings = array_values($horasPosiblesStrings);

        return response()->json($horasPosiblesStrings);
    }



public function verificarInventario($reserva_id)
{
    $reserva = Reserva::with('areaComun.inventarios')->findOrFail($reserva_id);
    return view('reservas.verificar_inventario', compact('reserva'));
}

public function guardarVerificacion(Request $request, $reserva_id)
{
    $request->validate([
        'verificaciones' => 'required|array',
        'verificaciones.*.estado' => 'required|in:ok,faltante,roto,otro',
        'verificaciones.*.observacion' => 'nullable|string|max:255',
    ]);

    foreach ($request->verificaciones as $inventario_id => $verificacion) {
        VerificacionInventario::updateOrCreate(
            [
                'reserva_id' => $reserva_id,
                'inventario_id' => $inventario_id,
            ],
            [
                'estado' => $verificacion['estado'],
                'observacion' => $verificacion['observacion'] ?? null,
            ]
        );
    }

    return redirect()->route('reservas.index')->with('success', 'Verificación guardada correctamente.');
}

}
