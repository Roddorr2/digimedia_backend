<?php

namespace App\DTOs\Testimonio;

class CreateTestimonioDTO
{
    public function __construct(
        public readonly string $nombre,
        public readonly ?string $cargo,
        public readonly string $texto,
        public readonly int $rating,
        public readonly ?string $fecha_testimonio,
    ) {}

    public static function fromRequest($request): self
    {
        return new self(
            nombre: $request->nombre,
            cargo: $request->cargo,
            texto: $request->texto,
            rating: $request->rating,
            fecha_testimonio: $request->fecha_testimonio,
        );
    }

    public function toArray(): array
    {
        return [
            'nombre' => $this->nombre,
            'cargo'  => $this->cargo,
            'texto'  => $this->texto,
            'rating' => $this->rating,
            'fecha_testimonio' => $this->fecha_testimonio,
        ];
    }
}
