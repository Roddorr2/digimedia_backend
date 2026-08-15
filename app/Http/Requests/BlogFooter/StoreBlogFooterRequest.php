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

    protected function prepareForValidation(): void
    {
        $data = [];

        if ($this->has('imagenes')) {
            $imagenes = $this->input('imagenes', []);
            if (is_array($imagenes)) {
                foreach ($imagenes as $index => $imagen) {
                    $slot = $index + 1;
                    if (!is_array($imagen)) {
                        continue;
                    }
                    if (array_key_exists('public_image', $imagen)) {
                        $data["public_image{$slot}"] = $imagen['public_image'];
                    }
                    if (array_key_exists('url_image', $imagen)) {
                        $data["url_image{$slot}"] = $imagen['url_image'];
                    }
                    if (array_key_exists('alt', $imagen)) {
                        $data["alt_image{$slot}"] = $imagen['alt'];
                    }
                    if (array_key_exists('title', $imagen)) {
                        $data["title_image{$slot}"] = $imagen['title'];
                    }
                }
            }
        }

        if ($this->has('link')) {
            $link = $this->input('link', []);
            if (is_array($link)) {
                if (array_key_exists('palabra', $link)) {
                    $data['palabra'] = $link['palabra'];
                }
                if (array_key_exists('enlace', $link)) {
                    $data['enlace'] = $link['enlace'];
                }
            }
        }

        if (!empty($data)) {
            $this->merge($data);
        }
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
'alt_image1' => 'nullable|string|min:10|max:240',
             'title_image1' => 'nullable|string|min:10|max:140',
             'alt_image2' => 'nullable|string|min:10|max:240',
             'title_image2' => 'nullable|string|min:10|max:140',
             'alt_image3' => 'nullable|string|min:10|max:240',
             'title_image3' => 'nullable|string|min:10|max:140',
            'estado' => 'nullable|boolean',
            'bg_color' => 'nullable|string|max:7',
            'bg_type' => 'nullable|string|in:solid,gradient|max:20',
            'bg_colors' => 'nullable|string|max:50',
            'titulo_color' => 'nullable|regex:/^#[0-9A-Fa-f]{6}$/',
            'descripcion_color' => 'nullable|regex:/^#[0-9A-Fa-f]{6}$/',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json(['errors' => $validator->errors()], 400)
        );
    }
}
