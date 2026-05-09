<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TipoCuotaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nombre' => $this->faker->randomElement(['Agua', 'Mantenimiento', 'Internet', 'Electricidad']),
            'frecuencia' => $this->faker->randomElement(['mensual', 'anual', 'puntual']),
            'editable' => $this->faker->boolean(80),
        ];
    }
}
