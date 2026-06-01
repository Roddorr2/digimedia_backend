<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ModalWat\ChangeWatEstadoRequest;
use App\Http\Resources\WatModalResource;
use App\Services\ModalWhatsAppService;
use App\DTOs\ModalWat\ChangeWatEstadoDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ModalWatController extends Controller
{
    public function __construct(
        private ModalWhatsAppService $modalWhatsAppService
    ) {}

    /**
     * Redirects to wa.me with prefilled message.
     *
     * @param mixed $id
     * @return \Illuminate\Http\RedirectResponse|JsonResponse
     */
    public function sendWat($id)
    {
        try {
            $url = $this->modalWhatsAppService->generateWhatsAppUrl((int)$id);
            return redirect()->away($url);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Mensaje no encontrado'], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al generar la redirección',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Updates the WhatsApp state.
     *
     * @param ChangeWatEstadoRequest $request
     * @param mixed $id
     * @return JsonResponse
     */
    public function cambiarEstado(ChangeWatEstadoRequest $request, $id): JsonResponse
    {
        try {
            $dto = ChangeWatEstadoDTO::fromRequest($request);
            $modalWat = $this->modalWhatsAppService->cambiarEstado((int)$id, $dto);

            return response()->json(new WatModalResource($modalWat), 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Mensaje no encontrado'], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar el estado',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}
