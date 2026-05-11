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
            ['id_servicio' => 1, 'nombre' => 'Experiencia de Usuario y Diseño', 'slug' => 'experiencia-usuario'],
            ['id_servicio' => 1, 'nombre' => 'Optimización SEO', 'slug' => 'seo'],
            ['id_servicio' => 1, 'nombre' => 'Desarrollo Responsive', 'slug' => 'desarrollo-responsive'],
            ['id_servicio' => 1, 'nombre' => 'Integraciones Digitales', 'slug' => 'integraciones-digitales'],

            // Gestión de Redes Sociales
            ['id_servicio' => 2, 'nombre' => 'Estrategia de Contenido', 'slug' => 'estrategia-de-contenido'],
            ['id_servicio' => 2, 'nombre' => 'Social Ads & Performance', 'slug' => 'diseno-pautas'],
            ['id_servicio' => 2, 'nombre' => 'Producción Audiovisual', 'slug' => 'produccion-pautas'],
            ['id_servicio' => 2, 'nombre' => 'Diseño UX y UI', 'slug' => 'ui'],

            // Marketing y Gestión Digital
            ['id_servicio' => 3, 'nombre' => 'Análisis y Benchmarking', 'slug' => 'analisis-y-benchmarking'],
            ['id_servicio' => 3, 'nombre' => 'Campañas Digitales', 'slug' => 'publicidad-digital'],
            ['id_servicio' => 3, 'nombre' => 'Identidad Visual y Corporativa', 'slug' => 'identidad-visual'],
            ['id_servicio' => 3, 'nombre' => 'Análisis de Métricas', 'slug' => 'monitoreo-y-reporting'],

            // Branding y Diseño
            ['id_servicio' => 4, 'nombre' => 'Desarrollo de Brief', 'slug' => 'desarrollo-briefs'],
            ['id_servicio' => 4, 'nombre' => 'Planificación Estratégica', 'slug' => 'planificacion-estrategica'],
            ['id_servicio' => 4, 'nombre' => 'Diseño de Logo', 'slug' => 'naming-logo-slogan'],
            ['id_servicio' => 4, 'nombre' => 'Manual de Marca', 'slug' => 'manual-marca'],
        ];

        foreach ($subservicios as $subservicio) {
            Subservicio::updateOrCreate(
                ['slug' => $subservicio['slug']],
                $subservicio
            );
        }
    }
}
