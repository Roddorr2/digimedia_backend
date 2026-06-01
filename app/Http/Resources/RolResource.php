<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RolResource extends JsonResource
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
            'id_rol'   => $this->id_rol,
            'nombre'   => $this->nombre,
            'permisos' => PermisoResource::collection($this->whenLoaded('permisos')),
        ];
    }
}
