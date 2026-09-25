<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Contactanos;

class ContactanosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $contactos = [
            [
                'nombre' => 'Juan Pérez EJEMPLO',
                'email' => 'juan@gmail.com',
                'numero' => '987654321',
                'mensaje' => 'Me interesa obtener más información sobre sus servicios.',
                'estado' => 0,
            ],
            [
                'nombre' => 'Juan Rodriguez EJEMPLO',
                'email' => 'juan2@gmail.com',
                'numero' => '987654322',
                'mensaje' => 'Me interesa obtener más información sobre sus servicios.',
                'estado' => 1,
            ],
            [
                'nombre' => 'Juan Nuñez EJEMPLO',
                'email' => 'juan3@gmail.com',
                'numero' => '987654323',
                'mensaje' => 'Me interesa obtener más información sobre sus servicios.',
                'estado' => 1,
            ]
        ];

        foreach ($contactos as $c) {
            Contactanos::updateOrCreate(
                ['email' => $c['email'], 'mensaje' => $c['mensaje']],
                [
                    'nombre' => $c['nombre'],
                    'numero' => $c['numero'],
                    'estado' => $c['estado'],
                    'fecha' => $c['fecha'] ?? now(),
                ]
            );
        }
    }
}
