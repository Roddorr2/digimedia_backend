<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\modalservicios>
 */
class modalserviciosFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre' => fake()->name(),
            'telefono' => '9' . fake()->numerify('########'),
            'correo' => fake()->unique()->email(),
            'id_servicio' => fake()->numberBetween(1, 4),
            'estado' => 1,
            'fecha' => fake()->dateTimeBetween('-6 months', 'now'),  
        ];
    }
}
