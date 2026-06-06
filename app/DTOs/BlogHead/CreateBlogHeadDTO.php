<?php

namespace App\DTOs\BlogHead;

use Illuminate\Foundation\Http\FormRequest;

class CreateBlogHeadDTO
{
    public function __construct(
        public readonly string $titulo,
        public readonly string $texto_frase,
        public readonly string $texto_descripcion,
        public readonly string $public_image,
        public readonly ?string $url_image,
        public readonly ?string $alt,
        public readonly ?string $title,
        public readonly ?string $meta_title,
        public readonly ?string $meta_descripcion,
        public readonly ?string $bg_color,
        public readonly ?string $bg_type,
        public readonly ?string $bg_colors
    ) {}

    public static function fromRequest(FormRequest $request): static
    {
        $data = $request->validated();
        return new static(
            titulo: $data['titulo'],
            texto_frase: $data['texto_frase'],
            texto_descripcion: $data['texto_descripcion'],
            public_image: $data['public_image'],
            url_image: $data['url_image'] ?? null,
            alt: $data['alt'] ?? null,
            title: $data['title'] ?? null,
            meta_title: $data['meta_title'] ?? null,
            meta_descripcion: $data['meta_descripcion'] ?? null,
            bg_color: $data['bg_color'] ?? null,
            bg_type: $data['bg_type'] ?? null,
            bg_colors: $data['bg_colors'] ?? null
        );
    }

    public function toArray(): array
    {
        return array_filter(get_object_vars($this), fn($value) => $value !== null);
    }
}