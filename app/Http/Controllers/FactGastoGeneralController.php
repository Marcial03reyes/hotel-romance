<?php

namespace App\Http\Controllers;

use App\Models\FactGastoGeneral;
use App\Models\DimTipoGasto;
use App\Models\DimMetPago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FactGastoGeneralController extends Controller
{
    public function index()
    {
        // ✅ CONSULTA CON CAMPO COMPROBANTE PARA CÓDIGOS
        $gastos = DB::table('fact_gastos_generales as g')
            ->join('dim_tipo_gasto as tg', 'tg.id_tipo_gasto', '=', 'g.id_tipo_gasto')
            ->join('dim_met_pago as mp', 'mp.id_met_pago', '=', 'g.id_met_pago')
            ->select('g.id_gasto', 'tg.nombre as tipo', 'g.monto', 'mp.met_pago', 'g.fecha_gasto', 'g.comprobante', 'g.turno')            ->orderByDesc('g.fecha_gasto')
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
        // ✅ VALIDACIÓN PARA CÓDIGO DE COMPROBANTE
        $validated = $request->validate([
            'id_tipo_gasto' => 'required|exists:dim_tipo_gasto,id_tipo_gasto',
            'monto'         => 'required|numeric|min:0|max:999999.99',
            'id_met_pago'   => 'required|exists:dim_met_pago,id_met_pago',
            'fecha_gasto'   => 'required|date',
            'comprobante'   => 'nullable|string|max:100',
            'turno'         => 'required|in:0,1'
        ]);

        // Asignación directa
        $g = new FactGastoGeneral();
        $g->id_tipo_gasto = $validated['id_tipo_gasto'];
        $g->monto         = $validated['monto'];
        $g->id_met_pago   = $validated['id_met_pago'];
        $g->fecha_gasto   = $validated['fecha_gasto'];
        $g->comprobante   = $validated['comprobante'];
        $g->turno         = $validated['turno'];

        $g->save();

        return redirect()->route('gastos.index')->with('success', 'Gasto variable agregado correctamente.');
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

        // ✅ VALIDACIÓN PARA CÓDIGO DE COMPROBANTE EN UPDATE
        $validated = $request->validate([
            'id_tipo_gasto' => 'required|exists:dim_tipo_gasto,id_tipo_gasto',
            'monto'         => 'required|numeric|min:0|max:999999.99',
            'id_met_pago'   => 'required|exists:dim_met_pago,id_met_pago',
            'fecha_gasto'   => 'required|date',
            'comprobante'   => 'nullable|string|max:100',
            'turno'         => 'required|in:0,1'
        ]);

        // Asignación directa
        $gasto->id_tipo_gasto = $validated['id_tipo_gasto'];
        $gasto->monto         = $validated['monto'];
        $gasto->id_met_pago   = $validated['id_met_pago'];
        $gasto->fecha_gasto   = $validated['fecha_gasto'];
        $gasto->comprobante   = $validated['comprobante'];
        $gasto->turno         = $validated['turno'];

        $gasto->save();

        return redirect()->route('gastos.index')->with('success', 'Gasto variable actualizado correctamente.');
    }

    public function destroy(string $id)
    {
        $gasto = FactGastoGeneral::findOrFail($id);
        $gasto->delete();

        return redirect()->route('gastos.index')->with('success', 'Gasto variable eliminado correctamente.');
    }

    public function destroyMultiple(Request $request)
    {
        $ids = $request->input('ids', []);
        
        if (empty($ids)) {
            return redirect()->back()->withErrors('No se seleccionaron gastos para eliminar.');
        }

        $gastos = FactGastoGeneral::whereIn('id_gasto', $ids)->get();
        
        foreach ($gastos as $gasto) {
            $gasto->delete();
        }

        return redirect()->route('gastos.index')->with('success', count($gastos) . ' gastos variables eliminados correctamente.');
    }
}