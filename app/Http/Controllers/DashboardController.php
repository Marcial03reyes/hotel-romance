<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Routing\Controller as BaseController;

class DashboardController extends BaseController
{
    public function index()
    {
        // Datos iniciales para "Esta Semana"
        $ingresosPorTurno = $this->getIngresosPorTurno('esta_semana');
        $ocupacionSemanal = $this->getOcupacionSemanal('esta_semana');
        
        // Debug temporal - puedes quitar esto después
        // \Log::info('Ingresos por turno:', $ingresosPorTurno);
        // \Log::info('Ocupación semanal:', $ocupacionSemanal);
        
        return view('dashboard', compact('ingresosPorTurno', 'ocupacionSemanal'));
    }
    
    public function getIngresosTurnoDinamico(Request $request)
    {
        $periodo = $request->get('periodo', 'esta_semana');
        $datos = $this->getIngresosPorTurno($periodo);
        
        return response()->json($datos);
    }
    
    public function getOcupacionDinamica(Request $request)
    {
        $periodo = $request->get('periodo', 'esta_semana');
        $datos = $this->getOcupacionSemanal($periodo);
        
        return response()->json($datos);
    }
    
    private function getIngresosPorTurno($periodo)
    {
        [$fechaInicio, $fechaFin] = $this->calcularFechas($periodo);
        
        // Convertir a string para la consulta SQL
        $fechaInicioStr = $fechaInicio->format('Y-m-d');
        $fechaFinStr = $fechaFin->format('Y-m-d');
        
        \Log::info("Consultando ingresos desde {$fechaInicioStr} hasta {$fechaFinStr}");
        
        $ingresosPorTurno = DB::table('fact_pago_hab as fph')
            ->join('fact_registro_clientes as frc', 'frc.id_estadia', '=', 'fph.id_estadia')
            ->whereBetween('frc.fecha_ingreso', [$fechaInicioStr, $fechaFinStr])
            ->selectRaw('
                DAYOFWEEK(frc.fecha_ingreso) as dia_semana,
                frc.turno,
                SUM(fph.monto) as total_ingresos
            ')
            ->groupBy('dia_semana', 'frc.turno')
            ->get();
        
        \Log::info("Resultados encontrados: " . $ingresosPorTurno->count());
        
        // Organizar datos
        $diasSemana = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];
        $mapeoTurnos = [0 => 'Día', 1 => 'Noche'];
        
        // Inicializar con ceros
        $datosTurnos = [
            'Día' => [0, 0, 0, 0, 0, 0, 0],
            'Noche' => [0, 0, 0, 0, 0, 0, 0]
        ];
        
        foreach($ingresosPorTurno as $ingreso) {
            // DAYOFWEEK retorna 1=Domingo, 2=Lunes, ..., 7=Sábado
            // Necesitamos convertir a índice 0-6 (Lun-Dom)
            $diaMySQL = $ingreso->dia_semana; // 1-7
            
            // Convertir: 1(Dom)→6, 2(Lun)→0, 3(Mar)→1, ..., 7(Sab)→5
            if ($diaMySQL == 1) {
                $indiceDia = 6; // Domingo al final
            } else {
                $indiceDia = $diaMySQL - 2; // Lunes=0, Martes=1, etc.
            }
            
            $nombreTurno = $mapeoTurnos[$ingreso->turno] ?? null;
            
            if($nombreTurno && isset($datosTurnos[$nombreTurno][$indiceDia])) {
                $datosTurnos[$nombreTurno][$indiceDia] = (float) $ingreso->total_ingresos;
            }
        }
        
        return [
            'labels' => $diasSemana,
            'dia' => $datosTurnos['Día'],
            'noche' => $datosTurnos['Noche']
        ];
    }
    
    private function getOcupacionSemanal($periodo)
    {
        [$fechaInicio, $fechaFin] = $this->calcularFechas($periodo);
        
        $fechaInicioStr = $fechaInicio->format('Y-m-d');
        $fechaFinStr = $fechaFin->format('Y-m-d');
        
        \Log::info("Consultando ocupación desde {$fechaInicioStr} hasta {$fechaFinStr}");
        
        $ocupacion = DB::table('fact_registro_clientes')
            ->whereBetween('fecha_ingreso', [$fechaInicioStr, $fechaFinStr])
            ->selectRaw('
                DAYOFWEEK(fecha_ingreso) as dia_semana,
                COUNT(*) as total_habitaciones
            ')
            ->groupBy('dia_semana')
            ->get()
            ->pluck('total_habitaciones', 'dia_semana');
        
        \Log::info("Ocupación encontrada: " . $ocupacion->count() . " días");
        
        $diasSemana = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];
        $datosOcupacion = [0, 0, 0, 0, 0, 0, 0]; // Inicializar con ceros
        
        // Mapear correctamente los días de MySQL a índices del array
        foreach ($ocupacion as $diaMySQL => $cantidad) {
            // Convertir igual que arriba
            if ($diaMySQL == 1) {
                $indiceDia = 6; // Domingo
            } else {
                $indiceDia = $diaMySQL - 2; // Lunes=0, etc.
            }
            
            if ($indiceDia >= 0 && $indiceDia <= 6) {
                $datosOcupacion[$indiceDia] = (int) $cantidad;
            }
        }
        
        return [
            'labels' => $diasSemana,
            'ocupacion' => $datosOcupacion
        ];
    }
    
    private function calcularFechas($periodo)
    {
        switch($periodo) {
            case 'semana_anterior':
                $inicio = Carbon::now()->subWeek()->startOfWeek(Carbon::MONDAY);
                $fin = Carbon::now()->subWeek()->endOfWeek(Carbon::SUNDAY);
                break;
                
            case 'este_mes':
                $inicio = Carbon::now()->startOfMonth();
                $fin = Carbon::now()->endOfMonth();
                break;
                
            case 'esta_semana':
            default:
                $inicio = Carbon::now()->startOfWeek(Carbon::MONDAY);
                $fin = Carbon::now()->endOfWeek(Carbon::SUNDAY);
                break;
        }
        
        \Log::info("Período '{$periodo}': {$inicio->format('Y-m-d')} a {$fin->format('Y-m-d')}");
        
        return [$inicio, $fin];
    }
}