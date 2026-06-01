<?php

namespace App\Services;

use App\Repositories\ReclamacionRepository;
use App\DTOs\Reclamacion\CreateReclamacionDTO;
use App\DTOs\Reclamacion\UpdateReclamacionDTO;
use App\Models\libroreclamacion;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ReclamacionService
{
    public function __construct(
        private ReclamacionRepository $repository
    ) {}

    public function getReclamaciones(int $perPage = 4): LengthAwarePaginator
    {
        return $this->repository->getPaginated($perPage);
    }

    public function getReclamacionById(int $id): libroreclamacion
    {
        $reclamacion = $this->repository->findById($id);
        
        if (!$reclamacion) {
            throw new ModelNotFoundException('Contacto no encontrado');
        }
        
        return $reclamacion;
    }

    public function createReclamacion(CreateReclamacionDTO $dto): libroreclamacion
    {
        return $this->repository->create($dto);
    }

    public function updateEstado(int $id, UpdateReclamacionDTO $dto): libroreclamacion
    {
        $reclamacion = $this->getReclamacionById($id);
        
        // El DTO actualiza el campo estadoReclamo en la base de datos
        $this->repository->update($reclamacion, $dto);
        
        return $reclamacion->fresh();
    }

    public function deleteReclamacion(int $id): void
    {
        $reclamacion = $this->repository->findById($id);
        
        if (!$reclamacion) {
            throw new ModelNotFoundException('Reclamación no encontrada');
        }
        
        $this->repository->delete($reclamacion);
    }
}
