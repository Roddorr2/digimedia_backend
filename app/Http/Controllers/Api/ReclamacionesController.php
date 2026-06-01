<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ReclamacionService;
use App\DTOs\Reclamacion\CreateReclamacionDTO;
use App\DTOs\Reclamacion\UpdateReclamacionDTO;
use App\Http\Resources\ReclamacionResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ReclamacionesController extends Controller
{
    public function __construct(
        private ReclamacionService $reclamacionService
    ) {}

    public function get(Request $request)
    {
        $reclamaciones = $this->reclamacionService->getReclamaciones(4);
        return ReclamacionResource::collection($reclamaciones);
    }

    public function getById($id)
    {
        try {
            $reclamacion = $this->reclamacionService->getReclamacionById((int)$id);

            return response()->json([
                'status' => 'success',
                'data' => $reclamacion
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Contacto no encontrado'], 404);
        }
    }

    /* Guardar una reclamación */
    public function create(Request $request)
    {
        // Validación de datos
        $validated = Validator::make($request->all(), [
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'documento' => 'required|string|max:100',
            'numeroDocumento' => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'celular' => 'required|string|max:20',
            'direccion' => 'required|string|max:250',
            'distrito' => 'required|string|max:250',
            'ciudad' => 'required|string|max:250',
            'tipoReclamo' => 'required|string|max:20',
            'id_servicio' => 'required|integer|min:1|max:4',
            'reclamoPerson' => 'required|string|max:1050',
            'checkReclamoForm' => 'required|boolean',
            'aceptaPoliticaPrivacidad' => 'required|boolean',
            'fechaIncidente' => 'required|date',
        ]);

        if ($validated->fails()) {
            return response()->json(['errors' => $validated->errors()], 400);
        }

        $dto = CreateReclamacionDTO::fromRequest($request);
        $reclamacion = $this->reclamacionService->createReclamacion($dto);

        return response()->json([
            'message' => 'Reclamación guardada exitosamente',
            'data' => $reclamacion,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'estado' => 'required|in:PENDIENTE,ATENDIDO',
            ]);

            $dto = UpdateReclamacionDTO::fromRequest($request);
            $reclamacion = $this->reclamacionService->updateEstado((int)$id, $dto);

            return response()->json([
                'message' => 'Estado actualizado exitosamente',
                'data' => $reclamacion,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Reclamo no encontrado'], 404);
        }
    }

    public function delete($id)
    {
        try {
            $this->reclamacionService->deleteReclamacion((int)$id);

            return response()->json(['message' => 'Reclamación eliminada exitosamente'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Reclamación no encontrada'], 404);
        }
    }
}
