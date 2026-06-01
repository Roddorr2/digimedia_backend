<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PlantillaEmailResource extends JsonResource
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
            'id_plantilla_email' => $this->id_plantilla_email,
            'id_servicio'        => $this->id_servicio,
            'numero_plantilla'   => $this->numero_plantilla,
            'asunto'             => $this->asunto,
            'encabezado'         => $this->encabezado,
            'mensaje'            => $this->mensaje,
            'imagen_url'         => $this->imagen_url,
            'mensaje_boton'      => $this->mensaje_boton,
            'url_boton'          => $this->url_boton,
            'footer'             => $this->footer,
            'red_facebook'       => $this->red_facebook,
            'red_tiktok'         => $this->red_tiktok,
            'red_instagram'      => $this->red_instagram,
            'red_linkedin'       => $this->red_linkedin,
            'updated_by'         => $this->updated_by,
            'created_at'         => $this->created_at,
            'updated_at'         => $this->updated_at,
            'servicio'           => $this->whenLoaded('servicio', function() {
                return [
                    'id_servicio'     => $this->servicio->id_servicio,
                    'nombre'          => $this->servicio->nombre,
                    'nombre_servicio' => $this->servicio->nombre_servicio ?? $this->servicio->nombre,
                ];
            })
        ];
    }
}
