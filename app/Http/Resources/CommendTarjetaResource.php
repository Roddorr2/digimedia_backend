<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommendTarjetaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id_commend_tarjeta,
            'titulo' => $this->titulo,
            'textos' => [
                'texto1' => $this->texto1,
                'texto2' => $this->texto2,
                'texto3' => $this->texto3,
                'texto4' => $this->texto4,
                'texto5' => $this->texto5,
            ],

            // relación opcional
            'blog_body' => $this->whenLoaded('blog_body'),
        ];
    }
}

