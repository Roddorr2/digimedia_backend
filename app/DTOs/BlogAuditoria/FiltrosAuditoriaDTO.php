<?php

namespace App\DTOs\BlogAuditoria;

use Illuminate\Http\Request;

class FiltrosAuditoriaDTO
{
    public function __construct(
        public readonly ?int $blogId,
        public readonly int $perPage,
        public readonly string $orderBy,
        public readonly string $orderDir
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            blogId: $request->input('blog_id'),
            perPage: (int) $request->input('per_page', 20),
            orderBy: $request->input('order_by', 'fecha_hora'),
            orderDir: $request->input('order_dir', 'desc')
        );
    }
}