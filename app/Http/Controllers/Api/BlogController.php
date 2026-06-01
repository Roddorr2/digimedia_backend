<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Blog\StoreBlogRequest;
use App\Http\Requests\Blog\UpdateBlogRequest;
use App\Http\Resources\BlogResource;
use App\Services\Blog\BlogService;
use App\DTOs\Blog\FiltrosBlogDTO;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function __construct(
        private BlogService $blogService
    ) {}

    public function index()
    {
        try {
            $blogs = $this->blogService->getAllBlogs();
            return BlogResource::collection($blogs);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function blogByMonthYear(Request $request)
    {
        try {
            $filters = FiltrosBlogDTO::fromRequest($request);
            $blogs = $this->blogService->getBlogsByFilters($filters);
            return response()->json($blogs, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function create(StoreBlogRequest $request)
    {
        try {
            $blog = $this->blogService->createBlog($request->validated());
            
            return response()->json([
                "status" => 200,
                "message" => "Blog creado correctamente",
                "id" => $blog->id_blog,
                "link" => $blog->link,
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update(UpdateBlogRequest $request, int $id)
    {
        try {
            $blog = $this->blogService->updateBlog($id, $request->validated());
            
            return response()->json([
                'status' => 200,
                'message' => 'Blog actualizado',
                'id' => $blog->id_blog,
                'link' => $blog->link,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 404,
                'message' => 'Blog no encontrado'
            ], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show(int $id)
    {
        try {
            $blog = $this->blogService->getBlogById($id);
            
            return response()->json([
                'status' => 200,
                'data' => new BlogResource($blog)
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 404,
                'message' => 'Blog no encontrado'
            ], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function showLink(string $link)
    {
        try {
            $blog = $this->blogService->getBlogByLink($link);
            
            return response()->json([
                "status" => 200,
                'data' => new BlogResource($blog)
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                "status" => 404,
                "message" => "Blog no encontrado"
            ], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy(int $id)
    {
        try {
            $this->blogService->deleteBlog($id);
            
            return response()->json([
                "status" => 200,
                "message" => "Blog eliminado correctamente"
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 404,
                'message' => 'Blog no encontrado'
            ], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}