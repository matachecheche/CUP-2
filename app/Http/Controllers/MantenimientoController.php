<?php

namespace App\Http\Controllers;

use App\Models\Mantenimiento;
use App\Models\User;
use App\Models\EmpresaExterna;
use Illuminate\Http\Request;
use App\Traits\BitacoraTrait;
use Exception;

class MantenimientoController extends Controller
{
    use BitacoraTrait;

    public function index(Request $request)
    {
        $query = Mantenimiento::with(['usuario', 'empresa']);

        if ($request->filled('search')) {
            $search = $request->search;
            $filter = $request->filter;

            $query->where(function ($q) use ($search, $filter) {
                if ($filter === 'usuario') {
                    $q->whereHas('usuario', fn($q) => $q->where('name', 'like', "%$search%"));
                } elseif ($filter === 'empresa') {
                    $q->whereHas('empresa', fn($q) => $q->where('nombre', 'like', "%$search%"));
                } else {
                    $q->where('descripcion', 'like', "%$search%");
                }
            });
        }

        if ($request->filled('sort')) {
            $direction = $request->direction === 'asc' ? 'asc' : 'desc';
            $sort = $request->sort;

            if (in_array($sort, ['id', 'descripcion', 'monto', 'fecha_hora'])) {
                $query->orderBy($sort, $direction);
            } elseif ($sort === 'usuario') {
                $query->join('users as u', 'mantenimientos.usuario_id', '=', 'u.id')
                    ->orderBy('u.name', $direction)
                    ->select('mantenimientos.*');
            } elseif ($sort === 'empresa') {
                $query->join('empresas_externas as e', 'mantenimientos.empresaExterna_id', '=', 'e.id')
                    ->orderBy('e.nombre', $direction)
                    ->select('mantenimientos.*');
            }
        } else {
            $query->orderBy('id', 'desc'); // default orden por ID
        }

        $mantenimientos = $query->paginate(10);

        return view('mantenimientos.index', compact('mantenimientos'));
    }


    public function create()
    {
        $usuarios = User::all();
        $empresas = EmpresaExterna::all();
        return view('mantenimientos.create', compact('usuarios', 'empresas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'descripcion' => 'required|string',
            'estado' => 'required|integer',
            'fecha_hora' => 'required|date',
            'monto' => 'required|numeric',
            'usuario_id' => 'required|exists:users,id',
            'empresaExterna_id' => 'nullable|exists:empresa_externas,id',
        ]);

        try {
            $mantenimiento = Mantenimiento::create($request->all());
            $this->registrarEnBitacora('Mantenimiento creado', $mantenimiento->id);
            return redirect()->route('mantenimientos.index')->with('success', 'Mantenimiento creado correctamente.');
        } catch (Exception $e) {
            $this->registrarEnBitacora('Error al crear mantenimiento: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Ocurrió un error al guardar: ' . $e->getMessage()])->withInput();
            //return back()->withErrors(['error' => 'Ocurrió un error al guardar.'])->withInput();
        }
    }

    public function edit(Mantenimiento $mantenimiento)
    {
        $usuarios = User::all();
        $empresas = EmpresaExterna::all();
        return view('mantenimientos.edit', compact('mantenimiento', 'usuarios', 'empresas'));
    }

    public function update(Request $request, Mantenimiento $mantenimiento)
    {
        $request->validate([
            'descripcion' => 'required|string',
            'estado' => 'required|integer',
            'fecha_hora' => 'required|date',
            'monto' => 'required|numeric',
            'usuario_id' => 'required|exists:users,id',
        ]);

        try {
            $mantenimiento->update($request->all());
            $this->registrarEnBitacora('Mantenimiento actualizado', $mantenimiento->id);
            return redirect()->route('mantenimientos.index')->with('success', 'Mantenimiento actualizado.');
        } catch (Exception $e) {
            $this->registrarEnBitacora('Error al actualizar mantenimiento: ' . $e->getMessage());
            return back()->withErrors(['error' => 'No se pudo actualizar el mantenimiento.'])->withInput();
        }
    }
    /*
    public function destroy(string $id)
    {
        $mantenimiento = Mantenimiento::find($id);
        if (!$mantenimiento) {
            return redirect()->route('mantenimientos.index')->withErrors(['error' => 'Mantenimiento no encontrado.']);
        }

        try {
            // Baja lógica: cambiar estado a 0
            if ($mantenimiento->estado == 1) {
                $mantenimiento->estado = 0;
                $mantenimiento->save();
                $this->registrarEnBitacora('Mantenimiento desactivado', $mantenimiento->id);
                return redirect()->route('mantenimientos.index')->with('success', 'Mantenimiento desactivado.');
            }

            return redirect()->route('mantenimientos.index')->with('info', 'Este mantenimiento ya estaba desactivado.');
        } catch (Exception $e) {
            $this->registrarEnBitacora('Error al eliminar mantenimiento: ' . $e->getMessage());
            return redirect()->route('mantenimientos.index')->withErrors(['error' => 'No se pudo eliminar el mantenimiento.']);
        }
    }
    */
    public function destroy($id)
    {
        try {
            $mantenimiento = Mantenimiento::findOrFail($id);
            $mantenimiento->delete();

            $this->registrarEnBitacora("Mantenimiento eliminado", $mantenimiento->id);

            return redirect()->route('mantenimientos.index')->with('success', 'Mantenimiento eliminado correctamente.');
        } catch (\Exception $e) {
            $this->registrarEnBitacora("Error al eliminar mantenimiento: " . $e->getMessage());

            return redirect()->route('mantenimientos.index')->with('error', 'Ocurrió un error al eliminar el mantenimiento.');
        }
    }
}
