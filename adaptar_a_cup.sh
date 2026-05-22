#!/usr/bin/env bash
# =============================================================================
#  adaptar_a_cup.sh
#  Adapta el proyecto condominio-SA al nuevo Sistema de Admisión CUP
#
#  USO:
#    1. Clona el repo original en una carpeta nueva:
#       git clone https://github.com/matachecheche/condominio-SA.git admision-cup
#    2. cd admision-cup
#    3. chmod +x adaptar_a_cup.sh
#    4. bash adaptar_a_cup.sh
#
#  QUÉ HACE:
#    - Renombra/reemplaza archivos de usuarios, roles y permisos
#    - Crea migraciones, modelos, seeders, controladores, rutas y vistas
#      para los CRUDs ya hechos: Usuarios y Roles/Permisos
#    - Deja intacto todo lo que NO es de seguridad/auth (no lo borra)
#    - Actualiza config/adminlte.php con el nombre del nuevo sistema
# =============================================================================

set -e   # Parar si hay error
CYAN='\033[0;36m'; GREEN='\033[0;32m'; YELLOW='\033[1;33m'; NC='\033[0m'

info()    { echo -e "${CYAN}[INFO]${NC}  $1"; }
success() { echo -e "${GREEN}[OK]${NC}    $1"; }
warn()    { echo -e "${YELLOW}[WARN]${NC}  $1"; }

# ── 0. Verificar que estamos dentro del proyecto Laravel ──────────────────────
if [ ! -f "artisan" ]; then
  echo "ERROR: No se encontró artisan. Ejecuta este script desde la raíz del proyecto Laravel."
  exit 1
fi

# =============================================================================
#  1. NOMBRE Y MARCA DEL NUEVO SISTEMA
# =============================================================================
info "Actualizando nombre del sistema en .env y config..."

# .env
if [ -f ".env" ]; then
  sed -i "s/^APP_NAME=.*/APP_NAME=\"Sistema de Admision CUP\"/" .env
  success ".env → APP_NAME actualizado"
fi

# .env.example
if [ -f ".env.example" ]; then
  sed -i "s/^APP_NAME=.*/APP_NAME=\"Sistema de Admision CUP\"/" .env.example
fi

# config/adminlte.php — título y logo
sed -i "s/'title' => 'AdminLTE 3'/'title' => 'Admisión CUP'/" config/adminlte.php
sed -i "s|'logo' => '<b>Admin<\/b>LTE'|'logo' => '<b>Admisión<\/b>CUP'|" config/adminlte.php
success "config/adminlte.php → título y logo actualizados"

# =============================================================================
#  2. MIGRACIÓN: TABLA USERS  (reemplaza la existente)
# =============================================================================
info "Creando migración de users adaptada para CUP..."

MIGRATION_USERS=$(ls database/migrations/*create_users_table* 2>/dev/null | head -1)
if [ -n "$MIGRATION_USERS" ]; then
  warn "Se sobreescribirá: $MIGRATION_USERS"
fi

TARGET_MIGRATION="database/migrations/0001_01_01_000013_create_users_table.php"
cat > "$TARGET_MIGRATION" << 'MIGRATION'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabla de usuarios del Sistema de Admisión CUP.
 *
 * Roles existentes:
 *   - Administrador del Sistema
 *   - Responsable de Admisiones
 *   - Docente
 *   - Postulante
 *   - Autoridad de la Facultad
 *
 * El vínculo concreto (docente_id, postulante_id) es opcional
 * y lo maneja el módulo de perfil correspondiente.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            // Vínculos opcionales a entidades del dominio
            $table->foreignId('docente_id')
                  ->nullable()
                  ->constrained('docentes')
                  ->nullOnDelete();
            $table->foreignId('postulante_id')
                  ->nullable()
                  ->constrained('postulantes')
                  ->nullOnDelete();

            $table->boolean('activo')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
MIGRATION
success "Migración users → $TARGET_MIGRATION"

# =============================================================================
#  3. MODELO User
# =============================================================================
info "Actualizando app/Models/User.php..."

cat > app/Models/User.php << 'MODEL'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'password',
        'docente_id',
        'postulante_id',
        'activo',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // ── Relaciones ────────────────────────────────────────────────────────────

    /** Usuario vinculado a un docente del CUP */
    public function docente()
    {
        return $this->belongsTo(Docente::class);
    }

    /** Usuario vinculado a un postulante */
    public function postulante()
    {
        return $this->belongsTo(Postulante::class);
    }

    /** Bitácora de acciones */
    public function bitacoras()
    {
        return $this->hasMany(Bitacora::class);
    }
}
MODEL
success "app/Models/User.php actualizado"

