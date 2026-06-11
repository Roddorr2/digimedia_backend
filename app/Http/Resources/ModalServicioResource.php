<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ModalServicioResource extends JsonResource
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
            'id_modalservicio' => $this->id_modalservicio,
            'nombre'           => $this->nombre,
            'telefono'         => $this->telefono,
            'correo'           => $this->correo,
            'id_servicio'      => $this->id_servicio,
            'id_subservicio'   => $this->id_subservicio,
            'estado'           => (bool)$this->estado,
            'created_at'       => $this->created_at,
            'updated_at'       => $this->updated_at,
            'servicio'         => $this->whenLoaded('servicio', function() {
                return [
                    'id_servicio'     => $this->servicio->id_servicio,
                    'nombre'          => $this->servicio->nombre,
                    'nombre_servicio' => $this->servicio->nombre_servicio ?? $this->servicio->nombre,
                ];
            }),
            'subservicio'      => $this->whenLoaded('subservicio', function() {
                return [
                    'id_subservicio' => $this->subservicio->id_subservicio,
                    'nombre'         => $this->subservicio->nombre,
                ];
            }),
        ];
    }
}