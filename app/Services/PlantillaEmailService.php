<?php

namespace App\Services;

use App\Repositories\PlantillaEmailRepository;
use App\DTOs\PlantillaEmail\UpdatePlantillaEmailDTO;
use App\Models\PlantillaEmail;
use App\Models\servicios;
use App\Models\Subservicio;
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

        $plantillas->each(fn($p) => $p->imagen_url = $this->normalizeImageUrl($p->imagen_url));

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

    public function getByOwner(string $type, int $id): Collection
    {
        [$ownerType] = $this->resolveOwnerType($type, $id);

        $plantillas = $this->repository->getByOwner($ownerType, $id);

        $plantillas->each(fn($p) => $p->imagen_url = $this->normalizeImageUrl($p->imagen_url));

        return $plantillas;
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

    public function inicializarParaSubservicio(int $idSubservicio, int $userId): Collection
    {
        $subservicio = Subservicio::find($idSubservicio);

        if (!$subservicio) {
            throw new ModelNotFoundException('Subservicio no encontrado');
        }

        $existing = $this->repository->getByOwner(Subservicio::class, $idSubservicio);
        if ($existing->isNotEmpty()) {
            throw new \Exception('Este subservicio ya tiene plantillas de email inicializadas', 422);
        }

        $plantillasServicio = $this->repository->getByOwner(servicios::class, $subservicio->id_servicio);

        $creadas = collect();
        foreach ([1, 2, 3] as $numero) {
            $base = $plantillasServicio->firstWhere('numero_plantilla', $numero);

            $data = [
                'plantillable_id'   => $idSubservicio,
                'plantillable_type' => Subservicio::class,
                'numero_plantilla'  => $numero,
                'nombre'            => $base?->nombre,
                'asunto'            => $base?->asunto ?? 'Información para ti',
                'encabezado'        => $base?->encabezado ?? 'Hola {nombre}',
                'imagen_url'        => $base?->imagen_url ?? '',
                'mensaje'           => $base?->mensaje ?? 'Hola {nombre}, te contactamos desde Digimedia.',
                'mensaje_boton'     => $base?->mensaje_boton,
                'url_boton'         => $base?->url_boton,
                'footer'            => $base?->footer,
                'red_facebook'      => $base?->red_facebook,
                'red_tiktok'        => $base?->red_tiktok,
                'red_instagram'     => $base?->red_instagram,
                'red_linkedin'      => $base?->red_linkedin,
                'created_by'        => $userId,
                'updated_by'        => $userId,
            ];

            $creadas->push($this->repository->createForOwner($data));
        }

        return $creadas;
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
            if ($plantilla->imagen_url && str_contains($plantilla->imagen_url, 'cloudinary')) {
                $this->deleteCloudinaryImage($plantilla->imagen_url);
            }

            try {
                $uploadedFile = Cloudinary::uploadApi()->upload(
                    $dto->imagen->getRealPath(),
                    [
                        'folder'        => 'plantillas_email',
                        'resource_type' => 'image',
                        'curl_options'  => [
                            CURLOPT_SSL_VERIFYPEER => false,
                            CURLOPT_SSL_VERIFYHOST => false,
                        ],
                    ]
                );
                $data['imagen_url'] = $uploadedFile['secure_url'];
            } catch (\Exception $e) {
                Log::error('Error al subir imagen de plantilla Email a Cloudinary', ['error' => $e->getMessage()]);
                throw new \Exception('Error al subir imagen a Cloudinary: ' . $e->getMessage(), 500);
            }
        }

        $this->repository->update($plantilla, $data);

        return $plantilla->fresh();
    }

    public function normalizeImageUrl(?string $imageUrl): ?string
    {
        if (empty($imageUrl)) return null;

        if (preg_match('/^https?:\/\//i', $imageUrl)) return $imageUrl;

        return url($imageUrl);
    }

    private function resolveOwnerType(string $type, int $id): array
    {
        if ($type === 'servicio') {
            $owner = servicios::find($id);
            $ownerType = servicios::class;
        } elseif ($type === 'subservicio') {
            $owner = Subservicio::find($id);
            $ownerType = Subservicio::class;
        } else {
            throw new \InvalidArgumentException('Tipo inválido. Use servicio o subservicio');
        }

        if (!$owner) {
            throw new ModelNotFoundException("{$type} no encontrado");
        }

        return [$ownerType, $owner];
    }

    private function deleteCloudinaryImage(string $imageUrl): void
    {
        try {
            preg_match('/upload\/(?:v\d+\/)?(.+)\.\w+$/', $imageUrl, $matches);

            if (isset($matches[1])) {
                Cloudinary::uploadApi()->destroy($matches[1]);
            }
        } catch (\Exception $e) {
            Log::warning("No se pudo eliminar imagen de Cloudinary: {$imageUrl}", ['error' => $e->getMessage()]);
        }
    }
}
