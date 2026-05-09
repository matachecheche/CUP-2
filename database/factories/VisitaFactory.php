<?php

namespace Database\Factories;

use App\Models\Visita;
use App\Models\Residente;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class VisitaFactory extends Factory
{
    protected $model = Visita::class;

    public function definition(): array
    {
        $fechaInicio = $this->faker->dateTimeBetween('-1 week', '+1 week');
        $fechaFin = (clone $fechaInicio)->modify('+' . $this->faker->numberBetween(1, 8) . ' hours');
        
        return [
            'residente_id' => Residente::factory(),
            'nombre_visitante' => $this->faker->name(),
            'ci_visitante' => $this->faker->numerify('########'),
            'placa_vehiculo' => $this->faker->optional(0.6)->regexify('[A-Z]{3}-[0-9]{3}'),
            'motivo' => $this->faker->randomElement([
                'Visita familiar',
                'Servicio técnico',
                'Delivery',
                'Visita social',
                'Entrega de documentos',
                'Reunión de trabajo',
                'Servicio de limpieza',
                'Reparación'
            ]),
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => $fechaFin,
            'codigo' => $this->faker->unique()->numerify('######'),
            'estado' => $this->faker->randomElement(['pendiente', 'en_curso', 'finalizada', 'rechazada']),
            'observaciones' => $this->faker->optional(0.3)->sentence(),
        ];
    }

    // Estado específicos
    public function pendiente(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'pendiente',
            'fecha_inicio' => $this->faker->dateTimeBetween('now', '+1 week'),
            'hora_entrada' => null,
            'hora_salida' => null,
            'user_entrada_id' => null,
            'user_salida_id' => null,
        ]);
    }

    public function enCurso(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'en_curso',
            'fecha_inicio' => $this->faker->dateTimeBetween('-2 hours', 'now'),
            'hora_entrada' => $this->faker->dateTimeBetween('-2 hours', 'now'),
            'user_entrada_id' => User::factory(),
            'hora_salida' => null,
            'user_salida_id' => null,
        ]);
    }

    public function finalizada(): static
    {
        $horaEntrada = $this->faker->dateTimeBetween('-1 week', '-1 hour');
        $horaSalida = (clone $horaEntrada)->modify('+' . $this->faker->numberBetween(1, 6) . ' hours');
        
        return $this->state(fn (array $attributes) => [
            'estado' => 'finalizada',
            'fecha_inicio' => $this->faker->dateTimeBetween('-1 week', '-1 day'),
            'hora_entrada' => $horaEntrada,
            'hora_salida' => $horaSalida,
            'user_entrada_id' => User::factory(),
            'user_salida_id' => User::factory(),
        ]);
    }

    public function rechazada(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'rechazada',
            'fecha_inicio' => $this->faker->dateTimeBetween('-1 week', 'now'),
            'hora_entrada' => null,
            'hora_salida' => null,
            'user_entrada_id' => null,
            'user_salida_id' => null,
            'observaciones' => $this->faker->randomElement([
                'CI no coincide',
                'Código incorrecto',
                'Fuera de horario autorizado',
                'Residente no confirma la visita'
            ]),
        ]);
    }
}