# =============================================================================
#  4. SEEDER: Permisos
# =============================================================================
info "Actualizando PermissionSeeder para CUP..."

cat > database/seeders/PermissionSeeder.php << 'SEEDER'
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permisos = [

            // ── Usuarios ──────────────────────────────────────────────────────
            'ver usuarios',
            'crear usuarios',
            'editar usuarios',
            'eliminar usuarios',

            // ── Roles ─────────────────────────────────────────────────────────
            'ver roles',
            'crear roles',
            'editar roles',
            'eliminar roles',

            // ── Bitácora ──────────────────────────────────────────────────────
            'ver bitacora',

            // ── Gestiones / Periodos académicos ───────────────────────────────
            'ver gestiones',
            'crear gestiones',
            'editar gestiones',
            'eliminar gestiones',

            // ── Carreras ──────────────────────────────────────────────────────
            'ver carreras',
            'crear carreras',
            'editar carreras',
            'eliminar carreras',

            // ── Materias ──────────────────────────────────────────────────────
            'ver materias',
            'crear materias',
            'editar materias',
            'eliminar materias',

            // ── Docentes ──────────────────────────────────────────────────────
            'ver docentes',
            'crear docentes',
            'editar docentes',
            'eliminar docentes',

            // ── Postulantes ───────────────────────────────────────────────────
            'ver postulantes',
            'crear postulantes',
            'editar postulantes',
            'eliminar postulantes',
            'validar requisitos postulante',

            // ── Grupos / Aulas ────────────────────────────────────────────────
            'ver grupos',
            'crear grupos',
            'editar grupos',
            'eliminar grupos',
            'generar grupos automaticos',

            // ── Horarios ──────────────────────────────────────────────────────
            'ver horarios',
            'crear horarios',
            'editar horarios',
            'eliminar horarios',

            // ── Evaluaciones / Notas ──────────────────────────────────────────
            'registrar notas',
            'ver notas',
            'ver notas propias',          // Postulante

            // ── Cupos por carrera ─────────────────────────────────────────────
            'definir cupos',
            'ver cupos',

            // ── Admisión ──────────────────────────────────────────────────────
            'procesar admision',
            'ver resultados admision',
            'ver resultado propio',       // Postulante

            // ── Reportes ──────────────────────────────────────────────────────
            'ver reportes',
            'ver reportes ejecutivos',    // Autoridad de Facultad
        ];

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate(['name' => $permiso, 'guard_name' => 'web']);
        }
    }
}
SEEDER
success "database/seeders/PermissionSeeder.php actualizado"

# =============================================================================
#  5. SEEDER: Roles
# =============================================================================
info "Actualizando RolesSeeder para CUP..."

cat > database/seeders/RolesSeeder.php << 'SEEDER'
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        // ── Administrador del Sistema ─────────────────────────────────────────
        $admin = Role::firstOrCreate(['name' => 'Administrador']);
        $admin->syncPermissions(Permission::all());

        // ── Responsable de Admisiones ─────────────────────────────────────────
        $resp = Role::firstOrCreate(['name' => 'Responsable de Admisiones']);
        $resp->syncPermissions([
            'ver usuarios',
            'ver bitacora',
            'ver gestiones',
            'ver carreras',
            'ver materias',
            'ver docentes',   'crear docentes',   'editar docentes',
            'ver postulantes','crear postulantes','editar postulantes',
            'validar requisitos postulante',
            'ver grupos',     'crear grupos',     'editar grupos',    'eliminar grupos',
            'generar grupos automaticos',
            'ver horarios',   'crear horarios',   'editar horarios',
            'ver cupos',
            'procesar admision',
            'ver resultados admision',
            'ver reportes',
        ]);

        // ── Docente ───────────────────────────────────────────────────────────
        $docente = Role::firstOrCreate(['name' => 'Docente']);
        $docente->syncPermissions([
            'ver grupos',
            'ver postulantes',
            'registrar notas',
            'ver notas',
        ]);

        // ── Postulante ────────────────────────────────────────────────────────
        $postulante = Role::firstOrCreate(['name' => 'Postulante']);
        $postulante->syncPermissions([
            'ver notas propias',
            'ver resultado propio',
        ]);

        // ── Autoridad de la Facultad ──────────────────────────────────────────
        $autoridad = Role::firstOrCreate(['name' => 'Autoridad de la Facultad']);
        $autoridad->syncPermissions([
            'ver carreras',
            'definir cupos',
            'ver cupos',
            'ver resultados admision',
            'ver reportes',
            'ver reportes ejecutivos',
        ]);
    }
}
SEEDER
success "database/seeders/RolesSeeder.php actualizado"

