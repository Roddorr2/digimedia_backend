<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PopupConfigService;
use App\DTOs\PopupConfig\CreatePopupConfigDTO;
use App\DTOs\PopupConfig\UpdatePopupConfigDTO;
use App\Models\Subservicio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PopupConfigController extends Controller
{
    public function __construct(
        private PopupConfigService $popupConfigService
    ) {}

    // ENDPOINT PUBLICO UNIFICADO
    public function showByOwnerPublic(string $type, int $id): JsonResponse
    {
        try {
            $popup = $this->popupConfigService->getPopupByOwner($type, $id);

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
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener pop-up',
                'error' => config('app.debug') ? $e->getMessage() : null
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
            $popups = $this->popupConfigService->getPopups();

            return response()->json([
                'success' => true,
                'data' => $popups
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener pop-ups',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    // OBTENER POR ID
    public function show(int $id): JsonResponse
    {
        try {
            $popup = $this->popupConfigService->getPopupById($id);

            return response()->json([
                'success' => true,
                'data' => $popup
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Pop-up no encontrado'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener pop-up',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    // OBTENER POR SUBSERVICIO (dashboard)
    public function showBySubservicio(int $id_subservicio): JsonResponse
    {
        try {
            $popup = $this->popupConfigService->getPopupByOwner('subservicio', $id_subservicio);

            return response()->json([
                'success' => true,
                'data' => $popup
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => "Pop-up no encontrado para subservicio {$id_subservicio}"
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener pop-up',
                'error' => config('app.debug') ? $e->getMessage() : null
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

            $files = [];
            if ($request->hasFile('left_image')) {
                $files['left_image'] = $request->file('left_image');
            }
            if ($request->hasFile('right_image')) {
                $files['right_image'] = $request->file('right_image');
            }
            if ($request->hasFile('mobile_image')) {
                $files['mobile_image'] = $request->file('mobile_image');
            }

            $dto = CreatePopupConfigDTO::fromRequest($request);
            $popup = $this->popupConfigService->createPopup($dto, $files);

            return response()->json([
                'success' => true,
                'message' => 'Pop-up creado exitosamente',
                'data'    => $popup->load('popupable', 'createdBy:id,name', 'updatedBy:id,name')
            ], 201);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Owner no encontrado'
            ], 404);
        } catch (\Exception $e) {
            $status = $e->getCode() === 422 ? 422 : 500;
            return response()->json([
                'success' => false,
                'message' => 'Error al crear pop-up',
                'error'   => config('app.debug') ? $e->getMessage() : null
            ], $status);
        }
    }

    // ACTUALIZAR
    public function update(Request $request, int $id): JsonResponse
    {
        try {
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
                'remove_left_image'   => 'nullable|in:0,1',
                'remove_right_image'  => 'nullable|in:0,1',
                'remove_mobile_image' => 'nullable|in:0,1',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors'  => $validator->errors()
                ], 422);
            }

            $files = [];
            if ($request->hasFile('left_image')) {
                $files['left_image'] = $request->file('left_image');
            }
            if ($request->hasFile('right_image')) {
                $files['right_image'] = $request->file('right_image');
            }
            if ($request->hasFile('mobile_image')) {
                $files['mobile_image'] = $request->file('mobile_image');
            }

            $dto = UpdatePopupConfigDTO::fromRequest($request);
            $popup = $this->popupConfigService->updatePopup($id, $dto, $files);

            return response()->json([
                'success' => true,
                'message' => 'Pop-up actualizado exitosamente',
                'data'    => $popup->load('popupable', 'createdBy:id,name', 'updatedBy:id,name')
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Pop-up no encontrado'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar pop-up',
                'error'   => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    // ELIMINAR
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->popupConfigService->deletePopup($id);

            return response()->json([
                'success' => true,
                'message' => 'Pop-up eliminado exitosamente'
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Pop-up no encontrado'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar pop-up',
                'error'   => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}
