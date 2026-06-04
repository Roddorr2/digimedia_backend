<?php

namespace App\DTOs\ModalMail;

use Illuminate\Http\Request;

class ReportMailErrorDTO
{
    public function __construct(
        public readonly string $error
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            error: $request->input('error')
        );
    }
}
