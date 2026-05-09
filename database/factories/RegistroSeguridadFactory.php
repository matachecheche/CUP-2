<?php

namespace Database\Factories;

use App\Models\RegistroSeguridad;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RegistroSeguridad>
 */
class RegistroSeguridadFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = RegistroSeguridad::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $tipos = ['ronda', 'incidente', 'reporte'];
        $origenes = ['seguridad', 'residente'];
        $prioridades = ['baja', 'media', 'alta'];
        $estados = ['pendiente', 'en_revision', 'resuelto'];
        
        $tipo = $this->faker->randomElement($tipos);
        $origen = $this->faker->randomElement($origenes);
        
        // Si es residente, solo puede ser incidente
        if ($origen === 'residente') {
            $tipo = 'incidente';
        }
        
        $estado = $this->faker->randomElement($estados);
        $fechaHora = $this->faker->dateTimeBetween('-30 days', 'now');
        
        // Si está resuelto, necesita fecha de resolución
        $fechaResolucion = null;
        $resueltoPor = null;
        
        if ($estado === 'resuelto') {
            $fechaResolucion = $this->faker->dateTimeBetween($fechaHora, 'now');
            // Solo usuarios con rol de seguridad pueden resolver
            $resueltoPor = User::whereHas('roles', function($q) {
                $q->where('name', 'Personal de Seguridad');
            })->inRandomOrder()->first()?->id;
        }

        return [
            'user_id' => User::inRandomOrder()->first()?->id ?? 1,
            'tipo' => $tipo,
            'origen' => $origen,
            'fecha_hora' => $fechaHora,
            'ubicacion' => $this->generarUbicacion(),
            'descripcion' => $this->generarDescripcion($tipo, $origen),
            'prioridad' => $this->faker->randomElement($prioridades),
            'estado' => $estado,
            'observaciones' => $this->faker->optional(0.6)->sentence(),
            'resuelto_por' => $resueltoPor,
            'fecha_resolucion' => $fechaResolucion,
        ];
    }

    /**
     * Generar ubicaciones realistas para un condominio
     */
    private function generarUbicacion(): string
    {
        $ubicaciones = [
            // Áreas comunes
            'Entrada principal',
            'Recepción',
            'Lobby',
            'Parqueadero nivel 1',
            'Parqueadero nivel 2',
            'Parqueadero subterráneo',
            'Área de piscina',
            'Gimnasio',
            'Salón comunal',
            'Terraza BBQ',
            'Jardín central',
            'Área de juegos infantiles',
            'Lavandería comunal',
            'Cuarto de basuras',
            'Azotea',
            'Escaleras de emergencia',
            'Ascensor A',
            'Ascensor B',
            
            // Pisos y unidades
            'Piso 1 - Pasillo',
            'Piso 2 - Pasillo',
            'Piso 3 - Pasillo',
            'Piso 4 - Pasillo',
            'Piso 5 - Pasillo',
            'Unidad 101',
            'Unidad 201',
            'Unidad 301',
            'Unidad 401',
            'Unidad 501',
            'Unidad 102',
            'Unidad 202',
            'Unidad 302',
            
            // Áreas externas
            'Portería',
            'Garita de seguridad',
            'Zona de carga y descarga',
            'Área de visitantes',
            'Estacionamiento de visitantes',
            'Perímetro exterior',
            'Entrada vehicular',
            'Entrada peatonal'
        ];
        
        return $this->faker->randomElement($ubicaciones);
    }

    /**
     * Generar descripciones realistas según el tipo y origen
     */
    private function generarDescripcion(string $tipo, string $origen): string
    {
        if ($origen === 'residente') {
            // Descripciones típicas de residentes
            $descripciones = [
                'Ruido excesivo en unidad vecina después de las 22:00',
                'Luz del pasillo fundida desde hace varios días',
                'Goteo en el techo del parqueadero',
                'Ascensor con fallas mecánicas',
                'Persona sospechosa merodeando en el área común',
                'Vehículo desconocido parqueado en zona restringida',
                'Basura acumulada en área común',
                'Daños en la puerta del salón comunal',
                'Problema con el sistema de agua caliente',
                'Filtraciones en la pared del apartamento',
                'Música muy alta en área de piscina',
                'Mascotas sin correa en áreas comunes',
                'Visitante no autorizado en el edificio',
                'Daños en el mobiliario del lobby',
                'Problemas de iluminación en parqueadero'
            ];
        } else {
            // Descripciones según el tipo para personal de seguridad
            if ($tipo === 'ronda') {
                $descripciones = [
                    'Ronda nocturna completada - Todo en orden',
                    'Revisión de áreas comunes - Sin novedades',
                    'Inspección de parqueaderos - Normal',
                    'Ronda de madrugada - Edificio seguro',
                    'Verificación de puertas y ventanas - OK',
                    'Ronda perimetral - Sin anomalías',
                    'Revisión de sistema de iluminación - Funcionando',
                    'Control de accesos - Normal',
                    'Inspección de escaleras de emergencia - Libre',
                    'Ronda matutina - Sin incidentes'
                ];
            } elseif ($tipo === 'incidente') {
                $descripciones = [
                    'Intento de ingreso de persona no autorizada',
                    'Vehículo sospechoso en los alrededores',
                    'Alarma activada en área común',
                    'Conflicto entre residentes en lobby',
                    'Daños vandálicos en propiedad común',
                    'Intrusión en área restringida',
                    'Emergencia médica de residente',
                    'Fuga de agua en área común',
                    'Corte de energía eléctrica',
                    'Intento de robo en vehículo',
                    'Persona en estado de alicoramiento',
                    'Accidente menor en parqueadero',
                    'Activación falsa de alarma de incendio',
                    'Perdida de llaves por parte de residente',
                    'Problema con visitante agresivo'
                ];
            } else { // reporte
                $descripciones = [
                    'Reporte de turno diurno - Actividades normales',
                    'Reporte nocturno - 3 rondas completadas',
                    'Reporte de fin de semana - Sin incidentes',
                    'Reporte mensual de seguridad',
                    'Informe de visitantes del día',
                    'Reporte de mantenimiento requerido',
                    'Resumen de actividades del turno',
                    'Reporte de anomalías menores',
                    'Informe de entrega de turno',
                    'Reporte especial de eventos'
                ];
            }
        }
        
        return $this->faker->randomElement($descripciones);
    }

    /**
     * Crear registro de tipo ronda
     */
    public function ronda(): static
    {
        return $this->state(fn (array $attributes) => [
            'tipo' => 'ronda',
            'origen' => 'seguridad',
            'estado' => 'resuelto', // Las rondas siempre están completas
            'prioridad' => 'baja',
            'descripcion' => $this->faker->randomElement([
                'Ronda nocturna completada - Todo en orden',
                'Revisión de áreas comunes - Sin novedades',
                'Inspección de parqueaderos - Normal',
                'Ronda perimetral - Sin anomalías'
            ])
        ]);
    }

    /**
     * Crear registro de tipo incidente
     */
    public function incidente(): static
    {
        return $this->state(fn (array $attributes) => [
            'tipo' => 'incidente',
            'prioridad' => $this->faker->randomElement(['media', 'alta']),
            'estado' => $this->faker->randomElement(['pendiente', 'resuelto'])
        ]);
    }

    /**
     * Crear registro de tipo reporte
     */
    public function reporte(): static
    {
        return $this->state(fn (array $attributes) => [
            'tipo' => 'reporte',
            'origen' => 'seguridad',
            'estado' => 'resuelto', // Los reportes siempre están completos
            'prioridad' => 'baja'
        ]);
    }

    /**
     * Crear registro de residente
     */
    public function deResidente(): static
    {
        return $this->state(fn (array $attributes) => [
            'tipo' => 'incidente', // Residentes solo reportan incidentes
            'origen' => 'residente',
            'prioridad' => 'media', // Prioridad fija para residentes
            'estado' => $this->faker->randomElement(['pendiente', 'resuelto']),
            // Buscar usuario con rol de residente
            'user_id' => User::whereHas('roles', function($q) {
                $q->where('name', 'Residente');
            })->inRandomOrder()->first()?->id ?? 1
        ]);
    }

    /**
     * Crear registro de personal de seguridad
     */
    public function deSeguridad(): static
    {
        return $this->state(fn (array $attributes) => [
            'origen' => 'seguridad',
            // Buscar usuario con rol de personal de seguridad
            'user_id' => User::whereHas('roles', function($q) {
                $q->where('name', 'Personal de Seguridad');
            })->inRandomOrder()->first()?->id ?? 1
        ]);
    }

    /**
     * Crear registro pendiente
     */
    public function pendiente(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'pendiente',
            'resuelto_por' => null,
            'fecha_resolucion' => null
        ]);
    }

    /**
     * Crear registro resuelto
     */
    public function resuelto(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'resuelto',
            'fecha_resolucion' => $this->faker->dateTimeBetween($attributes['fecha_hora'] ?? '-1 day', 'now'),
            'resuelto_por' => User::whereHas('roles', function($q) {
                $q->where('name', 'Personal de Seguridad');
            })->inRandomOrder()->first()?->id
        ]);
    }

    /**
     * Crear registro de alta prioridad
     */
    public function altaPrioridad(): static
    {
        return $this->state(fn (array $attributes) => [
            'prioridad' => 'alta',
            'tipo' => 'incidente' // Solo incidentes pueden ser de alta prioridad
        ]);
    }

    /**
     * Crear registro de hoy
     */
    public function deHoy(): static
    {
        return $this->state(fn (array $attributes) => [
            'fecha_hora' => $this->faker->dateTimeBetween('today', 'now')
        ]);
    }
}