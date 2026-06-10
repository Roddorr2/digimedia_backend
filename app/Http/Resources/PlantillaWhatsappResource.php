<?php

namespace App\Http\Resources;

use App\Models\servicios;
use App\Models\Subservicio;
use Illuminate\Http\Resources\Json\JsonResource;

class PlantillaWhatsappResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id_plantilla_whatsapp' => $this->id_plantilla_whatsapp,
            'plantillable_id'       => $this->plantillable_id,
            'plantillable_type'     => $this->plantillable_type,
            'plantillable_type_name' => $this->plantillable_type_name,
            // Retrocompatibilidad: id_servicio calculado desde el owner
            'id_servicio'           => $this->id_servicio,
            'numero_plantilla'      => $this->numero_plantilla,
            'nombre'                => $this->nombre,
            'mensaje'               => $this->mensaje,
            'imagen_url'            => $this->imagen_url,
            'updated_by'            => $this->updated_by,
            'created_at'            => $this->created_at,
            'updated_at'            => $this->updated_at,
            'owner'                 => $this->whenLoaded('plantillable', function () {
                if ($this->plantillable_type === servicios::class) {
                    return [
                        'type'   => 'servicio',
                        'id'     => $this->plantillable->id_servicio,
                        'nombre' => $this->plantillable->nombre,
                    ];
                }

                if ($this->plantillable_type === Subservicio::class) {
                    return [
                        'type'        => 'subservicio',
                        'id'          => $this->plantillable->id_subservicio,
                        'nombre'      => $this->plantillable->nombre,
                        'id_servicio' => $this->plantillable->id_servicio,
                    ];
                }

                return null;
            }),
        ];
    }
}
