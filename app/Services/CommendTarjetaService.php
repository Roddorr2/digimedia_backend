<?php

namespace App\Services;

use App\Repositories\CommendTarjetaRepository;
use App\DTOs\CommendTarjeta\CreateCommendTarjetaDTO;
use App\DTOs\CommendTarjeta\UpdateCommendTarjetaDTO;
use App\Models\CommendTarjeta;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CommendTarjetaService
{
    public function __construct(
        private CommendTarjetaRepository $repository
    ) {}

    public function getTarjetaById(int $id): CommendTarjeta
    {
        $tarjeta = $this->repository->findById($id);

        if (!$tarjeta) {
            throw new ModelNotFoundException('CommendTarjeta no encontrada');
        }

        return $tarjeta;
    }

    public function createTarjeta(CreateCommendTarjetaDTO $dto): CommendTarjeta
    {
        DB::beginTransaction();
        try {
            $tarjeta = $this->repository->create($dto->toArray());
            DB::commit();
            return $tarjeta;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function updateTarjeta(int $id, UpdateCommendTarjetaDTO $dto): CommendTarjeta
    {
        $tarjeta = $this->repository->findById($id);

        if (!$tarjeta) {
            throw new ModelNotFoundException('Tarjeta no encontrada');
        }

        DB::beginTransaction();
        try {
            $this->repository->update($tarjeta, $dto->data);
            DB::commit();
            return $tarjeta->fresh();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function deleteTarjeta(int $id): void
    {
        $tarjeta = $this->repository->findById($id);

        if (!$tarjeta) {
            throw new ModelNotFoundException('CommendTarjeta no encontrada');
        }

        $this->repository->delete($tarjeta);
    }
}
