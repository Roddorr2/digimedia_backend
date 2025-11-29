<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Models\BlogHead;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Cloudinary\Cloudinary;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class BlogHeadController extends Controller
{
    public function create(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'titulo' => 'required|string|max:50',
                'texto_frase' => 'required|string|max:140',
                'texto_descripcion' => 'required|string|max:240',
                'public_image' => 'required|string',
                'url_image' => 'nullable|string',
                'alt'=> 'nullable|string|min:60|max:240',
                'title'=> 'nullable|string|min:50|max:140',
                'meta_title'=> 'nullable|string|min:50|max:120',
                'meta_descripcion'=> 'nullable|string|min:150|max:255'
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
                'texto_frase' => 'required|string|max:140',
                'texto_descripcion' => 'required|string|max:240',
                'public_image' => 'required|string',
                'url_image' => 'nullable|string',
                'alt'=> 'nullable|string|min:60|max:240',
                'title'=> 'nullable|string|min:50|max:140',
                'meta_title'=> 'nullable|string|min:50|max:120',
                'meta_descripcion'=> 'nullable|string|min:150|max:255'
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

            $blog = Blog::where('id_blog_head', $blogHead->id_blog_head)->first();

            if ($blog) {

                $originalSlug = Str::slug($blogHead->titulo);
                $link = $originalSlug;
                $counter = 1;

                while (Blog::where('link', $link)->where('id_blog', '<>', $blog->id_blog)->exists()) {
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


    public function show(int $id)
    {
        try {
            $blogHead = BlogHead::find($id);

            if (!$blogHead) {
                return response()->json([
                    "status" => 404,
                    "message" => "BlogHead no encontrado"
                ], 404);
            }

            // 1. Convertir MySQL → array
            $data = $blogHead->toArray();

            // 2. Revisar si hay autoguardado en Redis
            $redisKey = "blog:{$blogHead->id_blog_head}";
            $temporal = Redis::get($redisKey);
            $temporal = $temporal ? json_decode($temporal, true) : null;

            // 3. Si NO hay autoguardado en Redis → devolver datos normales
            if (!$temporal) {
                return response()->json([
                    "status" => 200,
                    "autoguardado" => false,
                    "data" => $data
                ], 200);
            }

            // 4. Si hay datos en Redis → fusionar Redis SOBRE MySQL
            $data = array_merge($data, $temporal);

            return response()->json([
                "status" => 200,
                "autoguardado" => true,
                "data" => $data
            ], 200);

        } catch (\Exception $ex) {
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
