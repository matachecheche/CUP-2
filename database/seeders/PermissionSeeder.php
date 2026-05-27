<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
class PermissionSeeder extends Seeder {
    public function run(): void {
        $perms = [
            'ver usuarios','crear usuarios','editar usuarios','eliminar usuarios',
            'ver roles','crear roles','editar roles','eliminar roles',
            'ver bitacora',
            'ver postulantes','crear postulantes','editar postulantes','eliminar postulantes',
            'ver gestiones','crear gestiones','editar gestiones','eliminar gestiones',
            'ver carreras','crear carreras','editar carreras','eliminar carreras',
            'ver materias','crear materias','editar materias','eliminar materias',
            'ver docentes','crear docentes','editar docentes','eliminar docentes',
            'ver grupos','crear grupos','editar grupos','eliminar grupos',
            'ver notas','crear notas','editar notas',
            'ver cupos','crear cupos','editar cupos',
            'ver grupos','crear grupos','editar grupos','eliminar grupos',
            'ver notas','crear notas','editar notas',
            'ver admision','procesar admision','publicar admision',
            'ver reportes',
        ];
        foreach($perms as $p) Permission::firstOrCreate(['name'=>$p,'guard_name'=>'web']);
    }
}
