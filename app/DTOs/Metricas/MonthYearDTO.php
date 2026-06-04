<?php

namespace App\DTOs\Metricas;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class MonthYearDTO
{
    public function __construct(
        public readonly int $month,
        public readonly int $year
    ) {}

    public static function fromRequest(Request $request): self
    {
        $month = $request->input('month') ? (int)$request->input('month') : (int)Carbon::now()->month;
        $year  = $request->input('year') ? (int)$request->input('year') : (int)Carbon::now()->year;

        return new self(month: $month, year: $year);
    }
}
