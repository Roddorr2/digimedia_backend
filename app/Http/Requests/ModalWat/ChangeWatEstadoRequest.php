<?php

namespace App\Http\Requests\ModalWat;

use Illuminate\Foundation\Http\FormRequest;

class ChangeWatEstadoRequest extends FormRequest
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
            'estado' => 'required|integer|in:0,1',
            'error'  => 'nullable|string|max:500',
        ];
    }
}
