<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CampaniaWhatsAppResource extends JsonResource
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
            'id_campania'         => $this->id_campania,
            'servicio'            => optional($this->servicio)->nombre,
            'estado'              => $this->estado,
            'total_destinatarios' => $this->total_destinatarios,
            'envios_exitosos'     => $this->envios_exitosos,
            'envios_fallidos'     => $this->envios_fallidos,
            'envios_pendientes'   => $this->envios_pendientes,
            'porcentaje'          => $this->getProgressPercentage(),
            'fecha_inicio'        => $this->fecha_inicio,
            'fecha_fin'           => $this->fecha_fin,
            'created_at'          => $this->created_at,
        ];
    }
}
