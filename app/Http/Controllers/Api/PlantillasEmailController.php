<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PlantillaEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class PlantillasEmailController extends Controller
{
    /**
     * Listar todas las plantillas Email con sus servicios
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $plantillas = PlantillaEmail::with('servicio')->orderBy('id_servicio')->orderBy('numero_plantilla')->get();

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
     * Obtener plantilla Email por ID
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $plantilla = PlantillaEmail::with('servicio')->find($id);

            if (!$plantilla) {
                return response()->json([
                    'success' => false,
                    'message' => 'Plantilla no encontrada'
                ], 404);
            }

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
     * Obtener plantilla Email por servicio y número
     * Endpoint usado por whatsapp-service (Node.js) y Jobs de Laravel
     *
     * @param int $id_servicio
     * @param int $numero_plantilla
     * @return \Illuminate\Http\JsonResponse
     */
    public function showByServicioNumero($id_servicio, $numero_plantilla)
    {
        try {
            $plantilla = PlantillaEmail::where('id_servicio', $id_servicio)
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
     * Actualizar plantilla Email (todos los campos)
     * Solo para admin y marketing desde dashboard
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function actualizar(Request $request, $id)
    {
        try {
            $plantilla = PlantillaEmail::findOrFail($id);

            // Validar datos
            $validator = Validator::make($request->all(), [
                'asunto' => 'required|string|max:255',
                'encabezado' => 'required|string|max:500',
                'mensaje' => 'required|string|max:10000',
                'imagen' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120', // 5MB
                'mensaje_boton' => 'nullable|string|max:100',
                'url_boton' => 'nullable|url|max:500',
                'footer' => 'nullable|string|max:500',
                'red_facebook' => 'nullable|url|max:255',
                'red_tiktok' => 'nullable|url|max:255',
                'red_instagram' => 'nullable|url|max:255',
                'red_linkedin' => 'nullable|url|max:255'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Actualizar todos los campos de texto
            $plantilla->asunto = $request->asunto;
            $plantilla->encabezado = $request->encabezado;
            $plantilla->mensaje = $request->mensaje;
            $plantilla->mensaje_boton = $request->mensaje_boton;
            $plantilla->url_boton = $request->url_boton;
            $plantilla->footer = $request->footer;
            $plantilla->red_facebook = $request->red_facebook;
            $plantilla->red_tiktok = $request->red_tiktok;
            $plantilla->red_instagram = $request->red_instagram;
            $plantilla->red_linkedin = $request->red_linkedin;

            // Si hay nueva imagen, subir a Cloudinary
            if ($request->hasFile('imagen')) {
                // Eliminar imagen anterior de Cloudinary si existe
                if ($plantilla->imagen_url && str_contains($plantilla->imagen_url, 'cloudinary')) {
                    $this->deleteCloudinaryImage($plantilla->imagen_url);
                }

                // Subir nueva imagen
                $uploadedFile = Cloudinary::upload(
                    $request->file('imagen')->getRealPath(),
                    [
                        'folder' => 'plantillas_email',
                        'resource_type' => 'image'
                    ]
                );

                $plantilla->imagen_url = $uploadedFile->getSecurePath();
            }

            // Registrar quién actualizó
            $plantilla->updated_by = $request->user()->id;
            $plantilla->save();

            return response()->json([
                'success' => true,
                'message' => 'Plantilla Email actualizada exitosamente',
                'data' => [
                    'id_plantilla_email' => $plantilla->id_plantilla_email,
                    'asunto' => $plantilla->asunto,
                    'encabezado' => $plantilla->encabezado,
                    'mensaje' => $plantilla->mensaje,
                    'imagen_url' => $plantilla->imagen_url,
                    'mensaje_boton' => $plantilla->mensaje_boton,
                    'url_boton' => $plantilla->url_boton,
                    'footer' => $plantilla->footer,
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
     * Eliminar imagen de Cloudinary extrayendo public_id de la URL
     *
     * @param string $imageUrl
     * @return void
     */
    private function deleteCloudinaryImage($imageUrl)
    {
        try {
            // Extraer public_id de la URL de Cloudinary
            // Ejemplo: https://res.cloudinary.com/cloud_name/image/upload/v1234567890/plantillas_email/abc123.jpg
            // public_id: plantillas_email/abc123
            
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
