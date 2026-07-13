<?php
namespace App\Services;

use App\Repositories\TestimonioRepository;
use App\DTOs\Testimonio\CreateTestimonioDTO;
use App\DTOs\Testimonio\UpdateTestimonioDTO;
use App\DTOs\Testimonio\UpdateTestimonioImageDTO;
use App\Models\Testimonio;
use Cloudinary\Cloudinary;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TestimonioService
{
    public function __construct(
        private TestimonioRepository $repository
    ) {}

    public function getAllPublic()
    {
        return $this->repository->getAllPublic();
    }

    public function getPaginated(array $params): array
    {
        $paginated = $this->repository->getAllPaginated(
            $params['search'] ?? '',
            $params['sortBy'] ?? 'created_at',
            $params['sortOrder'] ?? 'desc',
            $params['limit'] ?? 10,
        );

        return [
            'data' => $paginated->items(),
            'total' => $paginated->total(),
            'page' => $paginated->currentPage(),
        ];
    }

    public function getById(int $id): Testimonio
    {
        $testimonio = $this->repository->findById($id);
        if (!$testimonio) {
            throw new ModelNotFoundException('Testimonio no encontrado');
        }
        return $testimonio;
    }

    public function create(CreateTestimonioDTO $dto): Testimonio
    {
        return $this->repository->create($dto->toArray());
    }

    public function update(int $id, UpdateTestimonioDTO $dto): Testimonio
    {
        $testimonio = $this->repository->findById($id);
        if (!$testimonio) {
            throw new ModelNotFoundException('Testimonio no encontrado');
        }

        $this->repository->update($testimonio, $dto->data);

        return $testimonio->fresh();
    }

    public function delete(int $id): void
    {
        $testimonio = $this->repository->findById($id);
        if (!$testimonio) {
            throw new ModelNotFoundException('Testimonio no encontrado');
        }

        // borra la imagen en Cloudinary antes de borrar el registro
        if ($testimonio->imagen_public_id) {
            $this->destroyCloudinaryImage($testimonio->imagen_public_id);
        }

        $this->repository->delete($testimonio);
    }

    public function generateUploadSignature(int $id, array $params): string
    {
        $testimonio = $this->repository->findById($id);
        if (!$testimonio) {
            throw new ModelNotFoundException('Testimonio no encontrado');
        }

        $paramsToSign = $params;

        // el backend impone los parámetros críticos, igual que en Empleados
        $paramsToSign['folder'] = "testimonios/{$id}";
        $paramsToSign['public_id'] = 'foto';
        $paramsToSign['overwrite'] = 'true';

        $excludedKeys = ['file', 'cloud_name', 'resource_type', 'api_key'];
        $filteredParams = array_filter($paramsToSign, fn($key) => !in_array($key, $excludedKeys), ARRAY_FILTER_USE_KEY);

        foreach ($filteredParams as $key => $value) {
            if (is_bool($value)) {
                $filteredParams[$key] = $value ? 'true' : 'false';
            }
        }

        ksort($filteredParams);

        $signatureString = implode('&', array_map(
            fn($k, $v) => "{$k}={$v}",
            array_keys($filteredParams),
            $filteredParams
        ));
        $signatureString .= env('CLOUDINARY_API_SECRET');

        return hash('sha256', $signatureString);
    }

    public function updateImage(int $id, UpdateTestimonioImageDTO $dto): array
    {
        $testimonio = $this->repository->findById($id);
        if (!$testimonio) {
            throw new ModelNotFoundException('Testimonio no encontrado');
        }

        $expectedPublicId = "testimonios/{$id}/foto";
        if ($dto->public_id !== $expectedPublicId) {
            Log::warning('public_id no coincide con la carpeta esperada del testimonio', [
                'expected' => $expectedPublicId,
                'received' => $dto->public_id,
            ]);
            throw new \Exception('Imagen no autorizada para este testimonio', 403);
        }

        DB::beginTransaction();
        try {
            // si ya había una imagen previa, la borra de Cloudinary
            if ($testimonio->imagen_public_id && $testimonio->imagen_public_id !== $dto->public_id) {
                $this->destroyCloudinaryImage($testimonio->imagen_public_id);
            }

            $testimonio->imagen_public_id = $dto->public_id;
            $testimonio->imagen_url = $dto->secure_url;
            $testimonio->save();

            DB::commit();

            return [
                'public_id' => $testimonio->imagen_public_id,
                'url' => $testimonio->imagen_url,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function deleteImage(int $id): void
    {
        $testimonio = $this->repository->findById($id);
        if (!$testimonio) {
            throw new ModelNotFoundException('Testimonio no encontrado');
        }

        if ($testimonio->imagen_public_id) {
            $this->destroyCloudinaryImage($testimonio->imagen_public_id);
            $testimonio->imagen_public_id = null;
            $testimonio->imagen_url = null;
            $testimonio->save();
        }
    }

    private function destroyCloudinaryImage(string $publicId): void
    {
        try {
            $cloudinary = new Cloudinary([
                'cloud' => [
                    'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                    'api_key'    => env('CLOUDINARY_API_KEY'),
                    'api_secret' => env('CLOUDINARY_SECRET'),
                ]
            ]);
            $result = $cloudinary->uploadApi()->destroy($publicId);
            Log::info('Resultado de eliminación en Cloudinary:', ['result' => $result]);
        } catch (\Exception $e) {
            Log::warning("Error al eliminar imagen en Cloudinary: " . $e->getMessage());
        }
    }
    //activo o inactivo
    public function toggleActivo(int $id): Testimonio
    {
    $testimonio = $this->repository->findById($id);
    if (!$testimonio) {
        throw new ModelNotFoundException('Testimonio no encontrado');
    }

    $testimonio->activo = !$testimonio->activo;
    $testimonio->save();

    return $testimonio;
}
}