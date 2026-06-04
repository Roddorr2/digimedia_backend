<?php

namespace App\DTOs\Rol;

use App\Http\Requests\Rol\StoreRolRequest;

class StoreRolDTO
{
    public function __construct(
        public readonly string $nombre,
        public readonly array $permisos
    ) {}

    public static function fromRequest(StoreRolRequest $request): self
    {
        return new self(
            nombre: $request->input('nombre'),
            permisos: $request->input('permisos', [])
        );
    }
}
