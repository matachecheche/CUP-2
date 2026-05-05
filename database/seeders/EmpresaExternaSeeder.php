<?php

// database/seeders/EmpresaExternaSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EmpresaExterna;

class EmpresaExternaSeeder extends Seeder
{
    public function run(): void
    {
        EmpresaExterna::factory()->count(50)->create();
    }
}
