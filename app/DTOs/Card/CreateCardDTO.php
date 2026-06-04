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
        public readonly bool $estado_publicacion,
        public readonly ?string $logo,
        public readonly ?string $url_logo
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
            estado_publicacion: $data['estado_publicacion'] ?? false,
            logo: $data['logo'] ?? null,
            url_logo: $data['url_logo'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'id_plantilla' => $this->id_plantilla,
            'id_blog' => $this->id_blog,
            'id_empleado' => $this->id_empleado,
            'titulo' => $this->titulo,
            'descripcion' => $this->descripcion,
            'estado_publicacion' => $this->estado_publicacion,
            'logo' => $this->logo,
            'url_logo' => $this->url_logo,
        ];
    }
}