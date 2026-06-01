<?php

namespace App\DTOs\Empleado;

use Illuminate\Http\Request;

class UpdateProfileImageDTO
{
    public function __construct(
        public readonly string $public_id,
        public readonly string $secure_url
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            public_id: $request->input('public_id'),
            secure_url: $request->input('secure_url')
        );
    }
}
