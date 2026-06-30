<?php

namespace App\Repositories;

use App\Models\Contactanos;
use App\DTOs\Contactanos\CreateContactanosDTO;
use App\DTOs\Contactanos\UpdateContactanosDTO;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ContactanosRepository
{
    public function getPaginated(int $perPage = 10): LengthAwarePaginator
    {
        return Contactanos::paginate($perPage);
    }

    public function findById(int $id): ?Contactanos
    {
        return Contactanos::find($id);
    }

    public function create(CreateContactanosDTO $dto): Contactanos
    {
        return Contactanos::create($dto->toArray());
    }

    public function update(Contactanos $contacto, UpdateContactanosDTO $dto): bool
    {
        return $contacto->update($dto->toArray());
    }

    public function delete(Contactanos $contacto): bool
    {
        return $contacto->delete();
    }
}
