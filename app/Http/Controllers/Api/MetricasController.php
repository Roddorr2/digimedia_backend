<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blog;
use App\Models\Card;
use App\Models\Empleado;
use App\Models\BlogAuditoria;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class MetricasController extends Controller
{
    /**
     * Helper para resolver mes y año con validación estricta
     */
    private function resolveMonthYear(Request $request)
    {
        // Limites razonables: mes 1..12, año entre 1970 y (año actual + 1)
        $currentYear = (int) Carbon::now()->year;
        $validated = $request->validate([
            'month' => ['nullable', 'integer', 'min:1', 'max:12'],
            'year'  => ['nullable', 'integer', 'min:2000', 'max:' . ($currentYear + 1)],
        ]);

        $month = $validated['month'] ?? Carbon::now()->month;
        $year  = $validated['year'] ?? $currentYear;

        return [$month, $year];
    }
    /* ============================================================
     * 1. METRICAS BLOGS
     * ============================================================
     */
    // 1.1 Cantidad de blogs creados por mes y año
    public function countBlogsByMonth(Request $request)
    {
        [$month, $year] = $this->resolveMonthYear($request);

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        $count = BlogAuditoria::where('accion', 'CREAR')
            ->whereBetween('fecha_hora', [$startDate, $endDate])
            ->count();

        return response()->json([
            "status" => 200,
            "data" => [
                "month" => $month,
                "year" => $year,
                "total_blogs" => $count
            ]
        ]);
    }

    // 1.2 Blogs creados últimos 12 meses
    public function listBlogsByMonths12()
    {
        $endDate   = Carbon::now()->endOfMonth();
        $startDate = Carbon::now()->subMonths(11)->startOfMonth();

        $raw = BlogAuditoria::where('accion', 'CREAR')
            ->whereBetween('fecha_hora', [$startDate, $endDate])
            ->get()
            ->groupBy(fn($i) => Carbon::parse($i->fecha_hora)->format('Y-m'))
            ->map(fn($group) => [
                'y' => (int) $group->first()->fecha_hora->format('Y'),
                'm' => (int) $group->first()->fecha_hora->format('m'),
                'total' => $group->count()
            ])
            ->keyBy(fn($i) => $i['y'] . '-' . str_pad($i['m'], 2, '0', STR_PAD_LEFT));

        $data = [];
        for ($i = 0; $i < 12; $i++) {
            $date = $startDate->copy()->addMonths($i);
            $key  = $date->format('Y-m');

            $data[] = [
                "month" => $date->format('F Y'),
                "total_blogs" => isset($raw[$key]) ? $raw[$key]['total'] : 0
            ];
        }

        return response()->json([
            "status" => 200,
            "data" => $data
        ]);
    }

    // 1.3 Top 5 meses con más blogs
    public function top5MothsWithMoreBlogs()
    {
        $data = BlogAuditoria::where('accion', 'CREAR')
            ->get()
            ->groupBy(fn($i) => Carbon::parse($i->fecha_hora)->format('Y-m'))
            ->map(fn($group) => [
                'y' => (int) $group->first()->fecha_hora->format('Y'),
                'm' => (int) $group->first()->fecha_hora->format('m'),
                'total' => $group->count()
            ])
            ->sortByDesc('total')
            ->take(5)
            ->map(fn($i) => [
                "month" => Carbon::create($i['y'], $i['m'])->format('F Y'),
                "total_blogs" => $i['total']
            ])
            ->values();

        return response()->json([
            "status" => 200,
            "data" => $data
        ]);
    }
    /* ============================================================
     * 2. METRICAS POR PLANTILLA
     * ============================================================
     */
    // 2.1 Listar cards por plantilla
    public function listOfCardsByPlantilla(Request $request)
    {
        [$month, $year] = $this->resolveMonthYear($request);

        $validated = $request->validate([
            'id_plantilla' => ['required', 'integer', 'min:1'],
            // Si existe tabla plantillas, habilitar esta regla:
            // Rule::exists('plantillas', 'id_plantilla')
        ]);

        $plantilla = $validated['id_plantilla'];
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        $cards = Card::join('blog_auditoria as ba', 'ba.id_blog', '=', 'cards.id_blog')
            ->where('cards.id_plantilla', $plantilla)
            ->where('ba.accion', 'CREAR')
            ->whereBetween('ba.fecha_hora', [$startDate, $endDate])
            ->select('cards.*')
            ->get();

        return response()->json([
            "status" => 200,
            "data" => $cards
        ]);
    }

    // 2.2 Cantidad de cards por plantilla
    public function countListOfCardsByPlantilla(Request $request)
    {
        [$month, $year] = $this->resolveMonthYear($request);

        $validated = $request->validate([
            'id_plantilla' => ['required', 'integer', 'min:1'],
            // Rule::exists('plantillas', 'id_plantilla')
        ]);

        $plantilla = $validated['id_plantilla'];
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        $count = Card::join('blog_auditoria as ba', 'ba.id_blog', '=', 'cards.id_blog')
            ->where('cards.id_plantilla', $plantilla)
            ->where('ba.accion', 'CREAR')
            ->whereBetween('ba.fecha_hora', [$startDate, $endDate])
            ->count();

        return response()->json([
            "status" => 200,
            "count" => $count
        ]);
    }

    // 2.3 Tabla cards por plantilla
    public function tableCardsByIdPlantilla(Request $request)
    {
        [$month, $year] = $this->resolveMonthYear($request);
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        $data = Card::query()
            ->selectRaw('cards.id_plantilla, COUNT(cards.id_card) as count_cards')
            ->join('blog_auditoria as ba', function ($join) use ($startDate, $endDate) {
                $join->on('ba.id_blog', '=', 'cards.id_blog')
                     ->where('ba.accion', '=', 'CREAR')
                     ->whereBetween('ba.fecha_hora', [$startDate, $endDate]);
            })
            ->groupBy('cards.id_plantilla')
            ->orderBy('cards.id_plantilla')
            ->get();

        return response()->json([
            "status" => 200,
            "data" => $data
        ]);
    }
    /* ============================================================
     * 3. METRICAS POR EMPLEADO
     * ============================================================
     */
    // 3.1 Cards por empleado
    public function listEmpleadoWithCards(Request $request)
    {
        [$month, $year] = $this->resolveMonthYear($request);

        $validated = $request->validate([
            'id_empleado' => [
                'required', 'integer', 'min:1',
                // Si el campo clave en tabla empleados es id_empleado:
                // Rule::exists('empleados', 'id_empleado')
            ],
        ]);

        $id = $validated['id_empleado'];
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        $cards = Card::join('blog_auditoria as ba', 'ba.id_blog', '=', 'cards.id_blog')
            ->where('cards.id_empleado', $id)
            ->where('ba.accion', 'CREAR')
            ->whereBetween('ba.fecha_hora', [$startDate, $endDate])
            ->select('cards.*')
            ->get();

        return response()->json([
            "status" => 200,
            "data" => $cards
        ]);
    }

    // 3.2 Cantidad cards por empleado
    public function countListOfCardsByEmpleado(Request $request)
    {
        [$month, $year] = $this->resolveMonthYear($request);

        $validated = $request->validate([
            'id_empleado' => [
                'required', 'integer', 'min:1',
                // Rule::exists('empleados', 'id_empleado')
            ],
        ]);

        $id = $validated['id_empleado'];
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        $count = Card::join('blog_auditoria as ba', 'ba.id_blog', '=', 'cards.id_blog')
            ->where('cards.id_empleado', $id)
            ->where('ba.accion', 'CREAR')
            ->whereBetween('ba.fecha_hora', [$startDate, $endDate])
            ->count();

        return response()->json([
            "status" => 200,
            "count" => $count
        ]);
    }

    // 3.3 Tabla cards por empleado
    public function tableCardsByEmpleado(Request $request)
    {
        [$month, $year] = $this->resolveMonthYear($request);
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        $data = Empleado::query()
            ->selectRaw('empleados.id_empleado, empleados.nombre as nombre_empleado, COUNT(DISTINCT CASE WHEN ba.id_blog IS NOT NULL THEN cards.id_card END) as count_cards')
            ->leftJoin('cards', 'cards.id_empleado', '=', 'empleados.id_empleado')
            ->leftJoin('blog_auditoria as ba', function ($join) use ($startDate, $endDate) {
                $join->on('ba.id_blog', '=', 'cards.id_blog')
                     ->where('ba.accion', '=', 'CREAR')
                     ->whereBetween('ba.fecha_hora', [$startDate, $endDate]);
            })
            ->where('empleados.id_rol', 1)
            ->groupBy('empleados.id_empleado', 'empleados.nombre')
            ->get();

        return response()->json([
            "status" => 200,
            "data" => $data
        ]);
    }
    /* ============================================================
     * 4. TIEMPO CREACIÓN → EDICIÓN
     * ============================================================
     */
    // 4.1 TIEMPO CREACIÓN → EDICIÓN
    public function tiempoCreacionEdicionPublicacionCard(Request $request)
    {
        [$month, $year] = $this->resolveMonthYear($request);
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        $data = BlogAuditoria::whereIn('accion', ['CREAR', 'ACTUALIZAR'])
            ->whereBetween('fecha_hora', [$startDate, $endDate])
            ->orderBy('id_blog')
            ->get()
            ->groupBy('id_blog')
            ->map(function ($items, $id_blog) {
                $crear = $items->firstWhere('accion', 'CREAR');
                $editar = $items->firstWhere('accion', 'ACTUALIZAR');

                if (!$crear || !$editar) return null;

                return [
                    "id_blog" => $id_blog,
                    "tiempo_minutos" =>
                        Carbon::parse($crear->fecha_hora)
                            ->diffInMinutes(Carbon::parse($editar->fecha_hora))
                ];
            })
            ->filter()
            ->values();

        return response()->json([
            "status" => 200,
            "data" => $data
        ]);
    }

    // 4.2 Frecuencia de publicación de cards todos los empleados
    // Devuelve la frecuencia de publicación de cards por empleado al mes.
    public function frecuenciaPublicacionCardsTodosEmpleados()
    {
        try {
            $now = now();

            $data = Empleado::where('id_rol', 1)
                ->withCount('cards')
                ->get()
                ->map(function ($empleado) use ($now) {

                    // Protección si created_at es null
                    $mesesTrabajados = $empleado->created_at
                        ? $empleado->created_at->diffInMonths($now) + 1
                        : 1;

                    return [
                        'id_empleado' => $empleado->id_empleado,
                        'nombre_empleado' => $empleado->nombre,
                        'frecuencia_publicacion_mensual' =>
                            round($empleado->cards_count / $mesesTrabajados, 2),
                    ];
                });

            return response()->json([
                "status" => 200,
                "data"   => $data,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
