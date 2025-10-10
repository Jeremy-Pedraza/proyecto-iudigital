<?php

namespace Database\Factories;

use App\Models\Parada;
use App\Models\Ruta;
use App\Models\Cliente;
use Illuminate\Database\Eloquent\Factories\Factory;

class ParadaFactory extends Factory
{
    protected $model = Parada::class;

    public function definition(): array
    {
        return [
            'ruta_id' => Ruta::factory(),
            'cliente_id' => Cliente::factory(),
            'orden' => 1,
            'estado' => $this->faker->randomElement(['pendiente', 'completada', 'no_atendida']),
            'hora_estimada_llegada' => $this->faker->dateTimeBetween('+1 hour', '+8 hours'),
            'hora_real_llegada' => $this->faker->optional(0.6)->dateTimeBetween('+1 hour', '+8 hours'),
            'hora_salida' => $this->faker->optional(0.6)->dateTimeBetween('+2 hours', '+9 hours'),
            'distancia_desde_anterior_km' => $this->faker->randomFloat(1, 0.5, 25),
            'tiempo_desde_anterior_min' => $this->faker->numberBetween(5, 60),
            'duracion_real_min' => $this->faker->optional(0.6)->numberBetween(10, 45),
            'notas' => $this->faker->optional(0.3)->sentence(),
            'motivo_no_atencion' => $this->faker->optional(0.1)->randomElement([
                'Cliente cerrado',
                'No había personal',
                'Rechazó visita',
                'Dirección incorrecta'
            ]),
            'lat_checkin' => $this->faker->optional(0.7)->latitude(),
            'lng_checkin' => $this->faker->optional(0.7)->longitude(),
        ];
    }

    /**
     * Estado: Pendiente
     */
    public function pendiente(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'pendiente',
            'hora_real_llegada' => null,
            'hora_salida' => null,
            'lat_checkin' => null,
            'lng_checkin' => null,
        ]);
    }

    /**
     * Estado: Completada
     */
    public function completada(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'completada',
            'hora_real_llegada' => $this->faker->dateTimeBetween('+1 hour', '+8 hours'),
            'hora_salida' => $this->faker->dateTimeBetween('+2 hours', '+9 hours'),
            'duracion_real_min' => $this->faker->numberBetween(15, 60),
            'lat_checkin' => $this->faker->latitude(),
            'lng_checkin' => $this->faker->longitude(),
        ]);
    }

    /**
     * Estado: No atendida
     */
    public function noAtendida(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'no_atendida',
            'motivo_no_atencion' => $this->faker->randomElement([
                'Cliente cerrado',
                'No había personal',
                'Rechazó visita',
                'Dirección incorrecta'
            ]),
            'hora_real_llegada' => $this->faker->dateTimeBetween('+1 hour', '+8 hours'),
            'hora_salida' => $this->faker->dateTimeBetween('+2 hours', '+9 hours'),
        ]);
    }
}
