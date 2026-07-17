<?php

namespace App\Http\Requests\CommendTarjeta;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateCommendTarjetaRequest extends FormRequest
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
            'titulo' => 'nullable|string|max:255',
            'texto1' => 'nullable|string|max:255',
            'texto2' => 'nullable|string|max:255',
            'texto3' => 'nullable|string|max:255',
            'texto4' => 'nullable|string|max:255',
            'texto5' => 'nullable|string|max:255',
            'palabra' => 'nullable|string|max:255',
            'enlace' => 'nullable|string|max:255',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json(['errors' => $validator->errors()], 400)
        );
    }
}
