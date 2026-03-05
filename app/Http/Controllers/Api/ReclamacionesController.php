<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\libroreclamacion;
use App\Http\Resources\ReclamacionResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReclamacionesController extends Controller
{
    public function get(Request $request)
    {
        $reclamaciones = libroReclamacion::orderBy('id_reclamacion', 'asc')->paginate(4);
        return ReclamacionResource::collection($reclamaciones);
    }

    public function getById($id){
        $reclamacion = libroreclamacion::find($id);

        if (!$reclamacion) {
            return response()->json(['error' => 'Contacto no encontrado'], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $reclamacion
        ], 200);
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

        if($validated->fails()){
            return response()->json(['errors' => $validated->errors()], 400);
        }

        // Crear una nueva reclamación
        $reclamacion = libroReclamacion::create($request->all());

        return response()->json([
            'message' => 'Reclamación guardada exitosamente',
            'data' => $reclamacion,
        ], 201);
    }

    public function update(Request $request, $id){
        $reclamacion = libroreclamacion::find($id);

        if (!$reclamacion) {
            return response()->json(['error' => 'Reclamo no encontrado'], 404);
        }

        $validated = $request->validate([
            'estado' => 'required|in:PENDIENTE,ATENDIDO',
        ]);

        $reclamacion->update([
            'estadoReclamo' => $request->estado,
        ]);

        return response()->json([
            'message' => 'Estado actualizado exitosamente',
            'data' => $reclamacion,
        ], 200);
    }

    public function delete($id)
    {
        $reclamacion = libroReclamacion::find($id);

        if (!$reclamacion) {
            return response()->json(['error' => 'Reclamación no encontrada'], 404);
        }

        $reclamacion->delete();

        return response()->json(['message' => 'Reclamación eliminada exitosamente'], 200);
    }
}
