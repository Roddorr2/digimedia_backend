<?php

namespace App\DTOs\Campania;

use Illuminate\Http\Request;

class CampaniaFiltersDTO
{
    public function __construct(
        public readonly ?string $estado,
        public readonly int $perPage
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            estado: $request->input('estado'),
            perPage: (int) $request->input('per_page', 10)
        );
    }
}