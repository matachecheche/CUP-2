<?php

namespace App\Http\Controllers;

use App\Traits\BitacoraTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    use BitacoraTrait;

    // Roles del sistema que NO pueden eliminarse
    const ROLES_PROTEGIDOS = ['Administrador del Sistema', 'Docente', 'Postulante', 'Coordinador del CUP'];

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:ver roles')->only('index');
        $this->middleware('permission:crear roles')->only(['create', 'store']);
        $this->middleware('permission:editar roles')->only(['edit', 'update']);
        $this->middleware('permission:eliminar roles')->only('destroy');
    }

    public function index()
    {
        $roles = Role::with('permissions')->get();
        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        $permisos = Permission::all()->groupBy(function ($p) {
            // Agrupa por la última palabra del nombre del permiso (módulo)
            $parts = explode(' ', $p->name);
            return ucfirst(end($parts));
        });
        return view('roles.create', compact('permisos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|unique:roles,name',
            'permission'   => 'required|array|min:1',
            'permission.*' => 'exists:permissions,id',
        ]);

        DB::beginTransaction();
        try {
            $role = Role::create(['name' => $request->name, 'guard_name' => 'web']);
            $permissions = Permission::whereIn('id', $request->permission)->pluck('name');
            $role->syncPermissions($permissions);
            $this->registrarEnBitacora('Rol creado: ' . $role->name, $role->id);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al crear el rol: ' . $e->getMessage()])->withInput();
        }

        return redirect()->route('roles.index')->with('success', 'Rol creado correctamente.');
    }

    public function edit(Role $role)
    {
        $permisos = Permission::all()->groupBy(function ($p) {
            $parts = explode(' ', $p->name);
            return ucfirst(end($parts));
        });
        $permisosRol = $role->permissions->pluck('name')->toArray();
        return view('roles.edit', compact('role', 'permisos', 'permisosRol'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name'         => 'required|string|unique:roles,name,' . $role->id,
            'permission'   => 'required|array|min:1',
            'permission.*' => 'exists:permissions,id',
        ]);

        DB::beginTransaction();
        try {
            $role->update(['name' => $request->name]);
            $permissions = Permission::whereIn('id', $request->permission)->pluck('name');
            $role->syncPermissions($permissions);
            $this->registrarEnBitacora('Rol actualizado: ' . $role->name, $role->id);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al actualizar el rol: ' . $e->getMessage()]);
        }

        return redirect()->route('roles.index')->with('success', 'Rol actualizado correctamente.');
    }

    public function destroy(Role $role)
    {
        if (in_array($role->name, self::ROLES_PROTEGIDOS)) {
            return back()->withErrors(['No se puede eliminar el rol "' . $role->name . '" porque es un rol base del sistema.']);
        }
        $this->registrarEnBitacora('Rol eliminado: ' . $role->name, $role->id);
        $role->delete();
        return redirect()->route('roles.index')->with('success', 'Rol eliminado correctamente.');
    }
}
