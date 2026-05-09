<?php

namespace Database\Factories;

use App\Models\Mantenimiento;
use App\Models\User;
use App\Models\EmpresaExterna;
use Illuminate\Database\Eloquent\Factories\Factory;

class MantenimientoFactory extends Factory
{
    protected $model = Mantenimiento::class;

    public function definition(): array
    {
        return [
            'descripcion' => $this->faker->sentence,
            'estado' => $this->faker->numberBetween(0, 1),
            'fecha_hora' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'monto' => $this->faker->randomFloat(2, 100, 1000),
            'usuario_id' => User::factory(), // o pon un ID fijo si ya tienes usuarios
            'empresaExterna_id' => EmpresaExterna::factory(), // relación válida
        ];
    }
}