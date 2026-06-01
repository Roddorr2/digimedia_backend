<?php

namespace App\Services;

use App\Repositories\ContactanosRepository;
use App\DTOs\Contactanos\CreateContactanosDTO;
use App\DTOs\Contactanos\UpdateContactanosDTO;
use App\Models\Contactanos;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ContactanosService
{
    public function __construct(
        private ContactanosRepository $repository
    ) {}

    public function getContactos(int $perPage = 4): LengthAwarePaginator
    {
        return $this->repository->getPaginated($perPage);
    }

    public function getContactoById(int $id): Contactanos
    {
        $contacto = $this->repository->findById($id);
        
        if (!$contacto) {
            throw new ModelNotFoundException('Contacto no encontrado');
        }
        
        return $contacto;
    }

    public function createContacto(CreateContactanosDTO $dto): Contactanos
    {
        return $this->repository->create($dto);
    }

    public function updateContacto(int $id, UpdateContactanosDTO $dto): Contactanos
    {
        $contacto = $this->getContactoById($id);
        
        $this->repository->update($contacto, $dto);
        
        return $contacto->fresh();
    }

    public function deleteContacto(int $id): void
    {
        $contacto = $this->getContactoById($id);
        
        $this->repository->delete($contacto);
    }
}
