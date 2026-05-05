<?php

namespace App\Http\Controllers;

use App\Models\Comunicado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notificacion;
use App\Traits\BitacoraTrait;
use Carbon\Carbon;

class ComunicadoController extends Controller
{
    use BitacoraTrait;

    public function index()
    {
        $comunicados = Comunicado::with('usuario')
            ->where('fecha_publicacion', '<=', now())
            ->latest()
            ->paginate(10);

        return view('comunicados.index', compact('comunicados'));
    }

    public function create()
    {
        return view('comunicados.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'contenido' => 'required|string',
            'tipo' => 'required|in:Urgente,Informativo',
            'fecha_publicacion' => 'nullable|date_format:Y-m-d\TH:i',
        ]);

        $fechaPublicacion = $request->filled('fecha_publicacion')
            ? Carbon::parse($request->fecha_publicacion)
            : now();

        $comunicado = Comunicado::create([
            'titulo' => $request->titulo,
            'contenido' => $request->contenido,
            'tipo' => $request->tipo,
            'fecha_publicacion' => $fechaPublicacion,
            'usuario_id' => Auth::id()
        ]);

        Notificacion::create([
            'titulo' => 'Nuevo Comunicado',
            'contenido' => 'Se ha publicado un nuevo comunicado para todos los residentes.',
            'tipo' => 'Informativa',
            'fecha_hora' => now(),
            'residente_id' => null,
            'ruta' => route('comunicados.index'),
        ]);

        $this->registrarEnBitacora('Creó un nuevo comunicado.', auth()->id());

        return redirect()->route('comunicados.index')->with('success', 'Comunicado programado exitosamente.');
    }

    public function show(Comunicado $comunicado)
    {
        return view('comunicados.show', compact('comunicado'));
    }

    public function edit(Comunicado $comunicado)
    {
        return view('comunicados.edit', compact('comunicado'));
    }

    public function update(Request $request, Comunicado $comunicado)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'contenido' => 'required|string',
            'tipo' => 'required|in:Urgente,Informativo',
            'fecha_publicacion' => 'nullable|date_format:Y-m-d\TH:i',
        ]);

        $fechaPublicacion = $request->filled('fecha_publicacion')
            ? Carbon::parse($request->fecha_publicacion)
            : now();

        $comunicado->update([
            'titulo' => $request->titulo,
            'contenido' => $request->contenido,
            'tipo' => $request->tipo,
            'fecha_publicacion' => $fechaPublicacion,
        ]);

        $this->registrarEnBitacora('Actualizó un comunicado.', auth()->id());

        return redirect()->route('comunicados.index')->with('success', 'Comunicado actualizado exitosamente.');
    }

    public function destroy(Comunicado $comunicado)
    {
        $comunicado->delete();

        $this->registrarEnBitacora('Eliminó un comunicado.', auth()->id());

        return redirect()->route('comunicados.index')->with('success', 'Comunicado eliminado exitosamente.');
    }
}
