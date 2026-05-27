<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\{Role,Permission};
class RolesSeeder extends Seeder {
    public function run(): void {
        $admin = Role::firstOrCreate(['name'=>'Administrador del Sistema','guard_name'=>'web']);
        $admin->syncPermissions(Permission::all());

        $doc = Role::firstOrCreate(['name'=>'Docente','guard_name'=>'web']);
        $doc->syncPermissions(['ver grupos','ver postulantes','ver notas','crear notas','editar notas','ver admision']);

        $pos = Role::firstOrCreate(['name'=>'Postulante','guard_name'=>'web']);
        $pos->syncPermissions(['ver postulantes']);
    }
}