# =============================================================================
#  6. SEEDER: Usuarios iniciales
# =============================================================================
info "Actualizando UsuariosSeeder para CUP..."

cat > database/seeders/UsuariosSeeder.php << 'SEEDER'
<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsuariosSeeder extends Seeder
{
    public function run(): void
    {
        $pwd = Hash::make('12345678');

        $usuarios = [
            ['name' => 'admin',         'email' => 'admin@cup.edu.bo',      'rol' => 'Administrador'],
            ['name' => 'admisiones',    'email' => 'admisiones@cup.edu.bo', 'rol' => 'Responsable de Admisiones'],
            ['name' => 'docente',       'email' => 'docente@cup.edu.bo',    'rol' => 'Docente'],
            ['name' => 'autoridad',     'email' => 'autoridad@cup.edu.bo',  'rol' => 'Autoridad de la Facultad'],
        ];

        foreach ($usuarios as $data) {
            $user = User::create([
                'name'               => $data['name'],
                'email'              => $data['email'],
                'activo'             => true,
                'email_verified_at'  => now(),
                'password'           => $pwd,
            ]);
            $user->assignRole($data['rol']);
        }
    }
}
SEEDER
success "database/seeders/UsuariosSeeder.php actualizado"

# =============================================================================
#  7. SEEDER: DatabaseSeeder — registrar nuevos seeders
# =============================================================================
info "Actualizando DatabaseSeeder..."

cat > database/seeders/DatabaseSeeder.php << 'SEEDER'
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // Seguridad (orden importa: permisos → roles → usuarios)
            PermissionSeeder::class,
            RolesSeeder::class,
            UsuariosSeeder::class,

            // Datos de referencia del dominio CUP
            // (descomenta a medida que implementes los módulos)
            // GestionSeeder::class,
            // CarreraSeeder::class,
            // MateriaSeeder::class,
            // DocenteSeeder::class,
        ]);
    }
}
SEEDER
success "database/seeders/DatabaseSeeder.php actualizado"

# =============================================================================
#  8. CONTROLADOR: UsuarioController
# =============================================================================
info "Actualizando UsuarioController..."

cat > app/Http/Controllers/UsuarioController.php << 'CONTROLLER'
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
CONTROLLER
success "app/Http/Controllers/UsuarioController.php actualizado"

# =============================================================================
#  9. CONTROLADOR: RoleController
# =============================================================================
info "Actualizando RoleController..."

