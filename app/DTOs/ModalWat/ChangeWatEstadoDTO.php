<?php

namespace App\DTOs\ModalWat;

use Illuminate\Http\Request;

class ChangeWatEstadoDTO
{
    public function __construct(
        public readonly int $estado,
        public readonly ?string $error
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            estado: (int)$request->input('estado'),
            error: $request->input('error')
        );
    }
}
