<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
class RolesSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Role::firstOrCreate(['name' => 'Administrador del Sistema', 'guard_name' => 'web']);
        $admin->syncPermissions(Permission::all());

        // Docente: solo grupos (lectura) y notas (CRUD básico).
        // No ve postulantes, no procesa admisión.
        $doc = Role::firstOrCreate(['name' => 'Docente', 'guard_name' => 'web']);
        $doc->syncPermissions(['ver grupos', 'ver notas', 'crear notas', 'editar notas']);

        // Postulante: sin permisos del panel admin. Todo aparece como "Sin acceso".
        // (La consulta de sus propias notas se hace por un flujo dedicado, no por CRUD.)
        $pos = Role::firstOrCreate(['name' => 'Postulante', 'guard_name' => 'web']);
        $pos->syncPermissions([]);

        // Coordinador del CUP: supervision operativa y academica global.
        $coord = Role::firstOrCreate(['name' => 'Coordinador del CUP', 'guard_name' => 'web']);
        $coord->syncPermissions([
            'ver postulantes', 'editar postulantes',
            'ver gestiones', 'ver carreras', 'ver materias',
            'ver cupos', 'gestionar cupos',
            'ver docentes',
            'ver grupos', 'crear grupos', 'editar grupos', 'ver asignaciones',
            'ver notas', 'editar notas',
            'ver admision', 'procesar admision', 'publicar admision',
            'ver reportes',
            'ver comunicados', 'crear comunicados', 'editar comunicados',
            'ver consulta voz',
        ]);
    }
}
