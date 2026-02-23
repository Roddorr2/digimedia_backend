<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PlantillaWhatsapp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class PlantillasWhatsappController extends Controller
{
    /**
     * Listar todas las plantillas WhatsApp con sus servicios
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $plantillas = PlantillaWhatsapp::with('servicio')->orderBy('id_servicio')->orderBy('numero_plantilla')->get();

            // Normalizar URLs de imágenes
            $plantillas->each(function ($plantilla) {
                $plantilla->imagen_url = $this->normalizeImageUrl($plantilla->imagen_url);
            });

            return response()->json([
                'success' => true,
                'data' => $plantillas
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener plantillas',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener plantilla WhatsApp por ID
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $plantilla = PlantillaWhatsapp::with('servicio')->find($id);

            if (!$plantilla) {
                return response()->json([
                    'success' => false,
                    'message' => 'Plantilla no encontrada'
                ], 404);
            }

            // Normalizar URL de imagen
            $plantilla->imagen_url = $this->normalizeImageUrl($plantilla->imagen_url);

            return response()->json([
                'success' => true,
                'data' => $plantilla
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener plantilla',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener plantilla WhatsApp por servicio y número
     * Endpoint usado por whatsapp-service (Node.js)
     *
     * @param int $id_servicio
     * @param int $numero_plantilla
     * @return \Illuminate\Http\JsonResponse
     */
    public function showByServicioNumero($id_servicio, $numero_plantilla)
    {
        try {
            $plantilla = PlantillaWhatsapp::where('id_servicio', $id_servicio)
                ->where('numero_plantilla', $numero_plantilla)
                ->with('servicio')
                ->first();

            if (!$plantilla) {
                return response()->json([
                    'success' => false,
                    'message' => "Plantilla no encontrada para servicio {$id_servicio} y número {$numero_plantilla}"
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id_plantilla_whatsapp' => $plantilla->id_plantilla_whatsapp,
                    'id_servicio' => $plantilla->id_servicio,
                    'numero_plantilla' => $plantilla->numero_plantilla,
                    'mensaje' => $plantilla->mensaje,
                    'imagen_url' => $this->normalizeImageUrl($plantilla->imagen_url),
                    'servicio' => [
                        'id_servicio' => $plantilla->servicio->id_servicio,
                        'nombre_servicio' => $plantilla->servicio->nombre_servicio
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener plantilla',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar plantilla WhatsApp (mensaje e imagen)
     * Solo para admin y marketing desde dashboard
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function actualizar(Request $request, $id)
    {
        try {
            $plantilla = PlantillaWhatsapp::findOrFail($id);

            // Validar datos
            $validator = Validator::make($request->all(), [
                'mensaje' => 'required|string|max:5000',
                'imagen' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120' // 5MB
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Actualizar mensaje
            $plantilla->mensaje = $request->mensaje;

            // Si hay nueva imagen, subir a Cloudinary
            if ($request->hasFile('imagen')) {
                // Eliminar imagen anterior de Cloudinary si existe
                if ($plantilla->imagen_url && str_contains($plantilla->imagen_url, 'cloudinary')) {
                    $this->deleteCloudinaryImage($plantilla->imagen_url);
                }

                // Subir nueva imagen
                $uploadedFile = Cloudinary::uploadApi()->upload(
                    $request->file('imagen')->getRealPath(),
                    [
                        'folder' => 'plantillas_whatsapp',
                        'resource_type' => 'image'
                    ]
                );

                $plantilla->imagen_url = $uploadedFile['secure_url'];
            }

            // Registrar quién actualizó
            $plantilla->updated_by = $request->user()->id;
            $plantilla->save();

            return response()->json([
                'success' => true,
                'message' => 'Plantilla WhatsApp actualizada exitosamente',
                'data' => [
                    'id_plantilla_whatsapp' => $plantilla->id_plantilla_whatsapp,
                    'mensaje' => $plantilla->mensaje,
                    'imagen_url' => $plantilla->imagen_url,
                    'updated_by' => $plantilla->updated_by,
                    'updated_at' => $plantilla->updated_at
                ]
            ]);
        } catch (\Exception $e) {
            $statusCode = $e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException ? 404 : 500;
            $message = $e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException
                ? 'Plantilla no encontrada'
                : 'Error al actualizar plantilla';

            return response()->json([
                'success' => false,
                'message' => $message,
                'error' => $e->getMessage()
            ], $statusCode);
        }
    }

    /**
     * Normalizar URL de imagen (convertir rutas relativas a URLs completas)
     *
     * @param string|null $imageUrl
     * @return string|null
     */
    private function normalizeImageUrl($imageUrl)
    {
        if (empty($imageUrl)) {
            return null;
        }

        // Si ya es una URL completa (http/https), devolverla tal cual
        if (preg_match('/^https?:\/\//i', $imageUrl)) {
            return $imageUrl;
        }

        // Convertir ruta relativa a URL completa
        return url($imageUrl);
    }

    /**
     * Eliminar imagen de Cloudinary extrayendo public_id de la URL
     *
     * @param string $imageUrl
     * @return void
     */
    private function deleteCloudinaryImage($imageUrl)
    {
        try {
            // Extraer public_id de la URL de Cloudinary
            // Ejemplo: https://res.cloudinary.com/cloud_name/image/upload/v1234567890/plantillas_whatsapp/abc123.jpg
            // public_id: plantillas_whatsapp/abc123
            
            preg_match('/upload\/(?:v\d+\/)?(.+)\.\w+$/', $imageUrl, $matches);
            
            if (isset($matches[1])) {
                $publicId = $matches[1];
                Cloudinary::destroy($publicId);
            }
        } catch (\Exception $e) {
            // Log pero no fallar si no se puede eliminar imagen antigua
            Log::warning("No se pudo eliminar imagen de Cloudinary: {$imageUrl}", ['error' => $e->getMessage()]);
        }
    }
}
