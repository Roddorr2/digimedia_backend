<?php

namespace App\Repositories;

use App\Models\BlogFooter;
use App\DTOs\BlogFooter\CreateBlogFooterDTO;
use App\DTOs\BlogFooter\UpdateBlogFooterDTO;

class BlogFooterRepository
{
    public function create(CreateBlogFooterDTO $dto): BlogFooter
    {
        return BlogFooter::create($dto->toArray());
    }

    public function findById(int $id): ?BlogFooter
    {
        return BlogFooter::find($id);
    }

    public function update(BlogFooter $blogFooter, UpdateBlogFooterDTO $dto): bool
    {
        return $blogFooter->update($dto->toArray());
    }

    public function delete(BlogFooter $blogFooter): bool
    {
        return $blogFooter->delete();
    }
}