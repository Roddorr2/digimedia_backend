<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConfiguracionTiempoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_configuracion_tiempo' => $this->id_configuracion_tiempo,
            'id_servicio'             => $this->id_servicio,
            'tipo'                    => $this->tipo,
            'numero_mensaje'          => $this->numero_mensaje,
            'unidad_tiempo'           => $this->unidad_tiempo,
            'valor_tiempo'            => $this->valor_tiempo,
            'created_at'              => $this->created_at,
            'updated_at'              => $this->updated_at,
        ];
    }
}
