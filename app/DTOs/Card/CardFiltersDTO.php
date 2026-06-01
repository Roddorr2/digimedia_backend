<?php

namespace App\DTOs\Card;

use Illuminate\Http\Request;

class CardFiltersDTO
{
    public function __construct(
        public readonly ?int $empleadoId,
        public readonly ?bool $publicOnly
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            empleadoId: $request->input('empleado_id') ? (int) $request->input('empleado_id') : null,
            publicOnly: $request->input('public_only') ? (bool) $request->input('public_only') : false
        );
    }
}