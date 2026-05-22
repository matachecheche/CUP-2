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
