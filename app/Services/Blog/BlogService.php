<?php

namespace App\Services\Blog;

use App\DTOs\Blog\CreateBlogDTO;
use App\DTOs\Blog\UpdateBlogDTO;
use App\DTOs\Blog\FiltrosBlogDTO;
use App\Models\Blog;
use App\Repositories\BlogRepository;
use App\Repositories\BlogHeadRepository;
use App\Services\Blog\BlogCascadeDeleteService;
use App\Services\Blog\BlogStorageService;
use App\Services\AuditoriaService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Collection;

class BlogService
{
    public function __construct(
        private BlogRepository $blogRepository,
        private BlogHeadRepository $blogHeadRepository,
        private BlogSlugService $slugService,
        private BlogCascadeDeleteService $cascadeDeleteService,
        private BlogStorageService $storageService
    ) {}

    public function getAllBlogs(): Collection
    {
        return $this->blogRepository->getAll();
    }

    public function getBlogsByFilters(FiltrosBlogDTO $filters): Collection
    {
        return $this->blogRepository->getByFilters($filters);
    }

    public function getBlogById(int $id): Blog
    {
        $blog = $this->blogRepository->findById($id);
        
        if (!$blog) {
            throw new ModelNotFoundException('Blog no encontrado');
        }
        
        return $blog;
    }

    public function getBlogByLink(string $link): Blog
    {
        $blog = $this->blogRepository->findByLink($link);
        
        if (!$blog) {
            throw new ModelNotFoundException('Blog no encontrado');
        }
        
        return $blog;
    }

    public function getBlogsByLink(string $link): Blog
    {
        return $this->getBlogByLink($link);
    }

    public function createBlog(array $data): Blog
    {
        $blogHead = $this->blogHeadRepository->findById($data['id_blog_head']);
        if (!$blogHead) {
            throw new ModelNotFoundException('BlogHead no encontrado');
        }
        
        $slug = $this->slugService->generateUniqueSlug($blogHead->titulo ?? 'blog');
        
        $dto = new CreateBlogDTO(
            id_blog_head: $data['id_blog_head'],
            id_blog_body: $data['id_blog_body'],
            id_blog_footer: $data['id_blog_footer'],
            id_card: $data['id_card'],
            id_empleado: $data['id_empleado'],
            link: $slug
        );
        
        $blog = $this->blogRepository->create($dto);
        
        // Registrar auditoría
        AuditoriaService::registrar(
            $blog->id_blog,
            $dto->id_empleado,
            'CREAR',
            $blogHead->titulo ?? 'blog'
        );
        
        return $blog;
    }

    public function updateBlog(int $id, array $data): Blog
    {
        $blog = $this->getBlogById($id);
        
        $blogHead = $this->blogHeadRepository->findById($data['id_blog_head']);
        if (!$blogHead) {
            throw new ModelNotFoundException('BlogHead no encontrado');
        }
        
        $slug = $this->slugService->generateUniqueSlug($blogHead->titulo ?? 'blog', $id);
        
        $dto = new UpdateBlogDTO(
            id_blog_head: $data['id_blog_head'],
            id_blog_body: $data['id_blog_body'],
            id_blog_footer: $data['id_blog_footer'],
            id_card: $data['id_card'],
            id_empleado: $data['id_empleado'],
            link: $slug,
            descripcion: $data['descripcion'] ?? null
        );
        
        $this->blogRepository->update($blog, $dto);
        
        // Registrar auditoría
        AuditoriaService::registrar(
            $blog->id_blog,
            $dto->id_empleado,
            'ACTUALIZAR',
            $blogHead->titulo ?? 'blog',
            $dto->descripcion
        );
        
        return $blog->fresh();
    }

    public function deleteBlog(int $id): void
    {
        $blog = $this->getBlogById($id);
        
        // Eliminar imágenes del storage
        $this->storageService->deleteBlogImages($blog);
        
        // Registrar auditoría antes de eliminar
        AuditoriaService::registrar(
            $blog->id_blog,
            $blog->card->id_empleado ?? null,
            'ELIMINAR',
            $blog->head->titulo ?? 'blog'
        );
        
        // Eliminar en cascada
        $this->cascadeDeleteService->deleteWithRelations($blog);
    }
}