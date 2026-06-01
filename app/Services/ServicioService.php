<?php

namespace App\Services;

use App\Repositories\ServicioRepository;
use App\DTOs\Servicio\ServicioFiltersDTO;
use App\DTOs\Servicio\CreateServicioDTO;
use App\DTOs\Servicio\UpdateServicioDTO;
use App\Models\servicios;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ServicioService
{
    public function __construct(
        private ServicioRepository $repository
    ) {}

    public function getServicios(ServicioFiltersDTO $filters): LengthAwarePaginator
    {
        return $this->repository->getPaginated($filters);
    }

    public function getServicioById(int $id): servicios
    {
        $servicio = $this->repository->findById($id);
        
        if (!$servicio) {
            throw new ModelNotFoundException('Servicio no encontrado');
        }
        
        return $servicio;
    }

    public function createServicio(CreateServicioDTO $dto): servicios
    {
        return $this->repository->create($dto);
    }

    public function updateServicio(int $id, UpdateServicioDTO $dto): servicios
    {
        $servicio = $this->getServicioById($id);
        
        $this->repository->update($servicio, $dto);
        
        return $servicio->fresh();
    }

    public function deleteServicio(int $id): void
    {
        $servicio = $this->getServicioById($id);
        
        $this->repository->delete($servicio);
    }
}
