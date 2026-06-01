<?php

namespace App\DTOs\Metricas;

use Illuminate\Http\Request;

class CardMetricDTO
{
    public function __construct(
        public readonly int $id_plantilla,
        public readonly MonthYearDTO $dateDto
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            id_plantilla: (int)$request->input('id_plantilla'),
            dateDto: MonthYearDTO::fromRequest($request)
        );
    }
}
