<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CampaniaWhatsApp>
 */
class CampaniaWhatsAppFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_servicio' => fake()->numberBetween(1, 4),
            'parrafo' => fake()->paragraph(),
            'imagen_url' => fake()->imageUrl(),
            'estado' => $estado = fake()->randomElement(['pendiente', 'en_proceso', 'completada', 'cancelada', 'error']),
            'total_destinatarios' => $total = fake()->numberBetween(10, 100),
            'fecha_inicio' => fake()->dateTimeBetween('-1 month', 'now'),
            'fecha_fin' => fake()->optional()->dateTimeBetween('now', '+1 month'),
        ];
    }

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterMaking(function (CampaniaWhatsApp $campania) {
            $total = $campania->total_destinatarios;

            if ($campania->estado === 'pendiente') {
                $campania->envios_pendientes = $total;
                $campania->envios_exitosos = 0;
                $campania->envios_fallidos = 0;
            } elseif ($campania->estado === 'completada') {
                $exitosos = fake()->numberBetween(0, $total);
                $campania->envios_exitosos = $exitosos;
                $campania->envios_fallidos = $total - $exitosos;
                $campania->envios_pendientes = 0;
            } else {  // en_proceso o cancelada o error
                $procesados = fake()->numberBetween(0, $total - 1); 
                $exitosos = fake()->numberBetween(0, $procesados);
                $campania->envios_exitosos = $exitosos;
                $campania->envios_fallidos = $procesados - $exitosos;
                $campania->envios_pendientes = $total - $procesados;
            }
        });
    }
}
