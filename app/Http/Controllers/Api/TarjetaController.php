<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Blog\TarjetaService;
use App\DTOs\Tarjeta\CreateTarjetaDTO;
use App\DTOs\Tarjeta\UpdateTarjetaDTO;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TarjetaController extends Controller
{
    public function __construct(
        private TarjetaService $tarjetaService
    ) {}

    public function showAll(int $id)
    {
        try {
            $tarjetas = $this->tarjetaService->getTarjetasByBlogBodyId($id);

            if ($tarjetas->isEmpty()) {
                return response()->json(['error' => 'No se encontraron tarjetas'], 404);
            }

            return response()->json($tarjetas, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function create(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'titulo' => 'required|string|max:140',
                'descripcion' => 'required|string',
                'enlace' => 'nullable|string',
                'palabra' => 'nullable|string',
                'id_blog_body' => 'required|integer|exists:blog_bodies,id_blog_body',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 400);
            }

            $dto = CreateTarjetaDTO::fromRequest($request);
            $tarjeta = $this->tarjetaService->createTarjeta($dto);

            return response()->json([
                "status" => 200,
                "message" => "Tarjeta creada correctamente",
                "id" => $tarjeta->id_tarjeta
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, int $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'titulo' => 'required|string|max:140',
                'descripcion' => 'required|string',
                'id_blog_body' => 'required|integer|exists:blog_bodies,id_blog_body',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 400);
            }

            $dto = UpdateTarjetaDTO::fromRequest($request);
            $tarjeta = $this->tarjetaService->updateTarjeta($id, $dto);

            return response()->json([
                "status" => 200,
                "message" => "Tarjeta creada correctamente",
                "id" => $tarjeta->id_tarjeta
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 400,
                'message' => 'Tarjeta no encontrada'
            ], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy(int $id)
    {
        try {
            $this->tarjetaService->deleteTarjeta($id);

            return response()->json([
                "status" => 200,
                "message" => "Tarjeta eliminada correctamente"
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Tarjeta no encontrada'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroyAll(int $id)
    {
        try {
            $this->tarjetaService->deleteTarjetasByBlogBodyId($id);

            return response()->json([
                "status" => 200,
                "message" => "Tarjetas eliminadas correctamente"
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'No se encontraron tarjetas'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
