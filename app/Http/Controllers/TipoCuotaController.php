<?php

namespace App\Http\Controllers;

use App\Models\TipoCuota;
use App\Traits\BitacoraTrait;
use Illuminate\Http\Request;

class TipoCuotaController extends Controller
{
    use BitacoraTrait;

    public function index()
    {
        $tipos = TipoCuota::all();
        return view('cuotas.tipos_cuotas.index', compact('tipos'));
    }

    public function create()
    {
        return view('cuotas.tipos_cuotas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'     => 'required|string',
            'frecuencia' => 'required|in:mensual,anual,puntual',
        ]);

        $tipo = TipoCuota::create($request->all());
        $this->registrarEnBitacora('Creó tipo de cuota: ' . $tipo->nombre, $tipo->id);

        return redirect()->route('tipos-cuotas.index')->with('success', 'Tipo de cuota creado.');
    }

    public function edit(TipoCuota $tipos_cuota)
    {
        return view('cuotas.tipos_cuotas.edit', ['tipo' => $tipos_cuota]);
    }

    public function update(Request $request, TipoCuota $tipos_cuota)
    {
        $tipos_cuota->update($request->all());
        $this->registrarEnBitacora('Actualizó tipo de cuota: ' . $tipos_cuota->nombre, $tipos_cuota->id);

        return redirect()->route('tipos-cuotas.index')->with('success', 'Actualizado correctamente.');
    }

    public function destroy(TipoCuota $tipos_cuota)
    {
        $nombre = $tipos_cuota->nombre;
        $id     = $tipos_cuota->id;
        $tipos_cuota->delete();
        $this->registrarEnBitacora('Eliminó tipo de cuota: ' . $nombre, $id);

        return redirect()->route('tipos-cuotas.index')->with('success', 'Eliminado correctamente.');
    }
}