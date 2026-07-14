<?php
namespace App\DTOs\Testimonio;

class UpdateTestimonioDTO
{
    public function __construct(public readonly array $data) {}

    public static function fromRequest($request): self
    {
        return new self($request->only(['nombre', 'cargo', 'texto', 'rating','fecha_testimonio']));
    }
}