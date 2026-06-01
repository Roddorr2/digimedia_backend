<?php

namespace App\DTOs\Rol;

use App\Http\Requests\Rol\UpdateRolRequest;

class UpdateRolDTO
{
    public function __construct(
        public readonly string $nombre,
        public readonly ?array $permisos
    ) {}

    public static function fromRequest(UpdateRolRequest $request): self
    {
        return new self(
            nombre: $request->input('nombre'),
            permisos: $request->has('permisos') ? $request->input('permisos') : null
        );
    }
}
