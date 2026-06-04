<?php

namespace App\Services;

use App\Repositories\RolRepository;
use App\DTOs\Rol\StoreRolDTO;
use App\DTOs\Rol\UpdateRolDTO;
use App\DTOs\Rol\SyncPermisosDTO;
use App\Models\Rol;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class RolService
{
    public function __construct(
        private RolRepository $repository
    ) {}

    /**
     * Get all roles.
     */
    public function getRoles(): Collection
    {
        return $this->repository->getAllWithFields();
    }

    /**
     * Create a new role.
     */
    public function createRol(StoreRolDTO $dto): Rol
    {
        return DB::transaction(function () use ($dto) {
            $rol = $this->repository->create([
                'nombre' => $dto->nombre,
            ]);

            if (!empty($dto->permisos)) {
                $rol->permisos()->attach($dto->permisos);
            }

            return $rol;
        });
    }

    /**
     * Get role by ID with optional relations.
     */
    public function getRolById(int $id, array $relations = []): Rol
    {
        $rol = $this->repository->findById($id, $relations);

        if (!$rol) {
            throw new ModelNotFoundException('Rol no encontrado');
        }

        return $rol;
    }

    /**
     * Update an existing role.
     */
    public function updateRol(int $id, UpdateRolDTO $dto): Rol
    {
        return DB::transaction(function () use ($id, $dto) {
            $rol = $this->getRolById($id);

            $this->repository->update($rol, [
                'nombre' => $dto->nombre,
            ]);

            if ($dto->permisos !== null) {
                $rol->permisos()->sync($dto->permisos);
            }

            return $rol->fresh();
        });
    }

    /**
     * Delete a role, ensuring no employees are associated.
     */
    public function deleteRol(int $id): void
    {
        DB::transaction(function () use ($id) {
            $rol = $this->getRolById($id);

            // verificar si hay empleados con este rol
            if ($rol->empleados()->count() > 0) {
                throw new \RuntimeException('No se puede eliminar el rol porque tiene empleados asociados');
            }

            // eliminar la relación con permisos
            $rol->permisos()->detach();
            $this->repository->delete($rol);
        });
    }

    /**
     * Get all permissions of a role.
     */
    public function getPermisosDeRol(int $id): Collection
    {
        $rol = $this->getRolById($id, ['permisos']);
        return $rol->permisos;
    }

    /**
     * Sync permissions of a role.
     */
    public function syncPermisosDeRol(int $id, SyncPermisosDTO $dto): Collection
    {
        return DB::transaction(function () use ($id, $dto) {
            $rol = $this->getRolById($id);

            $rol->permisos()->sync($dto->permisos);

            return $rol->permisos()->get();
        });
    }
}
