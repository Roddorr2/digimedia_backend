<?php

namespace App\DTOs\Blog;

use Illuminate\Http\Request;

class FiltrosBlogDTO
{
    public function __construct(
        public readonly ?int $month,
        public readonly ?int $year
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            month: $request->input('month'),
            year: $request->input('year')
        );
    }

    public function hasFilters(): bool
    {
        return $this->month !== null || $this->year !== null;
    }
}