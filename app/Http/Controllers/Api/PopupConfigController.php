<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PopupConfig;
use App\Models\Subservicio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class PopupConfigController extends Controller
{
    /**
     * Listar todas las configuraciones de pop-ups
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $popups = PopupConfig::with([
                'subservicio.servicio',
                'createdBy:id,name',
                'updatedBy:id,name'
            ])->get();

            return response()->json([
                'success' => true,
                'data' => $popups
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener pop-ups',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener configuración por ID
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $popup = PopupConfig::with([
                'subservicio.servicio',
                'createdBy:id,name',
                'updatedBy:id,name'
            ])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $popup
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Pop-up no encontrado'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener pop-up',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener configuración por subservicio
     * Endpoint usado por el dashboard para obtener config específica
     *
     * @param int $id_subservicio
     * @return \Illuminate\Http\JsonResponse
     */
    public function showBySubservicio($id_subservicio)
    {
        try {
            $popup = PopupConfig::with([
                'subservicio.servicio',
                'createdBy:id,name',
                'updatedBy:id,name'
            ])->where('id_subservicio', $id_subservicio)->firstOrFail();

            return response()->json([
                'success' => true,
                'data' => $popup
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => "Pop-up no encontrado para subservicio {$id_subservicio}"
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener pop-up',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear nueva configuración de pop-up
     * Solo un pop-up por subservicio (relación 1:1)
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id_subservicio' => 'required|integer|exists:subservicios,id_subservicio|unique:popup_configs',
                'title_text' => 'required|string|max:80',
                'button_text' => 'required|string|max:25',
                'service_color' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
                'trigger_time' => 'required|in:3,5,8',
                'left_image_url' => 'nullable|url|max:500',
                'left_opacity' => 'nullable|integer|min:0|max:100',
                'right_image_url' => 'nullable|url|max:500',
                'right_opacity' => 'nullable|integer|min:0|max:100',
                'mobile_image_url' => 'nullable|url|max:500',
                'mobile_opacity' => 'nullable|integer|min:0|max:100'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            $popup = PopupConfig::create([
                ...$request->validated(),
                'created_by' => $request->user()->id,
                'updated_by' => $request->user()->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pop-up creado exitosamente',
                'data' => $popup->load('subservicio.servicio', 'createdBy:id,name', 'updatedBy:id,name')
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error creating popup: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al crear pop-up',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar configuración de pop-up
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $popup = PopupConfig::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'title_text' => 'nullable|string|max:80',
                'button_text' => 'nullable|string|max:25',
                'service_color' => 'nullable|regex:/^#[0-9A-Fa-f]{6}$/',
                'trigger_time' => 'nullable|in:3,5,8',
                'left_image_url' => 'nullable|url|max:500',
                'left_opacity' => 'nullable|integer|min:0|max:100',
                'right_image_url' => 'nullable|url|max:500',
                'right_opacity' => 'nullable|integer|min:0|max:100',
                'mobile_image_url' => 'nullable|url|max:500',
                'mobile_opacity' => 'nullable|integer|min:0|max:100'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            $popup->fill($request->validated());
            $popup->updated_by = $request->user()->id;
            $popup->save();

            return response()->json([
                'success' => true,
                'message' => 'Pop-up actualizado exitosamente',
                'data' => $popup->load('subservicio.servicio', 'createdBy:id,name', 'updatedBy:id,name')
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Pop-up no encontrado'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error updating popup: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar pop-up',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar configuración de pop-up
     * También elimina las imágenes asociadas de Cloudinary
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $popup = PopupConfig::findOrFail($id);

            // Limpiar imágenes de Cloudinary si existen
            if ($popup->left_image_url && str_contains($popup->left_image_url, 'cloudinary')) {
                $this->deleteCloudinaryImage($popup->left_image_url);
            }
            if ($popup->right_image_url && str_contains($popup->right_image_url, 'cloudinary')) {
                $this->deleteCloudinaryImage($popup->right_image_url);
            }
            if ($popup->mobile_image_url && str_contains($popup->mobile_image_url, 'cloudinary')) {
                $this->deleteCloudinaryImage($popup->mobile_image_url);
            }

            $popup->delete();

            return response()->json([
                'success' => true,
                'message' => 'Pop-up eliminado exitosamente'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Pop-up no encontrado'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error deleting popup: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar pop-up',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Subir imagen a Cloudinary (desktop left/right o mobile)
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadImage(Request $request, $id)
    {
        try {
            $popup = PopupConfig::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'field' => 'required|in:left_image,right_image,mobile_image',
                'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            $field = $request->input('field');
            $urlField = "{$field}_url";
            $opacityField = str_replace('_image', '_opacity', $field);

            // Eliminar imagen anterior si existe
            if ($popup->{$urlField} && str_contains($popup->{$urlField}, 'cloudinary')) {
                $this->deleteCloudinaryImage($popup->{$urlField});
            }

            // Subir nueva imagen
            $uploadedFile = Cloudinary::uploadApi()->upload(
                $request->file('image')->getRealPath(),
                [
                    'folder' => 'popup_configs',
                    'resource_type' => 'image'
                ]
            );

            $popup->{$urlField} = $uploadedFile['secure_url'];
            if (!isset($popup->{$opacityField}) || is_null($popup->{$opacityField})) {
                $popup->{$opacityField} = 100;
            }
            $popup->updated_by = $request->user()->id;
            $popup->save();

            return response()->json([
                'success' => true,
                'message' => 'Imagen subida exitosamente',
                'data' => [
                    'field' => $field,
                    'url' => $popup->{$urlField},
                    'opacity' => $popup->{$opacityField}
                ]
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Pop-up no encontrado'
            ], 404);
        } catch (\Exception $e) {
            Log::error("Error uploading popup image: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al subir imagen',
                'error' => $e->getMessage()
            ], 500);
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
            // Ejemplo: https://res.cloudinary.com/cloud_name/image/upload/v1234567890/popup_configs/abc123.jpg
            // public_id: popup_configs/abc123
            
            preg_match('/upload\/(?:v\d+\/)?(.+)\.\w+$/', $imageUrl, $matches);
            
            if (isset($matches[1])) {
                $publicId = $matches[1];
                Cloudinary::destroy($publicId);
            }
        } catch (\Exception $e) {
            // Log pero no fallar si no se puede eliminar imagen antigua
            Log::warning("No se pudo eliminar imagen de Cloudinary: {$imageUrl}", [
                'error' => $e->getMessage()
            ]);
        }
    }
}
