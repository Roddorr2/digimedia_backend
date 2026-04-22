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
        $subservicios = [
            // Diseño Web y Desarrollo Web
            ['id_servicio' => 1, 'nombre' => 'Experiencia de Usuario y Diseño', 'slug' => 'experiencia-de-usuario-y-diseno'],
            ['id_servicio' => 1, 'nombre' => 'Optimización SEO', 'slug' => 'optimizacion-seo'],
            ['id_servicio' => 1, 'nombre' => 'Desarrollo Responsive', 'slug' => 'desarrollo-responsive'],
            ['id_servicio' => 1, 'nombre' => 'Integraciones Digitales', 'slug' => 'integraciones-digitales'],

            // Gestión de Redes Sociales
            ['id_servicio' => 2, 'nombre' => 'Estrategia de Contenido', 'slug' => 'estrategia-de-contenido'],
            ['id_servicio' => 2, 'nombre' => 'Social Ads & Performance', 'slug' => 'social-ads-performance'],
            ['id_servicio' => 2, 'nombre' => 'Producción Audiovisual', 'slug' => 'produccion-audiovisual'],
            ['id_servicio' => 2, 'nombre' => 'Diseño UX y UI', 'slug' => 'diseno-ux-ui'],

            // Marketing y Gestión Digital
            ['id_servicio' => 3, 'nombre' => 'Análisis y Benchmarking', 'slug' => 'analisis-benchmarking'],
            ['id_servicio' => 3, 'nombre' => 'Campañas Digitales', 'slug' => 'campanas-digitales'],
            ['id_servicio' => 3, 'nombre' => 'Identidad Visual y Corporativa', 'slug' => 'identidad-visual-corporativa'],
            ['id_servicio' => 3, 'nombre' => 'Análisis de Métricas', 'slug' => 'analisis-de-metricas'],

            // Branding y Diseño
            ['id_servicio' => 4, 'nombre' => 'Desarrollo de Brief', 'slug' => 'desarrollo-de-brief'],
            ['id_servicio' => 4, 'nombre' => 'Planificación Estratégica', 'slug' => 'planificacion-estrategica'],
            ['id_servicio' => 4, 'nombre' => 'Diseño de Logo', 'slug' => 'diseno-de-logo'], 
            ['id_servicio' => 4, 'nombre' => 'Manual de Marca', 'slug' => 'manual-de-marca'],
        ];

        foreach ($subservicios as $subservicio) {
            Subservicio::updateOrCreate(
                ['slug' => $subservicio['slug']],
                $subservicio
            );
        }
    }
}
