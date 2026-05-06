<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogAuditoriaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_blog_auditoria' => $this->id_blog_auditoria,

            'accion' => $this->accion,

            'titulo' => $this->titulo,

            'descripcion' => $this->descripcion,

            'fecha_hora' => $this->fecha_hora,

            'empleado' => $this->whenLoaded('empleado', function () {
                return [
                    'id_empleado' => $this->empleado?->id_empleado,
                    'nombre' => $this->empleado?->nombre,
                    'apellido' => $this->empleado?->apellido,
                ];
            }),
        ];
    }
}

