<?php

namespace Database\Seeders;

use App\Models\servicios;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServicioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Desactivar foreign keys para evitar errores
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        $servicios = [
            [
                'id_servicio' => 1,
                'nombre' => 'Diseño Web y Desarrollo Web', 
                'descripcion' => 'Ofrecemos diseño y desarrollo web para ayudar a tu negocio a destacar en línea. Creamos sitios atractivos y funcionales que reflejan tu marca y mejoran la experiencia del usuario.'
            ],
            [
                'id_servicio' => 2,
                'nombre' => 'Gestión de Redes Sociales', 
                'descripcion' => 'Te ayudamos a construir una voz única para tu marca, interactúa de manera auténtica con tu audiencia y transforma tus seguidores en clientes fieles.'
            ],
            [
                'id_servicio' => 3,
                'nombre' => 'Marketing y Gestión Digital', 
                'descripcion' => 'Creamos campañas que no solo se ven, sino que se sienten. Potenciamos tu presencia online con tácticas personalizadas, llevándote al siguiente nivel con resultados medibles y un impacto real. Tu éxito digital comienza aquí.'
            ],
            [
                'id_servicio' => 4,
                'nombre' => 'Branding y Diseño', 
                'descripcion' => 'Creamos marcas que hablan, emocionan y conectan. Desde una identidad visual memorable hasta mensajes que resuenan profundamente, hacemos que tu empresa sea tan única como inolvidable.'
            ],
        ];

        $creados = 0;
        $actualizados = 0;

        foreach ($servicios as $servicio) {
            $existing = servicios::find($servicio['id_servicio']);
            
            if ($existing) {
                // Actualizar existente
                $existing->update([
                    'nombre' => $servicio['nombre'],
                    'descripcion' => $servicio['descripcion'],
                ]);
                $actualizados++;
                $this->command->info("Actualizado: {$servicio['nombre']} (ID: {$servicio['id_servicio']})");
            } else {
                // Crear nuevo con ID específico
                servicios::create($servicio);
                $creados++;
                $this->command->info("Creado: {$servicio['nombre']} (ID: {$servicio['id_servicio']})");
            }
        }

        // Reactivar foreign keys
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $this->command->info("\nServicioSeeder completado!");
        $this->command->info("Creados: {$creados} | Actualizados: {$actualizados} | Total: " . servicios::count());
    }
}