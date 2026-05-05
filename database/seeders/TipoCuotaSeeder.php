<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TipoCuota;

class TipoCuotaSeeder extends Seeder
{
    public function run(): void
    {
        TipoCuota::factory()->count(5)->create();
    }
}
