<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmpleadoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * Campos EXCLUIDOS por seguridad:
     * - dni (dato personal sensible)
     * - telefono (dato personal sensible)
     * - id_user (referencia interna)
     * - id_subtipo_admin (referencia interna)
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_empleado' => $this->id_empleado,
            'nombre' => $this->nombre,
            'apellido' => $this->apellido,
            'email' => $this->email,
            'imagen_perfil' => $this->imagen_perfil,
            'imagen_perfil_url' => $this->imagen_perfil_url,
            'id_rol' => $this->id_rol,
            'created_at' => $this->created_at,
            'rol' => $this->whenLoaded('rol'),
            'subtipo_admin' => $this->whenLoaded('subtipoAdmin'),
        ];
    }
}
