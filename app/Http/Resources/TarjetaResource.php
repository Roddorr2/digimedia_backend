<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TarjetaResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_tarjeta' => $this->id_tarjeta,
            'titulo' => $this->titulo,
            'titulo_color' => $this->titulo_color,
            'descripcion' => $this->descripcion,
            'descripcion_color' => $this->descripcion_color,
            'enlace' => $this->enlace,
            'palabra' => $this->palabra,
            'id_blog_body' => $this->id_blog_body,
        ];
    }
}
