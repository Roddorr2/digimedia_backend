<?php

namespace App\Services\Blog;

use App\Models\Blog;
use App\Services\BlogFooterService;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BlogCascadeDeleteService
{
    public function __construct(
        private CardService $cardService,
        private BlogHeadService $blogHeadService,
        private BlogFooterService $blogFooterService,
        private TarjetaService $tarjetaService,
        private ConsejoService $consejoService
    ) {}

    public function deleteWithRelations(Blog $blog): void
    {
        $idBody = $blog->id_blog_body;
        $blogBody = $blog->body;

        // 1. Card
        if ($blog->card) {
            $this->cardService->deleteCard($blog->card->id_card);
        }

        // 2. Blog Head
        if ($blog->head) {
            $this->blogHeadService->delete($blog->head->id_blog_head);
        }

        // 3. Blog Footer
        if ($blog->footer) {
            $this->blogFooterService->delete($blog->footer->id_blog_footer);
        }

        // 4. Tarjetas asociadas al body
        $this->tarjetaService->deleteTarjetasByBlogBodyId($idBody);

        // 5. Consejos asociados al body
        try {
            $this->consejoService->deleteConsejosByBlogBodyId($idBody);
        } catch (ModelNotFoundException) {
            // No hay consejos asociados a este blog body, no es un error.
        }

        // 6. Blog Body
        if ($blogBody) {
            $blogBody->delete();
        }

        // 7. Finalmente el blog
        $blog->delete();
    }
}