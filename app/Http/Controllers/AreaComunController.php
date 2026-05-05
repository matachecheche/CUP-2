<?php

namespace App\Http\Controllers;

use App\Models\AreaComun;
use Illuminate\Http\Request;
use App\Traits\BitacoraTrait;

class AreaComunController extends Controller
{
    use BitacoraTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $areasComunes = AreaComun::with('reserva.residente')->get();
        return view('areas_comunes.index', compact('areasComunes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('areas_comunes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:50',
            'monto'  => 'required|numeric|min:0',
        ]);

        $areaComun=AreaComun::create($request->only('nombre','monto'));

        $this->registrarEnBitacora('Usuario Registro un Area Comun', $areaComun->id);

        return redirect()->route('areas-comunes.index')
                         ->with('success','Área creada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(AreaComun $areaComun)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AreaComun $areaComun)
    {
        return view('areas_comunes.edit', compact('areaComun'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AreaComun $areaComun)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:50',
            'monto'  => 'required|numeric|min:0',
            'estado' => 'required|in:activo,inactivo,mantenimiento',
        ]);

        $this->registrarEnBitacora('Usuario Actualizo un Area Comun', $areaComun->id);

        $areaComun->update($validated);
        return redirect()->route('areas-comunes.index')
                        ->with('success', 'Área Común actualizada correctamente.');
        }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AreaComun $areaComun)
    {
        $areaComun->delete();

        $this->registrarEnBitacora('Usuario Elimino un Area Comun', $areaComun->id);

        return redirect()->route('areas-comunes.index')
                         ->with('success', 'Área Comun eliminada correctamente.');
    }
}
