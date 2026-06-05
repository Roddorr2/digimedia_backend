<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PlantillaEmailService;
use App\Http\Requests\PlantillaEmail\UpdatePlantillaEmailRequest;
use App\Http\Resources\PlantillaEmailResource;
use App\DTOs\PlantillaEmail\UpdatePlantillaEmailDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PlantillasEmailController extends Controller
{
    public function __construct(
        private PlantillaEmailService $service
    ) {}

    public function index(): JsonResponse
    {
        try {
            $plantillas = $this->service->getPlantillas();

            return response()->json([
                'success' => true,
                'data'    => PlantillaEmailResource::collection($plantillas),
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al obtener plantillas', 'error' => $e->getMessage()], 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $plantilla = $this->service->getPlantillaById((int) $id);

            return response()->json([
                'success' => true,
                'data'    => new PlantillaEmailResource($plantilla),
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Plantilla no encontrada'], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al obtener plantilla', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Listar plantillas por owner (servicio o subservicio).
     * GET /api/plantillas/email/by-owner/{type}/{id}
     */
    public function showByOwner(string $type, int $id): JsonResponse
    {
        try {
            $plantillas = $this->service->getByOwner($type, $id);

            return response()->json([
                'success' => true,
                'data'    => PlantillaEmailResource::collection($plantillas),
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al obtener plantillas', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Inicializar 3 plantillas email para un subservicio copiando del servicio padre.
     * POST /api/plantillas/email/by-owner/subservicio/{id}/init
     */
    public function inicializar(string $type, int $id, Request $request): JsonResponse
    {
        try {
            if ($type !== 'subservicio') {
                return response()->json(['success' => false, 'message' => 'Solo se puede inicializar para subservicios'], 400);
            }

            $plantillas = $this->service->inicializarParaSubservicio($id, $request->user()->id);

            return response()->json([
                'success' => true,
                'message' => 'Plantillas de email inicializadas exitosamente',
                'data'    => PlantillaEmailResource::collection($plantillas),
            ], 201);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            $status = $e->getCode() === 422 ? 422 : 500;
            return response()->json(['success' => false, 'message' => $e->getMessage()], $status);
        }
    }

    public function showByServicioNumero($id_servicio, $numero_plantilla): JsonResponse
    {
        try {
            $plantilla = $this->service->getPlantillaByServicioNumero((int) $id_servicio, (int) $numero_plantilla);

            return response()->json([
                'success' => true,
                'data'    => new PlantillaEmailResource($plantilla),
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al obtener plantilla', 'error' => $e->getMessage()], 500);
        }
    }

    public function actualizar(UpdatePlantillaEmailRequest $request, $id): JsonResponse
    {
        try {
            $dto       = UpdatePlantillaEmailDTO::fromRequest($request);
            $plantilla = $this->service->updatePlantilla((int) $id, $dto, $request->user()->id);

            return response()->json([
                'success' => true,
                'message' => 'Plantilla Email actualizada exitosamente',
                'data'    => [
                    'id_plantilla_email' => $plantilla->id_plantilla_email,
                    'asunto'             => $plantilla->asunto,
                    'encabezado'         => $plantilla->encabezado,
                    'mensaje'            => $plantilla->mensaje,
                    'imagen_url'         => $plantilla->imagen_url,
                    'mensaje_boton'      => $plantilla->mensaje_boton,
                    'url_boton'          => $plantilla->url_boton,
                    'footer'             => $plantilla->footer,
                    'updated_by'         => $plantilla->updated_by,
                    'updated_at'         => $plantilla->updated_at,
                ],
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Plantilla no encontrada'], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al actualizar plantilla', 'error' => $e->getMessage()], 500);
        }
    }
}
