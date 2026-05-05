<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Mantenimiento;

class MantenimientoSeeder extends Seeder
{
    public function run(): void
    {
        Mantenimiento::factory()->count(10)->create();
    }
}
