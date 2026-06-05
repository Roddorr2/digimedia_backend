<?php

namespace App\Http\Requests\BlogBody;

use Illuminate\Foundation\Http\FormRequest;

abstract class BaseBlogBodyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'id_commend_tarjeta' => 'nullable|integer|exists:commend_tarjetas,id_commend_tarjeta',
            'public_image1' => 'nullable|string',
            'url_image1' => 'nullable|string',
            'alt_image1' => 'nullable|string|min:60|max:240',
            'title_image1' => 'nullable|string|min:50|max:140',
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
            'titulo_tarjeta' => 'nullable|string',
            'bg_color' => 'nullable|string|max:7',
            'bg_type' => 'nullable|string|in:solid,gradient|max:20',
            'bg_colors' => 'nullable|string|max:50',
        ];
    }
}