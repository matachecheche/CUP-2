<?php

namespace App\Http\Controllers;

use App\Models\EmpresaExterna;
use Illuminate\Http\Request;

class EmpresaExternaController extends Controller
{
    public function index()
    {
        $empresas = EmpresaExterna::latest()->paginate(10);
        return view('empresas.index', compact('empresas'));
    }

    public function create()
    {
        return view('empresas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'servicio' => 'required|string|max:255',
            'telefono' => 'nullable|string|max:50',
            'correo' => 'nullable|email|max:100',
            'direccion' => 'nullable|string|max:255',
            'observacion' => 'nullable|string',
        ]);

        EmpresaExterna::create($request->all());

        return redirect()->route('empresas.index')->with('success', 'Empresa registrada correctamente.');
    }

    public function show(EmpresaExterna $empresa)
    {
        return view('empresas.show', compact('empresa'));
    }

    public function edit(EmpresaExterna $empresa)
    {
        return view('empresas.edit', compact('empresa'));
    }

    public function update(Request $request, EmpresaExterna $empresa)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'servicio' => 'required|string|max:255',
            'telefono' => 'nullable|string|max:50',
            'correo' => 'nullable|email|max:100',
            'direccion' => 'nullable|string|max:255',
            'observacion' => 'nullable|string',
        ]);

        $empresa->update($request->all());

        return redirect()->route('empresas.index')->with('success', 'Empresa actualizada correctamente.');
    }

    public function destroy(EmpresaExterna $empresa)
    {
        $empresa->delete();
        return redirect()->route('empresas.index')->with('success', 'Empresa eliminada correctamente.');
    }
}
