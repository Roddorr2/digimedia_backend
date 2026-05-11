<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogFooterResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_blog_footer' => $this->id_blog_footer,

            'titulo' => $this->titulo,

            'descripcion' => $this->descripcion,

            'estado' => $this->estado,

            'link' => [
                'palabra' => $this->palabra,
                'enlace' => $this->enlace,
            ],

            'imagenes' => [
                [
                    'public_image' => $this->public_image1,
                    'url_image' => $this->url_image1,
                    'alt' => $this->alt_image1,
                    'title' => $this->title_image1,
                ],
                [
                    'public_image' => $this->public_image2,
                    'url_image' => $this->url_image2,
                    'alt' => $this->alt_image2,
                    'title' => $this->title_image2,
                ],
                [
                    'public_image' => $this->public_image3,
                    'url_image' => $this->url_image3,
                    'alt' => $this->alt_image3,
                    'title' => $this->title_image3,
                ],
            ],
        ];
    }
}


