<?php

namespace App\DTOs\Empleado;

use Illuminate\Http\Request;

class UpdateEmpleadoDTO
{
    public function __construct(
        public readonly array $data
    ) {}

    public static function fromRequest(Request $request): self
    {
        $fields = ['nombre', 'apellido', 'email', 'dni', 'telefono', 'id_rol'];
        $data = [];
        foreach ($fields as $field) {
            if ($request->has($field)) {
                if ($field === 'id_rol') {
                    $data[$field] = (int) $request->input($field);
                } else {
                    $data[$field] = $request->input($field);
                }
            }
        }
        return new self($data);
    }
}
