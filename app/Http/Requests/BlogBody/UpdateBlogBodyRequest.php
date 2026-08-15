<?php

namespace App\Http\Requests\BlogBody;

class UpdateBlogBodyRequest extends BaseBlogBodyRequest
{
    public function rules(): array
    {
        return [
            'titulo' => 'sometimes|string|max:255',
            'descripcion' => 'sometimes|string',
            'public_image1' => 'sometimes|nullable|string',
            'url_image1' => 'sometimes|nullable|string',
            'alt_image1' => 'sometimes|nullable|string|min:10|max:240',
            'title_image1' => 'sometimes|nullable|string|min:10|max:140',
            'public_image2' => 'sometimes|nullable|string',
            'url_image2' => 'sometimes|nullable|string',
            'alt_image2' => 'sometimes|nullable|string|min:10|max:240',
            'title_image2' => 'sometimes|nullable|string|min:10|max:140',
            'public_image3' => 'sometimes|nullable|string',
            'url_image3' => 'sometimes|nullable|string',
            'alt_image3' => 'sometimes|nullable|string|min:10|max:240',
            'title_image3' => 'sometimes|nullable|string|min:10|max:140',
            'flag_galeria' => 'sometimes|nullable|boolean',
            'flag_consejos' => 'sometimes|nullable|boolean',
            'flag_informacion' => 'sometimes|nullable|boolean',
            'service_url' => 'sometimes|nullable|string|max:255',
            'titulo_tarjeta' => 'sometimes|nullable|string',
            'titulo_consejos' => 'sometimes|nullable|string',
            'bg_color' => 'sometimes|nullable|string|max:7',
            'bg_type' => 'sometimes|nullable|string|in:solid,gradient|max:20',
            'bg_colors' => 'sometimes|nullable|string|max:50',
            'palabra' => 'sometimes|nullable|string',
            'enlace' => 'sometimes|nullable|string',
            'titulo_color' => 'sometimes|nullable|regex:/^#[0-9A-Fa-f]{6}$/',
            'descripcion_color' => 'sometimes|nullable|regex:/^#[0-9A-Fa-f]{6}$/',
            'titulo_tarjeta_color' => 'sometimes|nullable|regex:/^#[0-9A-Fa-f]{6}$/',
            'titulo_consejos_color' => 'sometimes|nullable|regex:/^#[0-9A-Fa-f]{6}$/',
        ];
    }
}
