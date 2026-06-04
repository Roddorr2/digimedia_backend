<?php

namespace App\DTOs\Campania;

use Illuminate\Http\Request;

class RetryLeadDTO
{
    public function __construct(
        public readonly int $campaniaId,
        public readonly int $watModalId
    ) {}

    public static function fromRequest(Request $request, int $campaniaId, int $watModalId): self
    {
        return new self(
            campaniaId: $campaniaId,
            watModalId: $watModalId
        );
    }
}