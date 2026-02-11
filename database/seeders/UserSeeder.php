<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Staging',
                'email' => 'staging_pruebas@digimedia-marketing.com',
                'password' => Hash::make('F@Q#n64QuJm%'),
            ],
            

        ];

        DB::table('users')->insert($users);
    }
}
