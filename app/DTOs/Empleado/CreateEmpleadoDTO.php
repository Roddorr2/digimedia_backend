<?php

namespace App\DTOs\Empleado;

use Illuminate\Http\Request;

class CreateEmpleadoDTO
{
    public function __construct(
        public readonly string $nombre,
        public readonly string $apellido,
        public readonly string $email,
        public readonly string $dni,
        public readonly ?string $telefono,
        public readonly int $id_rol
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            nombre: $request->input('nombre'),
            apellido: $request->input('apellido'),
            email: $request->input('email'),
            dni: $request->input('dni'),
            telefono: $request->input('telefono'),
            id_rol: (int) $request->input('id_rol')
        );
    }

    public function toArray(): array
    {
        return [
            'nombre' => $this->nombre,
            'apellido' => $this->apellido,
            'email' => $this->email,
            'dni' => $this->dni,
            'telefono' => $this->telefono,
            'id_rol' => $this->id_rol
        ];
    }
}
