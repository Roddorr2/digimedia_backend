<?php

namespace App\DTOs\ModalServicio;

use Illuminate\Http\Request;

class CreateModalServicioDTO
{
    public function __construct(
        public readonly string $nombre,
        public readonly string $telefono,
        public readonly string $correo,
        public readonly int $id_servicio
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            nombre: $request->input('nombre'),
            telefono: $request->input('telefono'),
            correo: $request->input('correo'),
            id_servicio: (int)$request->input('id_servicio')
        );
    }

    public function toArray(): array
    {
        return [
            'nombre' => $this->nombre,
            'telefono' => $this->telefono,
            'correo' => $this->correo,
            'id_servicio' => $this->id_servicio,
        ];
    }
}
