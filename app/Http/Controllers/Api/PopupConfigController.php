<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PopupConfig;
use App\Models\servicios;
use App\Models\Subservicio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\JsonResponse;

class PopupConfigController extends Controller
{
    // ENDPOINT PUBLICO UNIFICADO
    public function showByOwnerPublic(string $type, int $id): JsonResponse
    {
        try {
            if ($type === 'servicio') {
                $owner = servicios::with('popupConfig')->findOrFail($id);
            } elseif ($type === 'subservicio') {
                $owner = Subservicio::with('popupConfig')->findOrFail($id);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Tipo invalido. Use servicio o subservicio'
                ], 400);
            }

            $popup = $owner->popupConfig;

            if (!$popup) {
                return response()->json([
                    'success' => false,
                    'message' => "Pop-up no encontrado para este {$type}"
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id_popup_config' => $popup->id_popup_config,
                    'button_text' => $popup->button_text,
                    'button_color' => $popup->button_color,
                    'service_color' => $popup->service_color,
                    'service_color_2' => $popup->service_color_2,
                    'gradient_direction' => $popup->gradient_direction,
                    'trigger_time' => $popup->trigger_time,
                    'trigger_type' => $popup->trigger_type,
                    'layout' => $popup->layout,
                    'show_logo' => $popup->show_logo,
                    'left_text' => $popup->left_text,
                    'left_image_url' => $popup->left_image_url,
                    'left_opacity' => $popup->left_opacity,
                    'left_alt' => $popup->left_alt,
                    'right_image_url' => $popup->right_image_url,
                    'right_opacity' => $popup->right_opacity,
                    'right_alt' => $popup->right_alt,
                    'mobile_image_url' => $popup->mobile_image_url,
                    'mobile_opacity' => $popup->mobile_opacity,
                    'mobile_alt' => $popup->mobile_alt,
                    'id_servicio' => $popup->popupable_type === Subservicio::class 
                        ? $popup->popupable->id_servicio 
                        : $popup->popupable_id
                ]
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => "{$type} no encontrado"
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener pop-up',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Mantener por compatibilidad (opcional, puede redirigir)
    public function showBySubservicioPublic(int $id_subservicio): JsonResponse
    {
        return $this->showByOwnerPublic('subservicio', $id_subservicio);
    }

    public function showByServicioPublic(int $id_servicio): JsonResponse
    {
        return $this->showByOwnerPublic('servicio', $id_servicio);
    }

    // LISTAR TODOS
    public function index(): JsonResponse
    {
        try {
            $popups = PopupConfig::with(['popupable', 'createdBy:id,name', 'updatedBy:id,name'])->get();

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

    // OBTENER POR ID
    public function show(int $id): JsonResponse
    {
        try {
            $popup = PopupConfig::with(['popupable', 'createdBy:id,name', 'updatedBy:id,name'])->findOrFail($id);

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

    // OBTENER POR SUBSERVICIO (dashboard)
    public function showBySubservicio(int $id_subservicio): JsonResponse
    {
        try {
            $popup = PopupConfig::where('popupable_type', Subservicio::class)
                ->where('popupable_id', $id_subservicio)
                ->with(['popupable', 'createdBy:id,name', 'updatedBy:id,name'])
                ->firstOrFail();

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

    // CREAR
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'id_servicio'         => 'required_without:id_subservicio|integer|exists:servicios,id_servicio',
                'id_subservicio'      => 'required_without:id_servicio|integer|exists:subservicios,id_subservicio',
                'button_text'         => 'required|string|min:2|max:25',
                'button_color'        => 'nullable|regex:/^#[0-9A-Fa-f]{6}$/',
                'service_color'       => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
                'service_color_2'     => 'nullable|regex:/^#[0-9A-Fa-f]{6}$/',
                'gradient_direction'  => 'nullable|string|in:to bottom,to top,to right,to left,to bottom right,to bottom left',
                'trigger_time'        => 'required|in:3,5,8',
                'trigger_type'        => 'nullable|in:time,click',
                'layout'              => 'nullable|in:left-image,right-image',
                'show_logo'           => 'nullable|boolean',
                'left_text'           => 'nullable|string|max:255',
                'left_image'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
                'left_opacity'        => 'nullable|integer|min:0|max:100',
                'left_alt'            => 'nullable|string|max:255',
                'right_image'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
                'right_opacity'       => 'nullable|integer|min:0|max:100',
                'right_alt'           => 'nullable|string|max:255',
                'mobile_image'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
                'mobile_opacity'      => 'nullable|integer|min:0|max:100',
                'mobile_alt'          => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors'  => $validator->errors()
                ], 422);
            }

            // Determinar el owner
            if ($request->has('id_servicio')) {
                $owner = servicios::findOrFail($request->id_servicio);
                $popupableType = servicios::class;
                $popupableId = $request->id_servicio;
            } else {
                $owner = Subservicio::findOrFail($request->id_subservicio);
                $popupableType = Subservicio::class;
                $popupableId = $request->id_subservicio;
            }

            // Verificar si ya existe un popup para este owner
            $existing = PopupConfig::where('popupable_type', $popupableType)
                ->where('popupable_id', $popupableId)
                ->first();

            if ($existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ya existe un pop-up configurado para este elemento'
                ], 422);
            }

            $data = [
                'popupable_type' => $popupableType,
                'popupable_id' => $popupableId,
                'button_text'    => $request->button_text,
                'button_color'   => $request->button_color ?? '#7C3FD9',
                'service_color'  => $request->service_color,
                'service_color_2'=> $request->service_color_2 ?? null,
                'gradient_direction' => $request->gradient_direction ?? 'to bottom',
                'trigger_time'   => $request->trigger_time,
                'trigger_type'   => $request->trigger_type ?? 'time',
                'layout'         => $request->layout ?? 'left-image',
                'show_logo'      => filter_var($request->show_logo, FILTER_VALIDATE_BOOLEAN) ?? true,
                'left_text'      => $request->left_text ?? '',
                'left_opacity'   => $request->left_opacity ?? 100,
                'left_alt'       => $request->left_alt ?? '',
                'right_opacity'  => $request->right_opacity ?? 100,
                'right_alt'      => $request->right_alt ?? '',
                'mobile_opacity' => $request->mobile_opacity ?? 100,
                'mobile_alt'     => $request->mobile_alt ?? '',
                'created_by'     => $request->user()->id,
                'updated_by'     => $request->user()->id,
            ];

            // Subir solo las imágenes que vienen en el request
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
                'data'    => $popup->load('popupable', 'createdBy:id,name', 'updatedBy:id,name')
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

    // ACTUALIZAR
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $popup = PopupConfig::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'button_text'         => 'nullable|string|min:2|max:25',
                'button_color'        => 'nullable|regex:/^#[0-9A-Fa-f]{6}$/',
                'service_color'       => 'nullable|regex:/^#[0-9A-Fa-f]{6}$/',
                'service_color_2'     => 'nullable|regex:/^#[0-9A-Fa-f]{6}$/',
                'gradient_direction'  => 'nullable|string|in:to bottom,to top,to right,to left,to bottom right,to bottom left',
                'trigger_time'        => 'nullable|in:3,5,8',
                'trigger_type'        => 'nullable|in:time,click',
                'layout'              => 'nullable|in:left-image,right-image',
                'show_logo'           => 'nullable|boolean',
                'left_text'           => 'nullable|string|max:255',
                'left_image'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
                'left_opacity'        => 'nullable|integer|min:0|max:100',
                'left_alt'            => 'nullable|string|max:255',
                'right_image'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
                'right_opacity'       => 'nullable|integer|min:0|max:100',
                'right_alt'           => 'nullable|string|max:255',
                'mobile_image'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
                'mobile_opacity'      => 'nullable|integer|min:0|max:100',
                'mobile_alt'          => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors'  => $validator->errors()
                ], 422);
            }

