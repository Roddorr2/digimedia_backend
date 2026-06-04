<?php

namespace App\Repositories;

use App\Models\BlogHead;
use App\DTOs\BlogHead\CreateBlogHeadDTO;
use App\DTOs\BlogHead\UpdateBlogHeadDTO;

class BlogHeadRepository
{
    public function create(CreateBlogHeadDTO $dto): BlogHead
    {
        return BlogHead::create($dto->toArray());
    }

    public function findById(int $id): ?BlogHead
    {
        return BlogHead::find($id);
    }

    public function update(BlogHead $blogHead, UpdateBlogHeadDTO $dto): bool
    {
        return $blogHead->update($dto->toArray());
    }

    public function delete(BlogHead $blogHead): bool
    {
        return $blogHead->delete();
    }
}