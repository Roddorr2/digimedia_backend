<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ConfiguracionTiempo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConfiguracionTiempoController extends Controller
{
    /*
    * GET /api/servicios/(id_servicio)/tiempos
    * Obtiene la configuración de tiempo para un servicio
    */
    public function get(int $id_servicio) {
        $configuraciones = ConfiguracionTiempo::where('id_servicio', $id_servicio)
            ->orderBy('tipo')
            ->orderBy('numero_mensaje')
            ->get();

        $email = $configuraciones->where('tipo', 'email')->values();
        $whatsapp = $configuraciones->where('tipo', 'whatsapp')->values();

        return response()->json([
            'status' => 200,
            'data' => [
                'email' => $email,
                'whatsapp' => $whatsapp
            ]
        ], 200);
    }

    /**
     * PUT /api/servicios/{id_servicio}/tiempos
     * Reemplaza toda la configuración de tiempos de un servicio
     */
    public function update(Request $request, int $id_servicio)
    {
        $validated = $request->validate([
            'email' => 'required|array',
            'email.*.numero_mensaje' => 'required|integer|min:1',
            'email.*.unidad_tiempo' => 'required|in:minutos,horas,dias',
            'email.*.valor_tiempo' => 'required|integer|min:0',
            'whatsapp' => 'required|array',
            'whatsapp.*.numero_mensaje' => 'required|integer|min:1',
            'whatsapp.*.unidad_tiempo' => 'required|in:minutos,horas,dias',
            'whatsapp.*.valor_tiempo' => 'required|integer|min:0',
        ]);

        try {
        DB::beginTransaction();

        $emailNumeros = collect($validated['email'])->pluck('numero_mensaje')->toArray();
        $whatsappNumeros = collect($validated['whatsapp'])->pluck('numero_mensaje')->toArray();

        ConfiguracionTiempo::where('id_servicio', $id_servicio)
            ->where('tipo', 'email')
            ->whereNotIn('numero_mensaje', $emailNumeros)
            ->delete();
        
        ConfiguracionTiempo::where('id_servicio', $id_servicio)
            ->where('tipo', 'whatsapp')
            ->whereNotIn('numero_mensaje', $whatsappNumeros)
            ->delete();

        foreach ($validated['email'] as $config) {
            ConfiguracionTiempo::updateOrCreate(
                [
                    'id_servicio' => $id_servicio,
                    'tipo' => 'email',
                    'numero_mensaje' => $config['numero_mensaje'],
                ],
                [
                    'unidad_tiempo' => $config['unidad_tiempo'],
                    'valor_tiempo' => $config['valor_tiempo'],
                ]
            );
        }

        foreach ($validated['whatsapp'] as $config) {
            ConfiguracionTiempo::updateOrCreate(
                [
                    'id_servicio' => $id_servicio,
                    'tipo' => 'whatsapp',
                    'numero_mensaje' => $config['numero_mensaje'],
                ],
                [
                    'unidad_tiempo' => $config['unidad_tiempo'],
                    'valor_tiempo' => $config['valor_tiempo'],
                ]
            );
        }

        DB::commit();

        return response()->json([
            'status' => 200,
            'message' => 'Configuración actualizada correctamente'
        ], 200);

    } catch (\Exception $e) {
        DB::rollback();
        return response()->json([
            'status' => 500,
            'error' => 'Error al actualizar la configuración',
            'details' => $e->getMessage()
        ], 500);
    }
    }

    public function store(Request $request, int $id_servicio)
    {
        $validated = $request->validate([
            'tipo' => 'required|in:email,whatsapp',
            'numero_mensaje' => 'required|integer|min:1',
            'unidad_tiempo' => 'required|in:minutos,horas,dias',
            'valor_tiempo' => 'required|integer|min:0',
        ]);

        $config = ConfiguracionTiempo::updateOrCreate(
            [
                'id_servicio' => $id_servicio,
                'tipo' => $validated['tipo'],
                'numero_mensaje' => $validated['numero_mensaje'],
            ],
            [
                'unidad_tiempo' => $validated['unidad_tiempo'],
                'valor_tiempo' => $validated['valor_tiempo'],
            ]
        );

        return response()->json(['status' => 200, 'data' => $config], 200);
    }

    public function destroy(Request $request, int $id_servicio, string $tipo, int $numero_mensaje)
    {
        $deleted = ConfiguracionTiempo::where('id_servicio', $id_servicio)
            ->where('tipo', $tipo)
            ->where('numero_mensaje', $numero_mensaje)
            ->delete();

        if ($deleted) {
            return response()->json(['status' => 200, 'message' => 'Eliminado'], 200);
        }

        return response()->json(['status' => 404, 'message' => 'No encontrado'], 404);
    }
}
