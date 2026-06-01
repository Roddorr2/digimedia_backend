<?php

namespace App\Repositories;

use App\Models\servicios;
use App\DTOs\Servicio\ServicioFiltersDTO;
use App\DTOs\Servicio\CreateServicioDTO;
use App\DTOs\Servicio\UpdateServicioDTO;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ServicioRepository
{
    public function getPaginated(ServicioFiltersDTO $filters): LengthAwarePaginator
    {
        return servicios::orderBy('id_servicio', 'desc')->paginate($filters->perPage);
    }

    public function findById(int $id): ?servicios
    {
        return servicios::find($id);
    }

    public function create(CreateServicioDTO $dto): servicios
    {
        return servicios::create($dto->toArray());
    }

    public function update(servicios $servicio, UpdateServicioDTO $dto): bool
    {
        return $servicio->update($dto->toArray());
    }

    public function delete(servicios $servicio): bool
    {
        return $servicio->delete();
    }
}
