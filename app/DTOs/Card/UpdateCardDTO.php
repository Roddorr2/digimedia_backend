<?php

namespace App\DTOs\Card;

use App\Http\Requests\Card\UpdateCardRequest;

class UpdateCardDTO
{
    public function __construct(
        public readonly int $id_plantilla,
        public readonly int $id_blog,
        public readonly int $id_empleado,
        public readonly string $titulo,
        public readonly string $descripcion,
        public readonly ?bool $estado_publicacion,
        public readonly ?string $public_image,
        public readonly ?string $url_image
    ) {}

    public static function fromRequest(UpdateCardRequest $request): self
    {
        $data = $request->validated();
        return new self(
            id_plantilla: $data['id_plantilla'],
            id_blog: $data['id_blog'],
            id_empleado: $data['id_empleado'],
            titulo: $data['titulo'],
            descripcion: $data['descripcion'],
            estado_publicacion: $data['estado_publicacion'] ?? null,
            public_image: $data['public_image'] ?? null,
            url_image: $data['url_image'] ?? null
        );
    }

    public function toArray(): array
    {
        $data = [
            'id_plantilla' => $this->id_plantilla,
            'id_blog' => $this->id_blog,
            'id_empleado' => $this->id_empleado,
            'titulo' => $this->titulo,
            'descripcion' => $this->descripcion,
        ];
        
        if ($this->estado_publicacion !== null) {
            $data['estado_publicacion'] = $this->estado_publicacion;
        }
        
        if ($this->public_image !== null) {
            $data['public_image'] = $this->public_image;
        }
        
        if ($this->url_image !== null) {
            $data['url_image'] = $this->url_image;
        }
        
        return $data;
    }
}