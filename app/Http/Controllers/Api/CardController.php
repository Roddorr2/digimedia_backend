<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Card\StoreCardRequest;
use App\Http\Requests\Card\UpdateCardBodyImageRequest;
use App\Http\Requests\Card\UpdateCardFooterImageRequest;
use App\Http\Requests\Card\UpdateCardHeaderImageRequest;
use App\Http\Requests\Card\UpdateCardRequest;
use App\Services\Blog\CardService;
use App\DTOs\Card\CreateCardDTO;
use App\DTOs\Card\UpdateCardDTO;
use App\DTOs\Card\UploadImageDTO;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CardController extends Controller
{
    public function __construct(
        private CardService $cardService
    ) {}

    public function index_public()
    {
        try {
            $cards = $this->cardService->getPublicCards();
            return response()->json($cards, 200);
        } catch (\Exception $e) {
            return response()->json([
                "status" => 500,
                "message" => "Error interno del servidor",
                "error" => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function index()
    {
        try {
            $cards = $this->cardService->getAllCards();
            return response()->json($cards, 200);
        } catch (\Exception $e) {
            return response()->json([
                "status" => 500,
                "message" => "Error interno del servidor",
                "error" => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function get($id = null)
    {
        try {
            $cards = $this->cardService->getCardsByEmpleado($id ? (int)$id : null);
            return response()->json($cards, 200);
        } catch (\Exception $e) {
            return response()->json([
                "status" => 500,
                "message" => "Error interno del servidor",
                "error" => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function create(StoreCardRequest $request)
    {
        try {
            $dto = CreateCardDTO::fromRequest($request);
            $card = $this->cardService->createCard($dto);

            return response()->json([
                "status" => 200,
                "message" => "Card creada correctamente",
                "id" => $card->id_card
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "status" => 500,
                "message" => "Error al crear la card",
                "error" => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function update(UpdateCardRequest $request, $id)
    {
        try {
            $dto = UpdateCardDTO::fromRequest($request);
            $card = $this->cardService->updateCard((int)$id, $dto);

            return response()->json([
                'status' => 200,
                'message' => 'Card actualizado',
                'id' => $card->id_card
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 404,
                'message' => 'Card no encontrada'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                "status" => 500,
                "error" => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function imageHeader(UpdateCardHeaderImageRequest $request, int $id)
    {
        try {
            $dto = UploadImageDTO::fromRequest(
                $request->file('file'),
                'imagenPrincipal',
                'header',
                $id
            );
            $fullUrl = $this->cardService->uploadHeaderImage($dto);

            return response()->json([
                "status" => 200,
                "message" => "Success, imagen subida correctamente",
                "url_image" => $fullUrl
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                "status" => 404,
                "message" => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                "status" => 500,
                "error" => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function deleteCarpetaImages(int $id)
    {
        try {
            $card = $this->cardService->getCardById($id);
            $this->cardService->deleteCarpetaImages($card);

            return response()->json([
                "status" => 200,
                "message" => "Carpeta eliminada correctamente"
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                "status" => 404,
                "message" => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                "status" => 500,
                "error" => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function imagesBody(UpdateCardBodyImageRequest $request, int $id)
    {
        try {
            $dto = UploadImageDTO::fromRequest(
                $request->file('file'),
                $request->name,
                'body',
                $id
            );
            $fullUrl = $this->cardService->uploadBodyImage($dto);

            return response()->json([
                "status" => 200,
                "message" => "Success, imagen subida correctamente",
                "url" => $fullUrl
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                "status" => 404,
                "message" => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                "status" => 500,
                "error" => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function imagesFooter(UpdateCardFooterImageRequest $request, int $id)
    {
        try {
            $dto = UploadImageDTO::fromRequest(
                $request->file('file'),
                $request->name,
                'footer',
                $id
            );
            $fullUrl = $this->cardService->uploadFooterImage($dto);

            return response()->json([
                "status" => 200,
                "message" => "Success, imagen subida correctamente",
                "url" => $fullUrl
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                "status" => 404,
                "message" => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                "status" => 500,
                "error" => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function destroy(int $id)
    {
        try {
            $this->cardService->deleteCard($id);

            return response()->json([
                "status" => 200,
                "message" => "Card eliminada correctamente"
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                "status" => 404,
                "message" => "Card no encontrada"
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                "status" => 500,
                "message" => "Error al eliminar la card",
                "error" => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}
