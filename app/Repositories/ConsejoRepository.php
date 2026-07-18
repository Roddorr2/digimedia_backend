<?php

namespace App\Repositories;

use App\Models\Consejo;
use App\DTOs\Consejo\CreateConsejoDTO;
use App\DTOs\Consejo\UpdateConsejoDTO;
use Illuminate\Support\Collection;

class ConsejoRepository
{
    public function findById(int $id): ?Consejo
    {
        return Consejo::find($id);
    }

    public function getByBlogBodyId(int $blogBodyId): Collection
    {
        return Consejo::where('id_blog_body', $blogBodyId)->orderBy('orden')->get();
    }

    public function create(CreateConsejoDTO $dto): Consejo
    {
        return Consejo::create($dto->toArray());
    }

    public function update(Consejo $consejo, UpdateConsejoDTO $dto): bool
    {
        return $consejo->update($dto->toArray());
    }

    public function delete(Consejo $consejo): bool
    {
        return $consejo->delete();
    }
}
