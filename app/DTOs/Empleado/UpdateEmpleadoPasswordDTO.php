<?php

namespace App\DTOs\Empleado;

use Illuminate\Http\Request;

class UpdateEmpleadoPasswordDTO
{
    public function __construct(
        public readonly string $password
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            password: $request->input('password')
        );
    }
}
