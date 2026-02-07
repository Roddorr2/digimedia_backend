<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BlogFooterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $blog_footers = [
            [
                'titulo' => 'Conclusion',
                'descripcion' => 'Invertir en luces neón LED no solo mejora la estética de tu bar, sino que también influye en la percepción de los clientes y fortalece tu marca. ¡Haz que tu bar brille con luz propia!',
                'public_image1'=>'/blog/blog-2.webp',
                'public_image2'=>'/blog/blog-2.webp',
                'public_image3'=>'/blog/blog-2.webp'
            ],
            [
                'titulo' => 'Próximos Pasos',
                'descripcion' => 'No esperes más para transformar tu pizzería. Contacta a nuestro equipo para una consultoría sin costo. Analizaremos tu espacio y te presentaremos las mejores soluciones en iluminación LED para tu negocio.',
                'public_image1'=>'/blog/blog-2.webp',
                'public_image2'=>'/blog/blog-2.webp',
                'public_image3'=>'/blog/blog-2.webp'
            ],
            [
                'titulo' => 'Resultados Comprobados',
                'descripcion' => 'Más de 500 restaurantes en la región ya han visto resultados. Aumento de 35% en permanencia de clientes, 40% más en consumo per cápita, y un ROI en menos de 18 meses. ¿Listo para transformar tu negocio?',
                'public_image1'=>'/blog/blog-2.webp',
                'public_image2'=>'/blog/blog-2.webp',
                'public_image3'=>'/blog/blog-2.webp'
            ],
            [
                'titulo' => 'Tu Momento es Ahora',
                'descripcion' => 'La iluminación adecuada es el secreto que los grandes cafés ya conocen. Crea espacios instagrameables donde los clientes quieran pasar horas. Garantiza que cada visita sea especial y memorable.',
                'public_image1'=>'/blog/blog-2.webp',
                'public_image2'=>'/blog/blog-2.webp',
                'public_image3'=>'/blog/blog-2.webp'
            ],
            [
                'titulo' => 'Experiencia Incomparable',
                'descripcion' => 'Los huéspedes hablan de tres cosas: limpieza, comodidad y ambiente. El 70% del ambiente depende de la iluminación. Nuestras soluciones garantizan reviews de 5 estrellas en el aspecto visual de tus espacios.',
                'public_image1'=>'/blog/blog-2.webp',
                'public_image2'=>'/blog/blog-2.webp',
                'public_image3'=>'/blog/blog-2.webp'
            ],
            [
                'titulo' => 'Conecta con Tus Clientes',
                'descripcion' => 'La venta moderna es experiencial. Los clientes compran lo que sienten, no solo lo que ven. Implementa la iluminación correcta y observa cómo tus clientes se convierten en promotores de tu marca en redes sociales.',
                'public_image1'=>'/blog/blog-2.webp',
                'public_image2'=>'/blog/blog-2.webp',
                'public_image3'=>'/blog/blog-2.webp'
            ],
            [
                'titulo' => 'Haz la Diferencia',
                'descripcion' => 'La competencia ya está usando iluminación inteligente. Sé de los primeros en tu zona en implementar sistemas de vanguardia. Destácate, atrae clientes premium y aumenta tu rentabilidad exponencialmente.',
                'public_image1'=>'/blog/blog-2.webp',
                'public_image2'=>'/blog/blog-2.webp',
                'public_image3'=>'/blog/blog-2.webp'
            ],
            [
                'titulo' => 'Preserva y Resalta',
                'descripcion' => 'El arte merece más que buena iluminación, merece iluminación perfecta. Nuestros sistemas especializados garantizan que cada obra sea vista como el artista la imaginó, sin daño UV, con precisión cromática total.',
                'public_image1'=>'/blog/blog-2.webp',
                'public_image2'=>'/blog/blog-2.webp',
                'public_image3'=>'/blog/blog-2.webp'
            ],
            [
                'titulo' => 'Motiva y Energiza',
                'descripcion' => 'La ciencia demuestra que la iluminación correcta aumenta entre 5-15% el rendimiento atlético. Tus miembros entrenarán más duro, gastarán más dinero en membresías y recomendarán tu gimnasio con orgullo.',
                'public_image1'=>'/blog/blog-2.webp',
                'public_image2'=>'/blog/blog-2.webp',
                'public_image3'=>'/blog/blog-2.webp'
            ],
            [
                'titulo' => 'Sana y Relaja',
                'descripcion' => 'La luz es medicina. Sistemas de iluminación diseñados científicamente para inducir relajación, reducir estrés y mejorar la circulación. Tus clientes saldrán renovados y volverán lo antes posible.',
                'public_image1'=>'/blog/blog-2.webp',
                'public_image2'=>'/blog/blog-2.webp',
                'public_image3'=>'/blog/blog-2.webp'
            ],
            // Enero 2026
            [
                'titulo' => 'Brilla en Exclusividad',
                'descripcion' => 'La boutique de lujo no compite por precio, sino por experiencia. Crea espacios menorables con iluminación que envuelve al cliente en una atmósfera de exclusividad absoluta. Marca la diferencia en el mercado premium.',
                'public_image1'=>'/blog/blog-2.webp',
                'public_image2'=>'/blog/blog-2.webp',
                'public_image3'=>'/blog/blog-2.webp'
            ],
            [
                'titulo' => 'Cena con Estilo',
                'descripcion' => 'Los restaurantes finos han descubierto el secreto: la iluminación es el 60% de la experiencia gastronómica. Implementa nuestros sistemas y prepárate para que tus reservas se cierren meses en avance.',
                'public_image1'=>'/blog/blog-2.webp',
                'public_image2'=>'/blog/blog-2.webp',
                'public_image3'=>'/blog/blog-2.webp'
            ],
            [
                'titulo' => 'Patrimonio Iluminado',
                'descripcion' => 'Los museos más prestigiosos del mundo confían en sistemas de iluminación LED especializados. Preserva la historia con la tecnología del futuro. Proporciona a tus visitantes una experiencia visual única.',
                'public_image1'=>'/blog/blog-2.webp',
                'public_image2'=>'/blog/blog-2.webp',
                'public_image3'=>'/blog/blog-2.webp'
            ],
            [
                'titulo' => 'Espectáculo desde la Entrada',
                'descripcion' => 'Los cines modernos entienden que la experiencia comienza fuera de la sala. Iluminación envolvente que crea anticipación, que genera buzz, que llena de clientes tu concesión. La magia comienza antes de que apaguen las luces.',
                'public_image1'=>'/blog/blog-2.webp',
                'public_image2'=>'/blog/blog-2.webp',
                'public_image3'=>'/blog/blog-2.webp'
            ],
            // Febrero 2026
            [
                'titulo' => 'Productividad Visible',
                'descripcion' => 'Las empresas líderes conocen el impacto de la iluminación en productividad. Implementa sistemas inteligentes que se adapten a los ritmos circadianos de tus equipos. Verás disminución en ausentismo y aumento en eficiencia entre 15-25%.',
                'public_image1'=>'/blog/blog-2.webp',
                'public_image2'=>'/blog/blog-2.webp',
                'public_image3'=>'/blog/blog-2.webp'
            ],
            [
                'titulo' => 'Confianza y Profesionalismo',
                'descripcion' => 'En medicina estética, la confianza es primordial. Espacios bien iluminados transmiten profesionalismo, higiene y seguridad. Tus pacientes se sentirán en manos de expertos desde el momento en que entren a la clínica.',
                'public_image1'=>'/blog/blog-2.webp',
                'public_image2'=>'/blog/blog-2.webp',
                'public_image3'=>'/blog/blog-2.webp'
            ],
            [
                'titulo' => 'Lujo Accesible',
                'descripcion' => 'El cliente económico quiere sentarse valorado. Iluminación eficiente que crea atmósfera de lujo sin presupuesto de lujo. Habitaciones acogedoras, áreas comunes atractivas, servicios que se sienten premium a precio accesible.',
                'public_image1'=>'/blog/blog-2.webp',
                'public_image2'=>'/blog/blog-2.webp',
                'public_image3'=>'/blog/blog-2.webp'
            ],
            [
                'titulo' => 'Momentos Memorables',
                'descripcion' => 'Los eventos extraordinarios requieren iluminación extraordinaria. Nuestros sistemas dinámicos convierten espacios ordinarios en escenarios de magia. Tus invitados hablarán del evento durante años. La iluminación es el alma de la celebración.',
                'public_image1'=>'/blog/blog-2.webp',
                'public_image2'=>'/blog/blog-2.webp',
                'public_image3'=>'/blog/blog-2.webp'
            ],
        ];

        DB::table('blog_footers')->truncate();
        foreach ($blog_footers as $footer) {
            DB::table('blog_footers')->insert($footer);
        }
        
    }
}