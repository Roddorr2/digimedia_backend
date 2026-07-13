<?php
namespace App\DTOs\Testimonio;

class UpdateTestimonioImageDTO
{
    public function __construct(
        public readonly string $public_id,
        public readonly string $secure_url,
    ) {}

    public static function fromRequest($request): self
    {
        return new self(
            public_id: $request->public_id,
            secure_url: $request->secure_url,
        );
    }
}