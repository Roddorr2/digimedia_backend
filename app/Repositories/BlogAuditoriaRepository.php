<?php

namespace App\Repositories;

use App\Models\BlogAuditoria;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BlogAuditoriaRepository
{
    public function getAuditoriasPaginadas(
        ?int $blogId = null,
        int $perPage = 20,
        string $orderBy = 'fecha_hora',
        string $orderDir = 'desc'
    ): LengthAwarePaginator {
        $query = BlogAuditoria::with([
            'empleado:id_empleado,nombre,apellido',
            'blog.card:id_card,titulo,descripcion,public_image,url_image,id_blog',
        ]);

        if ($blogId) {
            $query->where('id_blog', $blogId);
        }

        return $query->orderBy($orderBy, $orderDir)->paginate($perPage);
    }

    public function existsForBlog(int $blogId): bool
    {
        return BlogAuditoria::where('id_blog', $blogId)->exists();
    }
}