<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Autorizar la request
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Reglas de validación
     */
    public function rules(): array
    {
        return [
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
            // 'captcha_token' => ['required', 'string'], // activar si usas Turnstile
        ];
    }

    /**
     * Mensajes personalizados (opcional)
     */
    public function messages(): array
    {
        return [
            'email.required'    => 'El email es obligatorio.',
            'email.email'       => 'El email no es válido.',
            'password.required' => 'La contraseña es obligatoria.',
        ];
    }

    /**
     * Normalizar datos antes de validar
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => strtolower($this->email),
        ]);
    }
}

