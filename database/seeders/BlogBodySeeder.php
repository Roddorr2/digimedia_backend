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
                'id_commend_tarjeta' => 1,
                'titulo' => 'Tu Bar, en la Mira',
                'descripcion' => 'Las luces neón LED se han convertido en un elemento diferenciador en el mundo de la hospitalidad. No solo son visualmente atractivos, sino que también refuerzan la identidad de tu negocio. En este artículo, exploraremos cómo las letras luminosas pueden marcar la diferencia en la experiencia de tus clientes.',
                'public_image1'=>'/blog/blog-2.webp',
                'public_image2'=>'/blog/blog-2.webp',
                'public_image3'=>'/blog/blog-2.webp'
            ],
        ];

        foreach ($blog_bodies as $body) {
            DB::table('blog_bodies')->updateOrInsert(
                ['id_commend_tarjeta' => $body['id_commend_tarjeta']],
                $body
            );
        }

    }
}
