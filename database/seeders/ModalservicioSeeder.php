<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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

        DB::table('modalservicios')->insert($modalServicios);
    }
}
