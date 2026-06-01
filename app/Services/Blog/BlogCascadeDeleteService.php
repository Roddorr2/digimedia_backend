<?php

namespace App\Services\Blog;

use App\Models\Blog;
use App\Services\BlogFooterService;
use App\Services\CommendTarjetaService;

class BlogCascadeDeleteService
{
    public function __construct(
        private CardService $cardService,
        private BlogHeadService $blogHeadService,
        private BlogFooterService $blogFooterService,
        private TarjetaService $tarjetaService,
        private CommendTarjetaService $commendTarjetaService
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

        // 5. Commend Tarjeta
        if ($blogBody && $blogBody->id_commend_tarjeta) {
            $this->commendTarjetaService->deleteTarjeta($blogBody->id_commend_tarjeta);
        }

        // 6. Blog Body
        if ($blogBody) {
            $blogBody->delete();
        }

        // 7. Finalmente el blog
        $blog->delete();
    }
}