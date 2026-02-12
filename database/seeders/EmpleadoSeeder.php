<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Empleado;
use App\Models\Rol;
use App\Models\SubtipoAdmin;

class EmpleadoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $empleados = [
            [
                'nombre' => 'Kevin Esteeven',
                'apellido' => 'Parimango Gomez',
                'email' => 'keving.kpg@gmail.com',
                'dni' => '72899618',
                'telefono' => '929686486',
                'id_rol' => 'administrador',
                'id_subtipo_admin' => 2
            ],
            [
                'nombre' => 'Jose Luis',
                'apellido' => 'Gutierrez',
                'email' => 'joseluisjlgd123@gmail.com',
                'dni' => '75308553',
                'telefono' => '927249150',
                'id_rol' => 'administrador',
                'id_subtipo_admin' => 2
            ],
            [
                'nombre' => 'Juan Carlos',
                'apellido' => 'Molina Orrego',
                'email' => 'tmlighting@hotmail.com',
                'dni' => '10299639',
                'telefono' => '936910425',
                'id_rol' => 'administrador',
                'id_subtipo_admin' => 1
            ],
            [
                'nombre' => 'Krizzia Martina',
                'apellido' => 'Saavedra Navarro',
                'email' => 'krizzia_saavedra201@hotmail.com',
                'dni' => '72851260',
                'telefono' => '938405611',
                'id_rol' => 'administrador',
                'id_subtipo_admin' => 3
            ],
            [
                'nombre' => 'Gonzalo Fernando',
                'apellido' => 'Gallardo Huertas',
                'email' => 'gogozgallardo22@gmail.com',
                'dni' => '73068386',
                'telefono' => '924783666',
                'id_rol' => 'administrador',
                'id_subtipo_admin' => 3
            ],
            [
                'nombre' => 'Diego Arturo',
                'apellido' => 'Torres Pacherres',
                'email' => 'diego_torres_11@hotmail.com',
                'dni' => '48314547',
                'telefono' => '986377441',
                'id_rol' => 'administrador',
                'id_subtipo_admin' => 3
            ],
            [
                'nombre' => 'Marco Andres',
                'apellido' => 'Herrera Albites',
                'email' => 'marcoandresha@gmail.com',
                'dni' => '75550462',
                'telefono' => '942135168',
                'id_rol' => 'administrador',
                'id_subtipo_admin' => 3
            ],
        ];

        // Map numeric subtipo IDs (original fixtures) to descriptions
        $subtipoMap = [
            1 => 'superadmin',
            2 => 'desarrollador',
            3 => 'comun',
        ];

        foreach ($empleados as $data) {
            $email = $data['email'];
            $name = $data['nombre'] . ' ' . $data['apellido'];

            // Crear o obtener usuario asociado (no sobreescribir password si ya existe)
            $user = User::firstOrCreate(
                ['email' => $email],
                ['name' => $name, 'password' => Hash::make(Str::random(24))]
            );

            // Asegurar nombre actualizado
            if ($user->name !== $name) {
                $user->update(['name' => $name]);
            }

            // Obtener rol por nombre (no crear)
            $rol = null;
            if (!empty($data['id_rol'])) {
                $rol = Rol::where('nombre', $data['id_rol'])->first();
            }

            // Obtener subtipo admin por mapping (no crear)
            $subtipo = null;
            if (!empty($data['id_subtipo_admin'])) {
                $descripcion = $subtipoMap[$data['id_subtipo_admin']] ?? null;
                if ($descripcion) {
                    $subtipo = SubtipoAdmin::where('description', $descripcion)->first();
                }
            }

            // Crear o actualizar empleado usando email como campo único
            $empleadoAttrs = [
                'nombre' => $data['nombre'],
                'apellido' => $data['apellido'],
                'dni' => $data['dni'],
                'telefono' => $data['telefono'],
                'id_user' => $user->id,
                'id_rol' => $rol ? $rol->id_rol : null,
                'id_subtipo_admin' => $subtipo ? $subtipo->id : null,
            ];

            Empleado::updateOrCreate(
                ['email' => $email],
                $empleadoAttrs
            );
        }
    }
}
