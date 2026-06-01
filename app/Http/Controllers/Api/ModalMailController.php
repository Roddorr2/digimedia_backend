<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ModalMail\ReportMailErrorRequest;
use App\Http\Resources\EmailModalResource;
use App\Services\ModalEmailService;
use App\DTOs\ModalMail\ReportMailErrorDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ModalMailController extends Controller
{
    public function __construct(
        private ModalEmailService $modalEmailService
    ) {}

    public function sendMail($id): JsonResponse
    {
        try {
            $modalMail = $this->modalEmailService->sendMail((int)$id);

            return response()->json(new EmailModalResource($modalMail), 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Mensaje no encontrado'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al enviar el correo'], 500);
        }
    }

    public function reportarError(ReportMailErrorRequest $request, $id): JsonResponse
    {
        try {
            $dto = ReportMailErrorDTO::fromRequest($request);
            $modalMail = $this->modalEmailService->reportarError((int)$id, $dto);

            return response()->json([
                'message' => 'Error reportado exitosamente',
                'modal_mail' => new EmailModalResource($modalMail)
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Mensaje no encontrado'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al reportar el error'], 500);
        }
    }
}
