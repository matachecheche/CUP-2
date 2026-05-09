<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        // Rol Administrador
        $admin = Role::firstOrCreate(['name' => 'Administrador']);
        $admin->syncPermissions(Permission::all()); // Todos los permisos

        // Rol Residente
        $residente = Role::firstOrCreate(['name' => 'Residente']);
        $residente->syncPermissions([
            'gestionar visitas',
            'reportar-incidentes',
            'ver comunicados',
            'ver notificaciones',
            'ver agenda',
            'ver documentos',
            'ver foro',
            'ver calificaciones',
        ]);

        // Rol Portero
        $portero = Role::firstOrCreate(['name' => 'Portero']);
        $portero->syncPermissions([
            'ver control de acceso',
            'crear-registro-seguridad',
            'operar porteria',
            'ver invitaciones',
            'ver vigilancia',
        ]);

        // Rol Directiva
        $directiva = Role::firstOrCreate(['name' => 'Miembro de Directiva']);
        $directiva->syncPermissions([
            'administrar visitas',
            'ver-registros-seguridad',   // ← Solo lectura
            'gestionar visitas',
            'ver usuarios',
            'ver roles',
            'ver empleados',
            'ver residentes',
            'ver bitacora',
            'ver reportes',
        ]);
    }
}
