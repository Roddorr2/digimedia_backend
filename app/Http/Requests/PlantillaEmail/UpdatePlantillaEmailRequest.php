<?php

namespace App\Http\Requests\PlantillaEmail;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdatePlantillaEmailRequest extends FormRequest
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
            'asunto' => 'required|string|max:255',
            'encabezado' => 'required|string|max:500',
            'mensaje' => 'required|string|max:10000',
            'imagen' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120', // 5MB
            'mensaje_boton' => 'nullable|string|max:100',
            'url_boton' => 'nullable|url|max:500',
            'footer' => 'nullable|string|max:500',
            'red_facebook' => 'nullable|url|max:255',
            'red_tiktok' => 'nullable|url|max:255',
            'red_instagram' => 'nullable|url|max:255',
            'red_linkedin' => 'nullable|url|max:255'
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422)
        );
    }
}
