<?php

namespace App\Http\Requests\Blog;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateBlogRequest extends FormRequest
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
            'id_blog_head' => 'sometimes|required|integer|exists:blog_heads,id_blog_head',
            'id_blog_body' => 'sometimes|required|integer|exists:blog_bodies,id_blog_body',
            'id_blog_footer' => 'sometimes|required|integer|exists:blog_footers,id_blog_footer',
            'fecha' => 'sometimes|date',
            'id_empleado' => 'sometimes|required|integer|exists:empleados,id_empleado',
            'descripcion' => 'sometimes|nullable|string',

            // BlogHead fields
            'titulo' => 'sometimes|string|max:50',
            'texto_frase' => 'sometimes|string|max:140',
            'texto_descripcion' => 'sometimes|string|max:240',
            'public_image' => 'sometimes|string',
            'url_image' => 'sometimes|nullable|string',
            'alt' => 'sometimes|nullable|string|min:60|max:240',
            'title' => 'sometimes|nullable|string|min:50|max:140',
            'meta_title' => 'sometimes|nullable|string|min:50|max:120',
            'meta_descripcion' => 'sometimes|nullable|string|min:150|max:255',

            // BlogBody fields
            'id_commend_tarjeta' => 'sometimes|nullable|integer|exists:commend_tarjetas,id_commend_tarjeta',
            'public_image1' => 'sometimes|nullable|string',
            'url_image1' => 'sometimes|nullable|string',
            'alt_image1' => 'sometimes|nullable|string|min:60|max:240',
            'title_image1' => 'sometimes|nullable|string|min:50|max:140',
            'public_image2' => 'sometimes|nullable|string',
            'url_image2' => 'sometimes|nullable|string',
            'alt_image2' => 'sometimes|nullable|string|min:60|max:240',
            'title_image2' => 'sometimes|nullable|string|min:50|max:140',
            'public_image3' => 'sometimes|nullable|string',
            'url_image3' => 'sometimes|nullable|string',
            'alt_image3' => 'sometimes|nullable|string|min:60|max:240',
            'title_image3' => 'sometimes|nullable|string|min:50|max:140',
            'flag_galeria' => 'sometimes|nullable|boolean',
            'flag_consejos' => 'sometimes|nullable|boolean',
            'flag_informacion' => 'sometimes|nullable|boolean',
            'service_url' => 'sometimes|nullable|string|max:255',
            'titulo_tarjeta' => 'sometimes|nullable|string',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json(['errors' => $validator->errors()], 400)
        );
    }
}
