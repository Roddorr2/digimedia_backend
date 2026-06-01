<?php

namespace App\Services\Blog;

use App\Repositories\CardRepository;
use App\DTOs\Card\CreateCardDTO;
use App\DTOs\Card\UpdateCardDTO;
use App\DTOs\Card\UploadImageDTO;
use App\Models\Card;
use App\Models\Blog;
use App\Models\BlogHead;
use App\Models\BlogBody;
use App\Models\BlogFooter;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CardService
{
    private string $urlApi;

    public function __construct(
        private CardRepository $cardRepository
    ) {
        $this->urlApi = config('app.url');
    }

    public function getPublicCards(): Collection
    {
        return $this->cardRepository->getAllPublic();
    }

    public function getAllCards(): Collection
    {
        return $this->cardRepository->getAll();
    }

    public function getCardsByEmpleado(?int $empleadoId): Collection
    {
        if (!$empleadoId) {
            return $this->cardRepository->getAllWithRelations();
        }
        
        return $this->cardRepository->getByEmpleado($empleadoId);
    }

    public function getCardById(int $id): Card
    {
        $card = $this->cardRepository->findById($id);
        
        if (!$card) {
            throw new ModelNotFoundException('Card no encontrada');
        }
        
        return $card;
    }

    public function createCard(CreateCardDTO $dto): Card
    {
        try {
            DB::beginTransaction();
            $card = $this->cardRepository->create($dto);
            DB::commit();
            
            return $card;
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error al crear la card', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function updateCard(int $id, UpdateCardDTO $dto): Card
    {
        try {
            DB::beginTransaction();
            $card = $this->getCardById($id);
            $this->cardRepository->update($card, $dto);
            DB::commit();
            
            return $card;
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error al actualizar la card', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function uploadHeaderImage(UploadImageDTO $dto): string
    {
        $card = $this->getCardById($dto->cardId);
        
        $blog = Blog::find($card->id_blog);
        if (!$blog) {
            throw new ModelNotFoundException('Blog asociado no encontrado');
        }
        
        $blogHead = BlogHead::find($blog->id_blog_head);
        if (!$blogHead) {
            throw new ModelNotFoundException('BlogHead asociado no encontrado');
        }

        // Eliminar imagen antigua
        $oldRelativeUrl = $card->url_image;
        if ($oldRelativeUrl) {
            $oldFilePath = str_replace('/storage/', '', $oldRelativeUrl);
            if (Storage::disk('public')->exists($oldFilePath)) {
                Storage::disk('public')->delete($oldFilePath);
                Log::info("Archivo antiguo de cabecera eliminado: " . $oldFilePath);
            }
        }

        $relativePath = "images/templates/plantilla{$card->id_plantilla}/{$card->id_blog}/head";
        $baseName = "imagenPrincipal";
        $timestamp = Carbon::now()->format('Ymd_His');
        $fileName = "{$baseName}_{$timestamp}.webp";
        $filePath = "{$relativePath}/{$fileName}";

        // Procesar imagen
        $image = Image::read($dto->file)->cover(1900, 800);
        Storage::disk('public')->put($filePath, (string) $image->toWebp());

        $basePath = '/storage/';
        $fullUrl = $this->urlApi . $basePath . $filePath;
        $relativeUrl = $basePath . $filePath;

        $card->public_image = $fullUrl;
        $card->url_image = $relativeUrl;
        $card->save();

        $blogHead->public_image = $fullUrl;
        $blogHead->url_image = $relativeUrl;
        $blogHead->save();

        return $fullUrl;
    }

    public function uploadBodyImage(UploadImageDTO $dto): string
    {
        $card = $this->getCardById($dto->cardId);
        
        $blog = Blog::find($card->id_blog);
        if (!$blog) {
            throw new ModelNotFoundException('Blog asociado no encontrado');
        }
        
        $blogBody = BlogBody::find($blog->id_blog_body);
        if (!$blogBody) {
            throw new ModelNotFoundException('BlogBody asociado no encontrado');
        }

        $fileName = $dto->name . ".webp";
        $relativePath = "images/templates/plantilla{$card->id_plantilla}/{$card->id_blog}/body";
        $filePath = "{$relativePath}/{$fileName}";

        if (Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
        }

        // Procesar imagen
        $image = Image::read($dto->file)->cover(600, 350);
        Storage::disk('public')->put($filePath, (string) $image->toWebp());

        $basePath = '/storage/';
        $fullUrl = $this->urlApi . $basePath . $filePath;
        $relativeUrl = $basePath . $filePath;

        switch ($dto->name) {
            case "image1":
                $blogBody->public_image1 = $fullUrl;
                $blogBody->url_image1 = $relativeUrl;
                break;
            case "image2":
                $blogBody->public_image2 = $fullUrl;
                $blogBody->url_image2 = $relativeUrl;
                break;
            default:
                $blogBody->public_image3 = $fullUrl;
                $blogBody->url_image3 = $relativeUrl;
                break;
        }

        $blogBody->save();

        return $fullUrl;
    }

    public function uploadFooterImage(UploadImageDTO $dto): string
    {
        $card = $this->getCardById($dto->cardId);
        
        $blog = Blog::find($card->id_blog);
        if (!$blog) {
            throw new ModelNotFoundException('Blog asociado no encontrado');
        }
        
        $blogFooter = BlogFooter::find($blog->id_blog_footer);
        if (!$blogFooter) {
            throw new ModelNotFoundException('BlogFooter asociado no encontrado');
        }

        $fileName = $dto->name . ".webp";
        $relativePath = "images/templates/plantilla{$card->id_plantilla}/{$card->id_blog}/footer";
        $filePath = "{$relativePath}/{$fileName}";

        if (Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
        }

        // Procesar imagen
        $image = Image::read($dto->file)->cover(250, 200);
        Storage::disk('public')->put($filePath, (string) $image->toWebp());

        $basePath = '/storage/';
        $fullUrl = $this->urlApi . $basePath . $filePath;
        $relativeUrl = $basePath . $filePath;

        switch ($dto->name) {
            case "image1":
                $blogFooter->public_image1 = $fullUrl;
                $blogFooter->url_image1 = $relativeUrl;
                break;
            case "image2":
                $blogFooter->public_image2 = $fullUrl;
                $blogFooter->url_image2 = $relativeUrl;
                break;
            default:
                $blogFooter->public_image3 = $fullUrl;
                $blogFooter->url_image3 = $relativeUrl;
                break;
        }

        $blogFooter->save();

        return $fullUrl;
    }

    public function deleteCarpetaImages(Card $card): void
    {
        try {
            $relativePath = "images/templates/plantilla{$card->id_plantilla}/{$card->id_blog}";
            Storage::disk('public')->deleteDirectory($relativePath);
            Log::info("Carpeta de imágenes eliminada correctamente: " . $relativePath);
        } catch (\Exception $e) {
            Log::error("Error al eliminar carpeta de imágenes", ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function deleteCard(int $id): void
    {
        $card = $this->getCardById($id);
        
        $this->deleteCarpetaImages($card);
        $this->cardRepository->delete($card);
    }
}
