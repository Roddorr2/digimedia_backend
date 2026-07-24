<?php

namespace App\DTOs\Consejo;

use Illuminate\Http\Request;

class CreateConsejoDTO
{
    public function __construct(
        public readonly string $texto,
        public readonly ?string $enlace,
        public readonly ?string $palabra,
        public readonly int $orden,
        public readonly int $id_blog_body,
        public readonly ?string $texto_color
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            texto: $request->input('texto'),
            enlace: $request->input('enlace'),
            palabra: $request->input('palabra'),
            orden: (int) $request->input('orden', 0),
            id_blog_body: (int) $request->input('id_blog_body'),
            texto_color: $request->input('texto_color'),
        );
    }

    public function toArray(): array
    {
        return [
            'texto' => $this->texto,
            'enlace' => $this->enlace,
            'palabra' => $this->palabra,
            'orden' => $this->orden,
            'id_blog_body' => $this->id_blog_body,
            'texto_color' => $this->texto_color,
        ];
    }
}
