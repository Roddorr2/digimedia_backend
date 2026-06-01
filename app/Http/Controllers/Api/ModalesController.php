<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ModalServicioService;
use App\Http\Requests\ModalServicio\CreateModalServicioRequest;
use App\Http\Requests\ModalServicio\UpdateModalServicioRequest;
use App\Http\Resources\ModalServicioResource;
use App\Http\Resources\EmailModalResource;
use App\Http\Resources\WatModalResource;
use App\DTOs\ModalServicio\CreateModalServicioDTO;
use App\DTOs\ModalServicio\UpdateModalServicioDTO;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ModalesController extends Controller
{
    public function __construct(
        private ModalServicioService $modalServicioService
    ) {}

    public function get(Request $request): JsonResponse
    {
        try {
            $search = $request->get('search');
            $modals = $this->modalServicioService->getModales((string) $search);
            
            $modals->getCollection()->transform(function ($modal) {
                return (new ModalServicioResource($modal))->toArray(request());
            });

            return response()->json($modals, 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al obtener registros',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function getSendModales(int $id): JsonResponse
    {
        try {
            $result = $this->modalServicioService->getSendModales((int)$id);

            return response()->json([
                'mails' => EmailModalResource::collection($result['mails']),
                'wats' => WatModalResource::collection($result['wats']),
                'status' => 200
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Modal no encontrado'], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error interno del servidor',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function create(CreateModalServicioRequest $request): JsonResponse
    {
        try {
            $dto = CreateModalServicioDTO::fromRequest($request);
            $this->modalServicioService->createModal($dto);

            return response()->json([
                'status' => 201,
                'message' => 'Modal guardado exitosamente'
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $error) {
            return response()->json([
                'error' => 'Error en la validación',
                'details' => $error->errors()
            ], 400);
        } catch (\Exception $error) {
            return response()->json([
                'error' => 'Error al crear el registro',
                'details' => $error->getMessage()
            ], 500);
        }
    }

    public function getById(int $id): JsonResponse
    {
        try {
            $modal = $this->modalServicioService->getModalById((int)$id);

            return response()->json([
                'status' => 'success',
                'data' => new ModalServicioResource($modal)
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Modal no encontrado'], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error interno del servidor',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function update(UpdateModalServicioRequest $request, int $id): JsonResponse
    {
        try {
            $dto = UpdateModalServicioDTO::fromRequest($request);
            $modal = $this->modalServicioService->updateModal((int)$id, $dto);

            return response()->json([
                'message' => 'Estado actualizado exitosamente',
                'data' => new ModalServicioResource($modal),
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Modal no encontrado'], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al actualizar el estado',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function delete(int $id): JsonResponse
    {
        try {
            $this->modalServicioService->deleteModal((int)$id);

            return response()->json([
                'message' => 'Modal eliminado exitosamente'
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Modal no encontrado'], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al eliminar el modal',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}
