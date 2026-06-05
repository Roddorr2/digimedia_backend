<?php

namespace App\Http\Requests\BlogHead;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreBlogHeadRequest extends FormRequest
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
            'titulo' => 'required|string|max:50',
            'texto_frase' => 'required|string|max:140',
            'texto_descripcion' => 'required|string|max:240',
            'public_image' => 'required|string',
            'url_image' => 'nullable|string',
            'alt' => 'nullable|string|max:240',
            'title' => 'nullable|string|max:140',
            'meta_title' => 'nullable|string|max:120',
            'meta_descripcion' => 'nullable|string|max:255',
            'bg_color' => 'nullable|string|max:7',
            'bg_type' => 'nullable|string|in:solid,gradient|max:20',
            'bg_colors' => 'nullable|string|max:50',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json(['errors' => $validator->errors()], 400)
        );
    }
}
