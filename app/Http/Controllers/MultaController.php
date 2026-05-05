<?php

namespace App\Http\Controllers;

use App\Models\Multa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Residente;
use App\Models\Empleado;
use App\Traits\BitacoraTrait;

class MultaController extends Controller
{
    use BitacoraTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->residente_id) {
            // Residente ve sólo sus multas
            $multas = Multa::where('residente_id', $user->residente_id)
                            ->orderBy('fechaEmision', 'Desc')->get();
        } elseif ($user->empleado_id) {
            // Empleado ve sólo las multas que él registró
            $multas = Multa::where('empleado_id', $user->empleado_id)
                            ->orderBy('fechaEmision', 'Desc')->get();
        } else {
            // Administrador ve todas
            $multas = Multa::orderBy('id', 'Desc')->get();
        }

        return view('multas.index', compact('multas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $residentes = Residente::all();
        $empleados  = Empleado::all();
        return view('multas.create', compact('residentes','empleados'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'motivo'        => 'required|string|max:255',
            'monto'         => 'required|numeric|min:0',
            'fechaEmision'  => 'required|date',
            'fechaLimite'   => 'required|date|after_or_equal:fechaEmision',
            'residente_id'  => 'nullable|exists:residentes,id',
            'empleado_id'   => 'nullable|exists:empleados,id',
        ]);

        // Al menos uno debe estar presente
        if (empty($data['residente_id']) && empty($data['empleado_id'])) {
            return back()->withErrors(['residente_id' => 'Debes elegir un Residente o un Empleado'])->withInput();
        }

        $multa = Multa::create($data);

            // Determinar nombre del usuario al que se aplicó la multa
            $nombreUsuario = $multa->residente
                ? $multa->residente->nombre_completo
                : ($multa->empleado->nombre_completo ?? 'N/D');

            // Registrar en bitácora incluyendo ese nombre
            $this->registrarEnBitacora("Usuario registró una multa a {$nombreUsuario}", $multa->id);

        return redirect()->route('multas.index')->with('success', 'Multa creada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Multa $multa)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Multa $multa)
    {
        return view('multas.edit', compact('multa'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Multa $multa)
    {
        $data = $request->validate([
            'motivo'       => 'required|string|max:255',
            'monto'        => 'required|numeric|min:0',
            'fechaEmision' => 'required|date',
            'fechaLimite'  => 'required|date|after_or_equal:fechaEmision',
            'estado'       => 'required|in:pendiente,pagada,apelada,anulada',
        ]);

        // Guardar los valores originales para bitácora
        $oldEstado = $multa->estado;

        // Actualizar la multa
        $multa->update([
            'motivo'       => $data['motivo'],
            'monto'        => $data['monto'],
            'fechaEmision' => $data['fechaEmision'],
            'fechaLimite'  => $data['fechaLimite'],
            'estado'       => $data['estado'],
        ]);

        // Determinar usuario afectado
        $nombreUsuario = optional($multa->residente)->nombre_completo
                    ?? optional($multa->empleado)->nombre_completo
                    ?? 'N/D';

        // Registrar en bitácora
        $this->registrarEnBitacora("Usuario actualizó multa a {$nombreUsuario}: estado de {$oldEstado} a {$multa->estado}",$multa->id);

        return redirect()
            ->route('multas.index')
            ->with('success', 'Multa actualizada correctamente.');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Multa $multa)
    {
        try {
            // Determinar nombre del usuario afectado
            $nombreUsuario = $multa->residente
                ? $multa->residente->nombre_completo
                : ($multa->empleado->nombre_completo ?? 'N/D');

            $multa->delete();

            // Registrar en bitácora
            $this->registrarEnBitacora( "Usuario anuló la multa a {$nombreUsuario}", $multa->id);

            return redirect()->route('multas.index')
                            ->with('success', 'Multa anulada correctamente.');
        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Error al anular la multa: ' . $e->getMessage()
            ]);
        }
    }
}
