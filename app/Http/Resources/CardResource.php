<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_card' => $this->id_card,

            'titulo' => $this->titulo,

            'descripcion' => $this->descripcion,

            'imagen' => [
                'public_image' => $this->public_image,
                'url_image' => $this->url_image,
            ],

            'estado_publicacion' => $this->estado_publicacion,

            'id_plantilla' => $this->id_plantilla,

            'blog' => $this->whenLoaded('blog', function () {
                return [
                    'id_blog' => $this->blog?->id_blog,
                    'link' => $this->blog?->link,
                ];
            }),

            'head' => $this->whenLoaded('blog', function () {
                return [
                    'titulo' => $this->blog?->head?->titulo,
                ];
            }),

            'empleado' => $this->whenLoaded('empleado', function () {
                return [
                    'id_empleado' => $this->empleado?->id_empleado,
                    'nombre' => $this->empleado?->nombre,
                    'apellido' => $this->empleado?->apellido,
                ];
            }),
        ];
    }
}


