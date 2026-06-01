<?php

namespace App\Services\Blog;

use App\Models\BlogHead;
use App\Repositories\BlogRepository;
use Illuminate\Support\Str;

class BlogSlugSyncService
{
    public function __construct(
        private BlogRepository $blogRepository
    ) {}

    /**
     * Sincroniza el slug del blog cuando el título del BlogHead cambia
     */
    public function syncSlugFromHead(BlogHead $blogHead): ?string
    {
        $blog = \App\Models\Blog::where('id_blog_head', $blogHead->id_blog_head)->first();
        
        if (!$blog) {
            return null;
        }

        $originalSlug = Str::slug($blogHead->titulo);
        $link = $originalSlug;
        $counter = 1;

        while ($this->blogRepository->slugExists($link, $blog->id_blog)) {
            $link = $originalSlug . '-' . $counter++;
        }

        $blog->link = $link;
        $blog->save();

        return $link;
    }
}