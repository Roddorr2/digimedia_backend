<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Models\BlogHead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Cloudinary\Cloudinary;
use Illuminate\Support\Facades\Log;

class BlogHeadController extends Controller
{
    public function create(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'titulo' => 'required|string|max:50',
                'texto_frase' => 'required|string|max:70',
                'texto_descripcion' => 'required|string|max:120',
                'public_image' => 'required|string',
                'url_image' => 'nullable|string',
                'alt'=> 'nullable|string',
                'title'=> 'nullable|string',
                'meta_title'=> 'nullable|string',
                'meta_descripcion'=> 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 400);
            }

            DB::beginTransaction();

            $blogHead = BlogHead::create($request->all());

            DB::commit();

            return response()->json([
                "status" => 200,
                "message" => "BlogHead creado correctamente",
                "id" => $blogHead->id_blog_head
            ], 200);

        }catch(\Exception $ex){
            DB::rollback();
            return response()->json([
                "status" => 500,
                "message" => "Error interno del servidor",
                "error" => $ex->getMessage()
                ], 500);
        }
    }

    public function update(Request $request, int $id){
        try{
            $validator = Validator::make($request->all(), [
                'titulo' => 'required|string|max:50',
                'texto_frase' => 'required|string|max:70',
                'texto_descripcion' => 'required|string|max:120',
                'public_image' => 'required|string',
                'url_image' => 'nullable|string',
                'alt'=> 'nullable|string',
                'title'=> 'nullable|string',
                'meta_title'=> 'nullable|string',
                'meta_descripcion'=> 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 400);
            }

            $blogHead = BlogHead::find($id);

            if (!$blogHead){
                return response()->json([
                    'status'=> 404,
                    'message'=> 'BlogHead no encontrado'
                ], 404);
            }

            DB::beginTransaction();

            $blogHead->update($request->all());

            $blog = \App\Models\Blog::where('id_blog_head', $blogHead->id_blog_head)->first();

            if ($blog) {

                $originalSlug = Str::slug($blogHead->titulo);
                $link = $originalSlug;
                $counter = 1;

                while (\App\Models\Blog::where('link', $link)->where('id_blog', '<>', $blog->id_blog)->exists()) {
                    $link = $originalSlug . '-' . $counter++;
                }

                $blog->link = $link;
                $blog->save();
            }

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'BlogHead y slug del blog actualizado correctamente',
                'id' => $blogHead->id_blog_head,
                'link' => $blog ? $blog->link : null,
            ], 200);

            }catch(\Exception $ex){
                DB::rollback();
                return response()->json([
                    'status' => 500,
                    'message' => 'Error interno del servidor',
                    'error' => $ex->getMessage()
                ], 500);
            }
    }


    public function show(int $id){
        try{

            $blogHead = BlogHead::find($id);
            if (!$blogHead) {
                return response()->json([
                    "status" => 404,
                    "message" => "BlogHead no encontrado"
                ],404);
            }

            return response()->json([
                "status" => 200,
                "data" => $blogHead
            ], 200);

        }catch(\Exception $ex){
            return response()->json([
                "status" => 500,
                "message" => "Error interno del servidor",
                "error" => $ex->getMessage()
                ], 500);
        }
    }

    public function destroy($id)
    {
        try{

            $blogHead = BlogHead::find($id);

            if (!$blogHead) {
                return response()->json([
                    "status" => 404,
                    "message" => "BlogHead no encontrado"
                ]);
            }
            $blogHead->delete();
            return response()->json([
                "status" => 200,
                "message" => "BlogHead eliminado correctamente"
                ], 200);

        }catch(\Exception $ex){
            return response()->json([
                "status" => 500,
                "message" => "Error al eliminar el blogHead",
                "error" => $ex->getMessage()
                ], 500);
        }
    }
}
