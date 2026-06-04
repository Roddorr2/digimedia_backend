<?php

namespace App\Repositories;

use App\Models\Tarjeta;
use App\DTOs\Tarjeta\CreateTarjetaDTO;
use App\DTOs\Tarjeta\UpdateTarjetaDTO;
use Illuminate\Support\Collection;

class TarjetaRepository
{
    public function findById(int $id): ?Tarjeta
    {
        return Tarjeta::find($id);
    }

    public function getByBlogBodyId(int $blogBodyId): Collection
    {
        return Tarjeta::where('id_blog_body', $blogBodyId)->get();
    }

    public function create(CreateTarjetaDTO $dto): Tarjeta
    {
        return Tarjeta::create($dto->toArray());
    }

    public function update(Tarjeta $tarjeta, UpdateTarjetaDTO $dto): bool
    {
        return $tarjeta->update($dto->toArray());
    }

    public function delete(Tarjeta $tarjeta): bool
    {
        return $tarjeta->delete();
    }
}
