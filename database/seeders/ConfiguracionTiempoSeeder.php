<?php

namespace Database\Seeders;

use App\Models\ConfiguracionTiempo;
use Illuminate\Database\Seeder;

class ConfiguracionTiempoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tiempos = [
            ['numero_mensaje' => 1, 'unidad_tiempo' => 'minutos', 'valor_tiempo' => 0],
            ['numero_mensaje' => 2, 'unidad_tiempo' => 'minutos', 'valor_tiempo' => 30],
            ['numero_mensaje' => 3, 'unidad_tiempo' => 'minutos', 'valor_tiempo' => 60],
        ];

        $tipos = ['email', 'whatsapp'];

        for ($servicio = 1; $servicio <= 4; $servicio++) {
            foreach ($tipos as $tipo) {
                foreach ($tiempos as $tiempo) {
                    ConfiguracionTiempo::updateOrCreate(
                        [
                            'id_servicio' => $servicio,
                            'tipo' => $tipo,
                            'numero_mensaje' => $tiempo['numero_mensaje'],
                        ],
                        [
                            'unidad_tiempo' => $tiempo['unidad_tiempo'],
                            'valor_tiempo' => $tiempo['valor_tiempo'],
                        ]
                    );
                }
            }
        }
    }
}
