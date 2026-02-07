<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BlogHeaderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $blog_heads = [
            [
                'titulo' => 'Tu Bar, en la Mira',
                'texto_frase' => 'Ilumina tu espacio, cautiva a tus clientes',
                'texto_descripcion' => 'Transforma la atmósfera de tu bar con luces neón LED vibrantes y llenas de estilo.',
                'public_image'=>'/blog/fondo_blog_extend.webp'
            ],
            [
                'titulo' => 'Moderniza tu Pizzería',
                'texto_frase' => 'Diseño italiano, toque moderno',
                'texto_descripcion' => 'Crea una experiencia gastronómica memorable elevando el ambiente de tu local con iluminación LED personalizada.',
                'public_image'=>'/blog/fondo_blog_extend.webp'
            ],
            [
                'titulo' => 'Transformación Digital para Restaurantes',
                'texto_frase' => 'Tecnología al servicio del buen comer',
                'texto_descripcion' => 'Descubre cómo la iluminación inteligente mejora la experiencia del cliente y aumenta las ventas.',
                'public_image'=>'/blog/fondo_blog_extend.webp'
            ],
            [
                'titulo' => 'Cafés Premium: Ambiance Perfecta',
                'texto_frase' => 'Cada taza merece una atmósfera especial',
                'texto_descripcion' => 'Espacio acogedor con iluminación LED que realza cada momento de conexión con tus clientes.',
                'public_image'=>'/blog/fondo_blog_extend.webp'
            ],
            [
                'titulo' => 'Hoteles de Lujo: Experiencia Visual',
                'texto_frase' => 'Hospedaje que cautiva desde la entrada',
                'texto_descripcion' => 'Implementa soluciones de iluminación LED que reflejen la exclusividad y elegancia de tu marca.',
                'public_image'=>'/blog/fondo_blog_extend.webp'
            ],
            [
                'titulo' => 'Retail: Vendiendo con Luz',
                'texto_frase' => 'Resalta tus productos con estilo',
                'texto_descripcion' => 'La iluminación correcta atrae clientes y aumenta el tiempo de permanencia en tu tienda.',
                'public_image'=>'/blog/fondo_blog_extend.webp'
            ],
            [
                'titulo' => 'Bares Nocturnos: Atmósfera Vibrante',
                'texto_frase' => 'La noche viene viva con tu iluminación',
                'texto_descripcion' => 'Crea espacios que vibren al ritmo de tus clientes con sistemas LED dinámicos y respuesta a música.',
                'public_image'=>'/blog/fondo_blog_extend.webp'
            ],
            [
                'titulo' => 'Galerías de Arte: Luz que Destaca Obras',
                'texto_frase' => 'Ilumina el arte, amplifica la belleza',
                'texto_descripcion' => 'Sistemas de iluminación especializada para resaltar cada obra y crear espacios contemplativos.',
                'public_image'=>'/blog/fondo_blog_extend.webp'
            ],
            [
                'titulo' => 'Gimnasios Modernos: Energía Visual',
                'texto_frase' => 'Entrena bajo la mejor luz',
                'texto_descripcion' => 'Iluminación que motiva, energiza y mejora el rendimiento en cada sesión de entrenamiento.',
                'public_image'=>'/blog/fondo_blog_extend.webp'
            ],
            [
                'titulo' => 'Spas de Relajación: Serenidad Iluminada',
                'texto_frase' => 'Luz que relaja y sana',
                'texto_descripcion' => 'Crea espacios de bienestar con iluminación cálida que invita a la relajación profunda.',
                'public_image'=>'/blog/fondo_blog_extend.webp'
            ],
            // Enero 2026
            [
                'titulo' => 'Boutiques de Lujo: Tiendas Exclusivas',
                'texto_frase' => 'Cada pieza brilla con elegancia',
                'texto_descripcion' => 'Iluminación que envuelve y realza la propuesta exclusiva de tu boutique premium.',
                'public_image'=>'/blog/fondo_blog_extend.webp'
            ],
            [
                'titulo' => 'Restaurantes Finos: Elegancia Gastronómica',
                'texto_frase' => 'Cena memorable bajo la luz perfecta',
                'texto_descripcion' => 'Crea momentos únicos con iluminación que complementa cada plato y experiencia culinaria.',
                'public_image'=>'/blog/fondo_blog_extend.webp'
            ],
            [
                'titulo' => 'Museos: Iluminación Patrimonial',
                'texto_frase' => 'Preserva la historia con luz correcta',
                'texto_descripcion' => 'Sistemas especializados que realzan artefactos históricos sin comprometer su integridad.',
                'public_image'=>'/blog/fondo_blog_extend.webp'
            ],
            [
                'titulo' => 'Cines Modernos: Experiencia Cinematográfica',
                'texto_frase' => 'El cine que anticipa el espectáculo',
                'texto_descripcion' => 'Iluminación en pasillos y áreas comerciales que genera anticipación y atrae clientes.',
                'public_image'=>'/blog/fondo_blog_extend.webp'
            ],
            // Febrero 2026
            [
                'titulo' => 'Oficinas Corporativas: Productividad Lumínica',
                'texto_frase' => 'Trabaja mejor bajo la mejor luz',
                'texto_descripcion' => 'Iluminación inteligente que potencia concentración y reduce fatiga visual en equipos.',
                'public_image'=>'/blog/fondo_blog_extend.webp'
            ],
            [
                'titulo' => 'Clínicas Estéticas: Ambientes Sanadores',
                'texto_frase' => 'Confianza y serenidad en cada espacio',
                'texto_descripcion' => 'Iluminación que transmite seguridad y bienestar en cada área de la clínica.',
                'public_image'=>'/blog/fondo_blog_extend.webp'
            ],
            [
                'titulo' => 'Hoteles Económicos: Comodidad Iluminada',
                'texto_frase' => 'Lujo accesible comienza en la luz',
                'texto_descripcion' => 'Iluminación eficiente que crea espacios acogedores con presupuesto inteligente.',
                'public_image'=>'/blog/fondo_blog_extend.webp'
            ],
            [
                'titulo' => 'Eventos: Iluminación Espectacular',
                'texto_frase' => 'La luz que crea momentos memorables',
                'texto_descripcion' => 'Sistemas dinámicos que transforman espacios y emocionan a tus invitados en cada evento.',
                'public_image'=>'/blog/fondo_blog_extend.webp'
            ],
        ];

        DB::table('blog_heads')->truncate();
        foreach ($blog_heads as $head) {
            DB::table('blog_heads')->insert($head);
        }
    }
}
