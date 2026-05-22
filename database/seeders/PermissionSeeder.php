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
