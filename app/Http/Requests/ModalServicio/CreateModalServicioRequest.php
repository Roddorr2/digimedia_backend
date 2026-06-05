<?php

namespace App\Http\Requests\ModalServicio;

use Illuminate\Foundation\Http\FormRequest;

class CreateModalServicioRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nombre'         => 'required|string|max:100',
            'telefono'       => 'required|string|max:9',
            'correo'         => 'required|email|max:200',
            'id_servicio'    => 'required|integer|min:1',
            'id_subservicio' => 'nullable|integer|min:1',
        ];
    }
}
