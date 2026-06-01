<?php

namespace App\Services;

use App\Repositories\BlogAuditoriaRepository;
use App\DTOs\BlogAuditoria\FiltrosAuditoriaDTO;

class BlogAuditoriaService
{
    public function __construct(
        private BlogAuditoriaRepository $repository
    ) {}

    public function obtenerAuditorias(FiltrosAuditoriaDTO $filtros): array
    {
        $auditorias = $this->repository->getAuditoriasPaginadas(
            blogId: $filtros->blogId,
            perPage: $filtros->perPage,
            orderBy: $filtros->orderBy,
            orderDir: $filtros->orderDir
        );

        $hasResults = $auditorias->total() > 0;

        return [
            'has_results' => $hasResults,
            'auditorias' => $auditorias,
        ];
    }
}