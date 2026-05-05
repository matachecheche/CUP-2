<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AreaComun;

class AreaComunSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $areas = [
            [
                'nombre' => 'Salón de Eventos',
                'monto'  => 250.00,        // tarifa por reserva
                'estado' => 'activo',
            ],
            [
                'nombre' => 'Piscina',
                'monto'  => 150.00,
                'estado' => 'activo',
            ],
        ];

        foreach($areas as $area) {
            AreaComun::create([
                'nombre' => $area['nombre'],
                'monto'  => $area['monto'],
                'estado' => $area['estado'],
            ]);
        }
    }
}
