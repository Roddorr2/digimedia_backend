<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CampaniaService;
use App\Services\CampaniaLeadService;
use App\Http\Requests\Campania\CampaniaFiltersRequest;
use App\Http\Requests\Campania\LeadsFiltersRequest;
use App\Http\Resources\CampaniaWhatsAppResource;
use App\Http\Resources\CampaniaWhatsAppDetailsResource;
use App\Http\Resources\CampaniaLeadResource;
use App\DTOs\Campania\CampaniaFiltersDTO;
use App\DTOs\Campania\LeadsFiltersDTO;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CampaniasController extends Controller
{
    public function __construct(
        private CampaniaService $campaniaService,
        private CampaniaLeadService $campaniaLeadService
    ) {}

    public function index(CampaniaFiltersRequest $request): JsonResponse
    {
        try {
            $filters = CampaniaFiltersDTO::fromRequest($request);
            $result = $this->campaniaService->getCampaniasList($filters);

            $campanias = $result['campanias'];
            $campanias->getCollection()->transform(function ($campania) {
                return (new CampaniaWhatsAppResource($campania))->toArray(request());
            });

            return response()->json([
                'success' => true,
                'data' => $campanias,
                'active_campaign' => $result['active_campaign']
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener campañas',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $campania = $this->campaniaService->getCampaniaDetails($id);

            return response()->json([
                'success' => true,
                'data' => new CampaniaWhatsAppDetailsResource($campania)
            ]);
        } catch (\Exception $e) {
            $status = $e->getCode() === 404 ? 404 : 500;
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error' => config('app.debug') ? $e->getMessage() : null
            ], $status);
        }
    }

    public function leads(LeadsFiltersRequest $request, int $id): JsonResponse
    {
        try {
            $filters = LeadsFiltersDTO::fromRequest($request);
            $result = $this->campaniaLeadService->getLeadsList($id, [
                'estado' => $filters->estado,
                'search' => $filters->search,
                'perPage' => $filters->perPage
            ]);

            $leads = $result['leads'];
            $data = $leads->getCollection()->map(function ($lead) {
                return (new CampaniaLeadResource($lead))->toArray(request());
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'data' => $data,
                    'current_page' => $leads->currentPage(),
                    'last_page' => $leads->lastPage(),
                    'total' => $leads->total(),
                ]
            ]);
        } catch (\Exception $e) {
            $status = $e->getCode() === 404 ? 404 : 500;
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error' => config('app.debug') ? $e->getMessage() : null
            ], $status);
        }
    }

    public function retryLead(Request $request, int $id, int $wat_modal_id): JsonResponse
    {
        try {
            $result = $this->campaniaLeadService->retryLead($id, $wat_modal_id);
            
            return response()->json($result);
        } catch (\Exception $e) {
            $status = match ($e->getCode()) {
                400, 404 => $e->getCode(),
                default => 500
            };
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], $status);
        }
    }
}