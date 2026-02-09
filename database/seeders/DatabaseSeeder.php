<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
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
            PermisosSeeder::class
        ]);
    }
}
