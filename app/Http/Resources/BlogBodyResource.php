<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogBodyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_blog_body' => $this->id_blog_body,

            'titulo' => $this->titulo,

            'descripcion' => $this->descripcion,

            'flags' => [
                'galeria' => $this->flag_galeria,
                'consejos' => $this->flag_consejos,
                'informacion' => $this->flag_informacion,
            ],
            'service_url' => $this->service_url,

            'titulo_tarjeta' => $this->titulo_tarjeta,

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
            'commend_tarjeta' => $this->whenLoaded('commend_tarjeta'),

            'tarjetas' => $this->whenLoaded('tarjetas'),
        ];
    }
}

