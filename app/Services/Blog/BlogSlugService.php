<?php
// app/Services/Blog/BlogSlugService.php
namespace App\Services\Blog;

use App\Repositories\BlogRepository;
use Illuminate\Support\Str;

class BlogSlugService
{
    public function __construct(
        private BlogRepository $blogRepository
    ) {}

    public function generateUniqueSlug(string $title, ?int $excludeId = null): string
    {
        $originalSlug = Str::slug($title);
        $slug = $originalSlug;
        $counter = 1;

        while ($this->blogRepository->slugExists($slug, $excludeId)) {
            $slug = $originalSlug . '-' . $counter++;
        }

        return $slug;
    }
}