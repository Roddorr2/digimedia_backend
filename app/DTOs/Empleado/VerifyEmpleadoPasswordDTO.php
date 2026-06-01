<?php

namespace App\DTOs\Empleado;

use Illuminate\Http\Request;

class VerifyEmpleadoPasswordDTO
{
    public function __construct(
        public readonly int $id_empleado,
        public readonly string $currentPassword
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            id_empleado: (int) $request->input('id_empleado'),
            currentPassword: $request->input('currentPassword')
        );
    }
}
