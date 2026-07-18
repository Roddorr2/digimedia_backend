<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BlogBodySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $blog_bodies = [
            [
                'titulo_consejos' => 'Consejos para Elegir el Letrero Perfecto',
                'titulo' => 'Tu Bar, en la Mira',
                'descripcion' => 'Las luces neón LED se han convertido en un elemento diferenciador en el mundo de la hospitalidad. No solo son visualmente atractivos, sino que también refuerzan la identidad de tu negocio. En este artículo, exploraremos cómo las letras luminosas pueden marcar la diferencia en la experiencia de tus clientes.',
                'public_image1'=>'/blog/blog-2.webp',
                'public_image2'=>'/blog/blog-2.webp',
                'public_image3'=>'/blog/blog-2.webp'
            ],
            [
                'titulo_consejos' => 'Mantenimiento de Letrero Neón',
                'titulo' => 'Moderniza tu Pizzería',
                'descripcion' => 'La tradición italiana se encuentra con la innovación tecnológica. Nuestras soluciones de iluminación LED acentúan la calidez de tu horno de leña mientras crean un ambiente moderno y acogedor. Los clientes se sienten como en Italia, pero con el toque tecnológico del siglo XXI.',
                'public_image1'=>'/blog/blog-2.webp',
                'public_image2'=>'/blog/blog-2.webp',
                'public_image3'=>'/blog/blog-2.webp'
            ],
            [
                'titulo_consejos' => 'Regulaciones y Instalación',
                'titulo' => 'Transformación Digital',
                'descripcion' => 'La experiencia del cliente comienza el momento en que entra a tu local. La iluminación es el primer elemento sensorial que perciben. Implementar sistemas LED inteligentes no solo reduce consumo energético en un 80%, sino que también permite ajustar la atmósfera según la hora del día o evento especial.',
                'public_image1'=>'/blog/blog-2.webp',
                'public_image2'=>'/blog/blog-2.webp',
                'public_image3'=>'/blog/blog-2.webp'
            ],
            [
                'titulo_consejos' => 'Tendencias en Iluminación 2024-2025',
                'titulo' => 'Cafés Premium',
                'descripcion' => 'Tu café es más que una bebida, es una experiencia. Cada detalle cuenta, desde el aroma hasta el ambiente. Nuestros sistemas de iluminación LED crean espacios que invitan a quedarse, a conversar, a disfrutar. Los clientes no solo compran café, compran momentos.',
                'public_image1'=>'/blog/blog-2.webp',
                'public_image2'=>'/blog/blog-2.webp',
                'public_image3'=>'/blog/blog-2.webp'
            ],
            [
                'titulo_consejos' => 'ROI en Iluminación Premium',
                'titulo' => 'Hoteles de Lujo',
                'descripcion' => 'La primera impresión es fundamental. Desde el lobby hasta las habitaciones, la iluminación define el nivel de lujo percibido. Sillones confortables bajo luz cálida, áreas de trabajo con iluminación profesional, y balcones que se transforman al atardecer con nuestros sistemas de iluminación inteligente.',
                'public_image1'=>'/blog/blog-2.webp',
                'public_image2'=>'/blog/blog-2.webp',
                'public_image3'=>'/blog/blog-2.webp'
            ],
            [
                'titulo_consejos' => 'Casos de Éxito',
                'titulo' => 'Retail: Vendiendo con Luz',
                'descripcion' => 'El comportamiento de compra está directamente influenciado por la iluminación. Los estudios demuestran que clientes expuestos a iluminación LED de calidad pasan más tiempo en la tienda y realizan compras de mayor volumen. Además, los productos lucen más atractivos y los precios se perciben como más justos.',
                'public_image1'=>'/blog/blog-2.webp',
                'public_image2'=>'/blog/blog-2.webp',
                'public_image3'=>'/blog/blog-2.webp'
            ],
            [
                'titulo_consejos' => 'Efectos Psicológicos de la Luz',
                'titulo' => 'Bares Nocturnos',
                'descripcion' => 'La vida nocturna requiere iluminación dinámica y cautivadora. Sistemas RGB que responden a la música, crean movimiento y energía. Tus clientes vivirán una experiencia sensorial completa donde la luz es protagonista, no solo decoración.',
                'public_image1'=>'/blog/blog-2.webp',
                'public_image2'=>'/blog/blog-2.webp',
                'public_image3'=>'/blog/blog-2.webp'
            ],
            [
                'titulo_consejos' => 'Iluminación según Horarios',
                'titulo' => 'Galerías de Arte',
                'descripcion' => 'Cada obra de arte merece ser iluminada con precisión. Nuestros sistemas permiten ajustar temperatura de color, intensidad y ángulo de iluminación. Las verdaderas dimensiones y colores de las obras brillan bajo nuestra iluminación especializada.',
                'public_image1'=>'/blog/blog-2.webp',
                'public_image2'=>'/blog/blog-2.webp',
                'public_image3'=>'/blog/blog-2.webp'
            ],
            [
                'titulo_consejos' => 'Tecnología Inteligente en Sistemas LED',
                'titulo' => 'Gimnasios Modernos',
                'descripcion' => 'El entrenamiento físico se potencia con la iluminación correcta. Luz energizante en zonas cardio, iluminación motivacional en sala de pesas, y atmósfera relajante en área de estiramientos. Cada zona tiene su propia personalidad luminosa.',
                'public_image1'=>'/blog/blog-2.webp',
                'public_image2'=>'/blog/blog-2.webp',
                'public_image3'=>'/blog/blog-2.webp'
            ],
            [
                'titulo_consejos' => 'Sostenibilidad y Eficiencia',
                'titulo' => 'Spas de Relajación',
                'descripcion' => 'La relajación comienza con la luz. Tonos cálidos, intensidad baja, luz natural simulada. Cada sala de masaje, sauna o piscina tiene iluminación diseñada para inducir calma y bienestar profundo en tus clientes.',
                'public_image1'=>'/blog/blog-2.webp',
                'public_image2'=>'/blog/blog-2.webp',
                'public_image3'=>'/blog/blog-2.webp'
            ],
            // Enero 2026
            [
                'titulo_consejos' => 'Boutiques: Exclusividad Iluminada',
                'titulo' => 'Boutiques de Lujo',
                'descripcion' => 'En el mundo del lujo, cada detalle transmite exclusividad. Nuestros sistemas de iluminación LED de precisión realzan cada prenda, accesorio y artículo de tu boutique, creando una experiencia de compra elevada donde los clientes se sienten envueltos en elegancia.',
                'public_image1'=>'/blog/blog-2.webp',
                'public_image2'=>'/blog/blog-2.webp',
                'public_image3'=>'/blog/blog-2.webp'
            ],
            [
                'titulo_consejos' => 'Restaurantes Finos: Detalles Destellantes',
                'titulo' => 'Restaurantes Finos',
                'descripcion' => 'La gastronomía fina requiere una atmósfera a la altura. Iluminación que acompaña cada plato, que enfatiza la presentación, que crea momentos de conexión entre comensales. El ambiente es tan importante como el sabor.',
                'public_image1'=>'/blog/blog-2.webp',
                'public_image2'=>'/blog/blog-2.webp',
                'public_image3'=>'/blog/blog-2.webp'
            ],
            [
                'titulo_consejos' => 'Museos: Preservación Luminosa',
                'titulo' => 'Museos',
                'descripcion' => 'La preservación del patrimonio requiere precisión técnica. Nuestros sistemas de iluminación LED especializados revelan cada detalle de artefactos históricos sin exponerlos a radiación dañina. La luz es el vehículo perfecto para conectar visitantes con la historia.',
                'public_image1'=>'/blog/blog-2.webp',
                'public_image2'=>'/blog/blog-2.webp',
                'public_image3'=>'/blog/blog-2.webp'
            ],
            [
                'titulo_consejos' => 'Cines: Expectativa Visual',
                'titulo' => 'Cines Modernos',
                'descripcion' => 'El cine es espectáculo desde antes de entrar a la sala. Pasillos, áreas de venta, salas de espera con iluminación dinámica que genera expectativa. Cada zona visita diseñada para maximizar la experiencia y las ventas de concesiones.',
                'public_image1'=>'/blog/blog-2.webp',
                'public_image2'=>'/blog/blog-2.webp',
                'public_image3'=>'/blog/blog-2.webp'
            ],
            // Febrero 2026
            [
                'titulo_consejos' => 'Oficinas: Concentración y Bienestar',
                'titulo' => 'Oficinas Corporativas',
                'descripcion' => 'La productividad se potencia con iluminación correcta. Luz natural simulada en oficinas abierta, iluminación enfocada en áreas de trabajo, y espacios de descanso con luz cálida. Tus equipos rinden mejor bajo la luz correcta.',
                'public_image1'=>'/blog/blog-2.webp',
                'public_image2'=>'/blog/blog-2.webp',
                'public_image3'=>'/blog/blog-2.webp'
            ],
            [
                'titulo_consejos' => 'Clínicas: Trust y Seguridad',
                'titulo' => 'Clínicas Estéticas',
                'descripcion' => 'La medicina estética requiere trust y serenidad. Iluminación que transmite profesionalismo en consultorios, confort en áreas de tratamiento, y calidez en salas de recuperación. Los pacientes confían en espacios que los rodean correctamente.',
                'public_image1'=>'/blog/blog-2.webp',
                'public_image2'=>'/blog/blog-2.webp',
                'public_image3'=>'/blog/blog-2.webp'
            ],
            [
                'titulo_consejos' => 'Hoteles Económicos: Rentabilidad',
                'titulo' => 'Hoteles Económicos',
                'descripcion' => 'Lujo no significa presupuesto ilimitado. Nuestras soluciones LED económicas crean espacios acogedores en habitaciones, lobbies atractivos, y áreas comunes que superan expectativas. Máxima comodidad con mínimo costo operativo.',
                'public_image1'=>'/blog/blog-2.webp',
                'public_image2'=>'/blog/blog-2.webp',
                'public_image3'=>'/blog/blog-2.webp'
            ],
            [
                'titulo_consejos' => 'Eventos: Magia Espectacular',
                'titulo' => 'Eventos',
                'descripcion' => 'Los eventos memorables requieren iluminación espectacular. Desde bodas hasta conferencias, desde conciertos hasta galas corporativas. Sistemas dinámicos RGB que responden a música, que cambian con el ritmo del evento, que crean magia en cada momento.',
                'public_image1'=>'/blog/blog-2.webp',
                'public_image2'=>'/blog/blog-2.webp',
                'public_image3'=>'/blog/blog-2.webp'
            ],
        ];

        DB::table('blog_bodies')->truncate();
        foreach ($blog_bodies as $body) {
            DB::table('blog_bodies')->insert($body);
        }

    }
}
