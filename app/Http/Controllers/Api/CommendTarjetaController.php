<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CommendTarjeta\StoreCommendTarjetaRequest;
use App\Http\Requests\CommendTarjeta\UpdateCommendTarjetaRequest;
use App\Http\Resources\CommendTarjetaResource;
use App\Services\CommendTarjetaService;
use App\DTOs\CommendTarjeta\CreateCommendTarjetaDTO;
use App\DTOs\CommendTarjeta\UpdateCommendTarjetaDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CommendTarjetaController extends Controller
{
    public function __construct(
        private CommendTarjetaService $commendTarjetaService
    ) {}

    public function show(int $id): JsonResponse
    {
        try {
            $tarjeta = $this->commendTarjetaService->getTarjetaById($id);

            return response()->json([
                "status" => 200,
                "data" => new CommendTarjetaResource($tarjeta)
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'CommendTarjeta no encontrada'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function create(StoreCommendTarjetaRequest $request): JsonResponse
    {
        try {
            $dto = CreateCommendTarjetaDTO::fromRequest($request);
            $commendTarjeta = $this->commendTarjetaService->createTarjeta($dto);

            return response()->json([
                "status" => 200,
                "message" => "CommendTarjeta creada correctamente",
                "data" => new CommendTarjetaResource($commendTarjeta)
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update(UpdateCommendTarjetaRequest $request, int $id): JsonResponse
    {
        try {
            $dto = UpdateCommendTarjetaDTO::fromRequest($request);
            $tarjeta = $this->commendTarjetaService->updateTarjeta($id, $dto);

            return response()->json([
                'status' => 200,
                'message' => 'Tarjeta actualizada',
                'data' => new CommendTarjetaResource($tarjeta)
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 404,
                'message' => 'Tarjeta no encontrada'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 400,
                'message' => 'Error interno del servidor',
                'error' => $e->getMessage()
            ], 200);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $this->commendTarjetaService->deleteTarjeta((int)$id);

            return response()->json([
                "status" => 200,
                "message" => "CommendTarjeta eliminada correctamente"
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                "status" => 404,
                "message" => "CommendTarjeta no encontrada"
            ], 404);
        } catch (\Exception $ex) {
            return response()->json([
                "status" => 500,
                "message" => "Error al eliminar el CommendTarjeta",
                "error" => $ex->getMessage()
            ], 500);
        }
    }
}
