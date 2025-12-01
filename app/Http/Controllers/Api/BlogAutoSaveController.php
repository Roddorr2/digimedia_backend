<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class BlogAutoSaveController extends Controller
{
    public function guardarTemporal(Request $request)
    {
        $id_blog = $request->input('id_blog');
        $parte = $request->input('parte'); // ej: "blog_bodies"
        $datos = $request->input('datos'); // los campos modificados
        $id_item = $request->input('id_item'); // solo para relaciones 1:N

        $key = "blog:{$id_blog}";

        $blogTemporal = Redis::get($key);
        $blogTemporal = $blogTemporal ? json_decode($blogTemporal, true) : [];

        if ($id_item) { // relacion 1:N (ej: tarjetas)
            // crea estructura si no existe
            if (!isset($blogTemporal[$parte])) {
                $blogTemporal[$parte] = [];
            }
            // fusiona cambios por ID
            $blogTemporal[$parte][$id_item] = array_merge(
                $blogTemporal[$parte][$id_item] ?? [],
                $datos
            );
        } else { // relacion 1:1
            $blogTemporal[$parte] = array_merge(
                $blogTemporal[$parte] ?? [],
                $datos
            );
        }

        Redis::setex($key, 600, json_encode($blogTemporal));

        return response()->json(['status' => 'ok']);
    }


    public function obtenerTemporal($id_blog)
    {
        $key = "blog:{$id_blog}";
        $data = Redis::get($key);

        if ($data) {
            return response()->json(json_decode($data, true));
        }

        return response()->json(['status' => 'vacio']);
    }
}
