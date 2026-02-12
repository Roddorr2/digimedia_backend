<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Models\libroreclamacion;
use App\Models\servicios;


class ReclamacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $reclamaciones = [
            [
                'nombre' => 'Carlos EJEMPLO',
                'apellido' => 'Mendoza Vargas',
                'documento' => 'DNI',
                'numeroDocumento' => '45781236',
                'email' => 'carlos.mendoza@gmail.com',
                'celular' => '987654321',
                'direccion' => 'Av. Los Álamos 453, Dpto 302',
                'distrito' => 'San Borja',
                'ciudad' => 'Lima',
                'tipoReclamo' => 'RECLAMO',
                'id_servicio' => 1,
                'reclamoPerson' => 'Reclamo por el servicio el dia 15/2/2022',
                'checkReclamoForm' => true,
                'aceptaPoliticaPrivacidad' => true,
                'fechaIncidente' => Carbon::now(),
                'fechaReclamo' => Carbon::now(),
                'estadoReclamo' => 'PENDIENTE'
            ],
            [
                'nombre' => 'María EJEMPLO',
                'apellido' => 'Gómez Rodríguez',
                'documento' => 'CE',
                'numeroDocumento' => 'CE001234567',
                'email' => 'maria.gomez@outlook.com',
                'celular' => '912345678',
                'direccion' => 'Jr. Huancavelica 287',
                'distrito' => 'Miraflores',
                'ciudad' => 'Lima',
                'tipoReclamo' => 'QUEJA',
                'id_servicio' => 2,
                'reclamoPerson' => 'Servicio malo',
                'checkReclamoForm' => true,
                'aceptaPoliticaPrivacidad' => true,
                'fechaIncidente' => Carbon::now(),
                'fechaReclamo' => Carbon::now(),
                'estadoReclamo' => 'PENDIENTE'
            ],
            [
                'nombre' => 'José Luis EJEMPLO',
                'apellido' => 'Paredes Santillán',
                'documento' => 'RUC',
                'numeroDocumento' => '20512345678',
                'email' => 'administracion@empresaperu.com',
                'celular' => '976543210',
                'direccion' => 'Av. Javier Prado Este 1492',
                'distrito' => 'La Molina',
                'ciudad' => 'Lima',
                'tipoReclamo' => 'RECLAMO',
                'id_servicio' => 3,
                'reclamoPerson' => 'Necesito un reembolso',
                'checkReclamoForm' => true,
                'aceptaPoliticaPrivacidad' => true,
                'fechaIncidente' => Carbon::now(),
                'fechaReclamo' => Carbon::now(),
                'estadoReclamo' => 'PENDIENTE'
            ],
        ];

        // Map fixture service ids to service names
        $servicioMap = [
            1 => 'Diseño Web y Desarrollo Web',
            2 => 'Gestión de Redes Sociales',
            3 => 'Marketing y Gestión Digital',
        ];

        foreach ($reclamaciones as $r) {
            $servicioName = $servicioMap[$r['id_servicio']] ?? null;
            $servicio = $servicioName ? servicios::where('nombre', $servicioName)->first() : null;

            libroreclamacion::updateOrCreate(
                ['email' => $r['email'], 'numeroDocumento' => $r['numeroDocumento']],
                [
                    'nombre' => $r['nombre'],
                    'apellido' => $r['apellido'],
                    'documento' => $r['documento'],
                    'celular' => $r['celular'],
                    'direccion' => $r['direccion'],
                    'distrito' => $r['distrito'],
                    'ciudad' => $r['ciudad'],
                    'tipoReclamo' => $r['tipoReclamo'],
                    'id_servicio' => $servicio ? $servicio->id_servicio : null,
                    'reclamoPerson' => $r['reclamoPerson'],
                    'checkReclamoForm' => $r['checkReclamoForm'],
                    'aceptaPoliticaPrivacidad' => $r['aceptaPoliticaPrivacidad'],
                    'fechaIncidente' => $r['fechaIncidente'],
                    'fechaReclamo' => $r['fechaReclamo'],
                    'estadoReclamo' => $r['estadoReclamo'],
                ]
            );
        }
    }
}