            $textFields = [
                'button_text', 'button_color', 'service_color', 'service_color_2', 
                'gradient_direction', 'trigger_time', 'trigger_type', 'layout', 
                'left_text', 'left_opacity', 'right_opacity', 'mobile_opacity',
                'left_alt', 'right_alt', 'mobile_alt'
            ];
            
            foreach ($textFields as $field) {
                if ($request->has($field)) {
                    $popup->$field = $request->$field;
                }
            }

            if ($request->has('show_logo')) {
                $popup->show_logo = filter_var($request->show_logo, FILTER_VALIDATE_BOOLEAN);
            }

            // Subir nueva imagen primero; borrar la antigua después de enviar la respuesta
            $urlsToDelete = [];

            if ($request->hasFile('left_image')) {
                if ($popup->left_image_url && str_contains($popup->left_image_url, 'cloudinary')) {
                    $urlsToDelete[] = $popup->left_image_url;
                }
                $popup->left_image_url = $this->uploadCloudinaryImage($request->file('left_image'));
            }
            if ($request->hasFile('right_image')) {
                if ($popup->right_image_url && str_contains($popup->right_image_url, 'cloudinary')) {
                    $urlsToDelete[] = $popup->right_image_url;
                }
                $popup->right_image_url = $this->uploadCloudinaryImage($request->file('right_image'));
            }
            if ($request->hasFile('mobile_image')) {
                if ($popup->mobile_image_url && str_contains($popup->mobile_image_url, 'cloudinary')) {
                    $urlsToDelete[] = $popup->mobile_image_url;
                }
                $popup->mobile_image_url = $this->uploadCloudinaryImage($request->file('mobile_image'));
            }

