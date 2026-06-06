<?php

namespace App\DTOs\Card;

use App\Http\Requests\Card\StoreCardRequest;

class CreateCardDTO
{
    public function __construct(
        public readonly int $id_plantilla,
        public readonly int $id_blog,
        public readonly int $id_empleado,
        public readonly string $titulo,
        public readonly string $descripcion,
        public readonly ?bool $estado_publicacion
    ) {}

    public static function fromRequest(StoreCardRequest $request): self
    {
        $data = $request->validated();
        return new self(
            id_plantilla: $data['id_plantilla'],
            id_blog: $data['id_blog'],
            id_empleado: $data['id_empleado'],
            titulo: $data['titulo'],
            descripcion: $data['descripcion'],
            estado_publicacion: $data['estado_publicacion'] ?? null
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
            'public_image' => '/blog/fondo_blog_extend.webp',
            'url_image' => '',
        ];
        
        if ($this->estado_publicacion !== null) {
            $data['estado_publicacion'] = $this->estado_publicacion;
        }
        
        return $data;
    }
}