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
            // Core: roles and related
            RolSeeder::class,
            SubtipoAdminSeeder::class,
            PermisosSeeder::class,

            // Users and employees
            EmpleadoSeeder::class,
            UserSeeder::class,

            // Modules and other seeders
            BlogHeaderSeeder::class,
            BlogFooterSeeder::class,
            BlogBodySeeder::class,
            BlogSeeder::class,
            ServicioSeeder::class,
            ModalservicioSeeder::class,
            TarjetaSeeder::class,
            CommendTarjetaSeeder::class,
            ContactanosSeeder::class,
            ReclamacionSeeder::class,
            CardSeeder::class,
            MailModalSeeder::class,
            WatModalSeeder::class,
        ]);
    }
}
