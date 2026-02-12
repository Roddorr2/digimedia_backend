<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\modalservicios;
use App\Models\servicios;

class ModalservicioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modalServicios = [
            [
                'nombre' => 'Ana Torres EJEMPLO',
                'telefono' => '999384322',
                'correo' => 'ana@gmail.com',
                'id_servicio' => 1,
            ],
            [
                'nombre' => 'Lorena Rodriguez EJEMPLO',
                'telefono' => '999384322',
                'correo' => 'lorena@gmail.com',
                'id_servicio' => 2,
            ],
            [
                'nombre' => 'Jose Santos EJEMPLO',
                'telefono' => '999384322',
                'correo' => 'jose@gmail.com',
                'id_servicio' => 3,
            ],
            [
                'nombre' => 'Luis Romero EJEMPLO',
                'telefono' => '999384322',
                'correo' => 'luisito@gmail.com',
                'id_servicio' => 4,
            ],
        ];

        // Map fixture service ids to service names (order matches ServicioSeeder)
        $servicioMap = [
            1 => 'Diseño Web y Desarrollo Web',
            2 => 'Gestión de Redes Sociales',
            3 => 'Marketing y Gestión Digital',
            4 => 'Branding y Diseño',
        ];

        foreach ($modalServicios as $modal) {
            $servicioName = $servicioMap[$modal['id_servicio']] ?? null;
            $servicio = $servicioName ? servicios::where('nombre', $servicioName)->first() : null;

            modalservicios::updateOrCreate(
                ['correo' => $modal['correo']],
                [
                    'nombre' => $modal['nombre'],
                    'telefono' => $modal['telefono'],
                    'id_servicio' => $servicio ? $servicio->id_servicio : null,
                ]
            );
        }
    }
}
