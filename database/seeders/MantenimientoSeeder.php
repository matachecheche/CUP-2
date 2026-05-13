<?php

namespace Database\Seeders;

use App\Models\Mantenimiento;
use App\Models\User;
use App\Models\EmpresaExterna;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MantenimientoSeeder extends Seeder
{
    public function run(): void
    {
        $usuarios = User::whereIn('name', ['Admin', 'Supervisor'])->get();
        $empresas = EmpresaExterna::all();

        if ($usuarios->isEmpty()) {
            return;
        }

        $mantenimientos = [
            [
                'descripcion' => 'Reparación de tubería en área común - Fuga identificada en el sótano',
                'estado' => 1,
                'prioridad' => 'alta',
                'fecha_hora' => now()->addDays(1),
                'monto' => 150000,
                'usuario_id' => $usuarios->first()->id,
                'empresaExterna_id' => $empresas->first()?->id,
            ],
            [
                'descripcion' => 'Mantenimiento preventivo de ascensores - Lubricación y revisión general',
                'estado' => 1,
                'prioridad' => 'media',
                'fecha_hora' => now()->addDays(5),
                'monto' => 250000,
                'usuario_id' => $usuarios->first()->id,
                'empresaExterna_id' => $empresas->first()?->id,
            ],
            [
                'descripcion' => 'Limpieza de canaletas y desagües - Tarea de mantenimiento rutinaria',
                'estado' => 0,
                'prioridad' => 'baja',
                'fecha_hora' => now()->addDays(10),
                'monto' => 80000,
                'usuario_id' => $usuarios->last()->id,
                'empresaExterna_id' => null,
            ],
        ];

        foreach ($mantenimientos as $datos) {
            Mantenimiento::create($datos);
        }
    }
}