            // Las deletes se ejecutan después de que la respuesta ya fue enviada al cliente
            if (!empty($urlsToDelete)) {
                app()->terminating(function () use ($urlsToDelete) {
                    foreach ($urlsToDelete as $url) {
                        $this->deleteCloudinaryImage($url);
                    }
                });
            }

            // Eliminar imágenes individuales sin reemplazar
            if ($request->input('remove_left_image') === '1' && !$request->hasFile('left_image')) {
                if ($popup->left_image_url && str_contains($popup->left_image_url, 'cloudinary')) {
                    $this->deleteCloudinaryImage($popup->left_image_url);
                }
                $popup->left_image_url = null;
            }

            if ($request->input('remove_right_image') === '1' && !$request->hasFile('right_image')) {
                if ($popup->right_image_url && str_contains($popup->right_image_url, 'cloudinary')) {
                    $this->deleteCloudinaryImage($popup->right_image_url);
                }
                $popup->right_image_url = null;
            }

            if ($request->input('remove_mobile_image') === '1' && !$request->hasFile('mobile_image')) {
                if ($popup->mobile_image_url && str_contains($popup->mobile_image_url, 'cloudinary')) {
                    $this->deleteCloudinaryImage($popup->mobile_image_url);
                }
                $popup->mobile_image_url = null;
            }

            $popup->updated_by = $request->user()->id;
            $popup->save();

            return response()->json([
                'success' => true,
                'message' => 'Pop-up actualizado exitosamente',
                'data'    => $popup->load('popupable', 'createdBy:id,name', 'updatedBy:id,name')
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

    // ELIMINAR
    public function destroy(int $id): JsonResponse
    {
        try {
            $popup = PopupConfig::findOrFail($id);

            $urlsToDelete = array_filter([
                $popup->left_image_url,
                $popup->right_image_url,
                $popup->mobile_image_url,
            ], fn($url) => $url && str_contains($url, 'cloudinary'));

            $popup->delete();

            // Borrar imágenes de Cloudinary después de enviar la respuesta
            if (!empty($urlsToDelete)) {
                app()->terminating(function () use ($urlsToDelete) {
                    foreach ($urlsToDelete as $url) {
                        $this->deleteCloudinaryImage($url);
                    }
                });
            }

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

    private function uploadCloudinaryImage($file): string
    {
        $uploaded = Cloudinary::uploadApi()->upload(
            $file->getRealPath(),
            [
                'folder'        => 'popup_configs',
                'resource_type' => 'image',
                'curl_options'  => [
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_SSL_VERIFYHOST => false,
                ]
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
            preg_match('/upload\/(?:v\d+\/)?(.+)\.\w+$/', $imageUrl, $matches);

            if (isset($matches[1])) {
                Cloudinary::uploadApi()->destroy($matches[1]);
            }
        } catch (\Exception $e) {
            Log::warning("No se pudo eliminar imagen de Cloudinary: {$imageUrl}", [
                'error' => $e->getMessage()
            ]);
        }
    }
}