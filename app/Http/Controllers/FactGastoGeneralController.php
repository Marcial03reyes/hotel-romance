<?php

namespace App\Http\Controllers;

use App\Models\FactGastoGeneral;
use App\Models\DimTipoGasto;
use App\Models\DimMetPago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class FactGastoGeneralController extends Controller
{
    public function index()
    {
        // ← AGREGAMOS LOS NUEVOS CAMPOS A LA CONSULTA
        $gastos = DB::table('fact_gastos_generales as g')
            ->join('dim_tipo_gasto as tg', 'tg.id_tipo_gasto', '=', 'g.id_tipo_gasto')
            ->join('dim_met_pago as mp', 'mp.id_met_pago', '=', 'g.id_met_pago')
            ->select(
                'g.id_gasto', 
                'tg.nombre as tipo', 
                'g.monto', 
                'mp.met_pago', 
                'g.fecha_gasto', 
                'g.tipo_comprobante',    // ← NUEVO
                'g.codigo_comprobante',  // ← NUEVO
                'g.comprobante'
            )
            ->orderByDesc('g.fecha_gasto')
            ->orderByDesc('g.id_gasto')
            ->get();

        return view('gastos.index', compact('gastos'));
    }

    public function create()
    {
        $tipos = DimTipoGasto::orderBy('nombre')->get();
        $metodos = DimMetPago::orderBy('id_met_pago')->get();

        return view('gastos.create', compact('tipos', 'metodos'));
    }

    public function store(Request $request)
    {
        // ← AGREGAMOS VALIDACIÓN CONDICIONAL PARA LOS NUEVOS CAMPOS
        $rules = [
            'id_tipo_gasto' => 'required|exists:dim_tipo_gasto,id_tipo_gasto',
            'monto'         => 'required|numeric|min:0|max:999999.99',
            'id_met_pago'   => 'required|exists:dim_met_pago,id_met_pago',
            'fecha_gasto'   => 'required|date',
            'tipo_comprobante' => 'required|in:BOLETA,FACTURA,NINGUNO',
            'comprobante'   => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:5120'
        ];

        // ← VALIDACIÓN CONDICIONAL: Si tipo_comprobante es BOLETA o FACTURA, codigo_comprobante es obligatorio
        if (in_array($request->tipo_comprobante, ['BOLETA', 'FACTURA'])) {
            $rules['codigo_comprobante'] = 'required|string|max:50';
        } else {
            $rules['codigo_comprobante'] = 'nullable|string|max:50';
        }

        $validated = $request->validate($rules, [
            'tipo_comprobante.required' => 'El tipo de comprobante es obligatorio',
            'tipo_comprobante.in' => 'El tipo de comprobante debe ser BOLETA, FACTURA o NINGUNO',
            'codigo_comprobante.required' => 'El código de comprobante es obligatorio cuando seleccionas BOLETA o FACTURA',
            'codigo_comprobante.max' => 'El código de comprobante no puede tener más de 50 caracteres'
        ]);

        // Crear nuevo gasto
        $g = new FactGastoGeneral();
        $g->id_tipo_gasto = $validated['id_tipo_gasto'];
        $g->monto         = $validated['monto'];
        $g->id_met_pago   = $validated['id_met_pago'];
        $g->fecha_gasto   = $validated['fecha_gasto'];
        $g->tipo_comprobante = $validated['tipo_comprobante'];                    // ← NUEVO
        $g->codigo_comprobante = $validated['codigo_comprobante'] ?? null;       // ← NUEVO

        // Manejar subida del comprobante (archivo)
        if ($request->hasFile('comprobante')) {
            $file = $request->file('comprobante');
            $filename = 'comprobante_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('comprobantes', $filename, 'public');
            $g->comprobante = $path;
        }

        $g->save();

        return redirect()->route('gastos.index')->with('success', 'Gasto agregado correctamente.');
    }

    public function edit(string $id)
    {
        $gasto   = FactGastoGeneral::findOrFail($id);
        $tipos   = DimTipoGasto::orderBy('nombre')->get();
        $metodos = DimMetPago::orderBy('id_met_pago')->get();

        return view('gastos.edit', compact('gasto', 'tipos', 'metodos'));
    }

    public function update(Request $request, string $id)
    {
        $gasto = FactGastoGeneral::findOrFail($id);

        // ← MISMA VALIDACIÓN CONDICIONAL PARA UPDATE
        $rules = [
            'id_tipo_gasto' => 'required|exists:dim_tipo_gasto,id_tipo_gasto',
            'monto'         => 'required|numeric|min:0|max:999999.99',
            'id_met_pago'   => 'required|exists:dim_met_pago,id_met_pago',
            'fecha_gasto'   => 'required|date',
            'tipo_comprobante' => 'required|in:BOLETA,FACTURA,NINGUNO',
            'comprobante'   => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:5120'
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
            'codigo_comprobante.max' => 'El código de comprobante no puede tener más de 50 caracteres'
        ]);

        // Actualizar campos
        $gasto->id_tipo_gasto = $validated['id_tipo_gasto'];
        $gasto->monto         = $validated['monto'];
        $gasto->id_met_pago   = $validated['id_met_pago'];
        $gasto->fecha_gasto   = $validated['fecha_gasto'];
        $gasto->tipo_comprobante = $validated['tipo_comprobante'];           // ← NUEVO
        $gasto->codigo_comprobante = $validated['codigo_comprobante'] ?? null; // ← NUEVO

        // Manejar subida del nuevo comprobante
        if ($request->hasFile('comprobante')) {
            // Eliminar el comprobante anterior si existe
            if ($gasto->comprobante && Storage::disk('public')->exists($gasto->comprobante)) {
                Storage::disk('public')->delete($gasto->comprobante);
            }

            $file = $request->file('comprobante');
            $filename = 'comprobante_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('comprobantes', $filename, 'public');
            $gasto->comprobante = $path;
        }

        $gasto->save();

        return redirect()->route('gastos.index')->with('success', 'Gasto actualizado correctamente.');
    }

    public function destroy(string $id)
    {
        $gasto = FactGastoGeneral::findOrFail($id);
        
        // Eliminar el comprobante si existe
        if ($gasto->comprobante && Storage::disk('public')->exists($gasto->comprobante)) {
            Storage::disk('public')->delete($gasto->comprobante);
        }
        
        $gasto->delete();

        return redirect()->route('gastos.index')->with('success', 'Gasto eliminado.');
    }

    // ✅ NUEVOS MÉTODOS PARA MANEJAR COMPROBANTES

    /**
     * Mostrar el comprobante en el navegador
     */
    public function showComprobante($id)
    {
        $gasto = FactGastoGeneral::findOrFail($id);
        
        if (!$gasto->comprobante || !Storage::disk('public')->exists($gasto->comprobante)) {
            abort(404, 'Comprobante no encontrado');
        }

        return Storage::disk('public')->response($gasto->comprobante);
    }

    /**
     * Descargar el comprobante
     */
    public function downloadComprobante($id)
    {
        $gasto = FactGastoGeneral::findOrFail($id);
        
        if (!$gasto->comprobante || !Storage::disk('public')->exists($gasto->comprobante)) {
            abort(404, 'Comprobante no encontrado');
        }

        return Storage::disk('public')->download($gasto->comprobante, 'comprobante_gasto_' . $id . '.' . pathinfo($gasto->comprobante, PATHINFO_EXTENSION));
    }

    // ✅ MÉTODO PARA ELIMINACIÓN MÚLTIPLE (que usa tu JavaScript)
    public function destroyMultiple(Request $request)
    {
        $ids = $request->input('ids', []);
        
        if (empty($ids)) {
            return redirect()->route('gastos.index')->with('error', 'No se seleccionaron gastos para eliminar.');
        }

        $gastosEliminados = 0;
        
        foreach ($ids as $id) {
            $gasto = FactGastoGeneral::find($id);
            if ($gasto) {
                // Eliminar comprobante si existe
                if ($gasto->comprobante && Storage::disk('public')->exists($gasto->comprobante)) {
                    Storage::disk('public')->delete($gasto->comprobante);
                }
                $gasto->delete();
                $gastosEliminados++;
            }
        }

        $mensaje = $gastosEliminados === 1 
            ? 'Gasto eliminado correctamente.' 
            : "{$gastosEliminados} gastos eliminados correctamente.";

        return redirect()->route('gastos.index')->with('success', $mensaje);
    }
}