<?php

namespace App\DTOs\Auth;

use App\Http\Requests\Auth\RegisterRequest;

class RegisterDTO
{
    public function __construct(
        public readonly string $nombre,
        public readonly string $apellido,
        public readonly string $email,
        public readonly string $dni,
        public readonly ?string $telefono,
        public readonly int $id_rol
    ) {}

    public static function fromRequest(RegisterRequest $request): self
    {
        $validated = $request->validated();
        return new self(
            nombre: $validated['nombre'],
            apellido: $validated['apellido'],
            email: $validated['email'],
            dni: $validated['dni'],
            telefono: $validated['telefono'] ?? null,
            id_rol: $validated['id_rol']
        );
    }
}