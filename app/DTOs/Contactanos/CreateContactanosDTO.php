<?php

namespace App\DTOs\Contactanos;

use Illuminate\Http\Request;

class CreateContactanosDTO
{
    public function __construct(
        public readonly string $nombre,
        public readonly string $email,
        public readonly string $numero,
        public readonly string $mensaje
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            nombre: $request->input('nombre'),
            email: $request->input('email'),
            numero: $request->input('numero'),
            mensaje: $request->input('mensaje')
        );
    }

    public function toArray(): array
    {
        return [
            'nombre' => $this->nombre,
            'email' => $this->email,
            'numero' => $this->numero,
            'mensaje' => $this->mensaje
        ];
    }
}
