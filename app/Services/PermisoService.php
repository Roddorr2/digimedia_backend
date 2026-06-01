<?php

namespace App\Services;

use App\Repositories\PermisoRepository;
use App\DTOs\Permiso\StorePermisoDTO;
use App\DTOs\Permiso\UpdatePermisoDTO;
use App\Models\Permiso;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PermisoService
{
    public function __construct(
        private PermisoRepository $repository
    ) {}

    /**
     * Get all permissions.
     */
    public function getPermisos(): Collection
    {
        return $this->repository->getAll();
    }

    /**
     * Create a permission.
     */
    public function createPermiso(StorePermisoDTO $dto): Permiso
    {
        $data = $dto->toArray();
        $data['slug'] = Str::slug($dto->nombre);

        return $this->repository->create($data);
    }

    /**
     * Get a permission by ID.
     */
    public function getPermisoById(int $id): Permiso
    {
        $permiso = $this->repository->findById($id);

        if (!$permiso) {
            throw new ModelNotFoundException('Permiso no encontrado');
        }

        return $permiso;
    }

    /**
     * Update a permission.
     */
    public function updatePermiso(int $id, UpdatePermisoDTO $dto): Permiso
    {
        $permiso = $this->getPermisoById($id);

        $data = $dto->toArray();
        if ($permiso->nombre !== $dto->nombre) {
            $data['slug'] = Str::slug($dto->nombre);
        }

        $this->repository->update($permiso, $data);

        return $permiso->fresh();
    }

    /**
     * Delete a permission.
     */
    public function deletePermiso(int $id): void
    {
        $permiso = $this->getPermisoById($id);
        $this->repository->delete($permiso);
    }
}
