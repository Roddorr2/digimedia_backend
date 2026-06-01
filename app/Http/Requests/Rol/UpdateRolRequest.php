<?php

namespace App\Http\Requests\Rol;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRolRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $id = $this->route('id');

        return [
            'nombre' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'nombre')->ignore($id, 'id_rol'),
            ],
            'permisos'   => 'nullable|array',
            'permisos.*' => 'exists:permisos,id_permiso',
        ];
    }
}
