<?php

namespace App\Services\Blog;

use App\Repositories\BlogHeadRepository;
use App\DTOs\BlogHead\CreateBlogHeadDTO;
use App\DTOs\BlogHead\UpdateBlogHeadDTO;
use App\Services\Blog\BlogSlugSyncService;
use App\Models\BlogHead;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BlogHeadService
{
    public function __construct(
        private BlogHeadRepository $repository,
        private BlogSlugSyncService $slugSyncService
    ) {}

    public function create(CreateBlogHeadDTO $dto): BlogHead
    {
        return $this->repository->create($dto);
    }

    public function findById(int $id): BlogHead
    {
        $blogHead = $this->repository->findById($id);
        
        if (!$blogHead) {
            throw new ModelNotFoundException('BlogHead no encontrado');
        }
        
        return $blogHead;
    }

    public function update(int $id, UpdateBlogHeadDTO $dto): array
    {
        $blogHead = $this->findById($id);
        
        $oldTitulo = $blogHead->titulo;
        $this->repository->update($blogHead, $dto);
        
        $newLink = null;
        
        // Si el título cambió, sincronizar el slug del blog asociado
        if ($oldTitulo !== $dto->titulo) {
            $newLink = $this->slugSyncService->syncSlugFromHead($blogHead);
        }
        
        return [
            'blogHead' => $blogHead,
            'updated_link' => $newLink
        ];
    }

    public function delete(int $id): void
    {
        $blogHead = $this->findById($id);
        $this->repository->delete($blogHead);
    }
}