<?php

namespace App\DTOs\Contactanos;

use Illuminate\Http\Request;

class UpdateContactanosDTO
{
    public function __construct(
        public readonly bool $estado
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            estado: (bool) $request->input('estado')
        );
    }

    public function toArray(): array
    {
        return [
            'estado' => $this->estado
        ];
    }
}