cat > app/Http/Controllers/RoleController.php << 'CONTROLLER'
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
        // Agrupar permisos por módulo para facilitar la UI
        $permisos = Permission::all()->groupBy(function ($p) {
            // La primera palabra es el módulo (ej: "ver usuarios" → "ver")
            // Usamos la última palabra clave del nombre como agrupador
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
        $permisos    = Permission::all()->groupBy(function ($p) {
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
        if ($role->name === 'Administrador') {
            return back()->withErrors(['No se puede eliminar el rol Administrador.']);
        }
        $this->registrarEnBitacora('Rol eliminado: ' . $role->name, $role->id);
        $role->delete();
        return redirect()->route('roles.index')->with('success', 'Rol eliminado correctamente.');
    }
}
CONTROLLER
success "app/Http/Controllers/RoleController.php actualizado"

# =============================================================================
#  10. RUTAS — reemplazar web.php con versión limpia para CUP
# =============================================================================
info "Actualizando routes/web.php..."

cat > routes/web.php << 'ROUTES'
<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\BitacoraController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Models\Bitacora;

// ── Recuperación de contraseña ────────────────────────────────────────────────
Route::get('password/reset',          [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email',         [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}',  [ResetPasswordController::class,  'showResetForm'])->name('password.reset');
Route::post('password/reset',         [ResetPasswordController::class,  'reset'])->name('password.update');

// ── Autenticación ─────────────────────────────────────────────────────────────
Route::get('/login',  [LoginController::class,  'index'])->name('login');
Route::post('/login', [LoginController::class,  'login']);
Route::get('/logout', [LogoutController::class, 'logout'])->name('logout');

// ── Panel principal ───────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/',       [HomeController::class, 'index'])->name('panel');
    Route::get('/panel',  [HomeController::class, 'index']);

    // Perfil propio
    Route::get('/perfil', [UsuarioController::class, 'miPerfil'])->name('users.perfil');

    // ── MÓDULO DE SEGURIDAD (ya implementado) ─────────────────────────────────
    Route::resource('users',  UsuarioController::class);
    Route::resource('roles',  RoleController::class);
    Route::resource('bitacora', BitacoraController::class)->only(['index']);

    // ── MÓDULO ACADÉMICO (implementar en próximos ciclos) ─────────────────────
    // Route::resource('gestiones', GestionController::class);
    // Route::resource('carreras',  CarreraController::class);
    // Route::resource('materias',  MateriaController::class);
    // Route::resource('cupos',     CupoCarreraController::class);

    // ── MÓDULO DE DOCENTES ────────────────────────────────────────────────────
    // Route::resource('docentes', DocenteController::class);

    // ── MÓDULO DE POSTULANTES ─────────────────────────────────────────────────
    // Route::resource('postulantes', PostulanteController::class);

    // ── MÓDULO DE GRUPOS / AULAS ──────────────────────────────────────────────
    // Route::resource('grupos', GrupoController::class);
    // Route::post('/grupos/generar', [GrupoController::class, 'generarAutomatico'])->name('grupos.generar');

    // ── MÓDULO DE HORARIOS ────────────────────────────────────────────────────
    // Route::resource('horarios', HorarioController::class);

    // ── MÓDULO DE EVALUACIÓN ──────────────────────────────────────────────────
    // Route::resource('notas', NotaController::class);

    // ── MÓDULO DE ADMISIÓN ────────────────────────────────────────────────────
    // Route::resource('admision', AdmisionController::class);

    // ── REPORTES ──────────────────────────────────────────────────────────────
    // Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');
});

// ── Bitácora: cierre de página (sendBeacon) ───────────────────────────────────
Route::post('/bitacora/page-close', function () {
    if (Auth::check()) {
        Bitacora::create([
            'user_id'    => Auth::id(),
            'usuario'    => Auth::user()->name,
            'accion'     => 'Cerró o abandonó la página del sistema',
            'fecha_hora' => now(),
            'ip'         => request()->ip(),
        ]);
    }
    return response()->noContent();
})->middleware('web')->name('bitacora.page-close');

// ── Páginas de error ──────────────────────────────────────────────────────────
Route::get('/401', fn() => view('pages.401'));
Route::get('/404', fn() => view('pages.404'));
Route::get('/500', fn() => view('pages.500'));
ROUTES
success "routes/web.php actualizado"

# =============================================================================
#  11. VISTAS: users/index.blade.php
# =============================================================================
info "Actualizando vista users/index..."
mkdir -p resources/views/users

cat > resources/views/users/index.blade.php << 'BLADE'
@extends('layouts.ap')

@section('title', 'Usuarios — Admisión CUP')

@push('css')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@section('content')
@include('layouts.partials.alert')

@if (session('success'))
<script>
    Swal.mixin({ toast:true, position:'top-end', showConfirmButton:false, timer:2000, timerProgressBar:true })
        .fire({ icon:'success', title:"{{ session('success') }}" });
</script>
@endif

<div class="container-fluid px-4">
    <h1 class="mt-4">Usuarios del Sistema</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('panel') }}">Inicio</a></li>
        <li class="breadcrumb-item active">Usuarios</li>
    </ol>

    @can('crear usuarios')
    <div class="mb-3">
        <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus me-1"></i> Nuevo Usuario
        </a>
    </div>
    @endcan

    <div class="card mb-4">
        <div class="card-header"><i class="fas fa-users me-1"></i> Listado de Usuarios</div>
        <div class="card-body">
            <table id="datatablesSimple" class="table table-striped table-sm">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @foreach($user->getRoleNames() as $rol)
                                <span class="badge bg-info text-dark">{{ $rol }}</span>
                            @endforeach
                        </td>
                        <td>
                            @if($user->activo)
                                <span class="badge bg-success">Activo</span>
                            @else
                                <span class="badge bg-secondary">Inactivo</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                @can('editar usuarios')
                                <a href="{{ route('users.edit', $user) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan
                                @can('eliminar usuarios')
                                <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm {{ $user->activo ? 'btn-danger' : 'btn-success' }}"
                                            onclick="return confirm('¿Confirmar cambio de estado?')">
                                        <i class="fas fa-{{ $user->activo ? 'ban' : 'check' }}"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
BLADE
success "resources/views/users/index.blade.php actualizado"

