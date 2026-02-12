<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\WatModal;
use App\Models\modalservicios;

class WatModalSeeder extends Seeder
{

    public function run(): void
    {
        $wat_modals = [
            [
                'estado' => 0,
                'error' => '',
                'id_modalservicio' => 1,
                'number_message' => 1,
                'fecha' => now(),
            ],
            [
                'estado' => 0,
                'error' => '',
                'id_modalservicio' => 1,
                'number_message' => 2,
                'fecha' => now(),
            ],

            [
                'estado' => 1,
                'error' => '',
                'id_modalservicio' => 2,
                'number_message' => 1,
                'fecha' => now(),
            ],
            [
                'estado' => 1,
                'error' => '',
                'id_modalservicio' => 2,
                'number_message' => 2,
                'fecha' => now(),
            ],

            [
                'estado' => 0,
                'error' => '',
                'id_modalservicio' => 3,
                'number_message' => 1,
                'fecha' => now(),
            ],
            [
                'estado' => 0,
                'error' => '',
                'id_modalservicio' => 3,
                'number_message' => 2,
                'fecha' => now(),
            ],

            [
                'estado' => 1,
                'error' => '',
                'id_modalservicio' => 4,
                'number_message' => 1,
                'fecha' => now(),
            ],
            [
                'estado' => 1,
                'error' => '',
                'id_modalservicio' => 4,
                'number_message' => 2,
                'fecha' => now(),
            ],
        ];

        // Map fixture modalservicio ids to correo used in ModalservicioSeeder
        $modalMap = [
            1 => 'ana@gmail.com',
            2 => 'lorena@gmail.com',
            3 => 'jose@gmail.com',
            4 => 'luisito@gmail.com',
        ];

        foreach ($wat_modals as $wm) {
            $correo = $modalMap[$wm['id_modalservicio']] ?? null;
            $modal = $correo ? modalservicios::where('correo', $correo)->first() : null;
            if (!$modal) {
                continue;
            }

            WatModal::updateOrCreate(
                [
                    'id_modalservicio' => $modal->id_modalservicio,
                    'number_message' => $wm['number_message'],
                ],
                [
                    'estado' => $wm['estado'],
                    'error' => $wm['error'],
                    'fecha' => $wm['fecha'],
                ]
            );
        }
    }
}
