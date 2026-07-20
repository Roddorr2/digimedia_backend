<?php

namespace App\Services\Blog;

use App\Repositories\ConsejoRepository;
use App\DTOs\Consejo\CreateConsejoDTO;
use App\DTOs\Consejo\UpdateConsejoDTO;
use App\Models\Consejo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ConsejoService
{
    public function __construct(private ConsejoRepository $repository) {}

    public function getConsejoById(int $id): Consejo
    {
        $consejo = $this->repository->findById($id);
        if (!$consejo) {
            throw new ModelNotFoundException('Consejo no encontrado');
        }
        return $consejo;
    }

    public function getConsejosByBlogBodyId(int $blogBodyId): Collection
    {
        return $this->repository->getByBlogBodyId($blogBodyId);
    }

    public function createConsejo(CreateConsejoDTO $dto): Consejo
    {
        try {
            DB::beginTransaction();
            $consejo = $this->repository->create($dto);
            DB::commit();
            return $consejo;
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error al crear consejo', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function updateConsejo(int $id, UpdateConsejoDTO $dto): Consejo
    {
        try {
            DB::beginTransaction();
            $consejo = $this->getConsejoById($id);
            if (!$this->repository->update($consejo, $dto)) {
                throw new \RuntimeException('No se pudo actualizar el consejo');
            }
            DB::commit();
            return $consejo->fresh();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error al actualizar consejo', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function deleteConsejo(int $id): void
    {
        $consejo = $this->getConsejoById($id);
        $this->repository->delete($consejo);
    }

    public function deleteConsejosByBlogBodyId(int $blogBodyId): void
    {
        try {
            DB::beginTransaction();
            $consejos = $this->repository->getByBlogBodyId($blogBodyId);
            if ($consejos->isEmpty()) {
                throw new ModelNotFoundException('No se encontraron consejos');
            }
            foreach ($consejos as $consejo) {
                $this->repository->delete($consejo);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error al eliminar consejos por body ID', ['error' => $e->getMessage()]);
            throw $e;
        }
    }
}
