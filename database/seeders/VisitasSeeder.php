<?php


namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Visita;

class VisitasSeeder extends Seeder
{
    public function run(): void
    {
        // Crear visitas con diferentes estados
        Visita::factory(5)->pendiente()->create();
        Visita::factory(3)->enCurso()->create();
        Visita::factory(10)->finalizada()->create();
        Visita::factory(2)->rechazada()->create();
        
        // Crear algunas visitas aleatorias
        Visita::factory(5)->create();
    }
}