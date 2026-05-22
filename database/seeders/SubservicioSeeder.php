<?php

namespace Database\Seeders;

use App\Models\Subservicio;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubservicioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Desactivar foreign keys para evitar errores
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        $subservicios = [
            // Diseño Web y Desarrollo Web (id_servicio = 1)
            ['id_servicio' => 1, 'nombre' => 'Experiencia de Usuario y Diseño', 'slug' => 'experiencia-usuario'],
            ['id_servicio' => 1, 'nombre' => 'Optimización SEO', 'slug' => 'seo'],
            ['id_servicio' => 1, 'nombre' => 'Desarrollo Responsive', 'slug' => 'desarrollo-responsive'],
            ['id_servicio' => 1, 'nombre' => 'Integraciones Digitales', 'slug' => 'integraciones-digitales'],

            // Gestión de Redes Sociales (id_servicio = 2)
            ['id_servicio' => 2, 'nombre' => 'Estrategia de Contenido', 'slug' => 'estrategia-de-contenido'],
            ['id_servicio' => 2, 'nombre' => 'Social Ads & Performance', 'slug' => 'diseno-pautas'],
            ['id_servicio' => 2, 'nombre' => 'Producción Audiovisual', 'slug' => 'produccion-pautas'],
            ['id_servicio' => 2, 'nombre' => 'Diseño UX y UI', 'slug' => 'ui'],

            // Marketing y Gestión Digital (id_servicio = 3)
            ['id_servicio' => 3, 'nombre' => 'Análisis y Benchmarking', 'slug' => 'analisis-y-benchmarking'],
            ['id_servicio' => 3, 'nombre' => 'Campañas Digitales', 'slug' => 'publicidad-digital'],
            ['id_servicio' => 3, 'nombre' => 'Identidad Visual y Corporativa', 'slug' => 'identidad-visual'],
            ['id_servicio' => 3, 'nombre' => 'Análisis de Métricas', 'slug' => 'monitoreo-y-reporting'],

            // Branding y Diseño (id_servicio = 4)
            ['id_servicio' => 4, 'nombre' => 'Desarrollo de Brief', 'slug' => 'desarrollo-briefs'],
            ['id_servicio' => 4, 'nombre' => 'Planificación Estratégica', 'slug' => 'planificacion-estrategica'],
            ['id_servicio' => 4, 'nombre' => 'Diseño de Logo', 'slug' => 'naming-logo-slogan'],
            ['id_servicio' => 4, 'nombre' => 'Manual de Marca', 'slug' => 'manual-marca'],
        ];

        $creados = 0;
        $actualizados = 0;

        foreach ($subservicios as $subservicio) {
            $existing = Subservicio::where('slug', $subservicio['slug'])->first();
            
            if ($existing) {
                // Actualizar existente (idempotente)
                $existing->update([
                    'id_servicio' => $subservicio['id_servicio'],
                    'nombre' => $subservicio['nombre'],
                ]);
                $actualizados++;
                $this->command->info("Actualizado: {$subservicio['nombre']} (slug: {$subservicio['slug']})");
            } else {
                // Crear nuevo
                Subservicio::create($subservicio);
                $creados++;
                $this->command->info("Creado: {$subservicio['nombre']} (slug: {$subservicio['slug']})");
            }
        }

        // Reactivar foreign keys
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $this->command->info("\nSubservicioSeeder completado!");
        $this->command->info("Creados: {$creados} | Actualizados: {$actualizados} | Total: " . Subservicio::count());
    }
}