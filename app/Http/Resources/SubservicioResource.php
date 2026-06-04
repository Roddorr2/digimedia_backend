<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubservicioResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_subservicio' => $this->id_subservicio,
            'id_servicio'    => $this->id_servicio,
            'nombre'         => $this->nombre,
            'descripcion'    => $this->descripcion,
            'imagen_url'     => $this->imagen_url,
            'created_at'     => $this->created_at,
            'updated_at'     => $this->updated_at,
            'servicio'       => $this->whenLoaded('servicio', function() {
                return [
                    'id_servicio'     => $this->servicio->id_servicio,
                    'nombre'          => $this->servicio->nombre,
                    'nombre_servicio' => $this->servicio->nombre_servicio ?? $this->servicio->nombre,
                ];
            })
        ];
    }
}
