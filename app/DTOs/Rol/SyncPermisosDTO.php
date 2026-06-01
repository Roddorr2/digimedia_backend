<?php

namespace App\DTOs\Rol;

use App\Http\Requests\Rol\SyncPermisosRequest;

class SyncPermisosDTO
{
    public function __construct(
        public readonly array $permisos
    ) {}

    public static function fromRequest(SyncPermisosRequest $request): self
    {
        return new self(
            permisos: $request->input('permisos', [])
        );
    }
}