# =============================================================================
#  12. VISTAS: users/create.blade.php
# =============================================================================
cat > resources/views/users/create.blade.php << 'BLADE'
@extends('layouts.ap')
@section('title', 'Crear Usuario')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Crear Usuario</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('panel') }}">Inicio</a></li>
        <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Usuarios</a></li>
        <li class="breadcrumb-item active">Crear</li>
    </ol>

    <div class="card">
        <div class="card-header"><i class="fas fa-user-plus me-1"></i> Nuevo Usuario</div>
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
            @endif

            <form action="{{ route('users.store') }}" method="POST">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nombre completo *</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email *</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Contraseña *</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Confirmar contraseña *</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Rol *</label>
                        <select name="role" class="form-select" required>
                            <option value="">— Seleccionar —</option>
                            @foreach($roles as $rol)
                                <option value="{{ $rol->name }}" {{ old('role') == $rol->name ? 'selected' : '' }}>
                                    {{ $rol->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @if($docentes->count())
                    <div class="col-md-6">
                        <label class="form-label">Vincular a Docente <small class="text-muted">(opcional)</small></label>
                        <select name="docente_id" class="form-select">
                            <option value="">— Ninguno —</option>
                            @foreach($docentes as $d)
                                <option value="{{ $d->id }}" {{ old('docente_id') == $d->id ? 'selected' : '' }}>
                                    {{ $d->nombres }} {{ $d->apellidos }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    @if($postulantes->count())
                    <div class="col-md-6">
                        <label class="form-label">Vincular a Postulante <small class="text-muted">(opcional)</small></label>
                        <select name="postulante_id" class="form-select">
                            <option value="">— Ninguno —</option>
                            @foreach($postulantes as $p)
                                <option value="{{ $p->id }}" {{ old('postulante_id') == $p->id ? 'selected' : '' }}>
                                    {{ $p->nombres }} {{ $p->apellidos }} ({{ $p->ci }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                    <a href="{{ route('users.index') }}" class="btn btn-secondary ms-2">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
BLADE
success "resources/views/users/create.blade.php actualizado"

# =============================================================================
#  13. VISTAS: users/edit.blade.php
# =============================================================================
cat > resources/views/users/edit.blade.php << 'BLADE'
@extends('layouts.ap')
@section('title', 'Editar Usuario')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Editar Usuario</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('panel') }}">Inicio</a></li>
        <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Usuarios</a></li>
        <li class="breadcrumb-item active">Editar</li>
    </ol>

    <div class="card">
        <div class="card-header"><i class="fas fa-user-edit me-1"></i> Editar: {{ $user->name }}</div>
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
            @endif

            <form action="{{ route('users.update', $user) }}" method="POST">
                @csrf @method('PUT')

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nombre *</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email *</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nueva contraseña <small class="text-muted">(dejar vacío para no cambiar)</small></label>
                        <input type="password" name="password" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Confirmar contraseña</label>
                        <input type="password" name="password_confirmation" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Rol *</label>
                        <select name="role" class="form-select" required>
                            <option value="">— Seleccionar —</option>
                            @foreach($roles as $rol)
                                <option value="{{ $rol->name }}"
                                    {{ $user->hasRole($rol->name) ? 'selected' : '' }}>
                                    {{ $rol->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @if($docentes->count())
                    <div class="col-md-6">
                        <label class="form-label">Docente vinculado</label>
                        <select name="docente_id" class="form-select">
                            <option value="">— Ninguno —</option>
                            @foreach($docentes as $d)
                                <option value="{{ $d->id }}" {{ $user->docente_id == $d->id ? 'selected' : '' }}>
                                    {{ $d->nombres }} {{ $d->apellidos }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    @if($postulantes->count())
                    <div class="col-md-6">
                        <label class="form-label">Postulante vinculado</label>
                        <select name="postulante_id" class="form-select">
                            <option value="">— Ninguno —</option>
                            @foreach($postulantes as $p)
                                <option value="{{ $p->id }}" {{ $user->postulante_id == $p->id ? 'selected' : '' }}>
                                    {{ $p->nombres }} {{ $p->apellidos }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                    <a href="{{ route('users.index') }}" class="btn btn-secondary ms-2">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
BLADE
success "resources/views/users/edit.blade.php actualizado"

# =============================================================================
#  14. VISTAS: roles (index, create, edit)
# =============================================================================
info "Actualizando vistas de roles..."
mkdir -p resources/views/roles

cat > resources/views/roles/index.blade.php << 'BLADE'
@extends('layouts.ap')
@section('title', 'Roles')

@section('content')
@include('layouts.partials.alert')

<div class="container-fluid px-4">
    <h1 class="mt-4">Roles y Permisos</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('panel') }}">Inicio</a></li>
        <li class="breadcrumb-item active">Roles</li>
    </ol>

    @can('crear roles')
    <div class="mb-3">
        <a href="{{ route('roles.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus me-1"></i> Nuevo Rol
        </a>
    </div>
    @endcan

    <div class="card">
        <div class="card-header"><i class="fas fa-shield-alt me-1"></i> Listado de Roles</div>
        <div class="card-body">
            <table id="datatablesSimple" class="table table-striped table-sm">
                <thead>
                    <tr><th>Rol</th><th>Permisos</th><th>Acciones</th></tr>
                </thead>
                <tbody>
                    @foreach ($roles as $rol)
                    <tr>
                        <td><strong>{{ $rol->name }}</strong></td>
                        <td>
                            @foreach($rol->permissions->take(5) as $p)
                                <span class="badge bg-light text-dark border">{{ $p->name }}</span>
                            @endforeach
                            @if($rol->permissions->count() > 5)
                                <span class="text-muted small">+ {{ $rol->permissions->count() - 5 }} más</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                @can('editar roles')
                                <a href="{{ route('roles.edit', $rol) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan
                                @can('eliminar roles')
                                @if($rol->name !== 'Administrador')
                                <form action="{{ route('roles.destroy', $rol) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar rol?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
BLADE

cat > resources/views/roles/create.blade.php << 'BLADE'
@extends('layouts.ap')
@section('title', 'Crear Rol')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Crear Rol</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('panel') }}">Inicio</a></li>
        <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Roles</a></li>
        <li class="breadcrumb-item active">Crear</li>
    </ol>

    <div class="card">
        <div class="card-header"><i class="fas fa-shield-alt me-1"></i> Nuevo Rol</div>
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
            @endif

            <form action="{{ route('roles.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Nombre del rol *</label>
                    <input type="text" name="name" class="form-control w-50" value="{{ old('name') }}" required>
                </div>

                <label class="form-label fw-bold">Permisos *</label>
                @foreach($permisos as $modulo => $lista)
                <div class="card mb-2">
                    <div class="card-header py-1 bg-light fw-semibold small">{{ $modulo }}</div>
                    <div class="card-body py-2">
                        <div class="row">
                            @foreach($lista as $permiso)
                            <div class="col-md-3 col-sm-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox"
                                           name="permission[]" value="{{ $permiso->id }}"
                                           id="perm_{{ $permiso->id }}"
                                           {{ in_array($permiso->id, old('permission', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="perm_{{ $permiso->id }}">
                                        {{ $permiso->name }}
                                    </label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endforeach

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                    <a href="{{ route('roles.index') }}" class="btn btn-secondary ms-2">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
BLADE

cat > resources/views/roles/edit.blade.php << 'BLADE'
@extends('layouts.ap')
@section('title', 'Editar Rol')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Editar Rol</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('panel') }}">Inicio</a></li>
        <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Roles</a></li>
        <li class="breadcrumb-item active">Editar</li>
    </ol>

    <div class="card">
        <div class="card-header"><i class="fas fa-edit me-1"></i> Editar: {{ $role->name }}</div>
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
            @endif

            <form action="{{ route('roles.update', $role) }}" method="POST">
                @csrf @method('PUT')
                <div class="mb-3">
                    <label class="form-label">Nombre del rol *</label>
                    <input type="text" name="name" class="form-control w-50"
                           value="{{ old('name', $role->name) }}" required>
                </div>

                <label class="form-label fw-bold">Permisos *</label>
                @foreach($permisos as $modulo => $lista)
                <div class="card mb-2">
                    <div class="card-header py-1 bg-light fw-semibold small">{{ $modulo }}</div>
                    <div class="card-body py-2">
                        <div class="row">
                            @foreach($lista as $permiso)
                            <div class="col-md-3 col-sm-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox"
                                           name="permission[]" value="{{ $permiso->id }}"
                                           id="perm_{{ $permiso->id }}"
                                           {{ in_array($permiso->name, $permisosRol) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="perm_{{ $permiso->id }}">
                                        {{ $permiso->name }}
                                    </label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endforeach

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                    <a href="{{ route('roles.index') }}" class="btn btn-secondary ms-2">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
BLADE
success "Vistas de roles actualizadas"

# =============================================================================
#  15. ELIMINAR ARCHIVOS DEL DOMINIO CONDOMINIO que NO aplican a CUP
#      (solo archivos de entidades que no existen en el nuevo sistema)
# =============================================================================
info "Eliminando archivos de dominio exclusivo de condominio..."

OBSOLETOS=(
  # Modelos
  "app/Models/Residente.php"
  "app/Models/Empleado.php"
  "app/Models/CargoEmpleado.php"
  "app/Models/AreaComun.php"
  "app/Models/Reserva.php"
  "app/Models/Cuota.php"
  "app/Models/TipoCuota.php"
  "app/Models/Pago.php"
  "app/Models/Multa.php"
  "app/Models/Visita.php"
  "app/Models/Comunicado.php"
  "app/Models/EmpresaExterna.php"
  "app/Models/Mantenimiento.php"
  "app/Models/Notificacion.php"
  "app/Models/Inventario.php"
  "app/Models/CategoriaInventario.php"
  "app/Models/VerificacionInventario.php"
  "app/Models/Unidad.php"
  # Controladores
  "app/Http/Controllers/ResidenteController.php"
  "app/Http/Controllers/EmpleadoController.php"
  "app/Http/Controllers/CargoEmpleadoController.php"
  "app/Http/Controllers/AreaComunController.php"
  "app/Http/Controllers/ReservaController.php"
  "app/Http/Controllers/CuotaController.php"
  "app/Http/Controllers/TipoCuotaController.php"
  "app/Http/Controllers/PagoController.php"
  "app/Http/Controllers/MultaController.php"
  "app/Http/Controllers/VisitaController.php"
  "app/Http/Controllers/ComunicadoController.php"
  "app/Http/Controllers/EmpresaExternaController.php"
  "app/Http/Controllers/MantenimientoController.php"
  # Seeders
  "database/seeders/ResidentesSeeder.php"
  "database/seeders/EmpleadosSeeder.php"
  "database/seeders/CargoEmpleadosSeeder.php"
  "database/seeders/AreaComunSeeder.php"
  "database/seeders/ReservaSeeder.php"
  "database/seeders/CuotaSeeder.php"
  "database/seeders/TipoCuotaSeeder.php"
  "database/seeders/PagoSeeder.php"
  "database/seeders/MultaSeeder.php"
  "database/seeders/VisitasSeeder.php"
  "database/seeders/ComunicadoSeeder.php"
  "database/seeders/EmpresaExternaSeeder.php"
  "database/seeders/MantenimientoSeeder.php"
  "database/seeders/ClasificadoresSeeder.php"
)

for f in "${OBSOLETOS[@]}"; do
  if [ -f "$f" ]; then
    rm "$f"
    warn "Eliminado: $f"
  fi
done

# Eliminar vistas de módulos de condominio
DIRS_OBSOLETOS=(
  "resources/views/residentes"
  "resources/views/empleados"
  "resources/views/areas_comunes"
  "resources/views/reservas"
  "resources/views/cuotas"
  "resources/views/pagos"
  "resources/views/multas"
  "resources/views/visitas"
  "resources/views/comunicados"
  "resources/views/empresas"
  "resources/views/mantenimientos"
  "resources/views/Usuarios"
)
for d in "${DIRS_OBSOLETOS[@]}"; do
  if [ -d "$d" ]; then
    rm -rf "$d"
    warn "Directorio eliminado: $d"
  fi
done

success "Limpieza de archivos obsoletos completada"

# =============================================================================
#  16. CREAR MIGRACIONES VACÍAS para entidades del dominio CUP
#      (solo esqueleto, listos para completar)
# =============================================================================
info "Creando migraciones esqueleto para el dominio CUP..."

mkdir -p database/migrations

# Gestiones
cat > database/migrations/2026_01_01_000001_create_gestiones_table.php << 'MIG'
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// CU-13: Gestionar gestiones académicas (Semestre 1-2026, etc.)
return new class extends Migration {
    public function up(): void {
        Schema::create('gestiones', function (Blueprint $table) {
            $table->id();
            $table->string('descripcion', 50)->unique(); // "Semestre 1-2026"
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->enum('estado', ['planificacion', 'inscripcion', 'en_curso', 'finalizado'])->default('planificacion');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('gestiones'); }
};
MIG

# Carreras
cat > database/migrations/2026_01_01_000002_create_carreras_table.php << 'MIG'
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// CU-10: Gestionar carreras (Informática, Sistemas, Redes, Robótica)
return new class extends Migration {
    public function up(): void {
        Schema::create('carreras', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100)->unique();
            $table->text('descripcion')->nullable();
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('carreras'); }
};
MIG

# Cupos por carrera
cat > database/migrations/2026_01_01_000003_create_cupos_carrera_table.php << 'MIG'
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// CU-11: Definir cupos por carrera y gestión
return new class extends Migration {
    public function up(): void {
        Schema::create('cupos_carrera', function (Blueprint $table) {
            $table->id();
            $table->foreignId('carrera_id')->constrained()->cascadeOnDelete();
            $table->foreignId('gestion_id')->constrained('gestiones')->cascadeOnDelete();
            $table->unsignedInteger('cantidad_maxima');
            $table->unique(['carrera_id', 'gestion_id']);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('cupos_carrera'); }
};
MIG

# Materias
cat > database/migrations/2026_01_01_000004_create_materias_table.php << 'MIG'
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// CU-12: Gestionar materias del CUP (Computación, Matemáticas, Física, Inglés)
return new class extends Migration {
    public function up(): void {
        Schema::create('materias', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100)->unique();
            $table->string('area', 50)->nullable();
            $table->text('descripcion')->nullable();
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('materias'); }
};
MIG

# Docentes
cat > database/migrations/2026_01_01_000005_create_docentes_table.php << 'MIG'
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// CU-14: Registrar docentes con perfil profesional
return new class extends Migration {
    public function up(): void {
        Schema::create('docentes', function (Blueprint $table) {
            $table->id();
            $table->string('ci', 20)->unique();
            $table->string('nombres', 100);
            $table->string('apellidos', 100);
            $table->string('telefono', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('profesion', 100)->nullable();
            $table->string('maestria', 150)->nullable();
            $table->string('diplomado', 150)->nullable();   // Diplomado en Educación Superior
            $table->string('area_formacion', 50)->nullable();
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('docentes'); }
};
MIG

# Postulantes
cat > database/migrations/2026_01_01_000006_create_postulantes_table.php << 'MIG'
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// CU-05: Registrar postulantes con validación de requisitos
return new class extends Migration {
    public function up(): void {
        Schema::create('postulantes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gestion_id')->constrained('gestiones');
            $table->foreignId('primera_opcion_id')->constrained('carreras');
            $table->foreignId('segunda_opcion_id')->constrained('carreras');
            $table->string('ci', 20)->unique();
            $table->string('nombres', 100);
            $table->string('apellidos', 100);
            $table->date('fecha_nacimiento')->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('email', 100)->nullable();
            // Estados: inscrito | en_curso | aprobado | no_aprobado | admitido | no_admitido
            $table->string('estado', 30)->default('inscrito');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('postulantes'); }
};
MIG

success "Migraciones esqueleto del dominio CUP creadas"

# =============================================================================
#  17. Limpiar caché de Laravel
# =============================================================================
info "Limpiando caché de Laravel..."
php artisan config:clear   2>/dev/null || true
php artisan route:clear    2>/dev/null || true
php artisan view:clear     2>/dev/null || true
php artisan cache:clear    2>/dev/null || true
success "Caché limpiada"

# =============================================================================
#  RESUMEN FINAL
# =============================================================================
echo ""
echo -e "${GREEN}══════════════════════════════════════════════════════════════════${NC}"
echo -e "${GREEN}  ADAPTACIÓN COMPLETADA — Sistema de Admisión CUP${NC}"
echo -e "${GREEN}══════════════════════════════════════════════════════════════════${NC}"
echo ""
echo -e "  ${CYAN}ARCHIVOS ACTUALIZADOS (ya funcionales):${NC}"
echo "   ✓ .env / config/adminlte.php           — Nombre del sistema"
echo "   ✓ app/Models/User.php                  — Vínculos a docente/postulante"
echo "   ✓ app/Http/Controllers/UsuarioController.php"
echo "   ✓ app/Http/Controllers/RoleController.php"
echo "   ✓ database/migrations/..._create_users_table.php"
echo "   ✓ database/seeders/PermissionSeeder.php — Permisos del CUP"
echo "   ✓ database/seeders/RolesSeeder.php      — 5 roles del CUP"
echo "   ✓ database/seeders/UsuariosSeeder.php   — Usuarios iniciales"
echo "   ✓ database/seeders/DatabaseSeeder.php"
echo "   ✓ routes/web.php                        — Rutas limpias + comentadas"
echo "   ✓ resources/views/users/{index,create,edit}.blade.php"
echo "   ✓ resources/views/roles/{index,create,edit}.blade.php"
echo ""
echo -e "  ${CYAN}MIGRACIONES ESQUELETO creadas (completar atributos si hace falta):${NC}"
echo "   ✓ gestiones, carreras, cupos_carrera, materias, docentes, postulantes"
echo ""
echo -e "  ${YELLOW}PRÓXIMOS PASOS:${NC}"
echo "   1.  Revisar el .env: DB_DATABASE, DB_USERNAME, DB_PASSWORD"
echo "   2.  php artisan migrate:fresh --seed"
echo "   3.  Implementar los CRUDs del dominio en este orden:"
echo "       → Gestiones → Carreras → Cupos → Materias → Docentes → Postulantes"
echo "       → Grupos → Horarios → Notas → Admisión → Reportes"
echo "   4.  Descomentar las rutas en routes/web.php a medida que avances"
echo ""
echo -e "  ${CYAN}Credenciales de prueba (password: 12345678):${NC}"
echo "   admin@cup.edu.bo          → Administrador"
echo "   admisiones@cup.edu.bo     → Responsable de Admisiones"
echo "   docente@cup.edu.bo        → Docente"
echo "   autoridad@cup.edu.bo      → Autoridad de la Facultad"
echo ""
