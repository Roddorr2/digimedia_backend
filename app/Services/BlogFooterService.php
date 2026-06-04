<?php

namespace App\Services;

use App\Repositories\BlogFooterRepository;
use App\DTOs\BlogFooter\CreateBlogFooterDTO;
use App\DTOs\BlogFooter\UpdateBlogFooterDTO;
use App\Models\BlogFooter;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BlogFooterService
{
    public function __construct(
        private BlogFooterRepository $repository
    ) {}

    public function create(CreateBlogFooterDTO $dto): BlogFooter
    {
        return $this->repository->create($dto);
    }

    public function findById(int $id): BlogFooter
    {
        $blogFooter = $this->repository->findById($id);
        
        if (!$blogFooter) {
            throw new ModelNotFoundException('BlogFooter no encontrado');
        }
        
        return $blogFooter;
    }

    public function update(int $id, UpdateBlogFooterDTO $dto): BlogFooter
    {
        $blogFooter = $this->findById($id);
        $this->repository->update($blogFooter, $dto);
        
        return $blogFooter;
    }

    public function delete(int $id): void
    {
        $blogFooter = $this->findById($id);
        $this->repository->delete($blogFooter);
    }
}