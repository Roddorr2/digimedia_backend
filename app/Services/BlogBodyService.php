<?php

namespace App\Services;

use App\Repositories\BlogBodyRepository;
use App\DTOs\BlogBody\CreateBlogBodyDTO;
use App\DTOs\BlogBody\UpdateBlogBodyDTO;
use App\Models\BlogBody;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BlogBodyService
{
    public function __construct(
        private BlogBodyRepository $repository
    ) {}

    public function create(CreateBlogBodyDTO $dto): BlogBody
    {
        return $this->repository->create($dto->toArray());
    }

    public function findById(int $id): BlogBody
    {
        $blogBody = $this->repository->findWithRelations($id);
        
        if (!$blogBody) {
            throw new ModelNotFoundException('BlogBody no encontrado');
        }
        
        return $blogBody;
    }

    public function update(int $id, UpdateBlogBodyDTO $dto): BlogBody
    {
        $blogBody = $this->repository->findById($id);
        
        if (!$blogBody) {
            throw new ModelNotFoundException('BlogBody no encontrado');
        }
        
        $this->repository->update($blogBody, $dto->toArray());
        
        return $blogBody;
    }

    public function delete(int $id): void
    {
        $blogBody = $this->repository->findById($id);
        
        if (!$blogBody) {
            throw new ModelNotFoundException('BlogBody no encontrado');
        }
        
        $this->repository->delete($blogBody);
    }
}