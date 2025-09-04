<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\FactSunat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FactSunatController extends Controller
{
    public function index(Request $request)
    {
        $query = FactSunat::query();

        // Filtro por mes
        if ($request->filled('mes')) {
            $mesAnio = $request->input('mes'); // formato: "2025-01"
            $query->whereYear('fecha_comprobante', substr($mesAnio, 0, 4))
                ->whereMonth('fecha_comprobante', substr($mesAnio, 5, 2));
        }

        // Filtro por tipo de comprobante
        if ($request->filled('tipo')) {
            $query->where('tipo_comprobante', $request->input('tipo'));
        }

        $comprobantes = $query->orderByDesc('fecha_comprobante')
                            ->orderByDesc('id_sunat')
                            ->get();

        // Obtener solo los meses que tienen datos reales en la tabla
        $mesesConDatos = FactSunat::selectRaw('YEAR(fecha_comprobante) as anio, MONTH(fecha_comprobante) as mes')
                                ->distinct()
                                ->orderBy('anio', 'desc')
                                ->orderBy('mes', 'desc')
                                ->get();

        return view('sunat.index', compact('comprobantes', 'mesesConDatos'));
    }

    public function create()
    {
        return view('sunat.create');
    }

    public function store(Request $request)
    {
        $rules = [
            'tipo_comprobante' => 'required|in:BOLETA,FACTURA,NINGUNO',
            'monto' => 'required|numeric|min:0|max:99999999.99',
            'fecha_comprobante' => 'required|date',
            'archivo_comprobante' => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:5120'
        ];

        // Validación condicional: Si tipo_comprobante es BOLETA o FACTURA, codigo_comprobante es obligatorio
        if (in_array($request->tipo_comprobante, ['BOLETA', 'FACTURA'])) {
            $rules['codigo_comprobante'] = 'required|string|max:50';
        } else {
            $rules['codigo_comprobante'] = 'nullable|string|max:50';
        }

        $validated = $request->validate($rules, [
            'tipo_comprobante.required' => 'El tipo de comprobante es obligatorio',
            'tipo_comprobante.in' => 'El tipo de comprobante debe ser BOLETA, FACTURA o NINGUNO',
            'codigo_comprobante.required' => 'El código de comprobante es obligatorio cuando seleccionas BOLETA o FACTURA',
            'codigo_comprobante.max' => 'El código de comprobante no puede tener más de 50 caracteres',
            'monto.required' => 'El monto es obligatorio',
            'monto.numeric' => 'El monto debe ser un número válido',
            'monto.min' => 'El monto debe ser mayor o igual a 0',
            'monto.max' => 'El monto no puede ser mayor a 99,999,999.99',
            'fecha_comprobante.required' => 'La fecha es obligatoria',
            'fecha_comprobante.date' => 'La fecha debe ser válida',
            'archivo_comprobante.file' => 'Debe ser un archivo válido',
            'archivo_comprobante.mimes' => 'Solo se permiten archivos JPG, PNG o PDF',
            'archivo_comprobante.max' => 'El archivo no puede ser mayor a 5MB'
        ]);

        $comprobante = new FactSunat();
        $comprobante->tipo_comprobante = $validated['tipo_comprobante'];
        $comprobante->codigo_comprobante = $validated['codigo_comprobante'] ?? null;
        $comprobante->monto = $validated['monto'];
        $comprobante->fecha_comprobante = $validated['fecha_comprobante'];

        // Manejar subida del archivo
        if ($request->hasFile('archivo_comprobante')) {
            $file = $request->file('archivo_comprobante');
            $filename = 'sunat_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('comprobantes_sunat', $filename, 'public');
            $comprobante->archivo_comprobante = $path;
        }

        $comprobante->save();

        return redirect()->route('sunat.index')->with('success', 'Comprobante SUNAT agregado correctamente.');
    }

    public function edit(string $id)
    {
        $comprobante = FactSunat::findOrFail($id);
        return view('sunat.edit', compact('comprobante'));
    }

    public function update(Request $request, string $id)
    {
        $comprobante = FactSunat::findOrFail($id);

        $rules = [
            'tipo_comprobante' => 'required|in:BOLETA,FACTURA,NINGUNO',
            'monto' => 'required|numeric|min:0|max:99999999.99',
            'fecha_comprobante' => 'required|date',
            'archivo_comprobante' => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:5120'
        ];

        if (in_array($request->tipo_comprobante, ['BOLETA', 'FACTURA'])) {
            $rules['codigo_comprobante'] = 'required|string|max:50';
        } else {
            $rules['codigo_comprobante'] = 'nullable|string|max:50';
        }

        $validated = $request->validate($rules, [
            'tipo_comprobante.required' => 'El tipo de comprobante es obligatorio',
            'tipo_comprobante.in' => 'El tipo de comprobante debe ser BOLETA, FACTURA o NINGUNO',
            'codigo_comprobante.required' => 'El código de comprobante es obligatorio cuando seleccionas BOLETA o FACTURA',
            'codigo_comprobante.max' => 'El código de comprobante no puede tener más de 50 caracteres',
            'monto.required' => 'El monto es obligatorio',
            'monto.numeric' => 'El monto debe ser un número válido',
            'monto.min' => 'El monto debe ser mayor o igual a 0',
            'monto.max' => 'El monto no puede ser mayor a 99,999,999.99',
            'fecha_comprobante.required' => 'La fecha es obligatoria',
            'fecha_comprobante.date' => 'La fecha debe ser válida',
            'archivo_comprobante.file' => 'Debe ser un archivo válido',
            'archivo_comprobante.mimes' => 'Solo se permiten archivos JPG, PNG o PDF',
            'archivo_comprobante.max' => 'El archivo no puede ser mayor a 5MB'
        ]);

        $comprobante->tipo_comprobante = $validated['tipo_comprobante'];
        $comprobante->codigo_comprobante = $validated['codigo_comprobante'] ?? null;
        $comprobante->monto = $validated['monto'];
        $comprobante->fecha_comprobante = $validated['fecha_comprobante'];

        // Manejar subida del nuevo archivo
        if ($request->hasFile('archivo_comprobante')) {
            // Eliminar el archivo anterior si existe
            if ($comprobante->archivo_comprobante && Storage::disk('public')->exists($comprobante->archivo_comprobante)) {
                Storage::disk('public')->delete($comprobante->archivo_comprobante);
            }

            $file = $request->file('archivo_comprobante');
            $filename = 'sunat_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('comprobantes_sunat', $filename, 'public');
            $comprobante->archivo_comprobante = $path;
        }

        $comprobante->save();

        return redirect()->route('sunat.index')->with('success', 'Comprobante SUNAT actualizado correctamente.');
    }

    public function destroy(string $id)
    {
        $comprobante = FactSunat::findOrFail($id);
        
        // Eliminar el archivo si existe
        if ($comprobante->archivo_comprobante && Storage::disk('public')->exists($comprobante->archivo_comprobante)) {
            Storage::disk('public')->delete($comprobante->archivo_comprobante);
        }
        
        $comprobante->delete();

        return redirect()->route('sunat.index')->with('success', 'Comprobante SUNAT eliminado correctamente.');
    }

    /**
     * Mostrar el archivo comprobante en el navegador
     */
    public function showArchivo($id)
    {
        $comprobante = FactSunat::findOrFail($id);
        
        if (!$comprobante->archivo_comprobante || !Storage::disk('public')->exists($comprobante->archivo_comprobante)) {
            abort(404, 'Archivo no encontrado');
        }

        return Storage::disk('public')->response($comprobante->archivo_comprobante);
    }

    /**
     * Descargar el archivo comprobante
     */
    public function downloadArchivo($id)
    {
        $comprobante = FactSunat::findOrFail($id);
        
        if (!$comprobante->archivo_comprobante || !Storage::disk('public')->exists($comprobante->archivo_comprobante)) {
            abort(404, 'Archivo no encontrado');
        }

        $extension = pathinfo($comprobante->archivo_comprobante, PATHINFO_EXTENSION);
        $filename = 'comprobante_sunat_' . $comprobante->id_sunat . '.' . $extension;
        
        return Storage::disk('public')->download($comprobante->archivo_comprobante, $filename);
    }

    /**
     * Eliminación múltiple de comprobantes
     */
    public function destroyMultiple(Request $request)
    {
        $ids = $request->input('ids', []);
        
        if (empty($ids)) {
            return redirect()->route('sunat.index')->with('error', 'No se seleccionaron comprobantes para eliminar.');
        }

        $comprobantesEliminados = 0;
        
        foreach ($ids as $id) {
            $comprobante = FactSunat::find($id);
            if ($comprobante) {
                // Eliminar archivo si existe
                if ($comprobante->archivo_comprobante && Storage::disk('public')->exists($comprobante->archivo_comprobante)) {
                    Storage::disk('public')->delete($comprobante->archivo_comprobante);
                }
                $comprobante->delete();
                $comprobantesEliminados++;
            }
        }

        $mensaje = $comprobantesEliminados === 1 
            ? 'Comprobante eliminado correctamente.' 
            : "{$comprobantesEliminados} comprobantes eliminados correctamente.";

        return redirect()->route('sunat.index')->with('success', $mensaje);
    }
}