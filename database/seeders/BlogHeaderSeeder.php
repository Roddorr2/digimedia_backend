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
        ];

        foreach ($blog_heads as $head) {
            DB::table('blog_heads')->updateOrInsert(
                ['titulo' => $head['titulo']],
                $head
            );
        }
    }
}
