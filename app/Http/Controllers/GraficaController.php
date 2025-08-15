<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
     * Obtener gastos específicos del HOTEL (gastos generales + compras internas)
     */
    private function getGastosMensualesHotel()
    {
        // 1. Gastos generales del hotel
        $gastosGenerales = DB::table('fact_gastos_generales as fg')
            ->join('dim_tipo_gasto as tg', 'tg.id_tipo_gasto', '=', 'fg.id_tipo_gasto')
            ->selectRaw('
                tg.nombre as tipo_gasto,
                MONTH(fg.fecha_gasto) as mes,
                YEAR(fg.fecha_gasto) as anio,
                SUM(fg.monto) as total_mes
            ')
            ->whereRaw('fg.fecha_gasto >= DATE_SUB(NOW(), INTERVAL 12 MONTH)')
            ->groupBy('tg.nombre', 'mes', 'anio')
            ->get();

        // 2. Compras de productos internos del hotel (toallas, detergente, etc.)
        $comprasInternas = DB::table('fact_compra_interna as fci')
            ->join('dim_productos_hotel as dph', 'dph.id_prod_hotel', '=', 'fci.id_prod_bod')
            ->selectRaw('
                dph.nombre as tipo_gasto,
                MONTH(fci.fecha_compra) as mes,
                YEAR(fci.fecha_compra) as anio,
                SUM(fci.cantidad * fci.precio_unitario) as total_mes
            ')
            ->whereRaw('fci.fecha_compra >= DATE_SUB(NOW(), INTERVAL 12 MONTH)')
            ->groupBy('dph.nombre', 'mes', 'anio')
            ->get();

        // 3. Combinar ambos tipos de gastos del hotel
        $todosLosGastosHotel = $gastosGenerales->concat($comprasInternas);
        
        return $this->procesarGastosPorTipo($todosLosGastosHotel);
    }
    
    /**
     * Obtener gastos específicos de la BODEGA (compras de productos para venta)
     */
    private function getGastosMensualesBodega()
    {
        // Solo compras de productos que se venden a huéspedes (bodega/inventario)
        $compras = DB::table('fact_compra_bodega as fcb')
            ->join('dim_productos_bodega as dpb', 'dpb.id_prod_bod', '=', 'fcb.id_prod_bod')
            ->selectRaw('
                dpb.nombre as producto,
                MONTH(fcb.fecha_compra) as mes,
                YEAR(fcb.fecha_compra) as anio,
                SUM(fcb.cantidad * fcb.precio_unitario) as total_mes
            ')
            ->whereRaw('fcb.fecha_compra >= DATE_SUB(NOW(), INTERVAL 12 MONTH)')
            ->groupBy('dpb.nombre', 'mes', 'anio')
            ->orderBy('anio')
            ->orderBy('mes')
            ->get();
            
        return $this->procesarGastosPorTipo($compras, 'producto');
    }
    
    /**
     * Procesar datos de gastos por tipo/producto
     */
    private function procesarGastosPorTipo($gastos, $campo = 'tipo_gasto')
    {
        // Organizar datos por tipo de gasto/producto y mes
        $gastosPorTipo = [];
        $meses = [];
        
        foreach ($gastos as $gasto) {
            $mesNombre = $this->getNombreMesCorto($gasto->mes);
            $meses[$gasto->mes] = $mesNombre;
            
            $tipoKey = $campo === 'producto' ? $gasto->producto : $gasto->tipo_gasto;
            
            if (!isset($gastosPorTipo[$tipoKey])) {
                $gastosPorTipo[$tipoKey] = [];
            }
            
            $gastosPorTipo[$tipoKey][$gasto->mes] = $gasto->total_mes;
        }
        
        // Ordenar meses en orden ascendente (más antiguo primero)
        ksort($meses);
        
        // Calcular totales por mes
        $totalesPorMes = [];
        foreach ($meses as $mesNum => $mesNombre) {
            $total = 0;
            foreach ($gastosPorTipo as $tipo => $mesesData) {
                $total += $mesesData[$mesNum] ?? 0;
            }
            $totalesPorMes[$mesNum] = $total;
        }
        
        return [
            'gastosPorTipo' => $gastosPorTipo,
            'meses' => $meses,
            'totalesPorMes' => $totalesPorMes
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
     * Obtener datos de ingresos y gastos del HOTEL para gráficos
     */
    private function getDatosHotel()
    {
        // Ingresos del hotel (solo habitaciones)
        $ingresosHabitacion = DB::table('fact_pago_hab as fph')
            ->join('fact_registro_clientes as frc', 'frc.id_estadia', '=', 'fph.id_estadia')
            ->selectRaw('
                MONTH(frc.fecha_ingreso) as mes,
                YEAR(frc.fecha_ingreso) as anio,
                SUM(fph.monto) as total_ingresos
            ')
            ->whereRaw('frc.fecha_ingreso >= DATE_SUB(NOW(), INTERVAL 12 MONTH)')
            ->groupBy('mes', 'anio')
            ->orderBy('anio')
            ->orderBy('mes')
            ->get();

        // Gastos del hotel: generales + compras internas
        $gastosGenerales = DB::table('fact_gastos_generales')
            ->selectRaw('
                MONTH(fecha_gasto) as mes,
                YEAR(fecha_gasto) as anio,
                SUM(monto) as total_gastos
            ')
            ->whereRaw('fecha_gasto >= DATE_SUB(NOW(), INTERVAL 12 MONTH)')
            ->groupBy('mes', 'anio')
            ->get();

        $gastosComprasInternas = DB::table('fact_compra_interna')
            ->selectRaw('
                MONTH(fecha_compra) as mes,
                YEAR(fecha_compra) as anio,
                SUM(cantidad * precio_unitario) as total_gastos
            ')
            ->whereRaw('fecha_compra >= DATE_SUB(NOW(), INTERVAL 12 MONTH)')
            ->groupBy('mes', 'anio')
            ->get();

        // Combinar ambos tipos de gastos del hotel
        $gastosTotalesHotel = $this->combinarGastos($gastosGenerales, $gastosComprasInternas);
        
        return $this->combinarDatos($ingresosHabitacion, $gastosTotalesHotel);
    }
    
    /**
     * Obtener datos de ingresos y gastos de la BODEGA para gráficos
     */
    private function getDatosBodega()
    {
        // Ingresos de bodega (ventas de productos a huéspedes)
        $ingresos = DB::table('fact_pago_prod as fpp')
            ->join('fact_registro_clientes as frc', 'frc.id_estadia', '=', 'fpp.id_estadia')
            ->selectRaw('
                MONTH(frc.fecha_ingreso) as mes,
                YEAR(frc.fecha_ingreso) as anio,
                SUM(fpp.cantidad * fpp.precio_unitario) as total_ingresos
            ')
            ->whereRaw('frc.fecha_ingreso >= DATE_SUB(NOW(), INTERVAL 12 MONTH)')
            ->groupBy('mes', 'anio')
            ->orderBy('anio')
            ->orderBy('mes')
            ->get();
            
        // Gastos de bodega (compras de productos para venta)
        $gastos = DB::table('fact_compra_bodega')
            ->selectRaw('
                MONTH(fecha_compra) as mes,
                YEAR(fecha_compra) as anio,
                SUM(cantidad * precio_unitario) as total_gastos
            ')
            ->whereRaw('fecha_compra >= DATE_SUB(NOW(), INTERVAL 12 MONTH)')
            ->groupBy('mes', 'anio')
            ->orderBy('anio')
            ->orderBy('mes')
            ->get();
            
        return $this->combinarDatos($ingresos, $gastos);
    }
    
    /**
     * Combinar datos de ingresos y gastos por mes
     */
    private function combinarDatos($ingresos, $gastos)
    {
        $mesesCompletos = [];
        
        // Generar array de últimos 12 meses
        for ($i = 11; $i >= 0; $i--) {
            $fecha = Carbon::now()->subMonths($i);
            $mesKey = $fecha->month . '-' . $fecha->year;
            $mesesCompletos[$mesKey] = [
                'mes' => $fecha->month,
                'anio' => $fecha->year,
                'nombre' => $this->getNombreMesCorto($fecha->month) . ' ' . $fecha->year,
                'ingresos' => 0,
                'gastos' => 0
            ];
        }
        
        // Llenar ingresos
        foreach ($ingresos as $ingreso) {
            $key = $ingreso->mes . '-' . $ingreso->anio;
            if (isset($mesesCompletos[$key])) {
                $mesesCompletos[$key]['ingresos'] = (float) $ingreso->total_ingresos;
            }
        }
        
        // Llenar gastos
        foreach ($gastos as $gasto) {
            $key = $gasto->mes . '-' . $gasto->anio;
            if (isset($mesesCompletos[$key])) {
                $mesesCompletos[$key]['gastos'] = (float) $gasto->total_gastos;
            }
        }
        
        return array_values($mesesCompletos);
    }

    /**
     * Combinar dos arrays de gastos sumando los totales por mes
     */
    private function combinarGastos($gastos1, $gastos2)
    {
        $gastosCombinados = [];
        
        // Procesar primeros gastos
        foreach ($gastos1 as $gasto) {
            $key = $gasto->mes . '-' . $gasto->anio;
            $gastosCombinados[$key] = (object)[
                'mes' => $gasto->mes,
                'anio' => $gasto->anio,
                'total_gastos' => $gasto->total_gastos
            ];
        }
        
        // Sumar segundos gastos
        foreach ($gastos2 as $gasto) {
            $key = $gasto->mes . '-' . $gasto->anio;
            if (isset($gastosCombinados[$key])) {
                $gastosCombinados[$key]->total_gastos += $gasto->total_gastos;
            } else {
                $gastosCombinados[$key] = (object)[
                    'mes' => $gasto->mes,
                    'anio' => $gasto->anio,
                    'total_gastos' => $gasto->total_gastos
                ];
            }
        }
        
        return collect(array_values($gastosCombinados));
    }
    
    /**
     * Obtener nombre completo del mes
     */
    private function getNombreMes($numeroMes)
    {
        $meses = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];
        
        return $meses[$numeroMes] ?? 'Desconocido';
    }
    
    /**
     * Obtener nombre abreviado del mes (3 letras)
     */
    private function getNombreMesCorto($numeroMes)
    {
        $meses = [
            1 => 'Ene', 2 => 'Feb', 3 => 'Mar', 4 => 'Abr',
            5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Ago',
            9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dic'
        ];
        
        return $meses[$numeroMes] ?? 'N/A';
    }
}