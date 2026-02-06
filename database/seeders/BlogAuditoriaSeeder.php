<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BlogAuditoriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $blog_auditorias = [
            [
                'id_empleado' => 2,
                'id_blog' => 1,
                'accion' => 'CREAR',
                'fecha_hora' => '2025-10-15 10:30:00',
                'titulo' => 'Tu Bar, en la Mira',
                'descripcion' => 'Blog sobre iluminación para bares'
            ],
            [
                'id_empleado' => 3,
                'id_blog' => 2,
                'accion' => 'CREAR',
                'fecha_hora' => '2025-11-08 14:20:00',
                'titulo' => 'Moderniza tu Pizzería',
                'descripcion' => 'Blog sobre iluminación para pizzerías'
            ],
            [
                'id_empleado' => 4,
                'id_blog' => 3,
                'accion' => 'CREAR',
                'fecha_hora' => '2025-12-20 11:45:00',
                'titulo' => 'Transformación Digital para Restaurantes',
                'descripcion' => 'Blog sobre transformación digital en restaurantes'
            ],
            [
                'id_empleado' => 5,
                'id_blog' => 4,
                'accion' => 'CREAR',
                'fecha_hora' => '2025-11-25 09:15:00',
                'titulo' => 'Cafés Premium: Ambiance Perfecta',
                'descripcion' => 'Blog sobre iluminación para cafés premium'
            ],
            [
                'id_empleado' => 2,
                'id_blog' => 5,
                'accion' => 'CREAR',
                'fecha_hora' => '2025-10-30 15:40:00',
                'titulo' => 'Hoteles de Lujo: Experiencia Visual',
                'descripcion' => 'Blog sobre iluminación para hoteles de lujo'
            ],
            [
                'id_empleado' => 6,
                'id_blog' => 6,
                'accion' => 'CREAR',
                'fecha_hora' => '2025-12-03 08:22:00',
                'titulo' => 'Retail: Vendiendo con Luz',
                'descripcion' => 'Blog sobre iluminación para retail'
            ],
            [
                'id_empleado' => 3,
                'id_blog' => 7,
                'accion' => 'CREAR',
                'fecha_hora' => '2025-11-12 16:50:00',
                'titulo' => 'Bares Nocturnos: Atmósfera Vibrante',
                'descripcion' => 'Blog sobre iluminación dinámica para bares nocturnos'
            ],
            [
                'id_empleado' => 4,
                'id_blog' => 8,
                'accion' => 'CREAR',
                'fecha_hora' => '2025-10-22 12:10:00',
                'titulo' => 'Galerías de Arte: Luz que Destaca Obras',
                'descripcion' => 'Blog sobre iluminación especializada para galerías'
            ],
            [
                'id_empleado' => 5,
                'id_blog' => 9,
                'accion' => 'CREAR',
                'fecha_hora' => '2025-12-10 13:35:00',
                'titulo' => 'Gimnasios Modernos: Energía Visual',
                'descripcion' => 'Blog sobre iluminación para gimnasios'
            ],
            [
                'id_empleado' => 2,
                'id_blog' => 10,
                'accion' => 'CREAR',
                'fecha_hora' => '2025-11-05 10:05:00',
                'titulo' => 'Spas de Relajación: Serenidad Iluminada',
                'descripcion' => 'Blog sobre iluminación terapéutica para spas'
            ],
            // Enero 2026
            [
                'id_empleado' => 3,
                'id_blog' => 11,
                'accion' => 'CREAR',
                'fecha_hora' => '2026-01-05 09:20:00',
                'titulo' => 'Boutiques de Lujo - Tiendas Exclusivas',
                'descripcion' => 'Blog sobre iluminación para boutiques premium'
            ],
            [
                'id_empleado' => 4,
                'id_blog' => 12,
                'accion' => 'CREAR',
                'fecha_hora' => '2026-01-12 14:45:00',
                'titulo' => 'Restaurantes Finos - Elegancia Gastronómica',
                'descripcion' => 'Blog sobre iluminación para restaurantes finos'
            ],
            [
                'id_empleado' => 5,
                'id_blog' => 13,
                'accion' => 'CREAR',
                'fecha_hora' => '2026-01-18 11:30:00',
                'titulo' => 'Museos - Iluminación Patrimonial',
                'descripcion' => 'Blog sobre iluminación para museos'
            ],
            [
                'id_empleado' => 6,
                'id_blog' => 14,
                'accion' => 'CREAR',
                'fecha_hora' => '2026-01-25 16:15:00',
                'titulo' => 'Cines Modernos - Experiencia Cinematográfica',
                'descripcion' => 'Blog sobre iluminación para cines'
            ],
            // Febrero 2026
            [
                'id_empleado' => 2,
                'id_blog' => 15,
                'accion' => 'CREAR',
                'fecha_hora' => '2026-02-03 08:50:00',
                'titulo' => 'Oficinas Corporativas - Productividad Lumínica',
                'descripcion' => 'Blog sobre iluminación para oficinas'
            ],
            [
                'id_empleado' => 3,
                'id_blog' => 16,
                'accion' => 'CREAR',
                'fecha_hora' => '2026-02-10 13:25:00',
                'titulo' => 'Clínicas Estéticas - Ambientes Sanadores',
                'descripcion' => 'Blog sobre iluminación para clínicas'
            ],
            [
                'id_empleado' => 4,
                'id_blog' => 17,
                'accion' => 'CREAR',
                'fecha_hora' => '2026-02-17 10:40:00',
                'titulo' => 'Hoteles Económicos - Comodidad Iluminada',
                'descripcion' => 'Blog sobre iluminación para hoteles económicos'
            ],
            [
                'id_empleado' => 5,
                'id_blog' => 18,
                'accion' => 'CREAR',
                'fecha_hora' => '2026-02-24 15:55:00',
                'titulo' => 'Eventos - Iluminación Espectacular',
                'descripcion' => 'Blog sobre iluminación para eventos'
            ],
        ];

        DB::table('blog_auditoria')->insert($blog_auditorias);
    }
}
