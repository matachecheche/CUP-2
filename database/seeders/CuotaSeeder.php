<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cuota;

class CuotaSeeder extends Seeder
{
    public function run(): void
    {
      \App\Models\Cuota::factory()->count(10)->create();

    }
}
