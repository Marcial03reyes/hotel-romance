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
        
        return view('gastos-fijos.index', compact('gastosFijos'));
    }

    // Mostrar formulario para agregar nuevo servicio
    public function create()
    {
        return view('gastos-fijos.create');
    }

    public function edit($id)
    {
        $gastoFijo = FactGastoFijo::findOrFail($id);
        return view('gastos-fijos.edit', compact('gastoFijo'));
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

    public function update(Request $request, $id)
    {
        $gastoFijo = FactGastoFijo::findOrFail($id);
        
        $validated = $request->validate([
            'nombre_servicio' => [
                'required',
                'string',
                'max:100',
                'unique:fact_gastos_fijos,nombre_servicio,' . $id . ',id_gasto_fijo',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s\-]+$/'
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
            $gastoFijo->update($validated);
            
            return redirect()->route('gastos-fijos.index')
                ->with('success', 'Servicio actualizado correctamente');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al actualizar el servicio. Inténtalo de nuevo.');
        }
    }

    public function historial($id)
    {
        $gastoFijo = FactGastoFijo::findOrFail($id);
        $pagos = $gastoFijo->pagos()->orderBy('fecha_pago', 'desc')->get();
        $metodos = DimMetPago::orderBy('id_met_pago')->get();
        
        return view('gastos-fijos.historial', compact('gastoFijo', 'pagos', 'metodos'));
    }

    public function createPago($id)
    {
        $gastoFijo = FactGastoFijo::findOrFail($id);
        $metodos = DimMetPago::orderBy('id_met_pago')->get();
        
        return view('gastos-fijos.create-pago', compact('gastoFijo', 'metodos'));
    }

    public function storePago(Request $request, $id)
    {
        $gastoFijo = FactGastoFijo::findOrFail($id);
        
        $validated = $request->validate([
            'monto_pagado' => [
                'required',
                'numeric',
                'min:0.01',
                'max:999999.99'
            ],
            'id_met_pago' => 'required|exists:dim_met_pago,id_met_pago',
            'fecha_pago' => 'required|date',
            'comprobante' => [
                'nullable',
                'mimes:jpeg,jpg,png,pdf',
                'max:10240'
            ]
        ], [
            'monto_pagado.required' => 'El monto pagado es obligatorio.',
            'monto_pagado.min' => 'El monto debe ser mayor a 0.',
            'monto_pagado.max' => 'El monto no puede exceder 999,999.99.',
            'id_met_pago.required' => 'El método de pago es obligatorio.',
            'id_met_pago.exists' => 'El método de pago seleccionado no es válido.',
            'fecha_pago.required' => 'La fecha de pago es obligatoria.',
            'fecha_pago.date' => 'La fecha de pago debe ser válida.',
            'comprobante.mimes' => 'El comprobante debe ser PDF, PNG o JPG.',
            'comprobante.max' => 'El archivo no puede ser mayor a 10MB.',
        ]);

        try {
            $path = null;
            if ($request->hasFile('comprobante')) {
                $file = $request->file('comprobante');
                $fileName = 'gasto_fijo_' . $id . '_' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('comprobantes/gastos_fijos', $fileName, 'public');
            }
            
            FactPagoGastoFijo::create([
                'id_gasto_fijo' => $id,
                'monto_pagado' => $validated['monto_pagado'],
                'id_met_pago' => $validated['id_met_pago'],
                'comprobante' => $path,
                'fecha_pago' => $validated['fecha_pago'],
            ]);

            return redirect()->route('gastos-fijos.historial', $id)
                ->with('success', 'Pago registrado correctamente');

        } catch (\Exception $e) {
            \Log::error('Error al registrar pago de gasto fijo: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al registrar el pago. Inténtalo de nuevo.');
        }
    }

    public function editPago($id, $pagoId)
    {
        $gastoFijo = FactGastoFijo::findOrFail($id);
        $pago = FactPagoGastoFijo::where('id_pago_gasto', $pagoId)
                            ->where('id_gasto_fijo', $id)
                            ->firstOrFail();
        $metodos = DimMetPago::orderBy('id_met_pago')->get();
        
        return view('gastos-fijos.edit-pago', compact('gastoFijo', 'pago', 'metodos'));
    }

    public function updatePago(Request $request, $id, $pagoId)
    {
        $gastoFijo = FactGastoFijo::findOrFail($id);
        $pago = FactPagoGastoFijo::where('id_pago_gasto', $pagoId)
                            ->where('id_gasto_fijo', $id)
                            ->firstOrFail();
        
        $validated = $request->validate([
            'monto_pagado' => [
                'required',
                'numeric',
                'min:0.01',
                'max:999999.99'
            ],
            'id_met_pago' => 'required|exists:dim_met_pago,id_met_pago',
            'fecha_pago' => 'required|date',
            'comprobante' => [
                'nullable',
                'mimes:jpeg,jpg,png,pdf',
                'max:10240'
            ]
        ], [
            'monto_pagado.required' => 'El monto pagado es obligatorio.',
            'monto_pagado.min' => 'El monto debe ser mayor a 0.',
            'monto_pagado.max' => 'El monto no puede exceder 999,999.99.',
            'id_met_pago.required' => 'El método de pago es obligatorio.',
            'id_met_pago.exists' => 'El método de pago seleccionado no es válido.',
            'fecha_pago.required' => 'La fecha de pago es obligatoria.',
            'fecha_pago.date' => 'La fecha de pago debe ser válida.',
            'comprobante.mimes' => 'El comprobante debe ser PDF, PNG o JPG.',
            'comprobante.max' => 'El archivo no puede ser mayor a 10MB.',
        ]);

        try {
            // Si se sube nuevo comprobante, eliminar el anterior
            if ($request->hasFile('comprobante')) {
                if ($pago->comprobante) {
                    Storage::disk('public')->delete($pago->comprobante);
                }
                
                $file = $request->file('comprobante');
                $fileName = 'gasto_fijo_' . $id . '_' . time() . '.' . $file->getClientOriginalExtension();
                $validated['comprobante'] = $file->storeAs('comprobantes/gastos_fijos', $fileName, 'public');
            }
            
            $dataToUpdate = [
                'monto_pagado' => $validated['monto_pagado'],
                'id_met_pago' => $validated['id_met_pago'],
                'fecha_pago' => $validated['fecha_pago'],
            ];

            // Solo actualizar comprobante si se subió uno nuevo
            if (isset($validated['comprobante'])) {
                $dataToUpdate['comprobante'] = $validated['comprobante'];
            }

            $pago->update($dataToUpdate);

            return redirect()->route('gastos-fijos.historial', $id)
                ->with('success', 'Pago actualizado correctamente');

        } catch (\Exception $e) {
            \Log::error('Error al actualizar pago: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al actualizar el pago. Inténtalo de nuevo.');
        }
    }

    public function destroyPago($id, $pagoId)
    {
        try {
            $pago = FactPagoGastoFijo::where('id_pago_gasto', $pagoId)
                                ->where('id_gasto_fijo', $id)
                                ->firstOrFail();
            
            // Eliminar archivo de comprobante si existe
            if ($pago->comprobante) {
                Storage::disk('public')->delete($pago->comprobante);
            }
            
            $pago->delete();

            return redirect()->route('gastos-fijos.historial', $id)
                ->with('success', 'Pago eliminado correctamente');
                
        } catch (\Exception $e) {
            \Log::error('Error al eliminar pago: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error al eliminar el pago.');
        }
    }

    // Ver comprobante de pago
    public function verComprobante($id, $pagoId)
    {
        $pago = FactPagoGastoFijo::where('id_pago_gasto', $pagoId)
                            ->where('id_gasto_fijo', $id)
                            ->firstOrFail();          
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
}