<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $this->call([
            UserSeeder::class,
            BlogHeaderSeeder::class,
            BlogFooterSeeder::class,
            CommendTarjetaSeeder::class,
            BlogBodySeeder::class,
            TarjetaSeeder::class,
            BlogSeeder::class,
            BlogAuditoriaSeeder::class,
            ServicioSeeder::class,
            SubservicioSeeder::class,
            ContactanosSeeder::class,
            ReclamacionSeeder::class,
            ModalservicioSeeder::class,
            RolSeeder::class,
            SubtipoAdminSeeder::class,
            EmpleadoSeeder::class,
            CardSeeder::class,
            MailModalSeeder::class,
            WatModalSeeder::class,
            CampaniaWhatsAppSeeder::class,
            PermisosSeeder::class,
            PlantillasEmailSeeder::class,
            PlantillasWhatsappSeeder::class,
            PopupConfigSeeder::class,
            ConfiguracionTiempoSeeder::class
        ]);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
