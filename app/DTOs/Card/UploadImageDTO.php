<?php

namespace App\DTOs\Card;

use Illuminate\Http\UploadedFile;

class UploadImageDTO
{
    public function __construct(
        public readonly UploadedFile $file,
        public readonly string $name,
        public readonly string $type,
        public readonly int $cardId,
        public readonly ?string $alt = null,
        public readonly ?string $title = null,
    ) {}

    public static function fromRequest(UploadedFile $file, string $name, string $type, int $cardId, ?string $alt = null, ?string $title = null): self
    {
        return new self(
            file: $file,
            name: $name,
            type: $type,
            cardId: $cardId,
            alt: $alt,
            title: $title
        );
    }
}