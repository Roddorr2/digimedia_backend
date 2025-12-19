<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blog;
use App\Models\Card;
use App\Models\Empleado;
use App\Models\BlogAuditoria;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
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

        $count = BlogAuditoria::where('accion', 'CREAR')
            ->whereYear('fecha_hora', $year)
            ->whereMonth('fecha_hora', $month)
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

        $raw = BlogAuditoria::selectRaw(
                "YEAR(fecha_hora) as y, MONTH(fecha_hora) as m, COUNT(*) as total"
            )
            ->where('accion', 'CREAR')
            ->whereBetween('fecha_hora', [$startDate, $endDate])
            ->groupBy('y', 'm')
            ->get()
            ->keyBy(fn($i) => $i->y . '-' . str_pad($i->m, 2, '0', STR_PAD_LEFT));

        $data = [];
        for ($i = 0; $i < 12; $i++) {
            $date = $startDate->copy()->addMonths($i);
            $key  = $date->format('Y-m');

            $data[] = [
                "month" => $date->format('F Y'),
                "total_blogs" => $raw[$key]->total ?? 0
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
        $data = BlogAuditoria::selectRaw(
                "YEAR(fecha_hora) as y, MONTH(fecha_hora) as m, COUNT(*) as total"
            )
            ->where('accion', 'CREAR')
            ->groupBy('y', 'm')
            ->orderByDesc('total')
            ->limit(5)
            ->get()
            ->map(fn($i) => [
                "month" => Carbon::create($i->y, $i->m)->format('F Y'),
                "total_blogs" => $i->total
            ]);

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

        $cards = Card::join('blog_auditoria as ba', 'ba.id_blog', '=', 'cards.id_blog')
            ->where('cards.id_plantilla', $plantilla)
            ->where('ba.accion', 'CREAR')
            ->whereYear('ba.fecha_hora', $year)
            ->whereMonth('ba.fecha_hora', $month)
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

        $count = Card::join('blog_auditoria as ba', 'ba.id_blog', '=', 'cards.id_blog')
            ->where('cards.id_plantilla', $plantilla)
            ->where('ba.accion', 'CREAR')
            ->whereYear('ba.fecha_hora', $year)
            ->whereMonth('ba.fecha_hora', $month)
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

        $data = [];
        for ($i = 1; $i <= 3; $i++) {
            $count = Card::join('blog_auditoria as ba', 'ba.id_blog', '=', 'cards.id_blog')
                ->where('cards.id_plantilla', $i)
                ->where('ba.accion', 'CREAR')
                ->whereYear('ba.fecha_hora', $year)
                ->whereMonth('ba.fecha_hora', $month)
                ->count();

            $data[] = [
                "id_plantilla" => $i,
                "count_cards" => $count
            ];
        }

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

        $cards = Card::join('blog_auditoria as ba', 'ba.id_blog', '=', 'cards.id_blog')
            ->where('cards.id_empleado', $id)
            ->where('ba.accion', 'CREAR')
            ->whereYear('ba.fecha_hora', $year)
            ->whereMonth('ba.fecha_hora', $month)
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

        $count = Card::join('blog_auditoria as ba', 'ba.id_blog', '=', 'cards.id_blog')
            ->where('cards.id_empleado', $id)
            ->where('ba.accion', 'CREAR')
            ->whereYear('ba.fecha_hora', $year)
            ->whereMonth('ba.fecha_hora', $month)
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

        $empleados = Empleado::where('id_rol', 1)->get();

        $data = [];
        foreach ($empleados as $empleado) {
            $count = Card::join('blog_auditoria as ba', 'ba.id_blog', '=', 'cards.id_blog')
                ->where('cards.id_empleado', $empleado->id_empleado)
                ->where('ba.accion', 'CREAR')
                ->whereYear('ba.fecha_hora', $year)
                ->whereMonth('ba.fecha_hora', $month)
                ->count();

            $data[] = [
                "id_empleado" => $empleado->id_empleado,
                "nombre_empleado" => $empleado->nombre,
                "count_cards" => $count
            ];
        }

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

        $data = BlogAuditoria::whereIn('accion', ['CREAR', 'ACTUALIZAR'])
            ->whereYear('fecha_hora', $year)
            ->whereMonth('fecha_hora', $month)
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

    //4.2 Frecuencia de publicacion de cards todos los empleados
    //Devuelve la frecuencia de publicación de cards por empleado al mes.
    public function frecuenciaPublicacionCardsTodosEmpleados() {
        try {
            $empleados = Empleado::where('id_rol', 1)->get();
            $frecuenciaCards = [];
            foreach ($empleados as $empleado) {
                $cardsCount = Card::where('id_empleado', $empleado->id_empleado)->count();
                $mesesTrabajados = Carbon::now()->diffInMonths(Carbon::parse($empleado->created_at)) + 1;
                $frecuenciaMensual = $mesesTrabajados > 0 ? $cardsCount / $mesesTrabajados : 0;
                $frecuenciaCards[] = [
                    'id_empleado' => $empleado->id_empleado,
                    'nombre_empleado' => $empleado->nombre,
                    'frecuencia_publicacion_mensual' => round($frecuenciaMensual, 2)
                ];
            }
            return response()->json([
                "status" => 200,
                'data' => $frecuenciaCards
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
    // //NUEVAS METRICAS 5.0
    // //Metodos con filtro - Mes Año

    //  5.1 Resumen mensual por acción (CREAR, ACTUALIZAR, ELIMINAR) en BlogAuditoria
    //      - Filtro: month (1-12), year (YYYY)
    //      - Retorna: totales por acción, total general y desglose por día del mes
    // public function resumenMensualAcciones(Request $request)
    // {
    //     try {
    //         $month = (int) $request->input('month'); // 1-12
    //         $year = (int) $request->input('year');

    //         if (!$month || !$year || $month < 1 || $month > 12) {
    //             return response()->json([
    //                 'message' => 'Mes o año inválido'
    //             ], 400);
    //         }

    //         $acciones = ['CREAR', 'ACTUALIZAR', 'ELIMINAR'];

    //         $inicioMes = Carbon::createFromDate($year, $month, 1)->startOfMonth();
    //         $finMes = $inicioMes->copy()->endOfMonth();

    //         $query = BlogAuditoria::whereBetween('fecha_hora', [$inicioMes, $finMes]);

    //         $totalesPorAccion = BlogAuditoria::select('accion', DB::raw('COUNT(*) as total'))
    //             ->whereBetween('fecha_hora', [$inicioMes, $finMes])
    //             ->groupBy('accion')
    //             ->pluck('total', 'accion')
    //             ->toArray();

    //         $totalGeneral = array_sum($totalesPorAccion);

    //         // Desglose por día y acción
    //         $diasEnMes = $finMes->day;
    //         $desglose = [];
    //         for ($dia = 1; $dia <= $diasEnMes; $dia++) {
    //             $fecha = Carbon::createFromDate($year, $month, $dia);
    //             $clave = $fecha->format('Y-m-d');
    //             $desglose[$clave] = [
    //                 'date' => $clave,
    //                 'CREAR' => 0,
    //                 'ACTUALIZAR' => 0,
    //                 'ELIMINAR' => 0,
    //                 'total' => 0,
    //             ];
    //         }

    //         $registros = $query->get(['accion', 'fecha_hora']);
    //         foreach ($registros as $r) {
    //             $clave = Carbon::parse($r->fecha_hora)->format('Y-m-d');
    //             if (isset($desglose[$clave])) {
    //                 $accion = $r->accion;
    //                 if (!isset($desglose[$clave][$accion])) {
    //                     $desglose[$clave][$accion] = 0;
    //                 }
    //                 $desglose[$clave][$accion] += 1;
    //                 $desglose[$clave]['total'] += 1;
    //             }
    //         }

    //         // Asegurar presencia de todas las acciones
    //         foreach ($acciones as $a) {
    //             if (!isset($totalesPorAccion[$a])) $totalesPorAccion[$a] = 0;
    //         }

    //         return response()->json([
    //             'status' => 200,
    //             'data' => [
    //                 'month' => $month,
    //                 'year' => $year,
    //                 'totales' => [
    //                     'CREAR' => $totalesPorAccion['CREAR'] ?? 0,
    //                     'ACTUALIZAR' => $totalesPorAccion['ACTUALIZAR'] ?? 0,
    //                     'ELIMINAR' => $totalesPorAccion['ELIMINAR'] ?? 0,
    //                     'total' => $totalGeneral,
    //                 ],
    //                 'desglose_diario' => array_values($desglose),
    //             ]
    //         ], 200);
    //     } catch (\Exception $e) {
    //         return response()->json(['error' => $e->getMessage()], 500);
    //     }
    // }

    // /*
    //  5.2 Resumen por empleado en el mes: cantidad de acciones por accion y total
    //      - Filtro: month, year, id_empleado (opcional: si se envía, solo ese empleado)
    // */
    // public function resumenMensualPorEmpleado(Request $request)
    // {
    //     try {
    //         $month = (int) $request->input('month');
    //         $year = (int) $request->input('year');
    //         $idEmpleado = $request->input('id_empleado'); // opcional

    //         if (!$month || !$year || $month < 1 || $month > 12) {
    //             return response()->json([
    //                 'message' => 'Mes o año inválido'
    //             ], 400);
    //         }

    //         $inicioMes = Carbon::createFromDate($year, $month, 1)->startOfMonth();
    //         $finMes = $inicioMes->copy()->endOfMonth();

    //         $query = BlogAuditoria::whereBetween('fecha_hora', [$inicioMes, $finMes]);
    //         if (!empty($idEmpleado)) {
    //             $query->where('id_empleado', $idEmpleado);
    //         }

    //         $registros = $query->get(['id_empleado', 'accion']);

    //         $porEmpleado = [];
    //         foreach ($registros as $r) {
    //             $id = $r->id_empleado ?? 0;
    //             if (!isset($porEmpleado[$id])) {
    //                 $nombre = optional(Empleado::find($id))->nombre ?? 'Desconocido';
    //                 $porEmpleado[$id] = [
    //                     'id_empleado' => $id,
    //                     'nombre_empleado' => $nombre,
    //                     'CREAR' => 0,
    //                     'ACTUALIZAR' => 0,
    //                     'ELIMINAR' => 0,
    //                     'total' => 0,
    //                 ];
    //             }
    //             $porEmpleado[$id][$r->accion] = ($porEmpleado[$id][$r->accion] ?? 0) + 1;
    //             $porEmpleado[$id]['total'] += 1;
    //         }

    //         return response()->json([
    //             'status' => 200,
    //             'data' => array_values($porEmpleado),
    //         ], 200);
    //     } catch (\Exception $e) {
    //         return response()->json(['error' => $e->getMessage()], 500);
    //     }
    // }

    // /*
    //  5.3 Resumen mensual por plantilla: total de cards por id_plantilla y estado_publicacion
    //      - Filtro: month, year
    // */
    // public function resumenMensualPorPlantilla(Request $request)
    // {
    //     try {
    //         $month = (int) $request->input('month');
    //         $year = (int) $request->input('year');
    //         if (!$month || !$year || $month < 1 || $month > 12) {
    //             return response()->json([
    //                 'message' => 'Mes o año inválido'
    //             ], 400);
    //         }

    //         $inicioMes = Carbon::createFromDate($year, $month, 1)->startOfMonth();
    //         $finMes = $inicioMes->copy()->endOfMonth();

    //         // Joins: cards -> blogs (fecha) opcional si se necesita fecha por blog
    //         $cards = Card::select('id_plantilla', 'estado_publicacion')
    //             ->whereHas('blog', function ($q) use ($inicioMes, $finMes) {
    //                 $q->whereBetween('fecha', [$inicioMes->format('Y-m-d'), $finMes->format('Y-m-d')]);
    //             })
    //             ->get();

    //         $resumen = [
    //             1 => ['id_plantilla' => 1, 'publicado' => 0, 'borrador' => 0, 'total' => 0],
    //             2 => ['id_plantilla' => 2, 'publicado' => 0, 'borrador' => 0, 'total' => 0],
    //             3 => ['id_plantilla' => 3, 'publicado' => 0, 'borrador' => 0, 'total' => 0],
    //         ];

    //         foreach ($cards as $card) {
    //             $p = (int) $card->id_plantilla;
    //             if (!isset($resumen[$p])) {
    //                 $resumen[$p] = ['id_plantilla' => $p, 'publicado' => 0, 'borrador' => 0, 'total' => 0];
    //             }
    //             $estado = strtolower((string) $card->estado_publicacion);
    //             if ($estado === 'publicado' || $estado === 'publicada') {
    //                 $resumen[$p]['publicado'] += 1;
    //             } else {
    //                 $resumen[$p]['borrador'] += 1;
    //             }
    //             $resumen[$p]['total'] += 1;
    //         }

    //         return response()->json([
    //             'status' => 200,
    //             'data' => array_values($resumen)
    //         ], 200);
    //     } catch (\Exception $e) {
    //         return response()->json(['error' => $e->getMessage()], 500);
    //     }
    // }

    // //Metodos sin filtro - Graficos

    // /*
    //  5.4 Serie de 12 meses por acción (para gráficos de líneas/apilados)
    //      - Sin filtros: últimos 12 meses, totales por acción por mes
    // */
    // public function serie12MesesPorAccion()
    // {
    //     try {
    //         $fin = Carbon::now()->endOfMonth();
    //         $inicio = $fin->copy()->subMonths(11)->startOfMonth();

    //         $acciones = ['CREAR', 'ACTUALIZAR', 'ELIMINAR'];
    //         $serie = [];
    //         for ($i = 0; $i < 12; $i++) {
    //             $m = $inicio->copy()->addMonths($i);
    //             $key = $m->format('Y-m');
    //             $serie[$key] = [
    //                 'label' => $m->format('M Y'),
    //                 'CREAR' => 0,
    //                 'ACTUALIZAR' => 0,
    //                 'ELIMINAR' => 0,
    //                 'total' => 0,
    //             ];
    //         }

    //         $regs = BlogAuditoria::whereBetween('fecha_hora', [$inicio, $fin])->get(['accion', 'fecha_hora']);
    //         foreach ($regs as $r) {
    //             $key = Carbon::parse($r->fecha_hora)->format('Y-m');
    //             if (isset($serie[$key])) {
    //                 $serie[$key][$r->accion] = ($serie[$key][$r->accion] ?? 0) + 1;
    //                 $serie[$key]['total'] += 1;
    //             }
    //         }

    //         return response()->json([
    //             'status' => 200,
    //             'data' => array_values($serie)
    //         ], 200);
    //     } catch (\Exception $e) {
    //         return response()->json(['error' => $e->getMessage()], 500);
    //     }
    // }

    // /*
    //  5.5 Top empleados por actividad en últimos 12 meses
    // */
    // public function topEmpleadosActividad12Meses(Request $request)
    // {
    //     try {
    //         $limit = (int) ($request->input('limit') ?? 5);
    //         $fin = Carbon::now();
    //         $inicio = $fin->copy()->subMonths(12);

    //         $regs = BlogAuditoria::select('id_empleado', DB::raw('COUNT(*) as total'))
    //             ->whereBetween('fecha_hora', [$inicio, $fin])
    //             ->groupBy('id_empleado')
    //             ->orderByDesc('total')
    //             ->limit($limit)
    //             ->get();

    //         $data = $regs->map(function ($r) {
    //             $empleado = Empleado::find($r->id_empleado);
    //             return [
    //                 'id_empleado' => $r->id_empleado,
    //                 'nombre_empleado' => $empleado->nombre ?? 'Desconocido',
    //                 'total_acciones' => (int) $r->total,
    //             ];
    //         });

    //         return response()->json([
    //             'status' => 200,
    //             'data' => $data,
    //         ], 200);
    //     } catch (\Exception $e) {
    //         return response()->json(['error' => $e->getMessage()], 500);
    //     }
    // }

    // /*
    //  5.6 Distribución por plantilla (total histórico) para gráfico de dona
    // */
    // public function distribucionPorPlantilla()
    // {
    //     try {
    //         $resumen = [
    //             1 => ['id_plantilla' => 1, 'total' => 0],
    //             2 => ['id_plantilla' => 2, 'total' => 0],
    //             3 => ['id_plantilla' => 3, 'total' => 0],
    //         ];

    //         $cards = Card::select('id_plantilla')->get();
    //         foreach ($cards as $c) {
    //             $p = (int) $c->id_plantilla;
    //             if (!isset($resumen[$p])) $resumen[$p] = ['id_plantilla' => $p, 'total' => 0];
    //             $resumen[$p]['total'] += 1;
    //         }

    //         return response()->json([
    //             'status' => 200,
    //             'data' => array_values($resumen),
    //         ], 200);
    //     } catch (\Exception $e) {
    //         return response()->json(['error' => $e->getMessage()], 500);
    //     }
    // }

    // /*
    //  5.7 Tiempo promedio de edición entre acciones CREAR y ACTUALIZAR por mes (últimos 12 meses)
    // */
    // public function promedioTiempoEdicion12Meses()
    // {
    //     try {
    //         $fin = Carbon::now()->endOfMonth();
    //         $inicio = $fin->copy()->subMonths(11)->startOfMonth();

    //         // Agrupar por blog y obtener primera CREAR y última ACTUALIZAR dentro de la ventana
    //         $auditorias = BlogAuditoria::whereBetween('fecha_hora', [$inicio, $fin])
    //             ->orderBy('id_blog')
    //             ->orderBy('fecha_hora')
    //             ->get(['id_blog', 'accion', 'fecha_hora']);

    //         // Para cada blog: guardar primera CREAR y primera ACTUALIZAR posterior
    //         $crearPorBlog = [];
    //         $pares = [];
    //         foreach ($auditorias as $a) {
    //             $keyBlog = $a->id_blog;
    //             if ($a->accion === 'CREAR') {
    //                 if (!isset($crearPorBlog[$keyBlog])) {
    //                     $crearPorBlog[$keyBlog] = Carbon::parse($a->fecha_hora);
    //                 }
    //             } elseif ($a->accion === 'ACTUALIZAR') {
    //                 if (isset($crearPorBlog[$keyBlog])) {
    //                     $pares[] = [
    //                         'crear' => $crearPorBlog[$keyBlog],
    //                         'actualizar' => Carbon::parse($a->fecha_hora),
    //                     ];
    //                     // No unset para permitir múltiples pares; si se quisiera solo primer par, comentar siguiente línea
    //                     unset($crearPorBlog[$keyBlog]);
    //                 }
    //             }
    //         }

    //         // Calcular por mes promedio de minutos
    //         $serie = [];
    //         for ($i = 0; $i < 12; $i++) {
    //             $m = $inicio->copy()->addMonths($i);
    //             $key = $m->format('Y-m');
    //             $serie[$key] = [
    //                 'label' => $m->format('M Y'),
    //                 'promedio_minutos' => 0,
    //                 'pares' => 0,
    //             ];
    //         }

    //         foreach ($pares as $p) {
    //             $mesKey = $p['crear']->format('Y-m');
    //             if (isset($serie[$mesKey])) {
    //                 $diff = $p['crear']->diffInMinutes($p['actualizar']);
    //                 // Acumular sumas temporales
    //                 if (!isset($serie[$mesKey]['_suma'])) {
    //                     $serie[$mesKey]['_suma'] = 0;
    //                 }
    //                 $serie[$mesKey]['_suma'] += $diff;
    //                 $serie[$mesKey]['pares'] += 1;
    //             }
    //         }

    //         foreach ($serie as $k => $v) {
    //             if (($v['pares'] ?? 0) > 0) {
    //                 $serie[$k]['promedio_minutos'] = round($v['_suma'] / $v['pares'], 2);
    //             }
    //             unset($serie[$k]['_suma']);
    //         }

    //         return response()->json([
    //             'status' => 200,
    //             'data' => array_values($serie),
    //         ], 200);
    //     } catch (\Exception $e) {
    //         return response()->json(['error' => $e->getMessage()], 500);
    //     }
    // }
