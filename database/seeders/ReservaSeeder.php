<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Reserva;

class ReservaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Reserva::create([
        'fecha'          => '2025-05-01',
        'hora_inicio'    => '18:00',
        'hora_fin'       => '20:00',
        'estado'         => 'confirmada',
        'monto_total'    => 150,
        'area_comun_id'  => 1,
        'residente_id'   => 1,
        ]);
    }
}
