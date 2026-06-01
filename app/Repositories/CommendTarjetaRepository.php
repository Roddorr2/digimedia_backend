<?php

namespace App\Repositories;

use App\Models\CommendTarjeta;

class CommendTarjetaRepository
{
    public function findById(int $id): ?CommendTarjeta
    {
        return CommendTarjeta::find($id);
    }

    public function create(array $data): CommendTarjeta
    {
        return CommendTarjeta::create($data);
    }

    public function update(CommendTarjeta $tarjeta, array $data): bool
    {
        return $tarjeta->update($data);
    }

    public function delete(CommendTarjeta $tarjeta): ?bool
    {
        return $tarjeta->delete();
    }
}
