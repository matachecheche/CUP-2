<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\BitacoraTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Exception;

class UsuarioController extends Controller
{
    use BitacoraTrait;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:ver usuarios')->only('index');
        $this->middleware('permission:crear usuarios')->only(['create', 'store']);
        $this->middleware('permission:editar usuarios')->only(['edit', 'update']);
        $this->middleware('permission:eliminar usuarios')->only('destroy');
    }

    public function index()
    {
        $users = User::with('roles')->get();
        return view('users.index', compact('users'));
    }

    public function miPerfil()
    {
        $user = auth()->user();
        return view('users.perfil', compact('user'));
    }

    public function create()
    {
        $roles      = Role::all();
        $docentes   = class_exists(\App\Models\Docente::class)   ? \App\Models\Docente::all()   : collect();
        $postulantes = class_exists(\App\Models\Postulante::class) ? \App\Models\Postulante::all() : collect();
        return view('users.create', compact('roles', 'docentes', 'postulantes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:100|regex:/^[\pL\s\.\-]+$/u',
            'email'    => 'required|email|max:100|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role'     => 'required|exists:roles,name',
        ], [
            'name.regex'       => 'El nombre solo puede contener letras, espacios, punto y guion.',
            'email.unique'     => 'Ya existe un usuario con ese email.',
            'password.min'     => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed'=> 'La confirmación de la contraseña no coincide.',
        ]);

        // Validar que no se vinculen ambos tipos a la vez
        if ($request->filled('docente_id') && $request->filled('postulante_id')) {
            return back()
                ->withErrors(['vínculo' => 'Un usuario solo puede vincularse a un Docente O a un Postulante, no ambos.'])
                ->withInput();
        }

        // Coherencia rol↔vínculo
        $rol = $request->role;
        if ($rol === 'Docente' && !$request->filled('docente_id')) {
            return back()->withErrors(['docente_id'=>'El rol Docente requiere vincular un docente.'])->withInput();
        }
        if ($rol === 'Postulante' && !$request->filled('postulante_id')) {
            return back()->withErrors(['postulante_id'=>'El rol Postulante requiere vincular un postulante.'])->withInput();
        }

        try {
            DB::beginTransaction();
            $user = User::create([
                'name'              => $request->name,
                'email'             => $request->email,
                'password'          => Hash::make($request->password),
                'docente_id'        => $request->docente_id   ?: null,
                'postulante_id'     => $request->postulante_id ?: null,
                'email_verified_at' => now(),
                'activo'            => true,
            ]);
            $user->assignRole($request->role);
            $this->registrarEnBitacora('Usuario creado: ' . $user->name, $user->id);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            $this->registrarEnBitacora('Error al crear usuario: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Error al crear el usuario: ' . $e->getMessage()])->withInput();
        }

        return redirect()->route('users.index')->with('success', 'Usuario creado correctamente.');
    }

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $roles       = Role::all();
        $docentes    = class_exists(\App\Models\Docente::class)   ? \App\Models\Docente::all()   : collect();
        $postulantes = class_exists(\App\Models\Postulante::class) ? \App\Models\Postulante::all() : collect();
        return view('users.edit', compact('user', 'roles', 'docentes', 'postulantes'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'  => 'required|string|max:100|regex:/^[\pL\s\.\-]+$/u',
            'email' => 'required|email|max:100|unique:users,email,' . $user->id,
            'role'  => 'required|exists:roles,name',
        ], [
            'name.regex'  => 'El nombre solo puede contener letras, espacios, punto y guion.',
            'email.unique'=> 'Ya existe un usuario con ese email.',
        ]);

        if ($request->filled('docente_id') && $request->filled('postulante_id')) {
            return back()
                ->withErrors(['vínculo' => 'Un usuario solo puede vincularse a un Docente O a un Postulante, no ambos.'])
                ->withInput();
        }

        $rol = $request->role;
        if ($rol === 'Docente' && !$request->filled('docente_id')) {
            return back()->withErrors(['docente_id'=>'El rol Docente requiere vincular un docente.'])->withInput();
        }
        if ($rol === 'Postulante' && !$request->filled('postulante_id')) {
            return back()->withErrors(['postulante_id'=>'El rol Postulante requiere vincular un postulante.'])->withInput();
        }

        try {
            DB::beginTransaction();
            $user->update([
                'name'          => $request->name,
                'email'         => $request->email,
                'docente_id'    => $request->docente_id   ?: null,
                'postulante_id' => $request->postulante_id ?: null,
            ]);
            if ($request->filled('password')) {
                $request->validate(['password' => 'string|min:8|confirmed']);
                $user->update(['password' => Hash::make($request->password)]);
            }
            $user->syncRoles([$request->role]);
            $this->registrarEnBitacora('Usuario actualizado: ' . $user->name, $user->id);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al actualizar: ' . $e->getMessage()]);
        }

        return redirect()->route('users.index')->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(string $id)
    {
        $user   = User::findOrFail($id);
        $user->update(['activo' => !$user->activo]);
        $estado = $user->activo ? 'activado' : 'desactivado';
        $this->registrarEnBitacora("Usuario {$estado}: {$user->name}", $user->id);
        return redirect()->route('users.index')->with('success', "Usuario {$estado} correctamente.");
    }
}
