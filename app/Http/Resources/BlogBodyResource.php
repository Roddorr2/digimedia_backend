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
            'palabra' => $this->palabra,
            'enlace' => $this->enlace,

            'flag_informacion' => $this->flag_informacion,
            'flag_galeria' => $this->flag_galeria,
            'flag_consejos' => $this->flag_consejos,
            'service_url' => $this->service_url,

            'titulo_tarjeta' => $this->titulo_tarjeta,
            'titulo_consejos' => $this->titulo_consejos,

            'bg_color' => $this->bg_color,
            'bg_type' => $this->bg_type,
            'bg_colors' => $this->bg_colors,

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

            // Campos planos para compatibilidad directa con el frontend
            'public_image1' => $this->public_image1,
            'public_image2' => $this->public_image2,
            'public_image3' => $this->public_image3,
            'url_image1' => $this->url_image1,
            'url_image2' => $this->url_image2,
            'url_image3' => $this->url_image3,
            'alt_image1' => $this->alt_image1,
            'alt_image2' => $this->alt_image2,
            'alt_image3' => $this->alt_image3,
            'title_image1' => $this->title_image1,
            'title_image2' => $this->title_image2,
            'title_image3' => $this->title_image3,

            'consejos' => ConsejoResource::collection($this->whenLoaded('consejos')),

            'tarjetas' => TarjetaResource::collection($this->whenLoaded('tarjetas')),
        ];
    }
}
