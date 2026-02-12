<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BlogFooter;

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
        ];

        foreach ($blog_footers as $bf) {
            BlogFooter::updateOrCreate(
                ['titulo' => $bf['titulo']],
                [
                    'descripcion' => $bf['descripcion'] ?? null,
                    'public_image1' => $bf['public_image1'] ?? null,
                    'public_image2' => $bf['public_image2'] ?? null,
                    'public_image3' => $bf['public_image3'] ?? null,
                ]
            );
        }
    }
}
