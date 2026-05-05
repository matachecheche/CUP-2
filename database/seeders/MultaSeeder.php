<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MultaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $multas = [
            [
                'motivo'         => 'Estacionar en zona prohibida',
                'monto'          => 50.00,
                'fechaEmision'   => $now->subDays(10),
                'fechaLimite'    => $now->subDays(5),
                'estado'         => 'pendiente',
                'residente_id'   => 1,
                'empleado_id'    => null,
                'cuota_id'       => null,
                'created_at'     => $now,
                'updated_at'     => $now,
            ],
            [
                'motivo'         => 'Ruido excesivo en horario nocturno',
                'monto'          => 75.00,
                'fechaEmision'   => $now->subDays(8),
                'fechaLimite'    => $now->subDays(3),
                'estado'         => 'pagada',
                'residente_id'   => 3,
                'empleado_id'    => null,
                'cuota_id'       => null,
                'created_at'     => $now,
                'updated_at'     => $now,
            ],
            [
                'motivo'         => 'Dañar mobiliario común',
                'monto'          => 150.00,
                'fechaEmision'   => $now->subDays(15),
                'fechaLimite'    => $now->subDays(10),
                'estado'         => 'anulada',
                'residente_id'   => 5,
                'empleado_id'    => null,
                'cuota_id'       => null,
                'created_at'     => $now,
                'updated_at'     => $now,
            ],
            [
                'motivo'         => 'Depositar basura fuera de horario',
                'monto'          => 30.00,
                'fechaEmision'   => $now->subDays(5),
                'fechaLimite'    => $now->addDays(5),
                'estado'         => 'pendiente',
                'residente_id'   => null,
                'empleado_id'    => 2,
                'cuota_id'       => null,
                'created_at'     => $now,
                'updated_at'     => $now,
            ],
            [
                'motivo'         => 'Uso indebido de piscina',
                'monto'          => 100.00,
                'fechaEmision'   => $now->subDays(20),
                'fechaLimite'    => $now->subDays(15),
                'estado'         => 'pagada',
                'residente_id'   => 8,
                'empleado_id'    => null,
                'cuota_id'       => null,
                'created_at'     => $now,
                'updated_at'     => $now,
            ],
            [
                'motivo'         => 'Obstruir pasillo',
                'monto'          => 45.50,
                'fechaEmision'   => $now->subDays(2),
                'fechaLimite'    => $now->addDays(3),
                'estado'         => 'pendiente',
                'residente_id'   => 2,
                'empleado_id'    => null,
                'cuota_id'       => null,
                'created_at'     => $now,
                'updated_at'     => $now,
            ],
            [
                'motivo'         => 'Fuego en terraza sin permiso',
                'monto'          => 200.00,
                'fechaEmision'   => $now->subDays(12),
                'fechaLimite'    => $now->subDays(7),
                'estado'         => 'pendiente',
                'residente_id'   => null,
                'empleado_id'    => 1,
                'cuota_id'       => null,
                'created_at'     => $now,
                'updated_at'     => $now,
            ],
            [
                'motivo'         => 'Dejar mascotas sueltas',
                'monto'          => 60.00,
                'fechaEmision'   => $now->subDays(7),
                'fechaLimite'    => $now->subDays(1),
                'estado'         => 'pagada',
                'residente_id'   => 4,
                'empleado_id'    => null,
                'cuota_id'       => null,
                'created_at'     => $now,
                'updated_at'     => $now,
            ],
        ];

        DB::table('multas')->insert($multas);
    }
}
