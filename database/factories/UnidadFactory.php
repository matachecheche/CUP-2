<?php
// database/factories/UnidadFactory.php

namespace Database\Factories;

use App\Models\Unidad;
use App\Models\Residente;
use Illuminate\Database\Eloquent\Factories\Factory;

class UnidadFactory extends Factory
{
    /**
     * El nombre del modelo asociado a esta fábrica.
     *
     * @var string
     */
    protected $model = Unidad::class;

    /**
     * Define el estado por defecto del modelo.
     *
     * @return array
     */
    public function definition()
    {
        // Tomamos un residente existente al azar (o null si no hay)
        $residente = Residente::inRandomOrder()->first();

        return [
            'codigo'               => 'U-' . $this->faker->unique()->numerify('###'),
            'placa'                => strtoupper($this->faker->bothify('???-####')),
            'marca'                => $this->faker->randomElement(['Toyota','Nissan','Honda','Ford','Kia']),
            'capacidad'            => $this->faker->numberBetween(1,6),
            'estado'               => $this->faker->randomElement(['activa','inactiva']),
            'personas_por_unidad'  => $this->faker->numberBetween(1,5),
            'tiene_mascotas'       => $this->faker->boolean(30),
            'vehiculos'            => $this->faker->numberBetween(0,3),
            'residente_id'         => $residente?->id,
        ];
    }
}
