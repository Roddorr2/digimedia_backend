<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PlantillaEmailService;
use App\Http\Requests\PlantillaEmail\UpdatePlantillaEmailRequest;
use App\Http\Resources\PlantillaEmailResource;
use App\DTOs\PlantillaEmail\UpdatePlantillaEmailDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PlantillasEmailController extends Controller
{
    public function __construct(
        private PlantillaEmailService $service
    ) {}

    /**
     * Listar todas las plantillas Email con sus servicios
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $plantillas = $this->service->getPlantillas();

            return response()->json([
                'success' => true,
                'data' => PlantillaEmailResource::collection($plantillas)
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
     * Obtener plantilla Email por ID
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
                'data' => new PlantillaEmailResource($plantilla)
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
     * Obtener plantilla Email por servicio y número
     * Endpoint usado por whatsapp-service (Node.js) y Jobs de Laravel
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
                'data' => new PlantillaEmailResource($plantilla)
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
     * Actualizar plantilla Email (todos los campos)
     * Solo para admin y marketing desde dashboard
     *
     * @param \App\Http\Requests\PlantillaEmail\UpdatePlantillaEmailRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function actualizar(UpdatePlantillaEmailRequest $request, $id): JsonResponse
    {
        try {
            $dto = UpdatePlantillaEmailDTO::fromRequest($request);
            $plantilla = $this->service->updatePlantilla((int)$id, $dto, $request->user()->id);

            return response()->json([
                'success' => true,
                'message' => 'Plantilla Email actualizada exitosamente',
                'data' => [
                    'id_plantilla_email' => $plantilla->id_plantilla_email,
                    'asunto' => $plantilla->asunto,
                    'encabezado' => $plantilla->encabezado,
                    'mensaje' => $plantilla->mensaje,
                    'imagen_url' => $plantilla->imagen_url,
                    'mensaje_boton' => $plantilla->mensaje_boton,
                    'url_boton' => $plantilla->url_boton,
                    'footer' => $plantilla->footer,
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
