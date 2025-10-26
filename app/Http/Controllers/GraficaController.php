<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class GraficaController extends Controller
{
    public function index()
    {
        // Obtener datos separados de gastos mensuales para hotel y bodega
        $gastosMensualesHotel = $this->getGastosMensualesHotel();
        $gastosMensualesBodega = $this->getGastosMensualesBodega();
        
        // Obtener datos de ingresos y gastos por mes para gráficos
        $datosGraficos = $this->getDatosGraficos();

        return view('graficas.index', compact(
            'gastosMensualesHotel',
            'gastosMensualesBodega',
            'datosGraficos'
        ));
    }

    /**
     * Función auxiliar para determinar el período basado en una fecha
     * Retorna array con mes_inicio, año_inicio, mes_fin, año_fin, nombre_periodo
     */
    private function getPeriodoFromDate($fecha)
    {
        $carbon = Carbon::parse($fecha);

        $mesInicio = $carbon->month; 
        $anioInicio = $carbon->year;
        $mesFin = $carbon->month;
        $anioFin = $carbon->year;

        return [
            'mes_inicio' => $mesInicio,
            'anio_inicio' => $anioInicio,
            'mes_fin' => $mesFin,
            'anio_fin' => $anioFin,
            'nombre_periodo' => $this->getNombreMesCorto($mesInicio),
            'clave_periodo' => $anioInicio . '-' . str_pad($mesInicio, 2, '0', STR_PAD_LEFT)
        ];
        
    }

    /**
     * Generar los periodos donde hay registros
     */
    private function getPeriodosConDatos()
    {
        // Obtener el rango de fechas de todos los datos
        $fechaMinima = DB::selectOne("
            SELECT MIN(fecha) as min_fecha FROM (
                SELECT MIN(fecha_ingreso) as fecha FROM fact_registro_clientes
                UNION ALL
                SELECT MIN(fecha_gasto) as fecha FROM fact_gastos_generales
                UNION ALL
                SELECT MIN(fecha_pago) as fecha FROM fact_pagos_gastos_fijos WHERE fecha_pago IS NOT NULL
            ) t WHERE fecha IS NOT NULL
        ")->min_fecha;
        
        $fechaMaxima = DB::selectOne("
            SELECT MAX(fecha) as max_fecha FROM (
                SELECT MAX(fecha_ingreso) as fecha FROM fact_registro_clientes
                UNION ALL
                SELECT MAX(fecha_gasto) as fecha FROM fact_gastos_generales
                UNION ALL
                SELECT MAX(fecha_pago) as fecha FROM fact_pagos_gastos_fijos WHERE fecha_pago IS NOT NULL
            ) t WHERE fecha IS NOT NULL
        ")->max_fecha;
        
        if (!$fechaMinima || !$fechaMaxima) {
            return [];
        }
        
        // CORRECCIÓN: Determinar el período correcto para la fecha mínima
        $periodoMinimo = $this->getPeriodoFromDate($fechaMinima);
        $periodoMaximo = $this->getPeriodoFromDate($fechaMaxima);
        
        $periodos = [];
        
        // Comenzar desde el período que contiene la fecha mínima
        $fechaActual = Carbon::createFromDate($periodoMinimo['anio_inicio'], $periodoMinimo['mes_inicio'], 1);
        $fechaLimite = Carbon::createFromDate($periodoMaximo['anio_fin'], $periodoMaximo['mes_fin'], 1);
        
        while ($fechaActual <= $fechaLimite) {
            // Usar día 15 para obtener el período correcto
            $periodo = $this->getPeriodoFromDate($fechaActual);
            
            $periodos[$periodo['clave_periodo']] = $periodo;
            
            $fechaActual->addMonth();
        }
        
        return $periodos;
    }
    
    /**
     * Obtener gastos específicos de la BODEGA
     */
    private function getGastosMensualesBodega()
    {
        $ultimosPeriodos = $this->getPeriodosConDatos();
        
        // Usar UNION ALL para cada período
        $queries = [];
        foreach ($ultimosPeriodos as $periodo) {
            $fechaInicio = $periodo['anio_inicio'] . '-' . str_pad($periodo['mes_inicio'], 2, '0', STR_PAD_LEFT) . '-01';
            $fechaFin = Carbon::createFromDate($periodo['anio_fin'], $periodo['mes_fin'], 1)->endOfMonth()->format('Y-m-d');
            
            $queries[] = "(
                SELECT 
                    'COMPRAS BODEGA' as producto,
                    '{$periodo['clave_periodo']}' as periodo_clave,
                    SUM(fg.monto) as total_periodo
                FROM fact_gastos_generales fg
                INNER JOIN dim_tipo_gasto tg ON tg.id_tipo_gasto = fg.id_tipo_gasto
                WHERE tg.nombre = 'COMPRAS BODEGA'
                    AND fg.fecha_gasto >= '{$fechaInicio}'
                    AND fg.fecha_gasto <= '{$fechaFin}'
            )";
        }
        
        $unionQuery = implode(' UNION ALL ', $queries);
        $comprasBodega = collect(DB::select($unionQuery));
            
        return $this->procesarGastosPorPeriodo($comprasBodega, 'producto', $ultimosPeriodos);
    }
    
    /**
     * Procesar datos de gastos por tipo/producto y período
     */
    private function procesarGastosPorPeriodo($gastos, $campo = 'tipo_gasto', $ultimosPeriodos = null)
    {
        if (!$ultimosPeriodos) {
            $ultimosPeriodos = $this->getPeriodosConDatos();
        }
        
        // Organizar datos por tipo de gasto/producto y período
        $gastosPorTipo = [];
        
        foreach ($gastos as $gasto) {
            $tipoKey = $campo === 'producto' ? $gasto->producto : $gasto->tipo_gasto;
            
            if (!isset($gastosPorTipo[$tipoKey])) {
                $gastosPorTipo[$tipoKey] = [];
            }
            
            $gastosPorTipo[$tipoKey][$gasto->periodo_clave] = $gasto->total_periodo;
        }
        
        // Crear array de períodos ordenados con nombres
        $periodos = [];
        foreach ($ultimosPeriodos as $clave => $periodo) {
            $periodos[$clave] = $periodo['nombre_periodo'];
        }
        
        // Calcular totales por período
        $totalesPorPeriodo = [];
        foreach ($periodos as $clave => $nombre) {
            $total = 0;
            foreach ($gastosPorTipo as $tipo => $periodosData) {
                $total += $periodosData[$clave] ?? 0;
            }
            $totalesPorPeriodo[$clave] = $total;
        }

        // Filtrar períodos sin datos - versión más eficiente
        $periodosConDatos = [];
        $totalesConDatos = [];

        foreach ($periodos as $clave => $nombre) {
            // Si el total del período es mayor a 0, entonces tiene datos
            if ($totalesPorPeriodo[$clave] > 0) {
                $periodosConDatos[$clave] = $nombre;
                $totalesConDatos[$clave] = $totalesPorPeriodo[$clave];
            }
        }

        return [
            'gastosPorTipo' => $gastosPorTipo,
            'meses' => $periodosConDatos, // Solo períodos con datos
            'totalesPorMes' => $totalesConDatos
        ];
    }
    
    /**
     * Obtener datos para gráficos
     */
    private function getDatosGraficos()
    {
        // Datos para HOTEL
        $datosHotel = $this->getDatosHotel();
        
        // Datos para BODEGA
        $datosBodega = $this->getDatosBodega();
        
        return [
            'hotel' => $datosHotel,
            'bodega' => $datosBodega
        ];
    }
    
    /**
     * Obtener datos de ingresos y gastos de la BODEGA para gráficos
     */
    private function getDatosBodega()
    {
        $ultimosPeriodos = $this->getPeriodosConDatos();
        
        // Ingresos de bodega - SOLO ventas sin cliente (id_estadia NULL)
        $queriesIngresos = [];
        foreach ($ultimosPeriodos as $periodo) {
            $fechaInicio = $periodo['anio_inicio'] . '-' . str_pad($periodo['mes_inicio'], 2, '0', STR_PAD_LEFT) . '-01';
            $fechaFin = Carbon::createFromDate($periodo['anio_fin'], $periodo['mes_fin'], 1)->endOfMonth()->format('Y-m-d');
            
            $queriesIngresos[] = "(
                SELECT 
                    '{$periodo['clave_periodo']}' as periodo_clave,
                    SUM(fpp.cantidad * fpp.precio_unitario) as total_ingresos
                FROM fact_pago_prod fpp
                WHERE fpp.id_estadia IS NULL
                    AND fpp.fecha_venta >= '{$fechaInicio}'
                    AND fpp.fecha_venta <= '{$fechaFin}'
            )";
        }
        
        $unionQueryIngresos = implode(' UNION ALL ', $queriesIngresos);
        $ingresos = collect(DB::select($unionQueryIngresos));
            
        // Gastos de bodega usando UNION ALL (sin cambios)
        $queriesGastos = [];
        foreach ($ultimosPeriodos as $periodo) {
            $fechaInicio = $periodo['anio_inicio'] . '-' . str_pad($periodo['mes_inicio'], 2, '0', STR_PAD_LEFT) . '-01';
            $fechaFin = Carbon::createFromDate($periodo['anio_fin'], $periodo['mes_fin'], 1)->endOfMonth()->format('Y-m-d');
            
            $queriesGastos[] = "(
                SELECT 
                    '{$periodo['clave_periodo']}' as periodo_clave,
                    SUM(fg.monto) as total_gastos
                FROM fact_gastos_generales fg
                INNER JOIN dim_tipo_gasto tg ON tg.id_tipo_gasto = fg.id_tipo_gasto
                WHERE tg.nombre = 'COMPRAS BODEGA'
                    AND fg.fecha_gasto >= '{$fechaInicio}'
                    AND fg.fecha_gasto <= '{$fechaFin}'
            )";
        }
        
        $unionQueryGastos = implode(' UNION ALL ', $queriesGastos);
        $gastos = collect(DB::select($unionQueryGastos));
            
        return $this->combinarDatosPorPeriodos($ingresos, $gastos, $ultimosPeriodos);
    }

    /**
     * Combinar datos de ingresos y gastos por período
     */
    private function combinarDatosPorPeriodos($ingresos, $gastos, $ultimosPeriodos)
    {
        $periodosCompletos = [];
        
        // Inicializar todos los períodos con valores en 0
        foreach ($ultimosPeriodos as $clave => $periodo) {
            $periodosCompletos[$clave] = [
                'nombre' => $periodo['nombre_periodo'],
                'ingresos' => 0,
                'gastos' => 0
            ];
        }
        
        // Llenar ingresos
        foreach ($ingresos as $ingreso) {
            if (isset($periodosCompletos[$ingreso->periodo_clave])) {
                $periodosCompletos[$ingreso->periodo_clave]['ingresos'] = (float) $ingreso->total_ingresos;
            }
        }
        
        // Llenar gastos
        foreach ($gastos as $gasto) {
            if (isset($periodosCompletos[$gasto->periodo_clave])) {
                $periodosCompletos[$gasto->periodo_clave]['gastos'] = (float) $gasto->total_gastos;
            }
        }    

        // Filtrar períodos sin datos (ni ingresos ni gastos)
        $periodosConDatos = array_filter($periodosCompletos, function($periodo) {
            return $periodo['ingresos'] > 0 || $periodo['gastos'] > 0;
        });

        return array_values($periodosConDatos);
    }

    /* Obtener nombre completo del mes */
    private function getNombreMes($numeroMes)
    {
        $meses = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];
        
        return $meses[$numeroMes] ?? 'Desconocido';
    }
    
    /* Obtener nombre abreviado del mes (3 letras) */
    private function getNombreMesCorto($numeroMes)
    {
        $meses = [
            1 => 'Ene', 2 => 'Feb', 3 => 'Mar', 4 => 'Abr',
            5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Ago',
            9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dic'
        ];
        
        return $meses[$numeroMes] ?? 'N/A';
    }

    /* Obtener gastos fijos para incluir en gráficos y cuadre de caja */
    private function getGastosFijos()
    {
        $ultimosPeriodos = $this->getPeriodosConDatos();
        
        // Para gastos fijos usamos la fecha de pago
        $condicionesPeriodos = [];
        foreach ($ultimosPeriodos as $periodo) {
            $fechaInicio = $periodo['anio_inicio'] . '-' . str_pad($periodo['mes_inicio'], 2, '0', STR_PAD_LEFT) . '-15';
            $fechaFin = $periodo['anio_fin'] . '-' . str_pad($periodo['mes_fin'], 2, '0', STR_PAD_LEFT) . '-14';
            
            $condicionesPeriodos[] = "WHEN fpgf.fecha_pago >= '{$fechaInicio}' AND fpgf.fecha_pago <= '{$fechaFin}' THEN '{$periodo['clave_periodo']}'";
        }
        
        $casosPeriodos = implode(' ', $condicionesPeriodos);

        $gastosFijos = DB::table('fact_pagos_gastos_fijos as fpgf')
            ->join('fact_gastos_fijos as fgf', 'fgf.id_gasto_fijo', '=', 'fpgf.id_gasto_fijo')
            ->selectRaw('
                fgf.nombre_servicio as tipo_gasto,
                CASE ' . $casosPeriodos . ' END as periodo_clave,
                SUM(fpgf.monto_pagado) as total_periodo
            ')
            ->whereRaw('fpgf.fecha_pago >= ?', [
                array_values($ultimosPeriodos)[0]['anio_inicio'] . '-' . 
                str_pad(array_values($ultimosPeriodos)[0]['mes_inicio'], 2, '0', STR_PAD_LEFT) . '-15'
            ])
            ->groupByRaw('fgf.nombre_servicio, CASE ' . $casosPeriodos . ' END')
            ->havingRaw('CASE ' . $casosPeriodos . ' END IS NOT NULL')
            ->get();

        return $this->procesarGastosPorPeriodo($gastosFijos, 'tipo_gasto', $ultimosPeriodos);
    }

    /**
     * Obtener gastos mensuales del hotel incluyendo gastos fijos
     */
    private function getGastosMensualesHotel()
    {
        $ultimosPeriodos = $this->getPeriodosConDatos();
        
        // Usar UNION ALL para cada período para evitar problemas de GROUP BY
        $queries = [];
        foreach ($ultimosPeriodos as $periodo) {
            $fechaInicio = $periodo['anio_inicio'] . '-' . str_pad($periodo['mes_inicio'], 2, '0', STR_PAD_LEFT) . '-01';
            $fechaFin = Carbon::createFromDate($periodo['anio_fin'], $periodo['mes_fin'], 1)->endOfMonth()->format('Y-m-d');
            
            $queries[] = "(
                SELECT 
                    tg.nombre as tipo_gasto,
                    '{$periodo['clave_periodo']}' as periodo_clave,
                    SUM(fg.monto) as total_periodo
                FROM fact_gastos_generales fg
                INNER JOIN dim_tipo_gasto tg ON tg.id_tipo_gasto = fg.id_tipo_gasto
                WHERE tg.nombre != 'COMPRAS BODEGA'
                    AND fg.fecha_gasto >= '{$fechaInicio}'
                    AND fg.fecha_gasto <= '{$fechaFin}'
                GROUP BY tg.nombre
            )";
        }
        
        $unionQuery = implode(' UNION ALL ', $queries);
        $gastosGenerales = collect(DB::select($unionQuery));

        // Gastos fijos (servicios) - Solo si existe la tabla
        $gastosFijos = collect();
        if (Schema::hasTable('fact_pagos_gastos_fijos')) {
            $queriesGastosFijos = [];
            foreach ($ultimosPeriodos as $periodo) {
                $fechaInicio = $periodo['anio_inicio'] . '-' . str_pad($periodo['mes_inicio'], 2, '0', STR_PAD_LEFT) . '-01';
                $fechaFin = Carbon::createFromDate($periodo['anio_fin'], $periodo['mes_fin'], 1)->endOfMonth()->format('Y-m-d');
                
                $queriesGastosFijos[] = "(
                    SELECT 
                        fgf.nombre_servicio as tipo_gasto,
                        '{$periodo['clave_periodo']}' as periodo_clave,
                        SUM(fpgf.monto_pagado) as total_periodo
                    FROM fact_pagos_gastos_fijos fpgf
                    INNER JOIN fact_gastos_fijos fgf ON fgf.id_gasto_fijo = fpgf.id_gasto_fijo
                    WHERE fpgf.fecha_pago >= '{$fechaInicio}'
                        AND fpgf.fecha_pago <= '{$fechaFin}'
                    GROUP BY fgf.nombre_servicio
                )";
            }
            
            if (!empty($queriesGastosFijos)) {
                $unionQueryGastosFijos = implode(' UNION ALL ', $queriesGastosFijos);
                $gastosFijos = collect(DB::select($unionQueryGastosFijos));
            }
        }

        // Combinar todos los tipos de gastos del hotel
        $todosLosGastosHotel = $gastosGenerales->concat($gastosFijos);
        
        return $this->procesarGastosPorPeriodo($todosLosGastosHotel, 'tipo_gasto', $ultimosPeriodos);
    }

    /**
     * Obtener datos del hotel para gráficos
     */
    private function getDatosHotel()
    {
        $ultimosPeriodos = $this->getPeriodosConDatos();

        // Ingresos del hotel usando UNION ALL
        $queriesIngresos = [];
        foreach ($ultimosPeriodos as $periodo) {
            $fechaInicio = $periodo['anio_inicio'] . '-' . str_pad($periodo['mes_inicio'], 2, '0', STR_PAD_LEFT) . '-01';
            $fechaFin = Carbon::createFromDate($periodo['anio_fin'], $periodo['mes_fin'], 1)->endOfMonth()->format('Y-m-d');
            
            $queriesIngresos[] = "(
                SELECT 
                    '{$periodo['clave_periodo']}' as periodo_clave,
                    SUM(fph.monto) as total_ingresos
                FROM fact_pago_hab fph
                INNER JOIN fact_registro_clientes frc ON frc.id_estadia = fph.id_estadia
                WHERE frc.fecha_ingreso >= '{$fechaInicio}'
                    AND frc.fecha_ingreso <= '{$fechaFin}'
            )";
        }
        
        $unionQueryIngresos = implode(' UNION ALL ', $queriesIngresos);
        $ingresosHabitacion = collect(DB::select($unionQueryIngresos));

        // Gastos generales del hotel usando UNION ALL
        $queriesGastos = [];
        foreach ($ultimosPeriodos as $periodo) {
            $fechaInicio = $periodo['anio_inicio'] . '-' . str_pad($periodo['mes_inicio'], 2, '0', STR_PAD_LEFT) . '-01';
            $fechaFin = Carbon::createFromDate($periodo['anio_fin'], $periodo['mes_fin'], 1)->endOfMonth()->format('Y-m-d');
            
            $queriesGastos[] = "(
                SELECT 
                    '{$periodo['clave_periodo']}' as periodo_clave,
                    SUM(fg.monto) as total_gastos
                FROM fact_gastos_generales fg
                INNER JOIN dim_tipo_gasto tg ON tg.id_tipo_gasto = fg.id_tipo_gasto
                WHERE tg.nombre != 'COMPRAS BODEGA'
                    AND fg.fecha_gasto >= '{$fechaInicio}'
                    AND fg.fecha_gasto <= '{$fechaFin}'
            )";
        }
        
        $unionQueryGastos = implode(' UNION ALL ', $queriesGastos);
        $gastosGenerales = collect(DB::select($unionQueryGastos));

        // Incluir gastos fijos
        $gastosFijos = collect();
        if (Schema::hasTable('fact_pagos_gastos_fijos')) {
            $queriesGastosFijos = [];
            foreach ($ultimosPeriodos as $periodo) {
                $fechaInicio = $periodo['anio_inicio'] . '-' . str_pad($periodo['mes_inicio'], 2, '0', STR_PAD_LEFT) . '-01';
                $fechaFin = Carbon::createFromDate($periodo['anio_fin'], $periodo['mes_fin'], 1)->endOfMonth()->format('Y-m-d');
                
                $queriesGastosFijos[] = "(
                    SELECT 
                        '{$periodo['clave_periodo']}' as periodo_clave,
                        SUM(monto_pagado) as total_gastos
                    FROM fact_pagos_gastos_fijos
                    WHERE fecha_pago >= '{$fechaInicio}'
                        AND fecha_pago <= '{$fechaFin}'
                )";
            }
            
            if (!empty($queriesGastosFijos)) {
                $unionQueryGastosFijos = implode(' UNION ALL ', $queriesGastosFijos);
                $gastosFijos = collect(DB::select($unionQueryGastosFijos));
            }
        }

        // Combinar gastos
        $gastosTotalesHotel = $this->combinarGastosPorPeriodos($gastosGenerales, $gastosFijos, $ultimosPeriodos);
        
        return $this->combinarDatosPorPeriodos($ingresosHabitacion, $gastosTotalesHotel, $ultimosPeriodos);
    }

    /**
     * Obtener total de gastos fijos del período actual para el dashboard
     */
    public function getTotalGastosFijosPeriodoActual()
    {
        $periodoActual = $this->getPeriodoFromDate(Carbon::now());
        
        $fechaInicio = $periodoActual['anio_inicio'] . '-' . str_pad($periodoActual['mes_inicio'], 2, '0', STR_PAD_LEFT) . '-01';
        $fechaFin = Carbon::createFromDate($periodoActual['anio_fin'], $periodoActual['mes_fin'], 1)->endOfMonth()->format('Y-m-d');
        
        return DB::table('fact_pagos_gastos_fijos')
            ->whereBetween('fecha_pago', [$fechaInicio, $fechaFin])
            ->sum('monto_pagado');
    }

    /**
     * Combinar dos arrays de gastos por períodos
     */
    private function combinarGastosPorPeriodos($gastos1, $gastos2, $ultimosPeriodos)
    {
        $gastosCombinados = [];
        
        // Inicializar todos los períodos con 0
        foreach ($ultimosPeriodos as $clave => $periodo) {
            $gastosCombinados[$clave] = (object)[
                'periodo_clave' => $clave,
                'total_gastos' => 0
            ];
        }
        
        // Procesar primeros gastos
        foreach ($gastos1 as $gasto) {
            if (isset($gastosCombinados[$gasto->periodo_clave])) {
                $gastosCombinados[$gasto->periodo_clave]->total_gastos += $gasto->total_gastos;
            }
        }
        
        // Sumar segundos gastos
        foreach ($gastos2 as $gasto) {
            if (isset($gastosCombinados[$gasto->periodo_clave])) {
                $gastosCombinados[$gasto->periodo_clave]->total_gastos += $gasto->total_gastos;
            }
        }
        
        return collect(array_values($gastosCombinados));
    }
}