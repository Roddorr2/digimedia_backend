<?php

namespace App\Repositories;

use App\Models\Card;
use App\DTOs\Card\CreateCardDTO;
use App\DTOs\Card\UpdateCardDTO;
use Illuminate\Support\Collection;

class CardRepository
{
    public function getAllPublic(): Collection
    {
        return Card::with(['blog.head'])
            ->where('estado_publicacion', true)
            ->orderBy('id_card', 'asc')
            ->get();
    }

    public function getAll(): Collection
    {
        return Card::with('blog.head')->orderBy('id_card', 'asc')->get();
    }

    public function getByEmpleado(int $empleadoId): Collection
    {
        return Card::with('empleado', 'blog')
            ->where('id_empleado', $empleadoId)
            ->get();
    }

    public function getAllWithRelations(): Collection
    {
        return Card::with('empleado', 'blog')->get();
    }

    public function findById(int $id): ?Card
    {
        return Card::find($id);
    }

    public function create(CreateCardDTO $dto): Card
    {
        return Card::create($dto->toArray());
    }

    public function update(Card $card, UpdateCardDTO $dto): bool
    {
        return $card->update($dto->toArray());
    }

    public function delete(Card $card): bool
    {
        return $card->delete();
    }
}