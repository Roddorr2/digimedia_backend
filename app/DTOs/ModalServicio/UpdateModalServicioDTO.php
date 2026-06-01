<?php

namespace App\DTOs\ModalServicio;

use Illuminate\Http\Request;

class UpdateModalServicioDTO
{
    public function __construct(
        public readonly bool $estado
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            estado: (bool)$request->input('estado')
        );
    }
}
