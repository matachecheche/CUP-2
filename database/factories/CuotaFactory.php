<?php

namespace Database\Factories;

use App\Models\Cuota;
use App\Models\Residente;
use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\TipoCuota;
use App\Models\User;

class CuotaFactory extends Factory
{
    protected $model = Cuota::class;


    public function definition(): array
    {
        return [
            'titulo' => $this->faker->words(3, true),
            'descripcion' => $this->faker->sentence(),
            'fecha_emision' => $this->faker->date(),
            'fecha_vencimiento' => $this->faker->dateTimeBetween('+1 week', '+1 month')->format('Y-m-d'),
            'monto' => $this->faker->randomFloat(2, 50, 500),
            'estado' => $this->faker->randomElement(['pendiente', 'activa', 'cancelada']),
            'residente_id' => Residente::inRandomOrder()->first()?->id,
            'tipo_cuota_id' => TipoCuota::inRandomOrder()->first()?->id,
            'user_id' => User::inRandomOrder()->first()?->id,
            'observacion' => $this->faker->optional()->sentence(),
        ];
    }
}
