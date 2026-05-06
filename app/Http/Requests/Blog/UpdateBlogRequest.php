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
            'id_blog_head' => 'required|integer|exists:blog_heads,id_blog_head',
            'id_blog_body' => 'required|integer|exists:blog_bodies,id_blog_body',
            'id_blog_footer' => 'required|integer|exists:blog_footers,id_blog_footer',
            'fecha' => 'required|date',
            'id_empleado' => 'required|integer|exists:empleados,id_empleado',
            'descripcion' => 'nullable|string',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json(['errors' => $validator->errors()], 400)
        );
    }
}
