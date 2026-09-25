<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EmailModal;
use App\Models\modalservicios;

class MailModalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mail_modals = [
            [
                'estado' => 1,
                'error' => '',
                'id_modalservicio' => 1,
                'number_message' => 1,
                'fecha' => now(),
            ],
            [
                'estado' => 1,
                'error' => '',
                'id_modalservicio' => 1,
                'number_message' => 2,
                'fecha' => now(),
            ],
            [
                'estado' => 1,
                'error' => '',
                'id_modalservicio' => 1,
                'number_message' => 3,
                'fecha' => now(),
            ],

            [
                'estado' => 1,
                'error' => 'Error de envio, no existe el correo',
                'id_modalservicio' => 2,
                'number_message' => 1,
                'fecha' => now(),
            ],
            [
                'estado' => 0,
                'error' => '',
                'id_modalservicio' => 2,
                'number_message' => 2,
                'fecha' => now(),
            ],
            [
                'estado' => 0,
                'error' => '',
                'id_modalservicio' => 2,
                'number_message' => 3,
                'fecha' => now(),
            ],

            [
                'estado' => 1,
                'error' => '',
                'id_modalservicio' => 3,
                'number_message' => 1,
                'fecha' => now(),
            ],
            [
                'estado' => 1,
                'error' => '',
                'id_modalservicio' => 3,
                'number_message' => 2,
                'fecha' => now(),
            ],
            [
                'estado' => 1,
                'error' => '',
                'id_modalservicio' => 3,
                'number_message' => 3,
                'fecha' => now(),
            ],

            [
                'estado' => 1,
                'error' => 'Error de envio, no existe el correo',
                'id_modalservicio' => 4,
                'number_message' => 1,
                'fecha' => now(),
            ],
            [
                'estado' => 0,
                'error' => '',
                'id_modalservicio' => 4,
                'number_message' => 2,
                'fecha' => now(),
            ],
            [
                'estado' => 0,
                'error' => '',
                'id_modalservicio' => 4,
                'number_message' => 3,
                'fecha' => now(),
            ],

        ];

        $modalMap = [
            1 => 'ana@gmail.com',
            2 => 'lorena@gmail.com',
            3 => 'jose@gmail.com',
            4 => 'luisito@gmail.com',
        ];

        foreach ($mail_modals as $mm) {
            $correo = $modalMap[$mm['id_modalservicio']] ?? null;
            $modal = $correo ? modalservicios::where('correo', $correo)->first() : null;
            if (!$modal) {
                continue;
            }

            EmailModal::updateOrCreate(
                [
                    'id_modalservicio' => $modal->id_modalservicio,
                    'number_message' => $mm['number_message'],
                ],
                [
                    'estado' => $mm['estado'],
                    'error' => $mm['error'],
                    'fecha' => $mm['fecha'],
                ]
            );
        }
    }
}
