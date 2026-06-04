<?php

namespace App\Services\Blog;

use App\Repositories\TarjetaRepository;
use App\DTOs\Tarjeta\CreateTarjetaDTO;
use App\DTOs\Tarjeta\UpdateTarjetaDTO;
use App\Models\Tarjeta;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TarjetaService
{
    public function __construct(
        private TarjetaRepository $repository
    ) {}

    public function getTarjetaById(int $id): Tarjeta
    {
        $tarjeta = $this->repository->findById($id);
        
        if (!$tarjeta) {
            throw new ModelNotFoundException('Tarjeta no encontrada');
        }
        
        return $tarjeta;
    }

    public function getTarjetasByBlogBodyId(int $blogBodyId): Collection
    {
        return $this->repository->getByBlogBodyId($blogBodyId);
    }

    public function createTarjeta(CreateTarjetaDTO $dto): Tarjeta
    {
        try {
            DB::beginTransaction();
            $tarjeta = $this->repository->create($dto);
            DB::commit();
            
            return $tarjeta;
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error al crear tarjeta', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function updateTarjeta(int $id, UpdateTarjetaDTO $dto): Tarjeta
    {
        try {
            DB::beginTransaction();
            $tarjeta = $this->getTarjetaById($id);
            $this->repository->update($tarjeta, $dto);
            DB::commit();
            
            return $tarjeta->fresh();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error al actualizar tarjeta', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function deleteTarjeta(int $id): void
    {
        $tarjeta = $this->getTarjetaById($id);
        $this->repository->delete($tarjeta);
    }

    public function deleteTarjetasByBlogBodyId(int $blogBodyId): void
    {
        try {
            DB::beginTransaction();
            $tarjetas = $this->repository->getByBlogBodyId($blogBodyId);
            
            if ($tarjetas->isEmpty()) {
                throw new ModelNotFoundException('No se encontraron tarjetas');
            }
            
            foreach ($tarjetas as $tarjeta) {
                $this->repository->delete($tarjeta);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error al eliminar tarjetas por body ID', ['error' => $e->getMessage()]);
            throw $e;
        }
    }
}
