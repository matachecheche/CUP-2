<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Docente;
use App\Models\Postulante;
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
        $docentes   = class_exists(\App\Models\Docente::class)   ? Docente::all()   : collect();
        $postulantes = class_exists(\App\Models\Postulante::class) ? Postulante::all() : collect();
        return view('users.create', compact('roles', 'docentes', 'postulantes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role'     => 'required|exists:roles,name',
        ]);

        try {
            DB::beginTransaction();

            // Solo uno de los dos vínculos puede estar presente
            if ($request->filled('docente_id') && $request->filled('postulante_id')) {
                return back()->withErrors(['Solo puedes vincular a un docente O a un postulante, no ambos.'])->withInput();
            }

            $user = User::create([
                'name'               => $request->name,
                'email'              => $request->email,
                'password'           => Hash::make($request->password),
                'docente_id'         => $request->docente_id   ?: null,
                'postulante_id'      => $request->postulante_id ?: null,
                'email_verified_at'  => now(),
                'activo'             => true,
            ]);

            $user->assignRole($request->role);
            $this->registrarEnBitacora('Usuario creado: ' . $user->name, $user->id);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            $this->registrarEnBitacora('Error al crear usuario: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Error al crear el usuario.'])->withInput();
        }

        return redirect()->route('users.index')->with('success', 'Usuario creado con éxito.');
    }

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $roles       = Role::all();
        $docentes    = class_exists(\App\Models\Docente::class)   ? Docente::all()   : collect();
        $postulantes = class_exists(\App\Models\Postulante::class) ? Postulante::all() : collect();
        return view('users.edit', compact('user', 'roles', 'docentes', 'postulantes'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'  => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role'  => 'required|exists:roles,name',
        ]);

        try {
            DB::beginTransaction();

            if ($request->filled('docente_id') && $request->filled('postulante_id')) {
                return back()->withErrors(['Solo puedes vincular a un docente O a un postulante, no ambos.'])->withInput();
            }

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
            $this->registrarEnBitacora('Error al actualizar usuario: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Error al actualizar el usuario.'])->withInput();
        }

        return redirect()->route('users.index')->with('success', 'Usuario actualizado con éxito.');
    }

    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->update(['activo' => !$user->activo]);
        $estado = $user->activo ? 'activado' : 'desactivado';
        $this->registrarEnBitacora("Usuario {$estado}: {$user->name}", $user->id);
        return redirect()->route('users.index')->with('success', "Usuario {$estado} correctamente.");
    }
}
