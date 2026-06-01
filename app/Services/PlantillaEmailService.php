<?php

namespace App\Services;

use App\Repositories\PlantillaEmailRepository;
use App\DTOs\PlantillaEmail\UpdatePlantillaEmailDTO;
use App\Models\PlantillaEmail;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class PlantillaEmailService
{
    public function __construct(
        private PlantillaEmailRepository $repository
    ) {}

    public function getPlantillas(): Collection
    {
        $plantillas = $this->repository->getAll();

        $plantillas->each(function ($plantilla) {
            $plantilla->imagen_url = $this->normalizeImageUrl($plantilla->imagen_url);
        });

        return $plantillas;
    }

    public function getPlantillaById(int $id): PlantillaEmail
    {
        $plantilla = $this->repository->findById($id);

        if (!$plantilla) {
            throw new ModelNotFoundException('Plantilla no encontrada');
        }

        $plantilla->imagen_url = $this->normalizeImageUrl($plantilla->imagen_url);

        return $plantilla;
    }

    public function getPlantillaByServicioNumero(int $idServicio, int $numeroPlantilla): PlantillaEmail
    {
        $plantilla = $this->repository->findByServicioNumero($idServicio, $numeroPlantilla);

        if (!$plantilla) {
            throw new ModelNotFoundException("Plantilla no encontrada para servicio {$idServicio} y número {$numeroPlantilla}");
        }

        $plantilla->imagen_url = $this->normalizeImageUrl($plantilla->imagen_url);

        return $plantilla;
    }

    public function updatePlantilla(int $id, UpdatePlantillaEmailDTO $dto, int $userId): PlantillaEmail
    {
        $plantilla = $this->repository->findById($id);

        if (!$plantilla) {
            throw new ModelNotFoundException('Plantilla no encontrada');
        }

        $data = $dto->toArray();
        $data['updated_by'] = $userId;

        if ($dto->imagen) {
            // Eliminar imagen anterior si existe en Cloudinary
            if ($plantilla->imagen_url && str_contains($plantilla->imagen_url, 'cloudinary')) {
                $this->deleteCloudinaryImage($plantilla->imagen_url);
            }

            // Subir nueva imagen
            try {
                $uploadedFile = Cloudinary::uploadApi()->upload(
                    $dto->imagen->getRealPath(),
                    [
                        'folder' => 'plantillas_email',
                        'resource_type' => 'image',
                        'curl_options'  => [
                            CURLOPT_SSL_VERIFYPEER => false,
                            CURLOPT_SSL_VERIFYHOST => false,
                        ]
                    ]
                );
                $data['imagen_url'] = $uploadedFile['secure_url'];
            } catch (\Exception $e) {
                Log::error('Error al subir imagen de plantilla Email a Cloudinary', [
                    'error' => $e->getMessage()
                ]);
                throw new \Exception('Error al subir imagen a Cloudinary: ' . $e->getMessage(), 500);
            }
        }

        $this->repository->update($plantilla, $data);

        return $plantilla->fresh();
    }

    public function normalizeImageUrl(?string $imageUrl): ?string
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

    private function deleteCloudinaryImage(string $imageUrl): void
    {
        try {
            preg_match('/upload\/(?:v\d+\/)?(.+)\.\w+$/', $imageUrl, $matches);
            
            if (isset($matches[1])) {
                $publicId = $matches[1];
                Cloudinary::destroy($publicId);
            }
        } catch (\Exception $e) {
            Log::warning("No se pudo eliminar imagen de Cloudinary: {$imageUrl}", ['error' => $e->getMessage()]);
        }
    }
}
