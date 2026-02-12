<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Rol;
use App\Models\Empleado;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Crear o actualizar usuario staging
        $user = User::updateOrCreate(
            ['email' => 'staging_pruebas@digimedia-marketing.com'],
            [
                'name' => 'Staging',
                'password' => Hash::make('F@Q#n64QuJm%'),
            ]
        );

        // Intentar asignar rol administrador al empleado asociado (si existe)
        $rolAdmin = Rol::where('nombre', 'administrador')->first();

        if ($rolAdmin) {
            $empleado = Empleado::where('id_user', $user->id)->first();
            if ($empleado) {
                $empleado->update(['id_rol' => $rolAdmin->id_rol]);
            }
        } else {
            $this->command->warn('Rol administrador no existe.');
        }
    }
}
