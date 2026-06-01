<?php

namespace App\DTOs\BlogBody;

use App\Http\Requests\BlogBody\BaseBlogBodyRequest;

class CreateBlogBodyDTO
{
    public function __construct(
        public readonly string $titulo,
        public readonly string $descripcion,
        public readonly ?int $id_commend_tarjeta,
        public readonly ?string $public_image1,
        public readonly ?string $url_image1,
        public readonly ?string $alt_image1,
        public readonly ?string $title_image1,
        public readonly ?string $public_image2,
        public readonly ?string $url_image2,
        public readonly ?string $alt_image2,
        public readonly ?string $title_image2,
        public readonly ?string $public_image3,
        public readonly ?string $url_image3,
        public readonly ?string $alt_image3,
        public readonly ?string $title_image3,
        public readonly ?bool $flag_galeria,
        public readonly ?bool $flag_consejos,
        public readonly ?bool $flag_informacion,
        public readonly ?string $service_url,
        public readonly ?string $titulo_tarjeta
    ) {}

    public static function fromRequest(BaseBlogBodyRequest $request): static
    {
        $data = $request->validated();
        return new static(
            titulo: $data['titulo'],
            descripcion: $data['descripcion'],
            id_commend_tarjeta: $data['id_commend_tarjeta'] ?? null,
            public_image1: $data['public_image1'] ?? null,
            url_image1: $data['url_image1'] ?? null,
            alt_image1: $data['alt_image1'] ?? null,
            title_image1: $data['title_image1'] ?? null,
            public_image2: $data['public_image2'] ?? null,
            url_image2: $data['url_image2'] ?? null,
            alt_image2: $data['alt_image2'] ?? null,
            title_image2: $data['title_image2'] ?? null,
            public_image3: $data['public_image3'] ?? null,
            url_image3: $data['url_image3'] ?? null,
            alt_image3: $data['alt_image3'] ?? null,
            title_image3: $data['title_image3'] ?? null,
            flag_galeria: $data['flag_galeria'] ?? null,
            flag_consejos: $data['flag_consejos'] ?? null,
            flag_informacion: $data['flag_informacion'] ?? null,
            service_url: $data['service_url'] ?? null,
            titulo_tarjeta: $data['titulo_tarjeta'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter(get_object_vars($this), fn($value) => $value !== null);
    }
}