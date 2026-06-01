<?php

namespace App\Repositories;

use App\Models\BlogBody;

class BlogBodyRepository
{
    public function create(array $data): BlogBody
    {
        return BlogBody::create($data);
    }

    public function findById(int $id): ?BlogBody
    {
        return BlogBody::find($id);
    }

    public function findWithRelations(int $id): ?BlogBody
    {
        return BlogBody::with(['commend_tarjeta', 'tarjetas'])->find($id);
    }

    public function update(BlogBody $blogBody, array $data): bool
    {
        return $blogBody->update($data);
    }

    public function delete(BlogBody $blogBody): bool
    {
        return $blogBody->delete();
    }
}