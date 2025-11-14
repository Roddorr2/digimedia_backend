<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BlogBody;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class BlogBodyController extends Controller
{

    public function create(Request $request)
    {
        try{

            $validator = Validator::make($request->all(), [
                'titulo' => 'required|string|max:255',
                'descripcion' => 'required|string',
                'id_commend_tarjeta' => 'nullable|integer|exists:commend_tarjetas,id_commend_tarjeta',
                'public_image1' => 'nullable|string',
                'url_image1' => 'nullable|string',
<<<<<<< HEAD
                'alt_image1' => 'nullable|string|min:60|max:240', // alt 60-240
                'title_image1' => 'nullable|string|min:50|max:140', // title 50 - 140
                'public_image2' => 'nullable|string',
                'url_image2' => 'nullable|string',
                'alt_image2' => 'nullable|string|min:60|max:240',
                'title_image2' => 'nullable|string|min:50|max:140',
                'public_image3' => 'nullable|string',
                'url_image3' => 'nullable|string',
                'alt_image3' => 'nullable|string|min:60|max:240',
                'title_image3' => 'nullable|string||min:50|max:140',
=======
                'alt_image1' => 'nullable|string|min:60|max:120',
                'title_image1' => 'nullable|string|min:50|max:70',
                'public_image2' => 'nullable|string',
                'url_image2' => 'nullable|string',
                'alt_image2' => 'nullable|string|min:60|max:120',
                'title_image2' => 'nullable|string|min:50|max:70',
                'public_image3' => 'nullable|string',
                'url_image3' => 'nullable|string',
                'alt_image3' => 'nullable|string|min:60|max:120',
                'title_image3' => 'nullable|string|min:50|max:70',
>>>>>>> f922d4db137afbdda5c3013103efc0f204528d23
                'flag_galeria' => 'nullable|boolean',
                'flag_consejos' => 'nullable|boolean',
                'flag_informacion' => 'nullable|boolean',
                'service_url' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 400);
            }

            DB::beginTransaction();

            $blogBody = BlogBody::create($request->all());

            DB::commit();

            return response()->json([
                "status" => 200,
                "message" => "BlogBody creado correctamente",
                "id" => $blogBody->id_blog_body
            ], 200);

        }catch(\Exception $ex){
            DB::rollback();
            return response()->json([
                "status" => 500,
                "message" => "Error al crear el blogBody",
                "error" => $ex->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, int $id){
        try{
            $validator =  Validator::make($request->all(), [
                'titulo' => 'required|string|max:255',
                'descripcion' => 'required|string',
                'id_commend_tarjeta' => 'nullable|integer|exists:commend_tarjetas,id_commend_tarjeta',
                'public_image1' => 'nullable|string',
                'url_image1' => 'nullable|string',
                'alt_image1' => 'nullable|string|min:60|max:240', // alt 60 - 240
                'title_image1' => 'nullable|string|min:50|max:140', // title 50 - 140
                'public_image2' => 'nullable|string',
                'url_image2' => 'nullable|string',
                'alt_image2' => 'nullable|string|min:60|max:240',
                'title_image2' => 'nullable|string|min:50|max:140',
                'public_image3' => 'nullable|string',
                'url_image3' => 'nullable|string',
                'alt_image3' => 'nullable|string|min:60|max:240',
                'title_image3' => 'nullable|string|min:50|max:140',
                'flag_galeria' => 'nullable|boolean',
                'flag_consejos' => 'nullable|boolean',
                'flag_informacion' => 'nullable|boolean',
                'service_url' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors'=> $validator->errors()], 400);
            }

            $blogBody = BlogBody::find($id);

            if (!$blogBody){
                return response()->json([
                    'status'=> 404,
                    'message'=> 'BlogBody no encontrado'
                ], 404);
            }

            DB::beginTransaction();

            $blogBody->update($request->all());

            DB::commit();
            return response()->json([
                'status'=> 200,
                'message'=> 'Blog Body actualizado',
                'id'=> $blogBody->id_blog_body
            ], 200);
        }catch(\Exception $ex){
            DB::rollback();
            return response()->json([
                "status"=> 500,
                "message"=> $ex->getMessage(),
                "error"=> "Error interno del servidor"
            ],500);
        }
    }

    public function show(int $id){
        try{
            $blogBody = BlogBody::with('commend_tarjeta','tarjetas')->find($id);
            if (!$blogBody) {
                return response()->json([
                    "status" => 404,
                    "message" => "BlogBody no encontrada"
                ],404);
            }
            return response()->json([
                "status" => 200,
                "data" => $blogBody
            ], 200);

        }catch(\Exception $ex){
            return response()->json([
                "status" => 500,
                "message" => "Error interno",
                "error" => $ex->getMessage()
            ], 500);
        }
    }

    public function destroy(int $id)
    {
        try{

            $blogBody = BlogBody::find($id);

            if (!$blogBody) {
                return response()->json([
                    "status" => 404,
                    "message" => "BlogBody no encontrada"
                ],404);
            }

            $blogBody->delete();

            return response()->json([
                "status" => 200,
                "message" => "BlogBody eliminada correctamente"
            ], 200);

        }catch(\Exception $ex){
            return response()->json([
                "status" => 500,
                "message" => "Error al eliminar el BlogBody",
                "error" => $ex->getMessage()
            ], 500);
        }
    }
}
