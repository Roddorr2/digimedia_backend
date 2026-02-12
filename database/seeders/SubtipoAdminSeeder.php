<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SubtipoAdmin;

class SubtipoAdminSeeder extends Seeder
{
    public function run(): void
    {
        $subtipos = [
            ['description' => 'superadmin', 'hierarchy' => 100],
            ['description' => 'desarrollador', 'hierarchy' => 90],
            ['description' => 'comun', 'hierarchy' => 80],
        ];

        foreach ($subtipos as $subtipo) {
            SubtipoAdmin::updateOrCreate(
                ['description' => $subtipo['description']],
                $subtipo
            );
        }
    }
}
