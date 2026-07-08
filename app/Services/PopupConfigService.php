<?php

namespace App\Services;

use App\Repositories\PopupConfigRepository;
use App\DTOs\PopupConfig\CreatePopupConfigDTO;
use App\DTOs\PopupConfig\UpdatePopupConfigDTO;
use App\Models\PopupConfig;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PopupConfigService
{
    public function __construct(
        private PopupConfigRepository $repository
    ) {}

    public function getPopups(): Collection
    {
        return $this->repository->getAll();
    }

    public function getPopupById(int $id): PopupConfig
    {
        $popup = $this->repository->findById($id);
        
        if (!$popup) {
            throw new ModelNotFoundException('Pop-up no encontrado');
        }
        
        return $popup;
    }

    public function getPopupByOwner(string $type, int $id): PopupConfig
    {
        if ($type === 'servicio') {
            $ownerType = \App\Models\servicios::class;
            $owner = \App\Models\servicios::find($id);
        } elseif ($type === 'subservicio') {
            $ownerType = \App\Models\Subservicio::class;
            $owner = \App\Models\Subservicio::find($id);
        } else {
            throw new \InvalidArgumentException('Tipo inválido. Use servicio o subservicio', 400);
        }

        if (!$owner) {
            throw new ModelNotFoundException("{$type} no encontrado");
        }

        $popup = $this->repository->findByOwner($ownerType, $id);

        if (!$popup) {
            throw new ModelNotFoundException("Pop-up no encontrado para este {$type}");
        }

        return $popup;
    }

    public function getPopupBySubservicioSlug(string $slug): PopupConfig
    {
        $subservicio = \App\Models\Subservicio::where('slug', $slug)->first();

        if (!$subservicio) {
            throw new ModelNotFoundException("Subservicio '{$slug}' no encontrado");
        }

        return $this->getPopupByOwner('subservicio', $subservicio->id_subservicio);
    }

    public function createPopup(CreatePopupConfigDTO $dto, array $files = []): PopupConfig
    {
        $popupableType = $dto->id_servicio ? \App\Models\servicios::class : \App\Models\Subservicio::class;
        $popupableId = $dto->id_servicio ?: $dto->id_subservicio;
        
        if ($dto->id_servicio) {
            $owner = \App\Models\servicios::find($dto->id_servicio);
        } else {
            $owner = \App\Models\Subservicio::find($dto->id_subservicio);
        }

        if (!$owner) {
            throw new ModelNotFoundException('Owner no encontrado');
        }

        if ($this->repository->existsForOwner($popupableType, $popupableId)) {
            throw new \Exception('Ya existe un pop-up configurado para este elemento', 422);
        }

        $data = [
            'popupable_type' => $popupableType,
            'popupable_id' => $popupableId,
            'button_text'    => $dto->button_text,
            'button_color'   => $dto->button_color,
            'service_color'  => $dto->service_color,
            'service_color_2'=> $dto->service_color_2,
            'gradient_direction' => $dto->gradient_direction,
            'trigger_time'   => $dto->trigger_time,
            'trigger_type'   => $dto->trigger_type,
            'layout'         => $dto->layout,
            'show_logo'      => $dto->show_logo,
            'left_text'      => $dto->left_text,
            'left_opacity'   => $dto->left_opacity,
            'left_alt'       => $dto->left_alt,
            'right_opacity'  => $dto->right_opacity,
            'right_alt'      => $dto->right_alt,
            'mobile_opacity' => $dto->mobile_opacity,
            'mobile_alt'     => $dto->mobile_alt,
            'created_by'     => $dto->userId,
            'updated_by'     => $dto->userId,
        ];

        // Subir imagenes
        if (isset($files['left_image'])) {
            $data['left_image_url'] = $this->uploadCloudinaryImage($files['left_image']);
        }
        if (isset($files['right_image'])) {
            $data['right_image_url'] = $this->uploadCloudinaryImage($files['right_image']);
        }
        if (isset($files['mobile_image'])) {
            $data['mobile_image_url'] = $this->uploadCloudinaryImage($files['mobile_image']);
        }

        return $this->repository->create($data);
    }

    public function updatePopup(int $id, UpdatePopupConfigDTO $dto, array $files = []): PopupConfig
    {
        $popup = $this->getPopupById($id);

        $data = $dto->data;

        // Reasignar owner (servicio/subservicio) si vino un cambio en el request
        $newOwnerType = null;
        $newOwnerId = null;
        if ($dto->id_subservicio) {
            $newOwnerType = \App\Models\Subservicio::class;
            $newOwnerId = $dto->id_subservicio;
        } elseif ($dto->id_servicio) {
            $newOwnerType = \App\Models\servicios::class;
            $newOwnerId = $dto->id_servicio;
        }

        if ($newOwnerType !== null
            && ($newOwnerType !== $popup->popupable_type || $newOwnerId !== $popup->popupable_id)
        ) {
            $owner = $newOwnerType::find($newOwnerId);
            if (!$owner) {
                throw new ModelNotFoundException('Owner no encontrado');
            }

            if ($this->repository->existsForOwner($newOwnerType, $newOwnerId)) {
                throw new \Exception('Ya existe un pop-up configurado para este elemento', 422);
            }

            $data['popupable_type'] = $newOwnerType;
            $data['popupable_id'] = $newOwnerId;
        }

        // Manejar subida de imagenes
        if (isset($files['left_image'])) {
            if ($popup->left_image_url && str_contains($popup->left_image_url, 'cloudinary')) {
                $this->deleteCloudinaryImage($popup->left_image_url);
            }
            $data['left_image_url'] = $this->uploadCloudinaryImage($files['left_image']);
        }

        if (isset($files['right_image'])) {
            if ($popup->right_image_url && str_contains($popup->right_image_url, 'cloudinary')) {
                $this->deleteCloudinaryImage($popup->right_image_url);
            }
            $data['right_image_url'] = $this->uploadCloudinaryImage($files['right_image']);
        }

        if (isset($files['mobile_image'])) {
            if ($popup->mobile_image_url && str_contains($popup->mobile_image_url, 'cloudinary')) {
                $this->deleteCloudinaryImage($popup->mobile_image_url);
            }
            $data['mobile_image_url'] = $this->uploadCloudinaryImage($files['mobile_image']);
        }

        // Manejar eliminación de imagenes individuales (si no se subió una nueva)
        $imageFields = [
            'remove_left_image' => ['column' => 'left_image_url', 'file_key' => 'left_image'],
            'remove_right_image' => ['column' => 'right_image_url', 'file_key' => 'right_image'],
            'remove_mobile_image' => ['column' => 'mobile_image_url', 'file_key' => 'mobile_image'],
        ];

        foreach ($imageFields as $flag => $config) {
            if (isset($data[$flag]) && $data[$flag] == '1' && !isset($files[$config['file_key']])) {
                if ($popup->{$config['column']} && str_contains($popup->{$config['column']}, 'cloudinary')) {
                    $this->deleteCloudinaryImage($popup->{$config['column']});
                }
                $data[$config['column']] = null;
            }
            unset($data[$flag]);
        }
        
        $data['updated_by'] = $dto->userId;
        
        $this->repository->update($popup, $data);
        
        return $popup->fresh();
    }

    public function deletePopup(int $id): void
    {
        $popup = $this->getPopupById($id);
        
        if ($popup->left_image_url && str_contains($popup->left_image_url, 'cloudinary')) {
            $this->deleteCloudinaryImage($popup->left_image_url);
        }
        if ($popup->right_image_url && str_contains($popup->right_image_url, 'cloudinary')) {
            $this->deleteCloudinaryImage($popup->right_image_url);
        }
        if ($popup->mobile_image_url && str_contains($popup->mobile_image_url, 'cloudinary')) {
            $this->deleteCloudinaryImage($popup->mobile_image_url);
        }
        
        $this->repository->delete($popup);
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
