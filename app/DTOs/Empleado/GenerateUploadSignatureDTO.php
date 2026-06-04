<?php

namespace App\DTOs\Empleado;

use Illuminate\Http\Request;

class GenerateUploadSignatureDTO
{
    public function __construct(
        public readonly array $params
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            params: $request->all()
        );
    }
}
