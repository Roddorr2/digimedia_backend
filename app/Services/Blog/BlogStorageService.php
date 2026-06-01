<?php

namespace App\Services\Blog;

use App\Models\Blog;
use Illuminate\Support\Facades\Storage;

class BlogStorageService
{
    public function deleteBlogImages(Blog $blog): void
    {
        if (!$blog->card) {
            return;
        }

        $relativePath = "images/templates/plantilla{$blog->card->id_plantilla}/" . $blog->id_blog;

        if (Storage::disk('public')->exists($relativePath)) {
            Storage::disk('public')->deleteDirectory($relativePath);
        }
    }
}