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

        // Lista de prioridades
        $prioridades = Mantenimiento::prioridades();

        // FILTROS Y BÚSQUEDA
        if ($request->filled('search')) {

            $search = $request->search;
            $filter = $request->filter;

            $query->where(function ($q) use ($search, $filter) {

                // Buscar por usuario
                if ($filter === 'usuario') {

                    $q->whereHas('usuario', function ($q) use ($search) {
                        $q->where('name', 'like', "%$search%");
                    });

                // Buscar por empresa
                } elseif ($filter === 'empresa') {

                    $q->whereHas('empresa', function ($q) use ($search) {
                        $q->where('nombre', 'like', "%$search%");
                    });

                // Buscar por prioridad
                } elseif ($filter === 'prioridad') {

                    $q->where('prioridad', 'like', "%$search%");

                // Buscar por descripción
                } else {

                    $q->where('descripcion', 'like', "%$search%");
                }
            });
        }

        // ORDENAMIENTO
        if ($request->filled('sort')) {

            $direction = $request->direction === 'asc'
                ? 'asc'
                : 'desc';

            $sort = $request->sort;

            // Ordenamiento directo por columnas
            if (in_array($sort, [
                'id',
                'descripcion',
                'monto',
                'fecha_hora',
                'prioridad'
            ])) {

                $query->orderBy($sort, $direction);

            // Ordenar por usuario
            } elseif ($sort === 'usuario') {

                $query->join(
                    'users as u',
                    'mantenimientos.usuario_id',
                    '=',
                    'u.id'
                )
                ->orderBy('u.name', $direction)
                ->select('mantenimientos.*');

            // Ordenar por empresa
            } elseif ($sort === 'empresa') {

                $query->join(
                    'empresas_externas as e',
                    'mantenimientos.empresaExterna_id',
                    '=',
                    'e.id'
                )
                ->orderBy('e.nombre', $direction)
                ->select('mantenimientos.*');
            }

        } else {

            // Orden por defecto
            $query->orderBy('id', 'desc');
        }

        // PAGINACIÓN
        $mantenimientos = $query->paginate(10);

        return view(
            'mantenimientos.index',
            compact(
                'mantenimientos',
                'prioridades'
            )
        );
    }

    public function create()
    {
        $usuarios = User::all();

        $empresas = EmpresaExterna::all();

        // Lista de prioridades
        $prioridades = Mantenimiento::prioridades();

        return view(
            'mantenimientos.create',
            compact(
                'usuarios',
                'empresas',
                'prioridades'
            )
        );
    }

    public function store(Request $request)
    {
        $request->validate([

            'descripcion'       => 'required|string',

            'estado'            => 'required|integer',

            'fecha_hora'        => 'required|date',

            'monto'             => 'required|numeric',

            'prioridad'         => 'required|string',

            'usuario_id'        => 'required|exists:users,id',

            'empresaExterna_id' => 'nullable|exists:empresa_externas,id',
        ]);

        try {

            // Crear mantenimiento
            $mantenimiento = Mantenimiento::create($request->all());

            // Registrar en bitácora
            $this->registrarEnBitacora(
                'Mantenimiento creado',
                $mantenimiento->id
            );

            return redirect()
                ->route('mantenimientos.index')
                ->with(
                    'success',
                    'Mantenimiento creado correctamente.'
                );

        } catch (Exception $e) {

            $this->registrarEnBitacora(
                'Error al crear mantenimiento: ' . $e->getMessage()
            );

            return back()
                ->withErrors([
                    'error' =>
                        'Ocurrió un error al guardar: ' .
                        $e->getMessage()
                ])
                ->withInput();
        }
    }

    public function edit(Mantenimiento $mantenimiento)
    {
        $usuarios = User::all();

        $empresas = EmpresaExterna::all();

        // Lista de prioridades
        $prioridades = Mantenimiento::prioridades();

        return view(
            'mantenimientos.edit',
            compact(
                'mantenimiento',
                'usuarios',
                'empresas',
                'prioridades'
            )
        );
    }

    public function update(
        Request $request,
        Mantenimiento $mantenimiento
    ) {
        $request->validate([

            'descripcion'       => 'required|string',

            'estado'            => 'required|integer',

            'fecha_hora'        => 'required|date',

            'monto'             => 'required|numeric',

            'prioridad'         => 'required|string',

            'usuario_id'        => 'required|exists:users,id',

            'empresaExterna_id' => 'nullable|exists:empresa_externas,id',
        ]);

        try {

            // Actualizar mantenimiento
            $mantenimiento->update($request->all());

            // Registrar en bitácora
            $this->registrarEnBitacora(
                'Mantenimiento actualizado',
                $mantenimiento->id
            );

            return redirect()
                ->route('mantenimientos.index')
                ->with(
                    'success',
                    'Mantenimiento actualizado.'
                );

        } catch (Exception $e) {

            $this->registrarEnBitacora(
                'Error al actualizar mantenimiento: ' .
                $e->getMessage()
            );

            return back()
                ->withErrors([
                    'error' =>
                        'No se pudo actualizar el mantenimiento.'
                ])
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {

            // Buscar mantenimiento
            $mantenimiento = Mantenimiento::findOrFail($id);

            // Eliminar mantenimiento
            $mantenimiento->delete();

            // Registrar en bitácora
            $this->registrarEnBitacora(
                'Mantenimiento eliminado',
                $mantenimiento->id
            );

            return redirect()
                ->route('mantenimientos.index')
                ->with(
                    'success',
                    'Mantenimiento eliminado correctamente.'
                );

        } catch (Exception $e) {

            $this->registrarEnBitacora(
                'Error al eliminar mantenimiento: ' .
                $e->getMessage()
            );

            return redirect()
                ->route('mantenimientos.index')
                ->with(
                    'error',
                    'Ocurrió un error al eliminar el mantenimiento.'
                );
        }
    }
}