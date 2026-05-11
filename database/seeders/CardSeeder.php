<?php

namespace Database\Seeders;

use App\Models\Card;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Blog;
use App\Models\Empleado;
use App\Models\Rol;

class CardSeeder extends Seeder
{

    public function run(): void
    {
        $cards = [
            [
                'titulo' => 'Tu Bar, en la Mira',
                'descripcion' => 'Haz que el nombre de tu bar destaque con letras neón LED. Crea un ambiente único que atraiga miradas y clientes. ¡Ilumina tu identidad! 🍹🔆',
                'public_image' => '/blog/fondo_blog_extend.webp',
                'id_plantilla' => 3,
                'id_blog' => 1,
                'id_empleado' => 2,
            ],
        ];

        foreach ($cards as $card) {
            Card::updateOrCreate(
                ['id_blog' => $card['id_blog']],
                $card
            );
        }
    }
}
