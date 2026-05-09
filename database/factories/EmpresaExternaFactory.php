<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class EmpresaExternaFactory extends Factory
{
    public function definition(): array
    {
        $servicios = ['Seguridad', 'Limpieza', 'Jardinería', 'Mantenimiento', 'Electricidad', 'Internet', 'Cámaras de vigilancia'];

        return [
            'nombre' => $this->faker->unique()->company,
            'servicio' => $this->faker->randomElement($servicios),
            'telefono' => $this->faker->numerify('7#######'), // Simula un número boliviano
            'correo' => $this->faker->companyEmail,
            'direccion' => $this->faker->streetAddress . ', ' . $this->faker->city,
            'observacion' => $this->faker->optional()->realText(60),
        ];
    }
}
