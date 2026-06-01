<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BlogHead\StoreBlogHeadRequest;
use App\Http\Requests\BlogHead\UpdateBlogHeadRequest;
use App\Http\Resources\BlogHeadResource;
use App\Services\Blog\BlogHeadService;
use App\DTOs\BlogHead\CreateBlogHeadDTO;
use App\DTOs\BlogHead\UpdateBlogHeadDTO;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BlogHeadController extends Controller
{
    public function __construct(
        private BlogHeadService $blogHeadService
    ) {}

    public function create(StoreBlogHeadRequest $request)
    {
        try {
            $dto = CreateBlogHeadDTO::fromRequest($request);
            $blogHead = $this->blogHeadService->create($dto);

            return response()->json([
                "status" => 200,
                "message" => "BlogHead creado correctamente",
                "id" => $blogHead->id_blog_head
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "status" => 500,
                "message" => "Error interno del servidor",
                "error" => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function update(UpdateBlogHeadRequest $request, int $id)
    {
        try {
            $dto = UpdateBlogHeadDTO::fromRequest($request);
            $result = $this->blogHeadService->update($id, $dto);

            $responseData = [
                'status' => 200,
                'message' => 'BlogHead actualizado correctamente',
                'id' => $result['blogHead']->id_blog_head,
            ];

            if ($result['updated_link']) {
                $responseData['message'] = 'BlogHead y slug del blog actualizados correctamente';
                $responseData['link'] = $result['updated_link'];
            }

            return response()->json($responseData, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 404,
                'message' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Error interno del servidor',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function show(int $id)
    {
        try {
            $blogHead = $this->blogHeadService->findById($id);

            return response()->json([
                "status" => 200,
                "data" => new BlogHeadResource($blogHead)
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                "status" => 404,
                "message" => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                "status" => 500,
                "message" => "Error interno del servidor",
                "error" => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function destroy(int $id)
    {
        try {
            $this->blogHeadService->delete($id);

            return response()->json([
                "status" => 200,
                "message" => "BlogHead eliminado correctamente"
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                "status" => 404,
                "message" => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                "status" => 500,
                "message" => "Error al eliminar el blogHead",
                "error" => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}