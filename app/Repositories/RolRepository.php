<?php

namespace App\Repositories;

use App\Models\Rol;
use Illuminate\Database\Eloquent\Collection;

class RolRepository
{
    /**
     * Get all roles selecting id_rol and nombre.
     */
    public function getAllWithFields(): Collection
    {
        return Rol::select('id_rol', 'nombre')->get();
    }

    /**
     * Find a role by ID with optional relations loaded.
     */
    public function findById(int $id, array $relations = []): ?Rol
    {
        $query = Rol::query();

        if (!empty($relations)) {
            $query->with($relations);
        }

        return $query->find($id);
    }

    /**
     * Create a new role.
     */
    public function create(array $data): Rol
    {
        return Rol::create($data);
    }

    /**
     * Update an existing role.
     */
    public function update(Rol $rol, array $data): bool
    {
        return $rol->update($data);
    }

    /**
     * Delete a role.
     */
    public function delete(Rol $rol): ?bool
    {
        return $rol->delete();
    }
}
