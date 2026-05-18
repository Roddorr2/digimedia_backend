<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogHeadResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // 🔥 Solo devolvemos la ruta, sin dominio
        $imagePath = $this->url_image
            ? $this->url_image
            : ($this->public_image ?? null);

        return [
            'id_blog_head' => $this->id_blog_head,
            'titulo' => $this->titulo,
            'texto_frase' => $this->texto_frase,
            'texto_descripcion' => $this->texto_descripcion,

            'imagen' => [
                'path' => $imagePath, // ✅ solo ruta tipo /blog/xxx.webp
                'alt' => $this->alt,
                'title' => $this->title,
            ],

            'seo' => [
                'meta_title' => $this->meta_title,
                'meta_descripcion' => $this->meta_descripcion,
            ],
        ];
    }
}
