<?php

namespace App\DTOs\BlogFooter;

use Illuminate\Foundation\Http\FormRequest;

class CreateBlogFooterDTO
{
    public function __construct(
        public readonly ?string $titulo,
        public readonly ?string $descripcion,
        public readonly ?string $public_image1,
        public readonly ?string $url_image1,
        public readonly ?string $public_image2,
        public readonly ?string $url_image2,
        public readonly ?string $public_image3,
        public readonly ?string $url_image3,
        public readonly ?string $alt_image1,
        public readonly ?string $title_image1,
        public readonly ?string $alt_image2,
        public readonly ?string $title_image2,
        public readonly ?string $alt_image3,
        public readonly ?string $title_image3,
        public readonly ?bool $estado,
        public readonly ?string $palabra,
        public readonly ?string $enlace
    ) {}

    public static function fromRequest(FormRequest $request): static
    {
        $data = $request->validated();
        return new static(
            titulo: $data['titulo'] ?? null,
            descripcion: $data['descripcion'] ?? null,
            public_image1: $data['public_image1'] ?? null,
            url_image1: $data['url_image1'] ?? null,
            public_image2: $data['public_image2'] ?? null,
            url_image2: $data['url_image2'] ?? null,
            public_image3: $data['public_image3'] ?? null,
            url_image3: $data['url_image3'] ?? null,
            alt_image1: $data['alt_image1'] ?? null,
            title_image1: $data['title_image1'] ?? null,
            alt_image2: $data['alt_image2'] ?? null,
            title_image2: $data['title_image2'] ?? null,
            alt_image3: $data['alt_image3'] ?? null,
            title_image3: $data['title_image3'] ?? null,
            estado: isset($data['estado']) ? (bool)$data['estado'] : null,
            palabra: $data['palabra'] ?? null,
            enlace: $data['enlace'] ?? null
        );
    }

    public function toArray(): array
    {
        return array_filter(get_object_vars($this), fn($value) => $value !== null);
    }
}