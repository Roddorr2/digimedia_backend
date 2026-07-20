<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Blog\ConsejoService;
use App\DTOs\Consejo\CreateConsejoDTO;
use App\DTOs\Consejo\UpdateConsejoDTO;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ConsejoController extends Controller
{
    public function __construct(private ConsejoService $consejoService) {}

    public function showAll(int $id)
    {
        try {
            $consejos = $this->consejoService->getConsejosByBlogBodyId($id);
            if ($consejos->isEmpty()) {
                return response()->json(['error' => 'No se encontraron consejos'], 404);
            }
            return response()->json($consejos, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function create(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'texto' => 'required|string',
                'enlace' => 'nullable|string',
                'palabra' => 'nullable|string',
                'orden' => 'nullable|integer',
                'id_blog_body' => 'required|integer|exists:blog_bodies,id_blog_body',
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 400);
            }
            $dto = CreateConsejoDTO::fromRequest($request);
            $consejo = $this->consejoService->createConsejo($dto);
            return response()->json(["status" => 200, "message" => "Consejo creado correctamente", "id" => $consejo->id_consejo], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, int $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'texto' => 'required|string',
                'enlace' => 'nullable|string',
                'palabra' => 'nullable|string',
                'orden' => 'nullable|integer',
                'id_blog_body' => 'required|integer|exists:blog_bodies,id_blog_body',
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 400);
            }
            $dto = UpdateConsejoDTO::fromRequest($request);
            $consejo = $this->consejoService->updateConsejo($id, $dto);
            return response()->json(["status" => 200, "message" => "Consejo actualizado correctamente", "id" => $consejo->id_consejo], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['status' => 400, 'message' => 'Consejo no encontrado'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy(int $id)
    {
        try {
            $this->consejoService->deleteConsejo($id);
            return response()->json(["status" => 200, "message" => "Consejo eliminado correctamente"], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Consejo no encontrado'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroyAll(int $id)
    {
        try {
            $this->consejoService->deleteConsejosByBlogBodyId($id);
            return response()->json(["status" => 200, "message" => "Consejos eliminados correctamente"], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'No se encontraron consejos'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
