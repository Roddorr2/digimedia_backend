<?php

namespace App\DTOs\Tarjeta;

use Illuminate\Http\Request;

class UpdateTarjetaDTO
{
    public function __construct(
        public readonly string $titulo,
        public readonly string $descripcion,
        public readonly ?string $enlace,
        public readonly ?string $palabra,
        public readonly int $id_blog_body
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            titulo: $request->input('titulo'),
            descripcion: $request->input('descripcion'),
            enlace: $request->input('enlace'),
            palabra: $request->input('palabra'),
            id_blog_body: (int) $request->input('id_blog_body')
        );
    }

    public function toArray(): array
    {
        return [
            'titulo' => $this->titulo,
            'descripcion' => $this->descripcion,
            'enlace' => $this->enlace,
            'palabra' => $this->palabra,
            'id_blog_body' => $this->id_blog_body
        ];
    }
}
