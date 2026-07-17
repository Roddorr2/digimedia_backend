<?php

namespace App\Http\Requests\BlogBody;

use Illuminate\Foundation\Http\FormRequest;

abstract class BaseBlogBodyRequest extends FormRequest
{
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

        if ($this->has('flags')) {
            $flags = $this->input('flags', []);
            if (is_array($flags)) {
                if (array_key_exists('galeria', $flags)) {
                    $data['flag_galeria'] = $flags['galeria'];
                }
                if (array_key_exists('consejos', $flags)) {
                    $data['flag_consejos'] = $flags['consejos'];
                }
                if (array_key_exists('informacion', $flags)) {
                    $data['flag_informacion'] = $flags['informacion'];
                }
            }
        }

        if (!empty($data)) {
            $this->merge($data);
        }
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
            'palabra' => 'nullable|string',
            'enlace' => 'nullable|string',
        ];
    }
}