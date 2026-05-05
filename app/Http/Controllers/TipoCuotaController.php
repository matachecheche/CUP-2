<?php

namespace App\Http\Controllers;

use App\Models\TipoCuota;
use Illuminate\Http\Request;

class TipoCuotaController extends Controller
{
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
            'nombre' => 'required|string',
            'frecuencia' => 'required|in:mensual,anual,puntual',
        ]);

        TipoCuota::create($request->all());

        return redirect()->route('tipos-cuotas.index')->with('success', 'Tipo de cuota creado.');
    }

    public function edit(TipoCuota $tipos_cuota)
{
    return view('cuotas.tipos_cuotas.edit', ['tipo' => $tipos_cuota]);
}


    public function update(Request $request, TipoCuota $tipos_cuota)
    {
        $tipos_cuota->update($request->all());

        return redirect()->route('tipos-cuotas.index')->with('success', 'Actualizado correctamente.');
    }

    public function destroy(TipoCuota $tipos_cuota)
    {
        $tipos_cuota->delete();
        return redirect()->route('tipos-cuotas.index')->with('success', 'Eliminado correctamente.');
    }
}
