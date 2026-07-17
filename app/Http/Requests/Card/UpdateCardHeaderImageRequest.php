<?php

namespace App\Http\Requests\Card;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateCardHeaderImageRequest extends FormRequest
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
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,webp,avif,jfif|max:20480',
            'alt' => 'nullable|string|min:10|max:240',
            'title' => 'nullable|string|min:10|max:140',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'status' => 201,
                'data' => 'Guardar ruta imagen local',
                'message' => 'No se ha enviado la imagen'
            ], 201)
        );
    }
}
