<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PenalidadController extends Controller
{
    /**
     * Mostrar las penalizaciones de un cliente específico
     */
    public function index($id_estadia)
    {
        $penalizaciones = DB::table('fact_penalidad as fp')
            ->join('dim_met_pago as dmp', 'dmp.id_met_pago', '=', 'fp.id_met_pago')
            ->where('fp.id_estadia', $id_estadia)
            ->select(
                'fp.id_penalidad',
                'fp.monto',
                'dmp.met_pago',
                'fp.created_at'
            )
            ->orderBy('fp.created_at', 'desc')
            ->get();

        return response()->json($penalizaciones);
    }

    /**
     * Registrar una nueva penalización
     */
    public function store(Request $request, $id_estadia)
    {
        $request->validate([
            'monto' => 'required|numeric|min:0',
            'id_met_pago' => 'required|exists:dim_met_pago,id_met_pago'
        ]);

        try {
            // Crear la penalización
            DB::table('fact_penalidad')->insert([
                'id_estadia' => $id_estadia,
                'monto' => $request->monto,
                'id_met_pago' => $request->id_met_pago,
                'created_at' => now()
            ]);

            // Actualizar observaciones del cliente
            $this->actualizarObservaciones($id_estadia, $request->monto);

            return response()->json([
                'success' => true,
                'message' => 'Penalización registrada correctamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar la penalización: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener métodos de pago disponibles
     */
    public function getMetodosPago()
    {
        $metodos = DB::table('dim_met_pago')
            ->select('id_met_pago', 'met_pago')
            ->orderBy('met_pago')
            ->get();

        return response()->json($metodos);
    }

    /**
     * Actualizar las observaciones del cliente con la penalización
     */
    private function actualizarObservaciones($id_estadia, $monto)
    {
        $cliente = DB::table('fact_registro_clientes')->where('id_estadia', $id_estadia)->first();
        $observacionActual = $cliente->obs ?? '';
        
        $nuevaObservacion = trim($observacionActual . 
            "\n[" . now()->format('d/m/Y H:i') . "] DAÑO: S/" . number_format($monto, 2));

        DB::table('fact_registro_clientes')
            ->where('id_estadia', $id_estadia)
            ->update(['obs' => $nuevaObservacion]);
    }

    /**
     * Eliminar una penalización (opcional)
     */
    public function destroy($id_penalidad)
    {
        try {
            $penalidad = DB::table('fact_penalidad')->where('id_penalidad', $id_penalidad)->first();
            
            if (!$penalidad) {
                return response()->json([
                    'success' => false,
                    'message' => 'Penalización no encontrada'
                ], 404);
            }

            DB::table('fact_penalidad')->where('id_penalidad', $id_penalidad)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Penalización eliminada correctamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la penalización: ' . $e->getMessage()
            ], 500);
        }
    }
}