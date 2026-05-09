<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Comunicado;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comunicado>
 */
class ComunicadoFactory extends Factory
{
    protected $model = Comunicado::class;

    public function definition(): array
    {
        return [
            'titulo' => $this->faker->sentence,
            'contenido' => $this->faker->paragraph,
            'tipo' => $this->faker->randomElement(['Urgente', 'Informativo']),
            'usuario_id' => 1,
            'fecha_publicacion' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
