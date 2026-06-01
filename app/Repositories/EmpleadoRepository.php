<?php

namespace App\Repositories;

use App\Models\Empleado;
use App\Models\Rol;
use App\Models\User;

class EmpleadoRepository
{
    public function createForUser(array $data, int $userId): Empleado
    {
        return Empleado::create([
            'nombre' => $data['nombre'],
            'apellido' => $data['apellido'],
            'email' => $data['email'],
            'dni' => $data['dni'],
            'telefono' => $data['telefono'] ?? null,
            'id_user' => $userId,
            'id_rol' => $data['id_rol'],
        ]);
    }

    public function getByUser(User $user): ?Empleado
    {
        return $user->empleado;
    }

    public function loadRelations(Empleado $empleado): Empleado
    {
        return $empleado->load(['rol', 'subtipoAdmin']);
    }

    public function getRol(Empleado $empleado): ?Rol
    {
        return $empleado->rol;
    }

    public function getPermissions(Rol $rol): array
    {
        return $rol->permisos->pluck('slug')->toArray();
    }

    public function findById(int $id): ?Empleado
    {
        return Empleado::where('id_empleado', $id)->first();
    }

    public function findByUserId(int $userId): ?Empleado
    {
        return Empleado::where('id_user', $userId)->first();
    }

    public function getAllPaginated(string $search, string $rol, string $sortBy, string $sortOrder, int $limit)
    {
        $query = Empleado::with('rol', 'subtipoAdmin');

        if (!empty($search) && trim($search) !== '') {
            $query->where(function ($subQuery) use ($search) {
                $subQuery->where('nombre', 'LIKE', '%' . $search . '%')
                    ->orWhere('apellido', 'LIKE', '%' . $search . '%')
                    ->orWhere('email', 'LIKE', '%' . $search . '%')
                    ->orWhere('dni', 'LIKE', '%' . $search . '%')
                    ->orWhere('telefono', 'LIKE', '%' . $search . '%');
            });
        }

        if ($rol !== 'all' && !empty($rol)) {
            $query->where('id_rol', (int)$rol);
        }

        return $query->orderBy($sortBy, $sortOrder)->paginate($limit);
    }

    public function update(Empleado $empleado, array $data): bool
    {
        return $empleado->update($data);
    }

    public function delete(Empleado $empleado): ?bool
    {
        return $empleado->delete();
    }
}