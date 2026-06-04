<?php

namespace App\DTOs\Reclamacion;

use Illuminate\Http\Request;

class CreateReclamacionDTO
{
    public function __construct(
        public readonly string $nombre,
        public readonly string $apellido,
        public readonly string $documento,
        public readonly string $numeroDocumento,
        public readonly string $email,
        public readonly string $celular,
        public readonly string $direccion,
        public readonly string $distrito,
        public readonly string $ciudad,
        public readonly string $tipoReclamo,
        public readonly int $id_servicio,
        public readonly string $reclamoPerson,
        public readonly bool $checkReclamoForm,
        public readonly bool $aceptaPoliticaPrivacidad,
        public readonly string $fechaIncidente
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            nombre: $request->input('nombre'),
            apellido: $request->input('apellido'),
            documento: $request->input('documento'),
            numeroDocumento: $request->input('numeroDocumento'),
            email: $request->input('email'),
            celular: $request->input('celular'),
            direccion: $request->input('direccion'),
            distrito: $request->input('distrito'),
            ciudad: $request->input('ciudad'),
            tipoReclamo: $request->input('tipoReclamo'),
            id_servicio: (int) $request->input('id_servicio'),
            reclamoPerson: $request->input('reclamoPerson'),
            checkReclamoForm: (bool) $request->input('checkReclamoForm'),
            aceptaPoliticaPrivacidad: (bool) $request->input('aceptaPoliticaPrivacidad'),
            fechaIncidente: $request->input('fechaIncidente')
        );
    }

    public function toArray(): array
    {
        return [
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
            'fechaIncidente' => $this->fechaIncidente
        ];
    }
}
