<?php

namespace App\Http\Requests\BlogFooter;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreBlogFooterRequest extends FormRequest
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
            'titulo' => 'nullable|string',
            'descripcion' => 'nullable|string',
            'public_image1' => 'nullable|string',
            'url_image1' => 'nullable|string',
            'public_image2' => 'nullable|string',
            'url_image2' => 'nullable|string',
            'public_image3' => 'nullable|string',
            'url_image3' => 'nullable|string',
            'alt_image1' => 'nullable|string|min:60|max:240',
            'title_image1' => 'nullable|string|min:50|max:140',
            'alt_image2' => 'nullable|string|min:60|max:240',
            'title_image2' => 'nullable|string|min:50|max:140',
            'alt_image3' => 'nullable|string|min:60|max:240',
            'title_image3' => 'nullable|string|min:50|max:140',
            'estado' => 'nullable|boolean',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json(['errors' => $validator->errors()], 400)
        );
    }
}
