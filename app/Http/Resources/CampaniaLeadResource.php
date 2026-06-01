<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class CampaniaLeadResource extends JsonResource
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
            'id_modal_wat'     => $this->id_modal_wat,
            'nombre'           => $this->nombre,
            'telefono'         => $this->telefono,
            'estado'           => is_null($this->id_modal_wat)
                ? 'pendiente'
                : ($this->estado ? 'enviado' : 'fallido'),
            'intentos'         => $this->intentos ?? 0,
            'puede_reintentar' => (bool) ($this->puede_reintentar ?? false),
            'error'            => $this->error,
            'fecha'            => $this->fecha
                ? Carbon::parse($this->fecha)->format('Y-m-d\TH:i:s')
                : null,
        ];
    }
}
