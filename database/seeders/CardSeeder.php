<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Card;
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

        $blog = Blog::where('link', 'tu-bar-en-la-mira')->first();
        $rolAdmin = Rol::where('nombre', 'administrador')->first();

        $empleado = null;
        if ($rolAdmin) {
            $empleado = Empleado::where('id_rol', $rolAdmin->id_rol)->first();
        }
        if (!$empleado) {
            $empleado = Empleado::first();
        }

        foreach ($cards as $c) {
            Card::updateOrCreate(
                ['titulo' => $c['titulo']],
                [
                    'descripcion' => $c['descripcion'] ?? null,
                    'public_image' => $c['public_image'] ?? null,
                    'id_plantilla' => $c['id_plantilla'] ?? null,
                    'id_blog' => $blog ? $blog->id_blog : null,
                    'id_empleado' => $empleado ? $empleado->id_empleado : null,
                ]
            );
        }
    }
}
