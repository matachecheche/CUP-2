<?php

namespace Database\Factories;

use App\Models\Cuota;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PagoFactory extends Factory
{
    public function definition(): array
    {
        return [
        'cuota_id' => Cuota::inRandomOrder()->first()?->id ?? Cuota::factory()->create()->id,
        'monto_pagado' => $this->faker->randomFloat(2, 50, 500),
        'fecha_pago' => $this->faker->date(),
        'metodo' => $this->faker->randomElement(['efectivo', 'transferencia']),
        'observacion' => $this->faker->optional()->sentence(),
        'user_id' => User::inRandomOrder()->first()?->id ?? 1,
    ];
    }
}
