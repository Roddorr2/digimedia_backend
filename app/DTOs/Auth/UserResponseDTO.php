<?php

namespace App\DTOs\Auth;

use App\Models\User;
use App\Models\Empleado;

class UserResponseDTO
{
    private function __construct(
        public readonly array $data
    ) {}

    public static function fromUserEmpleadoRol(User $user, Empleado $empleado, string $rolNombre, array $permisos, ?string $token = null): self
    {
        $userArray = $user->toArray();
        unset($userArray['empleado']);

        $response = [
            'user' => $userArray,
            'empleado' => [
                'id' => $empleado->id,
                'nombre' => $empleado->nombre,
                'apellido' => $empleado->apellido,
                'email' => $empleado->email,
                'dni' => $empleado->dni,
                'telefono' => $empleado->telefono,
                'rol_nombre' => $rolNombre,
                'permisos' => $permisos,
            ],
            'rol' => $rolNombre,
            'permisos' => $permisos,
        ];

        if ($token) {
            $response['token'] = $token;
        }

        return new self($response);
    }

    public function toArray(): array
    {
        return $this->data;
    }
}