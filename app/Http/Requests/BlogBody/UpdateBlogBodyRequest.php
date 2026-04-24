<?php

namespace App\Http\Requests\BlogBody;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateBlogBodyRequest extends FormRequest
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
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'id_commend_tarjeta' => 'nullable|integer|exists:commend_tarjetas,id_commend_tarjeta',
            'public_image1' => 'nullable|string',
            'url_image1' => 'nullable|string',
            'alt_image1' => 'nullable|string|min:60|max:240', // alt 60-240
            'title_image1' => 'nullable|string|min:50|max:140', // title 50 - 140
            'public_image2' => 'nullable|string',
            'url_image2' => 'nullable|string',
            'alt_image2' => 'nullable|string|min:60|max:240',
            'title_image2' => 'nullable|string|min:50|max:140',
            'public_image3' => 'nullable|string',
            'url_image3' => 'nullable|string',
            'alt_image3' => 'nullable|string|min:60|max:240',
            'title_image3' => 'nullable|string|min:50|max:140',
            'flag_galeria' => 'nullable|boolean',
            'flag_consejos' => 'nullable|boolean',
            'flag_informacion' => 'nullable|boolean',
            'service_url' => 'nullable|string|max:255',
            'titulo_tarjeta' => 'nullable|string', // titulo tarjetas
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json(['errors' => $validator->errors()], 400)
        );
    }
}
