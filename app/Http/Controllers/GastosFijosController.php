<?php
// app/Http/Controllers/GastosFijosController.php

namespace App\Http\Controllers;

use App\Models\FactGastoFijo;
use App\Models\FactPagoGastoFijo;
use App\Models\DimMetPago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class GastosFijosController extends Controller
{
    // Mostrar la tabla de gastos fijos
    public function index()
    {
        $gastosFijos = FactGastoFijo::activos()->orderBy('nombre_servicio')->get();
        
        // Generar meses desde agosto 2025 hasta diciembre 2025 (expandible)
        $meses = $this->generarMesesTabla();

        // Obtener métodos de pago para el modal
        $metodosPago = DimMetPago::orderBy('id_met_pago')->get();
        
        return view('gastos-fijos.index', compact('gastosFijos', 'meses', 'metodosPago'));
    }

    // Mostrar formulario para agregar nuevo servicio
    public function create()
    {
        return view('gastos-fijos.create');
    }

    // Guardar nuevo servicio
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre_servicio' => [
                'required',
                'string',
                'max:100',
                'unique:fact_gastos_fijos,nombre_servicio',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s\-]+$/' // Permitir caracteres en español
            ],
            'dia_vencimiento' => [
                'required',
                'integer',
                'min:1',
                'max:31'
            ],
            'monto_fijo' => [
                'required',
                'numeric',
                'min:0',
                'max:999999.99'
            ]
        ], [
            'nombre_servicio.required' => 'El nombre del servicio es obligatorio.',
            'nombre_servicio.unique' => 'Ya existe un servicio con este nombre.',
            'nombre_servicio.regex' => 'El nombre solo puede contener letras, espacios y guiones.',
            'dia_vencimiento.required' => 'El día de vencimiento es obligatorio.',
            'dia_vencimiento.min' => 'El día debe ser entre 1 y 31.',
            'dia_vencimiento.max' => 'El día debe ser entre 1 y 31.',
            'monto_fijo.required' => 'El monto fijo es obligatorio.',
            'monto_fijo.min' => 'El monto debe ser mayor a 0.',
            'monto_fijo.max' => 'El monto no puede exceder 999,999.99.'
        ]);

        try {
            FactGastoFijo::create($validated);
            
            return redirect()->route('gastos-fijos.index')
                ->with('success', 'Servicio agregado correctamente');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al agregar el servicio. Inténtalo de nuevo.');
        }
    }

    // Registrar pago de un servicio
    public function registrarPago(Request $request)
    {
        $validated = $request->validate([
            'id_gasto_fijo' => 'required|exists:fact_gastos_fijos,id_gasto_fijo',
            'mes' => 'required|integer|min:1|max:12',
            'anio' => 'required|integer|min:2024|max:2030',
            'monto_pagado' => [
                'required',
                'numeric',
                'min:0.01',
                'max:999999.99'
            ],
            'id_met_pago' => 'required|exists:dim_met_pago,id_met_pago',
            'fecha_pago_real' => 'required|date',
            'turno' => 'required|in:0,1',
            'comprobante' => [
                'nullable', 
                'mimes:jpeg,jpg,png,pdf',
                'max:10240' 
            ]
        ], [
            'id_gasto_fijo.required' => 'Servicio no válido.',
            'id_gasto_fijo.exists' => 'El servicio seleccionado no existe.',
            'monto_pagado.required' => 'El monto pagado es obligatorio.',
            'monto_pagado.min' => 'El monto debe ser mayor a 0.',
            'id_met_pago.required' => 'El método de pago es obligatorio.', 
            'id_met_pago.exists' => 'El método de pago seleccionado no es válido.', 
            'fecha_pago_real.required' => 'La fecha de pago es obligatoria.', 
            'fecha_pago_real.date' => 'La fecha de pago debe ser válida.', 
            'comprobante.mimes' => 'El comprobante debe ser PDF, PNG o JPG.',
            'comprobante.max' => 'El archivo no puede ser mayor a 10MB.',
            'turno.required' => 'El turno es obligatorio.',  
            'turno.in' => 'El turno debe ser DÍA o NOCHE.', 
        ]);

        // Verificar que no exista ya un pago para este servicio en este mes
        $pagoExistente = FactPagoGastoFijo::where('id_gasto_fijo', $validated['id_gasto_fijo'])
            ->where('mes', $validated['mes'])
            ->where('anio', $validated['anio'])
            ->first();

        if ($pagoExistente) {
            return response()->json([
                'success' => false,
                'message' => 'Ya existe un pago registrado para este servicio en este mes'
            ], 422);
        }

        try {
            $path = null;
            if ($request->hasFile('comprobante')) {
                $file = $request->file('comprobante');
                $fileName = 'gasto_fijo_' . $validated['id_gasto_fijo'] . '_' . $validated['mes'] . '_' . $validated['anio'] . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('comprobantes/gastos_fijos', $fileName, 'public');
            }
            
            // Crear el pago
            $pago = FactPagoGastoFijo::create([
                'id_gasto_fijo' => $validated['id_gasto_fijo'],
                'mes' => $validated['mes'],
                'anio' => $validated['anio'],
                'monto_pagado' => $validated['monto_pagado'],
                'id_met_pago' => $validated['id_met_pago'], 
                'comprobante' => $path,
                'fecha_pago' => $validated['fecha_pago_real'],
                'turno' => $validated['turno'] 
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pago registrado correctamente',
                'pago' => $pago->load(['gastoFijo', 'metodoPago']) 
            ]);

        } catch (\Exception $e) {
            \Log::error('Error al registrar pago de gasto fijo: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor. Inténtalo de nuevo.'
            ], 500);
        }
    }

    // Ver comprobante de pago
    public function verComprobante(FactPagoGastoFijo $pago)
    {
        try {
            if (!$pago->comprobante) {
                abort(404, 'No hay comprobante disponible');
            }

            $pathToFile = storage_path('app/public/' . $pago->comprobante);
            
            if (!file_exists($pathToFile)) {
                abort(404, 'Archivo no encontrado');
            }

            $extension = strtolower(pathinfo($pago->comprobante, PATHINFO_EXTENSION));
            $mimeTypes = [
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'pdf' => 'application/pdf'
            ];

            if (!isset($mimeTypes[$extension])) {
                abort(404, 'Tipo de archivo no soportado');
            }

            return response()->file($pathToFile, [
                'Content-Type' => $mimeTypes[$extension],
                'Content-Disposition' => $extension === 'pdf' ? 'inline' : 'inline'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error al mostrar comprobante: ' . $e->getMessage());
            abort(500, 'Error al cargar el comprobante');
        }
    }

    /**
     * Eliminar un servicio
     */
    public function destroy(FactGastoFijo $gastoFijo)
    {
        try {
            // Eliminar comprobantes físicos
            foreach ($gastoFijo->pagos as $pago) {
                if ($pago->comprobante) {
                    Storage::disk('public')->delete($pago->comprobante);
                }
            }

            $gastoFijo->delete();

            return redirect()->route('gastos-fijos.index')
                ->with('success', 'Servicio eliminado correctamente');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al eliminar el servicio.');
        }
    }

    /**
     * Generar array de meses para la tabla
     */
    private function generarMesesTabla(): array
    {
        $meses = [];
        $anioActual = 2025;
        
        // Desde agosto 2025 hasta diciembre 2025 (puedes expandir según necesidad)
        for ($mes = 8; $mes <= 12; $mes++) {
            $meses[] = [
                'numero' => $mes,
                'anio' => $anioActual,
                'nombre' => $this->getNombreMesCorto($mes),
                'nombre_completo' => $this->getNombreMes($mes)
            ];
        }
        
        return $meses;
    }

    /**
     * Obtener nombre completo del mes
     */
    private function getNombreMes(int $numeroMes): string
    {
        $meses = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];
        
        return $meses[$numeroMes] ?? 'Desconocido';
    }

    /**
     * Obtener nombre abreviado del mes
     */
    private function getNombreMesCorto(int $numeroMes): string
    {
        $meses = [
            1 => 'ENE', 2 => 'FEB', 3 => 'MAR', 4 => 'ABR',
            5 => 'MAY', 6 => 'JUN', 7 => 'JUL', 8 => 'AGO',
            9 => 'SEP', 10 => 'OCT', 11 => 'NOV', 12 => 'DIC'
        ];
        
        return $meses[$numeroMes] ?? 'N/A';
    }
}