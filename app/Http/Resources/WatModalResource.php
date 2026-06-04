<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WatModalResource extends JsonResource
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
            'id_modal_wat'     => $this->id_modal_wat,
            'estado'           => (int)$this->estado,
            'error'            => $this->error,
            'id_modalservicio' => $this->id_modalservicio,
            'number_message'   => $this->number_message,
            'fecha'            => $this->fecha,
            'created_at'       => $this->created_at,
            'updated_at'       => $this->updated_at,
        ];
    }
}
