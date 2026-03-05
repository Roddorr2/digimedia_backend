<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReclamacionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_reclamacion' => $this->id_reclamacion,
            'nombre' => $this->nombre,
            'apellido' => $this->apellido,
            'documento' => $this->documento,
            'numeroDocumento' => $this->numeroDocumento,
            'email' => $this->email,
            'celular' => $this->celular,
            'direccion' => $this->direccion,
            'distrito' => $this->distrito,
            'ciudad' => $this->ciudad,
            'tipoReclamo' => $this->tipoReclamo,
            'id_servicio' => $this->id_servicio,
            'reclamoPerson' => $this->reclamoPerson,
            'checkReclamoForm' => $this->checkReclamoForm,
            'aceptaPoliticaPrivacidad' => $this->aceptaPoliticaPrivacidad,
            'fechaReclamo' => $this->fechaReclamo,
            'fechaIncidente' => $this->fechaIncidente,
            'estadoReclamo' => $this->estadoReclamo,
        ];
    }
}
