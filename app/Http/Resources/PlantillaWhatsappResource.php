<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PlantillaWhatsappResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id_plantilla_whatsapp' => $this->id_plantilla_whatsapp,
            'id_servicio'           => $this->id_servicio,
            'numero_plantilla'      => $this->numero_plantilla,
            'mensaje'               => $this->mensaje,
            'imagen_url'            => $this->imagen_url,
            'updated_by'            => $this->updated_by,
            'created_at'            => $this->created_at,
            'updated_at'            => $this->updated_at,
            'servicio'              => $this->whenLoaded('servicio', function() {
                return [
                    'id_servicio'     => $this->servicio->id_servicio,
                    'nombre'          => $this->servicio->nombre, // Mantiene el modelo original
                    'nombre_servicio' => $this->servicio->nombre_servicio ?? $this->servicio->nombre,
                ];
            })
        ];
    }
}
