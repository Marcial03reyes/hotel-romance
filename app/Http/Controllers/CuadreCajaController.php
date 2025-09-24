<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class CuadreCajaController extends Controller
{
    public function index(Request $request)
    {
        // Obtener fechas seleccionadas (múltiples días)
        $fechasSeleccionadas = $request->get('fechas', [today()->format('Y-m-d')]);
        if (!is_array($fechasSeleccionadas)) {
            $fechasSeleccionadas = [$fechasSeleccionadas];
        }
        
        // Obtener datos por días con estructura de matriz
        $datosPorDias = $this->getDatosPorDias($fechasSeleccionadas);
        
        // Calcular resumen final
        $resumenFinal = $this->calcularResumenFinalMatriz($datosPorDias);
        
        return view('cuadre-caja.index', compact('datosPorDias', 'fechasSeleccionadas', 'resumenFinal'));    
    }
    
    // Obtener datos por días con estructura de matriz
    private function getDatosPorDias($fechasSeleccionadas)
    {
        $datosPorDias = [];
        
        foreach ($fechasSeleccionadas as $fecha) {
            $datosPorDias[$fecha] = [
                'fecha' => Carbon::parse($fecha),
                'hotel' => [
                    'dia' => ['efectivo' => 0, 'yape_plin' => 0, 'tarjeta' => 0],
                    'noche' => ['efectivo' => 0, 'yape_plin' => 0, 'tarjeta' => 0]
                ],
                'bodega' => [
                    'dia' => ['efectivo' => 0, 'yape_plin' => 0, 'tarjeta' => 0],
                    'noche' => ['efectivo' => 0, 'yape_plin' => 0, 'tarjeta' => 0]
                ],
                'gastos' => [
                    'dia' => ['efectivo' => 0, 'yape_plin' => 0, 'tarjeta' => 0],
                    'noche' => ['efectivo' => 0, 'yape_plin' => 0, 'tarjeta' => 0]
                ]
            ];
        }

        // Llenar ingresos de HOTEL por turno y método
        $ingresosHotel = DB::table('fact_pago_hab as fph')
            ->join('fact_registro_clientes as frc', 'frc.id_estadia', '=', 'fph.id_estadia')
            ->join('dim_met_pago as dmp', 'dmp.id_met_pago', '=', 'fph.id_met_pago')
            ->whereIn('frc.fecha_ingreso', $fechasSeleccionadas)
            ->select(
                'frc.fecha_ingreso',
                'frc.turno',
                'dmp.met_pago', 
                DB::raw('SUM(fph.monto) as total')
            )
            ->groupBy('frc.fecha_ingreso', 'frc.turno', 'dmp.id_met_pago', 'dmp.met_pago')
            ->get();

        foreach ($ingresosHotel as $ingreso) {
            $fecha = $ingreso->fecha_ingreso;
            $turno = $ingreso->turno == 0 ? 'dia' : 'noche';
            $metodo = $this->clasificarMetodoDetallado($ingreso->met_pago);
            
            if (isset($datosPorDias[$fecha])) {
                $datosPorDias[$fecha]['hotel'][$turno][$metodo] += $ingreso->total;
            }
        }

        // Llenar ingresos de BODEGA (usando fecha de ingreso del huésped)
        $ingresosBodega = DB::table('fact_pago_prod as fpp')
            ->join('fact_registro_clientes as frc', 'frc.id_estadia', '=', 'fpp.id_estadia')
            ->join('dim_met_pago as dmp', 'dmp.id_met_pago', '=', 'fpp.id_met_pago')
            ->whereIn('frc.fecha_ingreso', $fechasSeleccionadas)
            ->select(
                'frc.fecha_ingreso',
                'frc.turno',
                'dmp.met_pago', 
                DB::raw('SUM(fpp.cantidad * fpp.precio_unitario) as total')
            )
            ->groupBy('frc.fecha_ingreso', 'frc.turno', 'dmp.id_met_pago', 'dmp.met_pago')
            ->get();

        foreach ($ingresosBodega as $ingreso) {
            $fecha = $ingreso->fecha_ingreso;
            $turno = $ingreso->turno == 0 ? 'dia' : 'noche';
            $metodo = $this->clasificarMetodoDetallado($ingreso->met_pago);
            
            if (isset($datosPorDias[$fecha])) {
                $datosPorDias[$fecha]['bodega'][$turno][$metodo] += $ingreso->total;
            }
        }

        // Llenar GASTOS (distribuir proporcionalmente entre días y turnos)
        $gastosGenerales = DB::table('fact_gastos_generales as fgg')
            ->join('dim_met_pago as dmp', 'dmp.id_met_pago', '=', 'fgg.id_met_pago')
            ->join('dim_tipo_gasto as dtg', 'dtg.id_tipo_gasto', '=', 'fgg.id_tipo_gasto')
            ->whereIn('fgg.fecha_gasto', $fechasSeleccionadas)
            ->select('fgg.fecha_gasto', 'dmp.met_pago', 'dtg.nombre as tipo_gasto', DB::raw('SUM(fgg.monto) as total'))
            ->groupBy('fgg.fecha_gasto', 'dmp.id_met_pago', 'dmp.met_pago', 'dtg.id_tipo_gasto', 'dtg.nombre')
            ->get();

        // Gastos fijos
        $gastosFijos = collect();
        if (Schema::hasTable('fact_pagos_gastos_fijos')) {
            $gastosFijos = DB::table('fact_pagos_gastos_fijos as fpgf')
                ->join('dim_met_pago as dmp', 'dmp.id_met_pago', '=', 'fpgf.id_met_pago')
                ->whereIn('fpgf.fecha_pago', $fechasSeleccionadas)
                ->select('fpgf.fecha_pago as fecha_gasto', 'dmp.met_pago', DB::raw('"GASTOS FIJOS" as tipo_gasto'), 'fpgf.monto_pagado as total')
                ->get();
        }

        // Procesar gastos (dividir entre DÍA y NOCHE)
        foreach ($gastosGenerales->concat($gastosFijos) as $gasto) {
            $fecha = $gasto->fecha_gasto;
            $metodo = $this->clasificarMetodoDetallado($gasto->met_pago);
            
            if (isset($datosPorDias[$fecha])) {
                // Dividir gastos entre día y noche (50% cada uno)
                $montoDia = $gasto->total / 2;
                $montoNoche = $gasto->total / 2;
                
                $datosPorDias[$fecha]['gastos']['dia'][$metodo] += $montoDia;
                $datosPorDias[$fecha]['gastos']['noche'][$metodo] += $montoNoche;
            }
        }

        return $datosPorDias;
    }

    // Clasificar método de pago detallado
    private function clasificarMetodoDetallado($metodo)
    {
        switch (strtolower($metodo)) {
            case 'efectivo':
                return 'efectivo';
            case 'yape':
            case 'plin':
                return 'yape_plin';
            default:
                return 'tarjeta';
        }
    }

    // Calcular resumen final matriz
    private function calcularResumenFinalMatriz($datosPorDias)
    {
        $resumen = [
            'hotel' => ['efectivo' => 0, 'cuenta' => 0],
            'bodega' => ['efectivo' => 0, 'cuenta' => 0],
            'totales' => ['efectivo' => 0, 'cuenta' => 0, 'total' => 0]
        ];

        foreach ($datosPorDias as $datos) {
            foreach (['dia', 'noche'] as $turno) {
                // HOTEL
                $hotelEfectivo = $datos['hotel'][$turno]['efectivo'];
                $hotelCuenta = $datos['hotel'][$turno]['yape_plin'] + $datos['hotel'][$turno]['tarjeta'];
                
                $resumen['hotel']['efectivo'] += $hotelEfectivo;
                $resumen['hotel']['cuenta'] += $hotelCuenta;

                // BODEGA  
                $bodegaEfectivo = $datos['bodega'][$turno]['efectivo'];
                $bodegaCuenta = $datos['bodega'][$turno]['yape_plin'] + $datos['bodega'][$turno]['tarjeta'];
                
                $resumen['bodega']['efectivo'] += $bodegaEfectivo;
                $resumen['bodega']['cuenta'] += $bodegaCuenta;

                // DESCONTAR GASTOS (distribuidos entre hotel y bodega)
                $gastosEfectivo = $datos['gastos'][$turno]['efectivo'] / 2;
                $gastosCuenta = ($datos['gastos'][$turno]['yape_plin'] + $datos['gastos'][$turno]['tarjeta']) / 2;
                
                $resumen['hotel']['efectivo'] -= $gastosEfectivo;
                $resumen['hotel']['cuenta'] -= $gastosCuenta;
                $resumen['bodega']['efectivo'] -= $gastosEfectivo;
                $resumen['bodega']['cuenta'] -= $gastosCuenta;
            }
        }

        // Calcular totales
        $resumen['totales']['efectivo'] = $resumen['hotel']['efectivo'] + $resumen['bodega']['efectivo'];
        $resumen['totales']['cuenta'] = $resumen['hotel']['cuenta'] + $resumen['bodega']['cuenta'];
        $resumen['totales']['total'] = $resumen['totales']['efectivo'] + $resumen['totales']['cuenta'];

        return $resumen;
    }

    // Métodos auxiliares existentes (mantener sin cambios)
    private function getDatosDiariosHotel($fechaInicio, $fechaFin)
    {
        // Ingresos diarios
        $ingresos = DB::table('fact_pago_hab as fph')
            ->join('fact_registro_clientes as frc', 'frc.id_estadia', '=', 'fph.id_estadia')
            ->join('dim_met_pago as dmp', 'dmp.id_met_pago', '=', 'fph.id_met_pago')
            ->whereBetween('frc.fecha_ingreso', [$fechaInicio, $fechaFin])
            ->select('frc.fecha_ingreso', 'dmp.met_pago', DB::raw('SUM(fph.monto) as total'))
            ->groupBy('frc.fecha_ingreso', 'dmp.id_met_pago', 'dmp.met_pago')
            ->get();

        // Gastos diarios
        $gastosGenerales = DB::table('fact_gastos_generales as fgg')
            ->join('dim_met_pago as dmp', 'dmp.id_met_pago', '=', 'fgg.id_met_pago')
            ->join('dim_tipo_gasto as dtg', 'dtg.id_tipo_gasto', '=', 'fgg.id_tipo_gasto')
            ->where('dtg.nombre', '!=', 'COMPRAS BODEGA')
            ->whereBetween('fgg.fecha_gasto', [$fechaInicio, $fechaFin])
            ->select('fgg.fecha_gasto as fecha', 'dmp.met_pago', DB::raw('SUM(fgg.monto) as total'))
            ->groupBy('fgg.fecha_gasto', 'dmp.id_met_pago', 'dmp.met_pago')
            ->get();

        // Incluir gastos fijos diarios 
        $gastosFijosDiarios = DB::table('fact_pagos_gastos_fijos as fpgf')
            ->join('dim_met_pago as dmp', 'dmp.id_met_pago', '=', 'fpgf.id_met_pago')
            ->whereBetween('fpgf.fecha_pago', [$fechaInicio, $fechaFin])
            ->select('fpgf.fecha_pago as fecha', 'dmp.met_pago', 'fpgf.monto_pagado as total')
            ->get();

        return $this->organizarDatosDiarios($ingresos, $gastosGenerales->concat($gastosFijosDiarios), $fechaInicio, $fechaFin);
    }

    private function getDatosDiariosBodega($fechaInicio, $fechaFin)
    {
        // Ingresos diarios
        $ingresos = DB::table('fact_pago_prod as fpp')
            ->join('fact_registro_clientes as frc', 'frc.id_estadia', '=', 'fpp.id_estadia')
            ->join('dim_met_pago as dmp', 'dmp.id_met_pago', '=', 'fpp.id_met_pago')
            ->whereBetween('frc.fecha_ingreso', [$fechaInicio, $fechaFin])
            ->select('frc.fecha_ingreso', 'dmp.met_pago', DB::raw('SUM(fpp.cantidad * fpp.precio_unitario) as total'))
            ->groupBy('frc.fecha_ingreso', 'dmp.id_met_pago', 'dmp.met_pago')
            ->get();
        
        // Gastos diarios: Solo categoría "COMPRAS BODEGA"
        $gastos = collect();

        $categoriaBodega = DB::table('dim_tipo_gasto')
            ->where('nombre', 'COMPRAS BODEGA')
            ->first();

        if ($categoriaBodega) {
            $gastos = DB::table('fact_gastos_generales as fgg')
                ->join('dim_met_pago as dmp', 'dmp.id_met_pago', '=', 'fgg.id_met_pago')
                ->where('fgg.id_tipo_gasto', $categoriaBodega->id_tipo_gasto)
                ->whereBetween('fgg.fecha_gasto', [$fechaInicio, $fechaFin])
                ->select('fgg.fecha_gasto as fecha', 'dmp.met_pago', DB::raw('SUM(fgg.monto) as total'))
                ->groupBy('fgg.fecha_gasto', 'dmp.id_met_pago', 'dmp.met_pago')
                ->get();
        }

        return $this->organizarDatosDiarios($ingresos, $gastos, $fechaInicio, $fechaFin);
    }

    // Organizar datos diarios por fecha y método de pago
    private function organizarDatosDiarios($ingresos, $gastos, $fechaInicio, $fechaFin)
    {
        $dias = [];
        $inicio = Carbon::parse($fechaInicio);
        $fin = Carbon::parse($fechaFin);

        // Generar todos los días del rango
        for ($fecha = $inicio->copy(); $fecha <= $fin; $fecha->addDay()) {
            $fechaStr = $fecha->format('Y-m-d');
            $dias[$fechaStr] = [
                'fecha' => $fecha->copy(),
                'ingresos' => ['Efectivo' => 0, 'Yape/Plin' => 0, 'Cuenta Bancaria' => 0],
                'gastos' => ['Efectivo' => 0, 'Yape/Plin' => 0, 'Cuenta Bancaria' => 0]
            ];
        }

        // Llenar ingresos
        foreach ($ingresos as $ingreso) {
            $fechaKey = $ingreso->fecha_ingreso ?? $ingreso->fecha;
            if (isset($dias[$fechaKey])) {
                $metodo = $this->mapearMetodoPago($ingreso->met_pago);
                $dias[$fechaKey]['ingresos'][$metodo] += $ingreso->total;
            }
        }

        // Llenar gastos
        foreach ($gastos as $gasto) {
            $fechaKey = $gasto->fecha;
            if (isset($dias[$fechaKey])) {
                $metodo = $this->mapearMetodoPago($gasto->met_pago);
                $dias[$fechaKey]['gastos'][$metodo] += $gasto->total;
            }
        }

        return $dias;
    }

    // Mapear métodos de pago a grupos
    private function mapearMetodoPago($metodo)
    {
        switch ($metodo) {
            case 'Yape':
            case 'Plin':
                return 'Yape/Plin';
            case 'Tarjeta':
            case 'Tarjeta crédito':
            case 'QR':
            case 'Transferencia':
                return 'Cuenta Bancaria';
            default:
                return 'Efectivo';
        }
    }

    // Combinar dos colecciones de gastos sumando por método de pago
    private function combinarGastos($gastos1, $gastos2)
    {
        $gastosCombinados = collect();
        $metodosPago = collect([$gastos1, $gastos2])->flatten(1)->groupBy('met_pago');
        
        foreach ($metodosPago as $metodo => $gastos) {
            $total = $gastos->sum('total');
            $gastosCombinados->push((object)[
                'met_pago' => $metodo,
                'total' => $total
            ]);
        }
        
        return $gastosCombinados;
    }
    
    // Obtener fecha de inicio según el filtro
    private function getFechaInicio($request)
    {
        $filtro = $request->get('filtro', 'dia');
        
        switch ($filtro) {
            case 'semana':
                return Carbon::now()->startOfWeek(Carbon::SUNDAY)->format('Y-m-d');
            case 'personalizado':
                return $request->get('fecha_inicio', Carbon::now()->format('Y-m-d'));
            default: // día
                return Carbon::now()->format('Y-m-d');
        }
    }
    
    // Obtener fecha de fin según el filtro
    private function getFechaFin($request)
    {
        $filtro = $request->get('filtro', 'dia');
        
        switch ($filtro) {
            case 'semana':
                return Carbon::now()->endOfWeek(Carbon::SATURDAY)->format('Y-m-d');
            case 'personalizado':
                return $request->get('fecha_fin', Carbon::now()->format('Y-m-d'));
            default: // día
                return Carbon::now()->format('Y-m-d');
        }
    }
    
    // Generar subtítulo con el rango de fechas
    private function getSubtituloFecha($fechaInicio, $fechaFin, $filtro)
    {
        $meses = [
            1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril',
            5 => 'mayo', 6 => 'junio', 7 => 'julio', 8 => 'agosto',
            9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre'
        ];
        
        $inicio = Carbon::parse($fechaInicio);
        $fin = Carbon::parse($fechaFin);
        
        if ($filtro === 'dia' && $inicio->isSameDay($fin)) {
            return 'Del día ' . $inicio->day . ' de ' . $meses[$inicio->month] . ' de ' . $inicio->year;
        }
        
        return 'Del ' . $inicio->day . ' de ' . $meses[$inicio->month] . ' al ' . $fin->day . ' de ' . $meses[$fin->month] . ' de ' . $fin->year;
    }
}