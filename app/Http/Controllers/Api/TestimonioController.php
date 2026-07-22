<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Testimonio\StoreTestimonioRequest;
use App\Http\Requests\Testimonio\UpdateTestimonioRequest;
use App\Http\Requests\Testimonio\GenerateTestimonioUploadSignatureRequest;
use App\Http\Requests\Testimonio\UpdateTestimonioImageRequest;
use App\DTOs\Testimonio\CreateTestimonioDTO;
use App\DTOs\Testimonio\UpdateTestimonioDTO;
use App\DTOs\Testimonio\UpdateTestimonioImageDTO;
use App\Http\Resources\TestimonioResource;
use App\Services\TestimonioService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TestimonioController extends Controller
{
    public function __construct(
        private TestimonioService $service
    ) {}

    // Público — usado por el carrusel en Next.js
    public function indexPublic(): JsonResponse
    {
        return response()->json($this->service->getAllPublic());
    }

    // Público — usado por el bloque de comentarios en Inicio (misma fuente de datos que Nosotros, paginado para "Mostrar más")
    public function indexHome(Request $request): AnonymousResourceCollection
    {
        $perPage = (int) $request->query('limit', 6);
        $perPage = $perPage > 0 ? min($perPage, 50) : 6;

        $paginated = $this->service->getAllPublicPaginated($perPage);

        return TestimonioResource::collection($paginated);
    }

    // Admin — listado paginado
    public function index(Request $request): JsonResponse
    {
        try {
            $params = [
                'search' => $request->get('search', ''),
                'limit' => $request->get('limit', 10),
                'sortBy' => $request->get('sortBy', 'created_at'),
                'sortOrder' => $request->get('sortOrder', 'desc'),
            ];

            $result = $this->service->getPaginated($params);

            return response()->json(["status" => 200, ...$result]);
        } catch (\Exception $e) {
            return response()->json(["status" => 500, "message" => "Error interno", "error" => $e->getMessage()], 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $testimonio = $this->service->getById((int)$id);
            return response()->json(["status" => 200, "data" => $testimonio]);
        } catch (ModelNotFoundException $e) {
            return response()->json(["status" => 404, "message" => "Testimonio no encontrado"], 404);
        }
    }

    public function store(StoreTestimonioRequest $request): JsonResponse
    {
        try {
            $dto = CreateTestimonioDTO::fromRequest($request);
            $testimonio = $this->service->create($dto);
            return response()->json(["status" => 201, "message" => "Testimonio creado correctamente", "data" => $testimonio], 201);
        } catch (\Exception $e) {
            return response()->json(["status" => 500, "message" => "Error al crear testimonio", "error" => $e->getMessage()], 500);
        }
    }

    public function update(UpdateTestimonioRequest $request, $id): JsonResponse
    {
        try {
            $dto = UpdateTestimonioDTO::fromRequest($request);
            $testimonio = $this->service->update((int)$id, $dto);
            return response()->json(["status" => 200, "message" => "Testimonio actualizado correctamente", "data" => $testimonio]);
        } catch (ModelNotFoundException $e) {
            return response()->json(["status" => 404, "message" => "Testimonio no encontrado"], 404);
        } catch (\Exception $e) {
            return response()->json(["status" => 500, "message" => $e->getMessage()], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $this->service->delete((int)$id);
            return response()->json(["status" => 200, "message" => "Testimonio eliminado correctamente"]);
        } catch (ModelNotFoundException $e) {
            return response()->json(["status" => 404, "message" => "Testimonio no encontrado"], 404);
        } catch (\Exception $e) {
            return response()->json(["status" => 500, "message" => $e->getMessage()], 500);
        }
    }

    public function generateUploadSignature(GenerateTestimonioUploadSignatureRequest $request, $id): JsonResponse
    {
        try {
            $signature = $this->service->generateUploadSignature((int)$id, $request->all());
            return response()->json(['signature' => $signature]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['status' => 404, 'message' => 'Testimonio no encontrado'], 404);
        } catch (\Exception $e) {
            return response()->json(['status' => 500, 'message' => $e->getMessage()], 500);
        }
    }

    public function updateImage(UpdateTestimonioImageRequest $request, $id): JsonResponse
    {
        try {
            $dto = UpdateTestimonioImageDTO::fromRequest($request);
            $result = $this->service->updateImage((int)$id, $dto);
            return response()->json(["status" => 200, "message" => "Imagen actualizada correctamente", "data" => $result]);
        } catch (ModelNotFoundException $e) {
            return response()->json(["status" => 404, "message" => "Testimonio no encontrado"], 404);
        } catch (\Exception $e) {
            $code = $e->getCode();
            $statusCode = in_array($code, [403, 422]) ? $code : 500;
            return response()->json(["status" => $statusCode, "message" => $e->getMessage()], $statusCode);
        }
    }

    public function deleteImage($id): JsonResponse
    {
        try {
            $this->service->deleteImage((int)$id);
            return response()->json(['status' => 200, 'message' => 'Imagen eliminada correctamente']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['status' => 404, 'message' => 'Testimonio no encontrado'], 404);
        } catch (\Exception $e) {
            return response()->json(['status' => 500, 'message' => $e->getMessage()], 500);
        }
    }
    public function toggleActivo($id): JsonResponse
    {
    try {
        $testimonio = $this->service->toggleActivo((int)$id);
        return response()->json([
            "status" => 200,
            "message" => $testimonio->activo ? "Testimonio activado" : "Testimonio ocultado",
            "data" => $testimonio
        ]);
    } catch (ModelNotFoundException $e) {
        return response()->json(["status" => 404, "message" => "Testimonio no encontrado"], 404);
    } catch (\Exception $e) {
        return response()->json(["status" => 500, "message" => $e->getMessage()], 500);
    }
    }
}