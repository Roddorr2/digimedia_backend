<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Blog;
use App\Models\BlogHead;
use App\Models\BlogBody;
use App\Models\BlogFooter;

class BlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $blogs = [
            [
                'link' => 'tu-bar-en-la-mira'
            ],
            [
                'id_blog_head' => 2,
                'id_blog_body' => 2,
                'id_blog_footer' => 2,
                'link' => 'moderniza-tu-pizzeria'
            ],
            [
                'id_blog_head' => 3,
                'id_blog_body' => 3,
                'id_blog_footer' => 3,
                'link' => 'transformacion-digital-restaurantes'
            ],
            [
                'id_blog_head' => 4,
                'id_blog_body' => 4,
                'id_blog_footer' => 4,
                'link' => 'cafes-premium-ambiance-perfecta'
            ],
            [
                'id_blog_head' => 5,
                'id_blog_body' => 5,
                'id_blog_footer' => 5,
                'link' => 'hoteles-de-lujo-experiencia-visual'
            ],
            [
                'id_blog_head' => 6,
                'id_blog_body' => 6,
                'id_blog_footer' => 6,
                'link' => 'retail-vendiendo-con-luz'
            ],
            [
                'id_blog_head' => 7,
                'id_blog_body' => 7,
                'id_blog_footer' => 7,
                'link' => 'bares-nocturnos-atmosfera-vibrante'
            ],
            [
                'id_blog_head' => 8,
                'id_blog_body' => 8,
                'id_blog_footer' => 8,
                'link' => 'galerias-arte-luz-destaca-obras'
            ],
            [
                'id_blog_head' => 9,
                'id_blog_body' => 9,
                'id_blog_footer' => 9,
                'link' => 'gimnasios-modernos-energia-visual'
            ],
            [
                'id_blog_head' => 10,
                'id_blog_body' => 10,
                'id_blog_footer' => 10,
                'link' => 'spas-relajacion-serenidad-iluminada'
            ],
            // Enero 2026
            [
                'id_blog_head' => 11,
                'id_blog_body' => 11,
                'id_blog_footer' => 11,
                'link' => 'boutiques-lujo-tiendas-exclusivas'
            ],
            [
                'id_blog_head' => 12,
                'id_blog_body' => 12,
                'id_blog_footer' => 12,
                'link' => 'restaurantes-finos-elegancia-gastronomica'
            ],
            [
                'id_blog_head' => 13,
                'id_blog_body' => 13,
                'id_blog_footer' => 13,
                'link' => 'museos-iluminacion-patrimonial'
            ],
            [
                'id_blog_head' => 14,
                'id_blog_body' => 14,
                'id_blog_footer' => 14,
                'link' => 'cines-modernos-experiencia-cinematografica'
            ],
            // Febrero 2026
            [
                'id_blog_head' => 15,
                'id_blog_body' => 15,
                'id_blog_footer' => 15,
                'link' => 'oficinas-corporativas-productividad-luminica'
            ],
            [
                'id_blog_head' => 16,
                'id_blog_body' => 16,
                'id_blog_footer' => 16,
                'link' => 'clinicas-esteticas-ambientes-sanadores'
            ],
            [
                'id_blog_head' => 17,
                'id_blog_body' => 17,
                'id_blog_footer' => 17,
                'link' => 'hoteles-economicos-comodidad-iluminada'
            ],
            [
                'id_blog_head' => 18,
                'id_blog_body' => 18,
                'id_blog_footer' => 18,
                'link' => 'eventos-iluminacion-espectacular'
            ],
        ];

        foreach ($blogs as $b) {
            $head = BlogHead::where('titulo', 'Tu Bar, en la Mira')->first();
            $body = BlogBody::where('titulo', 'Tu Bar, en la Mira')->first();
            $footer = BlogFooter::where('titulo', 'Conclusion')->first();

            Blog::updateOrCreate(
                ['link' => $b['link']],
                [
                    'id_blog_head' => $head ? $head->id_blog_head : null,
                    'id_blog_body' => $body ? $body->id_blog_body : null,
                    'id_blog_footer' => $footer ? $footer->id_blog_footer : null,
                ]
            );
        }
    }
}
