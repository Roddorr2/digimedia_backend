<?php

namespace App\DTOs\Campania;

use Illuminate\Http\Request;

class LeadsFiltersDTO
{
    public function __construct(
        public readonly ?string $estado,
        public readonly ?string $search,
        public readonly int $perPage
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            estado: $request->input('estado'),
            search: $request->input('search'),
            perPage: (int) $request->input('per_page', 15)
        );
    }
}