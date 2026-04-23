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
     * Acepta imágenes directamente vía multipart/form-data
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id_subservicio'  => 'required|integer|exists:subservicios,id_subservicio|unique:popup_configs',
                'title_text'      => 'required|string|min:5|max:80',
                'button_text'     => 'required|string|min:2|max:25',
                'service_color'   => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
                'trigger_time'    => 'required|in:3,5,8',
                // Desktop
                'left_image'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
                'left_opacity'    => 'nullable|integer|min:0|max:100',
                'right_image'     => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
                'right_opacity'   => 'nullable|integer|min:0|max:100',
                // Mobile
                'mobile_image'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
                'mobile_opacity'  => 'nullable|integer|min:0|max:100',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors'  => $validator->errors()
                ], 422);
            }

            $data = [
                'id_subservicio' => $request->id_subservicio,
                'title_text'     => $request->title_text,
                'button_text'    => $request->button_text,
                'service_color'  => $request->service_color,
                'trigger_time'   => $request->trigger_time,
                'left_opacity'   => $request->left_opacity   ?? 100,
                'right_opacity'  => $request->right_opacity  ?? 100,
                'mobile_opacity' => $request->mobile_opacity ?? 100,
                'created_by'     => $request->user()->id,
                'updated_by'     => $request->user()->id,
            ];

            // Subir imágenes a Cloudinary si se enviaron
            if ($request->hasFile('left_image')) {
                $data['left_image_url'] = $this->uploadCloudinaryImage($request->file('left_image'));
            }
            if ($request->hasFile('right_image')) {
                $data['right_image_url'] = $this->uploadCloudinaryImage($request->file('right_image'));
            }
            if ($request->hasFile('mobile_image')) {
                $data['mobile_image_url'] = $this->uploadCloudinaryImage($request->file('mobile_image'));
            }

            $popup = PopupConfig::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Pop-up creado exitosamente',
                'data'    => $popup->load('subservicio.servicio', 'createdBy:id,name', 'updatedBy:id,name')
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error creating popup: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al crear pop-up',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar configuración de pop-up
     * Acepta imágenes directamente vía multipart/form-data.
     * Si se envía una imagen nueva, reemplaza la anterior en Cloudinary.
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
                'title_text'     => 'nullable|string|min:5|max:80',
                'button_text'    => 'nullable|string|min:2|max:25',
                'service_color'  => 'nullable|regex:/^#[0-9A-Fa-f]{6}$/',
                'trigger_time'   => 'nullable|in:3,5,8',
                // Desktop
                'left_image'     => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
                'left_opacity'   => 'nullable|integer|min:0|max:100',
                'right_image'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
                'right_opacity'  => 'nullable|integer|min:0|max:100',
                // Mobile
                'mobile_image'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
                'mobile_opacity' => 'nullable|integer|min:0|max:100',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors'  => $validator->errors()
                ], 422);
            }

            // Actualizar campos de texto / opciones si vienen en el request
            $textFields = ['title_text', 'button_text', 'service_color', 'trigger_time',
                           'left_opacity', 'right_opacity', 'mobile_opacity'];
            foreach ($textFields as $field) {
                if ($request->has($field)) {
                    $popup->$field = $request->$field;
                }
            }

            // Reemplazar imágenes en Cloudinary si se enviaron nuevas
            if ($request->hasFile('left_image')) {
                if ($popup->left_image_url && str_contains($popup->left_image_url, 'cloudinary')) {
                    $this->deleteCloudinaryImage($popup->left_image_url);
                }
                $popup->left_image_url = $this->uploadCloudinaryImage($request->file('left_image'));
            }

            if ($request->hasFile('right_image')) {
                if ($popup->right_image_url && str_contains($popup->right_image_url, 'cloudinary')) {
                    $this->deleteCloudinaryImage($popup->right_image_url);
                }
                $popup->right_image_url = $this->uploadCloudinaryImage($request->file('right_image'));
            }

            if ($request->hasFile('mobile_image')) {
                if ($popup->mobile_image_url && str_contains($popup->mobile_image_url, 'cloudinary')) {
                    $this->deleteCloudinaryImage($popup->mobile_image_url);
                }
                $popup->mobile_image_url = $this->uploadCloudinaryImage($request->file('mobile_image'));
            }

            $popup->updated_by = $request->user()->id;
            $popup->save();

            return response()->json([
                'success' => true,
                'message' => 'Pop-up actualizado exitosamente',
                'data'    => $popup->load('subservicio.servicio', 'createdBy:id,name', 'updatedBy:id,name')
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
                'error'   => $e->getMessage()
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
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Subir imagen a Cloudinary en la carpeta popup_configs
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @return string URL segura de Cloudinary
     */
    private function uploadCloudinaryImage($file): string
    {
        $uploaded = Cloudinary::uploadApi()->upload(
            $file->getRealPath(),
            [
                'folder'        => 'popup_configs',
                'resource_type' => 'image',
            ]
        );

        return $uploaded['secure_url'];
    }

    /**
     * Eliminar imagen de Cloudinary extrayendo public_id de la URL
     * Patrón idéntico a PlantillasWhatsappController
     *
     * @param string $imageUrl
     * @return void
     */
    private function deleteCloudinaryImage($imageUrl)
    {
        try {
            // Ejemplo URL: https://res.cloudinary.com/cloud/image/upload/v123456/popup_configs/abc.jpg
            // public_id:   popup_configs/abc
            preg_match('/upload\/(?:v\d+\/)?(.+)\.\w+$/', $imageUrl, $matches);

            if (isset($matches[1])) {
                Cloudinary::destroy($matches[1]);
            }
        } catch (\Exception $e) {
            Log::warning("No se pudo eliminar imagen de Cloudinary: {$imageUrl}", [
                'error' => $e->getMessage()
            ]);
        }
    }
}
