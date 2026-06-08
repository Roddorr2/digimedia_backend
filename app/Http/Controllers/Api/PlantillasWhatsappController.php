<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PlantillaWhatsappService;
use App\Http\Requests\PlantillaWhatsapp\UpdatePlantillaWhatsappRequest;
use App\Http\Resources\PlantillaWhatsappResource;
use App\DTOs\PlantillaWhatsapp\UpdatePlantillaWhatsappDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PlantillasWhatsappController extends Controller
{
    public function __construct(
        private PlantillaWhatsappService $service
    ) {}

    public function index(): JsonResponse
    {
        try {
            $plantillas = $this->service->getPlantillas();

            return response()->json([
                'success' => true,
                'data'    => PlantillaWhatsappResource::collection($plantillas),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener plantillas',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $plantilla = $this->service->getPlantillaById((int) $id);

            return response()->json([
                'success' => true,
                'data'    => new PlantillaWhatsappResource($plantilla),
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Plantilla no encontrada'], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al obtener plantilla', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Listar plantillas por owner (servicio o subservicio).
     * GET /api/plantillas/whatsapp/by-owner/{type}/{id}
     */
    public function showByOwner(string $type, int $id): JsonResponse
    {
        try {
            $plantillas = $this->service->getByOwner($type, $id);

            return response()->json([
                'success' => true,
                'data'    => PlantillaWhatsappResource::collection($plantillas),
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
     * Inicializar 3 plantillas para un subservicio copiando del servicio padre.
     * POST /api/plantillas/whatsapp/by-owner/subservicio/{id}/init
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
                'message' => 'Plantillas inicializadas exitosamente',
                'data'    => PlantillaWhatsappResource::collection($plantillas),
            ], 201);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            $status = $e->getCode() === 422 ? 422 : 500;
            return response()->json(['success' => false, 'message' => $e->getMessage()], $status);
        }
    }

    /**
     * Endpoint para el whatsapp-service (Node.js) — busca plantilla de subservicio.
     * Si no existe, devuelve 404 y el Node.js cae al servicio padre.
     * GET /api/plantillas/whatsapp/subservicio/{id_subservicio}/{numero_plantilla}  (API key)
     */
    public function showBySubservicioNumero(int $id_subservicio, int $numero_plantilla): JsonResponse
    {
        try {
            $plantilla = $this->service->getByOwner('subservicio', $id_subservicio)
                ->firstWhere('numero_plantilla', $numero_plantilla);

            if (!$plantilla) {
                return response()->json([
                    'success' => false,
                    'message' => "Plantilla no encontrada para subservicio {$id_subservicio} número {$numero_plantilla}",
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data'    => new PlantillaWhatsappResource($plantilla),
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al obtener plantilla', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Endpoint para el whatsapp-service (Node.js) — mantener por compatibilidad.
     * GET /api/plantillas/whatsapp/{id_servicio}/{numero_plantilla}  (API key)
     */
    public function showByServicioNumero($id_servicio, $numero_plantilla): JsonResponse
    {
        try {
            $plantilla = $this->service->getPlantillaByServicioNumero((int) $id_servicio, (int) $numero_plantilla);

            return response()->json([
                'success' => true,
                'data'    => new PlantillaWhatsappResource($plantilla),
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al obtener plantilla', 'error' => $e->getMessage()], 500);
        }
    }

    public function actualizar(UpdatePlantillaWhatsappRequest $request, $id): JsonResponse
    {
        try {
            $dto      = UpdatePlantillaWhatsappDTO::fromRequest($request);
            $plantilla = $this->service->updatePlantilla((int) $id, $dto, $request->user()->id);

            return response()->json([
                'success' => true,
                'message' => 'Plantilla WhatsApp actualizada exitosamente',
                'data'    => [
                    'id_plantilla_whatsapp' => $plantilla->id_plantilla_whatsapp,
                    'mensaje'               => $plantilla->mensaje,
                    'imagen_url'            => $plantilla->imagen_url,
                    'updated_by'            => $plantilla->updated_by,
                    'updated_at'            => $plantilla->updated_at,
                ],
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Plantilla no encontrada'], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al actualizar plantilla', 'error' => $e->getMessage()], 500);
        }
    }
}
