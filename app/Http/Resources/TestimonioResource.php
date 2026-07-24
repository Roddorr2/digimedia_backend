<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TestimonioResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_testimonio' => $this->id_testimonio,
            'nombre' => $this->nombre,
            'cargo' => $this->cargo,
            'texto' => $this->texto,
            'rating' => $this->rating,
            'fecha_testimonio' => $this->fecha_testimonio,
            'imagen_url' => $this->imagen_url,
            'created_at' => $this->created_at,
        ];
    }
}
