<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $PASSWORD = 'F@Q#n64QuJm%';
        $credentials = [
            ['name' => 'Staging',                            'email' => 'staging_pruebas@digimedia-marketing.com'],
            ['name' => 'Kevin Esteeven Parimango Gomez',     'email' => 'keving.kpg@gmail.com'],
            ['name' => 'Jose Luis Gutierrez',                'email' => 'joseluisjlgd123@gmail.com'],
            ['name' => 'Juan Carlos Molina Orrego',          'email' => 'tmlighting@hotmail.com'],
            ['name' => 'Krizzia Martina Saavedra Navarro',   'email' => 'krizzia_saavedra201@hotmail.com'],
            ['name' => 'Gonzalo Fernando Gallardo Huertas',  'email' => 'gogozgallardo22@gmail.com'],
            ['name' => 'Diego Arturo Torres Pacherres',      'email' => 'diego_torres_11@hotmail.com'],
            ['name' => 'Marco Andres Herrera Albites',       'email' => 'marcoandresha@gmail.com'],
        ];

        foreach ($credentials as $data) {
            User::updateOrCreate(
                ['email' => $data['email']],
                ['name' => $data['name'], 'password' => Hash::make($PASSWORD)]
            );
        }
    }
}
