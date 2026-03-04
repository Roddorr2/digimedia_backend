<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\servicios;
use App\Http\Resources\ServicioResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ServiciosController extends Controller
{
    public function get(){
        return ServicioResource::collection(
            Servicios::orderBy('id_servicio', 'desc')->paginate(20)
        );
    }

    public function create(Request $request){
        $validator = Validator::make($request->all(),[
            'nombre' => 'required|string|max:100',
            'descripcion' => 'required|string|max:200'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $servicio = Servicios::create($request->all());

        return response()->json([
            'message' => 'Servicio creado exitosamente'
        ], 201);
    }

    public function update(Request $request, $id)
    {
        try {
            // Validar datos
            $validator = Validator::make($request->all(), [
                'nombre' => 'required|string|max:100',
                'descripcion' => 'required|string|max:200'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Buscar servicio
            $servicio = Servicios::findOrFail($id);

            if (!$servicio) {
                return response()->json([
                    'status' => false,
                    'message' => 'Servicio no encontrado'
                ], 404);
            }

            // Actualizar campos
            $servicio->nombre = $request->nombre;

            // Guardar cambios
            $servicio->save();

            return response()->json([
                'status' => true,
                'message' => 'Servicio actualizado exitosamente',
                'data' => $servicio
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al actualizar el servicio',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function delete($id){
        $servicio = Servicios::find($id);
        if(!$servicio) {
            return response()->json([
                'message' => 'Servicio no encontrado'
            ], 404);
        }

        $servicio->delete();
        return response()->json([
            'message' => 'Servicio eliminado exitosamente'
        ]);
    }
}
