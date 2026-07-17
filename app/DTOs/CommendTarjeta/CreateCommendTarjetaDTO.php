<?php

namespace App\DTOs\CommendTarjeta;

use Illuminate\Http\Request;

class CreateCommendTarjetaDTO
{
    public function __construct(
        public readonly ?string $titulo,
        public readonly ?string $texto1,
        public readonly ?string $texto2,
        public readonly ?string $texto3,
        public readonly ?string $texto4,
        public readonly ?string $texto5,
        public readonly ?string $palabra = null,
        public readonly ?string $enlace = null
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            titulo: $request->input('titulo'),
            texto1: $request->input('texto1'),
            texto2: $request->input('texto2'),
            texto3: $request->input('texto3'),
            texto4: $request->input('texto4'),
            texto5: $request->input('texto5'),
            palabra: $request->input('palabra'),
            enlace: $request->input('enlace')
        );
    }

    public function toArray(): array
    {
        return [
            'titulo' => $this->titulo,
            'texto1' => $this->texto1,
            'texto2' => $this->texto2,
            'texto3' => $this->texto3,
            'texto4' => $this->texto4,
            'texto5' => $this->texto5,
            'palabra' => $this->palabra,
            'enlace' => $this->enlace,
        ];
    }
}
