<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use App\Traits\BitacoraTrait;

class RoleController extends Controller
{
    use BitacoraTrait;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $roles = Role::all();
        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        $permisos = Permission::all();
        return view('roles.create', compact('permisos'));
    }

    public function store(Request $request)
    {
        // Validamos asegurando que permissions sea un array
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions' => 'required|array|min:1'
        ]);

        DB::beginTransaction();
        try {
            // 1. Crear el rol
            $role = Role::create([
                'name' => $request->name, 
                'guard_name' => 'web'
            ]);

            // 2. Convertir los IDs a números enteros para que Spatie no se confunda
            $permissionIds = array_map('intval', $request->permissions);
            
            // 3. Sincronizar permisos por ID
            $role->syncPermissions($permissionIds);

            // 4. Registrar en bitácora
            if (method_exists($this, 'registrarEnBitacora')) {
                $this->registrarEnBitacora('Rol creado: ' . $role->name, $role->id);
            }

            DB::commit();
            
            // Limpiamos la caché de Spatie por código para estar seguros
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

            return redirect()->route('roles.index')->with('success', 'Rol creado correctamente');

        } catch (\Exception $e) {
            DB::rollback();
            // Esto nos dirá exactamente qué falló en un mensaje de error
            return redirect()->back()->withInput()->with('error', 'Fallo al guardar: ' . $e->getMessage());
        }
    }

    public function edit(Role $role)
    {
        $permisos = Permission::all();
        // Obtenemos los IDs de los permisos que ya tiene el rol
        $permisosRol = $role->permissions->pluck('id')->toArray();
        return view('roles.edit', compact('role', 'permisos', 'permisosRol'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|unique:roles,name,' . $role->id,
            'permissions' => 'required|array|min:1'
        ]);

        DB::beginTransaction();
        try {
            $role->update(['name' => $request->name]);
            
            $permissionIds = array_map('intval', $request->permissions);
            $role->syncPermissions($permissionIds);

            if (method_exists($this, 'registrarEnBitacora')) {
                $this->registrarEnBitacora('Rol actualizado: ' . $role->name, $role->id);
            }

            DB::commit();
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

            return redirect()->route('roles.index')->with('success', 'Rol actualizado correctamente');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withInput()->with('error', 'Error al actualizar: ' . $e->getMessage());
        }
    }

    public function destroy(Role $role)
    {
        try {
            $roleId = $role->id;
            $roleName = $role->name;
            $role->delete();
            
            if (method_exists($this, 'registrarEnBitacora')) {
                $this->registrarEnBitacora('Rol eliminado: ' . $roleName, $roleId);
            }

            return redirect()->route('roles.index')->with('success', 'Rol eliminado correctamente');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'No se pudo eliminar el rol.');
        }
    }
}