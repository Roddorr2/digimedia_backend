<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para hacer esta request.
     */
    public function authorize(): bool
    {
        return true; // importante: permitir la request
    }

    /**
     * Reglas de validación.
     */
    public function rules(): array
    {
        return [
            'nombre'   => ['required', 'string', 'max:255'],
            'apellido' => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:empleados,email', 'unique:users,email'],
            'dni'      => ['required', 'string', 'max:20', 'unique:empleados,dni'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'id_rol'   => ['required', 'exists:roles,id_rol'],
        ];
    }

    /**
     * Mensajes personalizados (opcional pero recomendable).
     */
    public function messages(): array
    {
        return [
            'nombre.required'   => 'El nombre es obligatorio.',
            'apellido.required' => 'El apellido es obligatorio.',
            'email.required'    => 'El email es obligatorio.',
            'email.email'       => 'El email no tiene un formato válido.',
            'email.unique'      => 'El email ya está registrado.',
            'dni.required'      => 'El DNI es obligatorio.',
            'dni.unique'        => 'El DNI ya está registrado.',
            'id_rol.required'   => 'El rol es obligatorio.',
            'id_rol.exists'     => 'El rol seleccionado no existe.',
        ];
    }

    /**
     * Opcional: limpiar/normalizar datos antes de validar.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => strtolower($this->email),
        ]);
    }
}

