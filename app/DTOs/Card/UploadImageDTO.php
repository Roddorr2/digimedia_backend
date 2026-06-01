<?php

namespace App\DTOs\Card;

use Illuminate\Http\UploadedFile;

class UploadImageDTO
{
    public function __construct(
        public readonly UploadedFile $file,
        public readonly string $name,
        public readonly string $type, // 'header', 'body', 'footer'
        public readonly int $cardId
    ) {}

    public static function fromRequest(UploadedFile $file, string $name, string $type, int $cardId): self
    {
        return new self(
            file: $file,
            name: $name,
            type: $type,
            cardId: $cardId
        );
    }
}