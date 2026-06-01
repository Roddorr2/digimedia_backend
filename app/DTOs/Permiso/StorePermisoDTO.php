<?php

namespace App\DTOs\Permiso;

use App\Http\Requests\Permiso\StorePermisoRequest;

class StorePermisoDTO
{
    public function __construct(
        public readonly string $nombre,
        public readonly ?string $descripcion
    ) {}

    public static function fromRequest(StorePermisoRequest $request): self
    {
        return new self(
            nombre: $request->input('nombre'),
            descripcion: $request->input('descripcion')
        );
    }

    public function toArray(): array
    {
        return [
            'nombre'      => $this->nombre,
            'descripcion' => $this->descripcion,
        ];
    }
}
