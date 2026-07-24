<?php

namespace App\Repositories;

use App\Models\Testimonio;
use Illuminate\Pagination\LengthAwarePaginator;

class TestimonioRepository
{
    public function findById(int $id): ?Testimonio
    {
        return Testimonio::find($id);
    }

    public function getAllPublic(): \Illuminate\Support\Collection
    {
        return Testimonio::where('activo', true)
        ->orderBy('created_at', 'desc')
        ->get();
    }

    public function getAllPublicPaginated(int $perPage): LengthAwarePaginator
    {
        return Testimonio::where('activo', true)
        ->orderBy('created_at', 'desc')
        ->paginate($perPage);
    }

    public function getAllPaginated(string $search, string $sortBy, string $sortOrder, int $limit): LengthAwarePaginator
    {
        return Testimonio::when($search, function ($query, $search) {
                $query->where('nombre', 'like', "%{$search}%");
            })
            ->orderBy($sortBy, $sortOrder)
            ->paginate($limit);
    }

    public function create(array $data): Testimonio
    {
        return Testimonio::create($data);
    }

    public function update(Testimonio $testimonio, array $data): void
    {
        $testimonio->update($data);
    }

    public function delete(Testimonio $testimonio): void
    {
        $testimonio->delete();
    }
}