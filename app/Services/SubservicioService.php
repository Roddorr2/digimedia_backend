<?php

namespace App\Services;

use App\Repositories\SubservicioRepository;
use Illuminate\Support\Collection;

class SubservicioService
{
    public function __construct(
        private SubservicioRepository $repository
    ) {}

    public function getSubservicios(): Collection
    {
        return $this->repository->getAll();
    }

    public function getSubserviciosByServicio(int $idServicio): Collection
    {
        return $this->repository->getByServicio($idServicio);
    }
}
