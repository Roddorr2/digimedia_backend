<?php

namespace App\DTOs\Servicio;

use Illuminate\Http\Request;

class UpdateServicioDTO
{
    public function __construct(
        public readonly string $nombre,
        public readonly string $descripcion
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            nombre: $request->input('nombre'),
            descripcion: $request->input('descripcion')
        );
    }

    public function toArray(): array
    {
        return [
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion
        ];
    }
}
