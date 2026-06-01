<?php

namespace App\Repositories;

use App\Models\libroreclamacion;
use App\DTOs\Reclamacion\CreateReclamacionDTO;
use App\DTOs\Reclamacion\UpdateReclamacionDTO;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ReclamacionRepository
{
    public function getPaginated(int $perPage = 4): LengthAwarePaginator
    {
        return libroreclamacion::orderBy('id_reclamacion', 'asc')->paginate($perPage);
    }

    public function findById(int $id): ?libroreclamacion
    {
        return libroreclamacion::find($id);
    }

    public function create(CreateReclamacionDTO $dto): libroreclamacion
    {
        return libroreclamacion::create($dto->toArray());
    }

    public function update(libroreclamacion $reclamacion, UpdateReclamacionDTO $dto): bool
    {
        return $reclamacion->update($dto->toArray());
    }

    public function delete(libroreclamacion $reclamacion): bool
    {
        return $reclamacion->delete();
    }
}
