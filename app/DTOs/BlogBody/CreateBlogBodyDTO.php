<?php

namespace App\DTOs\BlogBody;

use App\Http\Requests\BlogBody\BaseBlogBodyRequest;

class CreateBlogBodyDTO
{
    public function __construct(
        public readonly string $titulo,
        public readonly string $descripcion,
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
        public readonly ?string $titulo_tarjeta,
        public readonly ?string $titulo_consejos,
        public readonly ?string $bg_color,
        public readonly ?string $bg_type,
        public readonly ?string $bg_colors,
        public readonly ?string $palabra = null,
        public readonly ?string $enlace = null,
        public readonly ?string $titulo_color = null,
        public readonly ?string $descripcion_color = null,
        public readonly ?string $titulo_tarjeta_color = null,
        public readonly ?string $titulo_consejos_color = null,
    ) {}

    public static function fromRequest(BaseBlogBodyRequest $request): static
    {
        $data = $request->validated();
        return new static(
            titulo: $data['titulo'],
            descripcion: $data['descripcion'],
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
            titulo_consejos: $data['titulo_consejos'] ?? null,
            bg_color: $data['bg_color'] ?? null,
            bg_type: $data['bg_type'] ?? null,
            bg_colors: $data['bg_colors'] ?? null,
            palabra: $data['palabra'] ?? null,
            enlace: $data['enlace'] ?? null,
            titulo_color: $data['titulo_color'] ?? null,
            descripcion_color: $data['descripcion_color'] ?? null,
            titulo_tarjeta_color: $data['titulo_tarjeta_color'] ?? null,
            titulo_consejos_color: $data['titulo_consejos_color'] ?? null,


        );
    }

    public static function fromArray(array $data): static
    {
        return new static(
            titulo: $data['titulo'],
            descripcion: $data['descripcion'],
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
            titulo_consejos: $data['titulo_consejos'] ?? null,
            bg_color: $data['bg_color'] ?? null,
            bg_type: $data['bg_type'] ?? null,
            bg_colors: $data['bg_colors'] ?? null,
            palabra: $data['palabra'] ?? null,
            enlace: $data['enlace'] ?? null,
            titulo_color: $data['titulo_color'] ?? null,
            descripcion_color: $data['descripcion_color'] ?? null,
            titulo_tarjeta_color: $data['titulo_tarjeta_color'] ?? null,
            titulo_consejos_color: $data['titulo_consejos_color'] ?? null,

        );
    }

    public function toArray(): array
    {
        return [
            'titulo' => $this->titulo,
            'descripcion' => $this->descripcion,
            'public_image1' => $this->public_image1,
            'url_image1' => $this->url_image1,
            'alt_image1' => $this->alt_image1,
            'title_image1' => $this->title_image1,
            'public_image2' => $this->public_image2,
            'url_image2' => $this->url_image2,
            'alt_image2' => $this->alt_image2,
            'title_image2' => $this->title_image2,
            'public_image3' => $this->public_image3,
            'url_image3' => $this->url_image3,
            'alt_image3' => $this->alt_image3,
            'title_image3' => $this->title_image3,
            'flag_galeria' => $this->flag_galeria,
            'flag_consejos' => $this->flag_consejos,
            'flag_informacion' => $this->flag_informacion,
            'service_url' => $this->service_url,
            'titulo_tarjeta' => $this->titulo_tarjeta,
            'titulo_consejos' => $this->titulo_consejos,
            'bg_color' => $this->bg_color,
            'bg_type' => $this->bg_type,
            'bg_colors' => $this->bg_colors,
            'palabra' => $this->palabra,
            'enlace' => $this->enlace,
            'titulo_color' => $this->titulo_color,
            'descripcion_color' => $this->descripcion_color,
            'titulo_tarjeta_color' => $this->titulo_tarjeta_color,
            'titulo_consejos_color' => $this->titulo_consejos_color

        ];
    }
}