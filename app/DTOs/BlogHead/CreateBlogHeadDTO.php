<?php

namespace App\DTOs\BlogHead;

use Illuminate\Foundation\Http\FormRequest;

class CreateBlogHeadDTO
{
    public function __construct(
        public readonly string $titulo,
        public readonly string $meta_description,
        public readonly ?string $meta_keywords,
        public readonly ?string $meta_author,
        public readonly ?string $og_title,
        public readonly ?string $og_description,
        public readonly ?string $og_image,
        public readonly ?string $twitter_card,
        public readonly ?string $twitter_title,
        public readonly ?string $twitter_description,
        public readonly ?string $twitter_image,
        public readonly ?string $canonical_url
    ) {}

    public static function fromRequest(FormRequest $request): static
    {
        $data = $request->validated();
        return new static(
            titulo: $data['titulo'],
            meta_description: $data['meta_description'],
            meta_keywords: $data['meta_keywords'] ?? null,
            meta_author: $data['meta_author'] ?? null,
            og_title: $data['og_title'] ?? null,
            og_description: $data['og_description'] ?? null,
            og_image: $data['og_image'] ?? null,
            twitter_card: $data['twitter_card'] ?? null,
            twitter_title: $data['twitter_title'] ?? null,
            twitter_description: $data['twitter_description'] ?? null,
            twitter_image: $data['twitter_image'] ?? null,
            canonical_url: $data['canonical_url'] ?? null
        );
    }

    public function toArray(): array
    {
        return array_filter(get_object_vars($this), fn($value) => $value !== null);
    }
}