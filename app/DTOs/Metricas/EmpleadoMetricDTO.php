<?php

namespace App\DTOs\Metricas;

use Illuminate\Http\Request;

class EmpleadoMetricDTO
{
    public function __construct(
        public readonly int $id_empleado,
        public readonly MonthYearDTO $dateDto
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            id_empleado: (int)$request->input('id_empleado'),
            dateDto: MonthYearDTO::fromRequest($request)
        );
    }
}
