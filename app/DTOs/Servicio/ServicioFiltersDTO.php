<?php

namespace App\DTOs\Servicio;

use Illuminate\Http\Request;

class ServicioFiltersDTO
{
    public function __construct(
        public readonly int $perPage
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            perPage: (int) $request->input('per_page', 20)
        );
    }
}
