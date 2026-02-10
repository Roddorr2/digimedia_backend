<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\CampaniaWhatsApp;

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
            'estado' => fake()->randomElement(['pendiente', 'en_proceso', 'completada', 'error']),
            'total_destinatarios' => fake()->numberBetween(10, 100),
            'fecha_inicio' => fake()->dateTimeBetween('-1 month', 'now'),
            'fecha_fin' => null,
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function (CampaniaWhatsApp $campania) {
            $total = $campania->total_destinatarios;
            $estado = $campania->estado;

            if ($estado === 'pendiente') {
                [$exitosos, $fallidos, $pendientes, $fechaFin] = [0, 0, $total, null];
            } elseif ($estado === 'en_proceso') {
                $procesados = fake()->numberBetween(1, $total - 1);
                $exitosos = fake()->numberBetween(0, $procesados);
                [$fallidos, $pendientes, $fechaFin] = [$procesados - $exitosos, $total - $procesados, null];
            } elseif ($estado === 'completada') {
                $exitosos = fake()->numberBetween(0, $total);
                [$fallidos, $pendientes] = [$total - $exitosos, 0];
                $fechaFin = $this->generarFechaFin($campania->fecha_inicio, '+2 hours');
            } else {
                $maxProcesados = fake()->boolean(80) ? (int)($total * 0.3) : $total - 1;
                $procesados = fake()->numberBetween(0, max(1, $maxProcesados));
                $exitosos = fake()->numberBetween(0, $procesados);
                [$fallidos, $pendientes] = [$procesados - $exitosos, $total - $procesados];
                $fechaFin = $this->generarFechaFin($campania->fecha_inicio, '+1 hour');
            }

            $campania->envios_exitosos = $exitosos;
            $campania->envios_fallidos = $fallidos;
            $campania->envios_pendientes = $pendientes;
            $campania->fecha_fin = $fechaFin;
        });
    }

    private function generarFechaFin($fechaInicio, $offset)
    {
        return fake()->dateTimeBetween($fechaInicio, $fechaInicio->format('Y-m-d H:i:s') . ' ' . $offset);
    }
}
