<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubtipoAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subtipos = [
            ['description' => 'superadmin', 'hierarchy' => 100],
            ['description' => 'desarrollador', 'hierarchy' => 90],
            ['description' => 'comun', 'hierarchy' => 80],
        ];

        DB::table('subtipo_admins')->insert($subtipos);
    }
}
