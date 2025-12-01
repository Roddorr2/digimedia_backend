<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blog;
use App\Models\BlogBody;
use App\Models\BlogHead;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Services\AuditoriaService;
use Illuminate\Support\Facades\Redis;

class BlogController extends Controller
{
    public function index()
    {
        $blogs = Blog::with('card')->get();
        return response()->json($blogs, 200);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_blog_head' => 'required|integer|exists:blog_heads,id_blog_head',
            'id_blog_body' => 'required|integer|exists:blog_bodies,id_blog_body',
            'id_blog_footer' => 'required|integer|exists:blog_footers,id_blog_footer',
            'fecha' => 'required|date',
            'id_empleado' => 'required|integer|exists:empleados,id_empleado',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $id_empleado = $request->id_empleado;

        DB::beginTransaction();

        try {
            $blogHead = BlogHead::findOrFail($request->id_blog_head);
            $titulo = $blogHead->titulo ?? 'blog';

            // Generar slug único para el campo 'link'
            $originalSlug = Str::slug($titulo);
            $link = $originalSlug;
            $counter = 1;

            while (Blog::where('link', $link)->exists()) {
                $link = $originalSlug . '-' . $counter++;
            }

            $data = $request->all();
            $data['link'] = $link;

            $blog = Blog::create($data);
            DB::commit();
            // Registrar en la tabla de auditoría
            AuditoriaService::registrar(
                $blog->id_blog,
                $id_empleado,
                'CREAR',
                (BlogHead::findOrFail($request->id_blog_head))->titulo,
            );

            return response()->json([
                "status" => 200,
                "message" => "Blog creado correctamente",
                "id" => $blog->id_blog,
                "link" => $blog->link,
            ], 200);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id){
        try{
            $validator = Validator::make($request->all(), [
                'id_blog_head' => 'required|integer|exists:blog_heads,id_blog_head',
                'id_blog_body' => 'required|integer|exists:blog_bodies,id_blog_body',
                'id_blog_footer' => 'required|integer|exists:blog_footers,id_blog_footer',
                'fecha' => 'required|date',
                'id_empleado' => 'required|integer|exists:empleados,id_empleado', // No necesario
                'descripcion' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors'=> $validator->errors()], 400);
            }

            // $id_empleado = $request->id_empleado;
            $id_empleado = $blog->card->id_empleado ?? null;
            $descripcion = $request->descripcion;

            $blog = Blog::find($id);

            if (!$blog){
                return response()->json([
                    'status'=> 404,
                    'message'=> 'Blog no encontrado'
                ], 404);
            }

            DB::beginTransaction();

            // Get the blog head to generate link from title
            $blogHead = BlogHead::findOrFail($request->id_blog_head);
            $titulo = $blogHead->titulo ?? 'blog';

            // Generate unique slug for the 'link' field, excluding current blog
            $originalSlug = Str::slug($titulo);
            $link = $originalSlug;
            $counter = 1;

            while (Blog::where('link', $link)->where('id_blog', '!=', $id)->exists()) {
                $link = $originalSlug . '-' . $counter++;
            }

            $data = $request->all();
            $data['link'] = $link;

            $blog->update($data);

            AuditoriaService::registrar(
                $blog->id_blog,
                $request->id_empleado,
                'ACTUALIZAR',
                (\App\Models\BlogHead::findOrFail($request->id_blog_head))->titulo,
                $descripcion,
            );

            DB::commit();

            $redisKey = "blog:{$blog->id_blog}";
            Redis::del($redisKey);

            return response()->json([
                'status'=> 200,
                'message'=> 'Blog actualizado',
                'id'=> $blog->id_blog,
                'link'=> $blog->link,
            ],200);
        }catch(\Exception $e){
            DB::rollback();
            return response()->json(['error'=> $e->getMessage()], 500);
        }
    }

    public function show(int $id)
    {
        try{

            $blog = Blog::with('card')->find($id);

            if (!$blog) {
                return response()->json([
                    "status" => 404,
                    "message" => "Blog no encontrada"
                ],400);
            }

            return response()->json([
                "status" => 200,
                'data' => $blog
            ],200);

        }catch(\Exception $e){
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

     public function showLink(string $link)
    {
        try{

            $blog = Blog::with(['card', 'body', 'head'])->where('link', $link)->first();

            if (!$blog) {
                return response()->json([
                    "status" => 404,
                    "message" => "Blog no encontrada"
                ],400);
            }

            return response()->json([
                "status" => 200,
                'data' => $blog,
            ],200);

        }catch(\Exception $e){
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function destroy(int $id)
    {
        try{

            $blog = Blog::with(['card', 'head'])->find($id);
            $id_empleado = $blog->card->id_empleado ?? null;

            if (!$blog){
                return response()->json([
                    'status'=> 404,
                    'message'=> 'Blog no encontrado'
                ], 404);
            }

            $id_header_blog = $blog->id_blog_head;

            $id_body_blog = $blog->id_blog_body;

            $id_footer_blog = $blog->id_blog_footer;

            $relativePath = "images/templates/plantilla{$blog->card->id_plantilla}/"
            //  . Str::slug($blog->head->titulo)
             . $blog->id_blog;

            //eliminarla pero ver si existe asi que normal obvia la anterior
            if (Storage::disk('public')->exists($relativePath)) {
                Storage::disk('public')->deleteDirectory($relativePath);
            }
            //Registrar blog_auditoria
            AuditoriaService::registrar(
                $id,
                $id_empleado,
                'ELIMINAR',
                //(BlogHead::findOrFail((Blog::findOrFail($id))->id_blog_head))->titulo,
                $titulo = $blog->head->titulo ?? 'blog', //CAMBIAR SI FUNCA
            );

            //primero card
            $card_object = new CardController();

            $card_object->destroy($blog->card->id_card);

            //segundo blog
            $blog->delete();

            //tecero blog_head
            $blog_head = new BlogHeadController();
            $blog_head->destroy($id_header_blog);

            //cuarto blog_footer
            $blog_footer = new BlogFooterController();
            $blog_footer->destroy($id_footer_blog);

            //quinto tarjetas
            $tarjeta = new TarjetaController();
            $tarjeta->destroyAll($id_body_blog);

            //sexto commend_tarjeta
            $blog_body_model = BlogBody::find($id_body_blog);
            $commend_tarjeta = new CommendTarjetaController();
            $commend_tarjeta->destroy($blog_body_model->id_commend_tarjeta);

            //por ultimo blog_body
            $blog_body_model->delete();

            return response()->json([
                "status" => 200,
                "message" => "Blog eliminado correctamente"
            ],200);


        }catch(\Exception $e){
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
