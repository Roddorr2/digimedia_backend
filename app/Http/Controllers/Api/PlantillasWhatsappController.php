<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PlantillaWhatsappService;
use App\Http\Requests\PlantillaWhatsapp\UpdatePlantillaWhatsappRequest;
use App\Http\Resources\PlantillaWhatsappResource;
use App\DTOs\PlantillaWhatsapp\UpdatePlantillaWhatsappDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PlantillasWhatsappController extends Controller
{
    public function __construct(
        private PlantillaWhatsappService $service
    ) {}

    /**
     * Listar todas las plantillas WhatsApp con sus servicios
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $plantillas = $this->service->getPlantillas();

            return response()->json([
                'success' => true,
                'data' => PlantillaWhatsappResource::collection($plantillas)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener plantillas',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener plantilla WhatsApp por ID
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id): JsonResponse
    {
        try {
            $plantilla = $this->service->getPlantillaById((int)$id);

            return response()->json([
                'success' => true,
                'data' => new PlantillaWhatsappResource($plantilla)
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Plantilla no encontrada'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener plantilla',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener plantilla WhatsApp por servicio y número
     * Endpoint usado por whatsapp-service (Node.js)
     *
     * @param int $id_servicio
     * @param int $numero_plantilla
     * @return \Illuminate\Http\JsonResponse
     */
    public function showByServicioNumero($id_servicio, $numero_plantilla): JsonResponse
    {
        try {
            $plantilla = $this->service->getPlantillaByServicioNumero((int)$id_servicio, (int)$numero_plantilla);

            return response()->json([
                'success' => true,
                'data' => new PlantillaWhatsappResource($plantilla)
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener plantilla',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar plantilla WhatsApp (mensaje e imagen)
     * Solo para admin y marketing desde dashboard
     *
     * @param \App\Http\Requests\PlantillaWhatsapp\UpdatePlantillaWhatsappRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function actualizar(UpdatePlantillaWhatsappRequest $request, $id): JsonResponse
    {
        try {
            $dto = UpdatePlantillaWhatsappDTO::fromRequest($request);
            $plantilla = $this->service->updatePlantilla((int)$id, $dto, $request->user()->id);

            return response()->json([
                'success' => true,
                'message' => 'Plantilla WhatsApp actualizada exitosamente',
                'data' => [
                    'id_plantilla_whatsapp' => $plantilla->id_plantilla_whatsapp,
                    'mensaje' => $plantilla->mensaje,
                    'imagen_url' => $plantilla->imagen_url,
                    'updated_by' => $plantilla->updated_by,
                    'updated_at' => $plantilla->updated_at
                ]
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Plantilla no encontrada'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar plantilla',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
