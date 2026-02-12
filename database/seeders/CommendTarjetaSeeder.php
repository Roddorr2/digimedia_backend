<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CommendTarjeta;


class CommendTarjetaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $commend_tarjetas = [
            [
                'titulo' => "Consejos para Elegir el Letrero Perfecto",
                'texto1' => "Opta por colores que reflejen la personalidad de tu bar.",
                'texto2' => "Elige un diseño legible y atractivo.",
                'texto3' => "Considera el lugar de instalación para maximizar su impacto.",
            ],
        ];

        foreach ($commend_tarjetas as $ct) {
            CommendTarjeta::updateOrCreate(
                ['titulo' => $ct['titulo']],
                [
                    'texto1' => $ct['texto1'] ?? null,
                    'texto2' => $ct['texto2'] ?? null,
                    'texto3' => $ct['texto3'] ?? null,
                    'texto4' => $ct['texto4'] ?? null,
                    'texto5' => $ct['texto5'] ?? null,
                ]
            );
        }
    }
}
