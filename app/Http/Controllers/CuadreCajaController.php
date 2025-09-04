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
        // Determinar el período de consulta según los filtros
        $fechaInicio = $this->getFechaInicio($request);
        $fechaFin = $this->getFechaFin($request);
        $filtro = $request->get('filtro', 'dia');
        
        // Obtener datos de HOTEL
        $datosHotel = $this->getDatosHotel($fechaInicio, $fechaFin);
        
        // Obtener datos de BODEGA
        $datosBodega = $this->getDatosBodega($fechaInicio, $fechaFin);
        
        // Formatear fechas para mostrar
        $subtituloFecha = $this->getSubtituloFecha($fechaInicio, $fechaFin, $filtro);

        $datosDiariosHotel = null;
        $datosDiariosBodega = null;
        if ($filtro === 'semana' || $filtro === 'personalizado') {
            $datosDiariosHotel = $this->getDatosDiariosHotel($fechaInicio, $fechaFin);
            $datosDiariosBodega = $this->getDatosDiariosBodega($fechaInicio, $fechaFin);
        }
        
        return view('cuadre-caja.index', compact('datosHotel', 'datosBodega', 'subtituloFecha', 'filtro', 'fechaInicio', 'fechaFin', 'datosDiariosHotel', 'datosDiariosBodega'));    
    }
    
    // Obtener datos de HOTEL (ingresos por habitaciones y gastos)
    private function getDatosHotel($fechaInicio, $fechaFin)
    {
        // INGRESOS: Pagos por habitaciones
        $ingresos = DB::table('fact_pago_hab as fph')
            ->join('fact_registro_clientes as frc', 'frc.id_estadia', '=', 'fph.id_estadia')
            ->join('dim_met_pago as dmp', 'dmp.id_met_pago', '=', 'fph.id_met_pago')
            ->whereBetween('frc.fecha_ingreso', [$fechaInicio, $fechaFin])
            ->select('dmp.met_pago', DB::raw('SUM(fph.monto) as total'))
            ->groupBy('dmp.id_met_pago', 'dmp.met_pago')
            ->get();
            
        // GASTOS: Solo gastos generales (fact_compra_interna no tiene método de pago)
        $gastosGenerales = DB::table('fact_gastos_generales as fgg')
            ->join('dim_met_pago as dmp', 'dmp.id_met_pago', '=', 'fgg.id_met_pago')
            ->join('dim_tipo_gasto as dtg', 'dtg.id_tipo_gasto', '=', 'fgg.id_tipo_gasto')
            ->where('dtg.nombre', '!=', 'COMPRAS BODEGA')
            ->whereBetween('fgg.fecha_gasto', [$fechaInicio, $fechaFin])
            ->select('dmp.met_pago', DB::raw('SUM(fgg.monto) as total'))
            ->groupBy('dmp.id_met_pago', 'dmp.met_pago')
            ->get();
        
        $gastos = $gastosGenerales;
        
        // Gastos fijos (sin método de pago, se asumen como Efectivo)
        $gastosFijos = collect();
        if (Schema::hasTable('fact_pagos_gastos_fijos')) {
        $gastosFijos = DB::table('fact_pagos_gastos_fijos as fpgf')
            ->join('dim_met_pago as dmp', 'dmp.id_met_pago', '=', 'fpgf.id_met_pago')
            ->whereBetween('fpgf.fecha_pago', [$fechaInicio, $fechaFin])
            ->select('dmp.met_pago', DB::raw('SUM(fpgf.monto_pagado) as total'))
            ->groupBy('dmp.id_met_pago', 'dmp.met_pago')
            ->get();
        }

        // Combinar todos los gastos (generales + compras + fijos)
        $gastos = $this->combinarGastos($gastosGenerales, $gastosFijos);

        return $this->organizarDatosPorMetodoPago($ingresos, $gastos);
    }
    
    // Obtener datos de BODEGA (ingresos por consumos y gastos por compras)
    private function getDatosBodega($fechaInicio, $fechaFin)
    {
        // INGRESOS: Consumos de productos (ventas a huéspedes)
        $ingresos = DB::table('fact_pago_prod as fpp')
            ->join('fact_registro_clientes as frc', 'frc.id_estadia', '=', 'fpp.id_estadia')
            ->join('dim_met_pago as dmp', 'dmp.id_met_pago', '=', 'fpp.id_met_pago')
            ->whereBetween('frc.fecha_ingreso', [$fechaInicio, $fechaFin])
            ->select('dmp.met_pago', DB::raw('SUM(fpp.cantidad * fpp.precio_unitario) as total'))
            ->groupBy('dmp.id_met_pago', 'dmp.met_pago')
            ->get();

        // GASTOS: Solo categoría "COMPRAS BODEGA" de gastos variables
        $gastos = collect(); // Inicializar vacío

        // Verificar si existe la categoría "COMPRAS BODEGA"
        $categoriaBodega = DB::table('dim_tipo_gasto')
            ->where('nombre', 'COMPRAS BODEGA')
            ->first();

        if ($categoriaBodega) {
            // Si existe la categoría, obtener los gastos
            $gastos = DB::table('fact_gastos_generales as fgg')
                ->join('dim_met_pago as dmp', 'dmp.id_met_pago', '=', 'fgg.id_met_pago')
                ->where('fgg.id_tipo_gasto', $categoriaBodega->id_tipo_gasto)
                ->whereBetween('fgg.fecha_gasto', [$fechaInicio, $fechaFin])
                ->select('dmp.met_pago', DB::raw('SUM(fgg.monto) as total'))
                ->groupBy('dmp.id_met_pago', 'dmp.met_pago')
                ->get();
        }

        return $this->organizarDatosPorMetodoPago($ingresos, $gastos);

        
    }
    
    // Obtener datos diarios de HOTEL para la semana
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
            ->select('fpgf.fecha_pago as fecha', 'dmp.met_pago', 'fpgf.monto_pagado as total') // ← Método real
            ->get();

        return $this->organizarDatosDiarios($ingresos, $gastosGenerales->concat($gastosFijosDiarios), $fechaInicio, $fechaFin);
    }

    // Obtener datos diarios de BODEGA para la semana
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
    
    // Organizar datos por método de pago con estructura uniforme
    private function organizarDatosPorMetodoPago($ingresos, $gastos)
    {
        $metodosPago = ['Efectivo', 'Yape', 'Plin', 'Tarjeta', 'Tarjeta crédito', 'QR', 'Transferencia'];
        $datos = [];
        
        // Convertir ingresos y gastos a arrays asociativos por método de pago
        $ingresosArray = $ingresos->keyBy('met_pago')->toArray();
        $gastosArray = $gastos->keyBy('met_pago')->toArray();
        
        foreach ($metodosPago as $metodo) {
            // Agrupar Yape y Plin juntos
            if ($metodo === 'Yape') {
                $ingresoYape = isset($ingresosArray['Yape']) ? $ingresosArray['Yape']->total : 0;
                $ingresoPlin = isset($ingresosArray['Plin']) ? $ingresosArray['Plin']->total : 0;
                $gastoYape = isset($gastosArray['Yape']) ? $gastosArray['Yape']->total : 0;
                $gastoPlin = isset($gastosArray['Plin']) ? $gastosArray['Plin']->total : 0;
                
                $datos['Yape/Plin'] = [
                    'ingreso' => $ingresoYape + $ingresoPlin,
                    'gasto' => $gastoYape + $gastoPlin,
                    'total' => ($ingresoYape + $ingresoPlin) - ($gastoYape + $gastoPlin)
                ];
                continue;
            }
            
            // Saltar Plin porque ya se procesó con Yape
            if ($metodo === 'Plin') {
                continue;
            }
            
            // Tarjeta, Tarjeta crédito, QR y Transferencia se agrupan como "Cuenta Bancaria"
            if ($metodo === 'Tarjeta') {
                $ingresoTarjeta = isset($ingresosArray['Tarjeta']) ? $ingresosArray['Tarjeta']->total : 0;
                $ingresoTarjetaCredito = isset($ingresosArray['Tarjeta crédito']) ? $ingresosArray['Tarjeta crédito']->total : 0;
                $ingresoQR = isset($ingresosArray['QR']) ? $ingresosArray['QR']->total : 0;
                $ingresoTransferencia = isset($ingresosArray['Transferencia']) ? $ingresosArray['Transferencia']->total : 0;
                
                $gastoTarjeta = isset($gastosArray['Tarjeta']) ? $gastosArray['Tarjeta']->total : 0;
                $gastoTarjetaCredito = isset($gastosArray['Tarjeta crédito']) ? $gastosArray['Tarjeta crédito']->total : 0;
                $gastoQR = isset($gastosArray['QR']) ? $gastosArray['QR']->total : 0;
                $gastoTransferencia = isset($gastosArray['Transferencia']) ? $gastosArray['Transferencia']->total : 0;
                
                $datos['Cuenta Bancaria'] = [
                    'ingreso' => $ingresoTarjeta + $ingresoTarjetaCredito + $ingresoQR + $ingresoTransferencia,
                    'gasto' => $gastoTarjeta + $gastoTarjetaCredito + $gastoQR + $gastoTransferencia,
                    'total' => ($ingresoTarjeta + $ingresoTarjetaCredito + $ingresoQR + $ingresoTransferencia) - 
                            ($gastoTarjeta + $gastoTarjetaCredito + $gastoQR + $gastoTransferencia)
                ];
                continue;
            }
            
            // Saltar estos porque ya se procesaron con Tarjeta
            if (in_array($metodo, ['Plin', 'Tarjeta crédito', 'QR', 'Transferencia'])) {
                continue;
            }
            
            // Efectivo
            $ingreso = isset($ingresosArray[$metodo]) ? $ingresosArray[$metodo]->total : 0;
            $gasto = isset($gastosArray[$metodo]) ? $gastosArray[$metodo]->total : 0;
            
            $datos[$metodo] = [
                'ingreso' => $ingreso,
                'gasto' => $gasto,
                'total' => $ingreso - $gasto
            ];
        }
        
        return $datos;
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