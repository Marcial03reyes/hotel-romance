<?php

namespace App\Http\Controllers;

use App\Models\FactInversion;
use App\Models\DimMetPago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FactInversionController extends Controller
{
    /**
     * Mostrar lista de inversiones
     */
    public function index()
    {
        $inversiones = DB::table('fact_inversiones as i')
            ->join('dim_met_pago as mp', 'mp.id_met_pago', '=', 'i.id_met_pago')
            ->select(
                'i.id_inversion', 
                'i.detalle', 
                'i.monto', 
                'mp.met_pago', 
                'i.fecha_inversion'
            )
            ->orderByDesc('i.fecha_inversion')
            ->orderByDesc('i.id_inversion')
            ->get();

        return view('inversiones.index', compact('inversiones'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        $metodos = DimMetPago::orderBy('id_met_pago')->get();
        return view('inversiones.create', compact('metodos'));
    }

    /**
     * Guardar nueva inversión
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'detalle'           => 'required|string|max:255',
            'monto'             => 'required|numeric|min:0|max:999999.99',
            'id_met_pago'       => 'required|exists:dim_met_pago,id_met_pago',
            'fecha_inversion'   => 'required|date'
        ]);

        $inversion = new FactInversion();
        $inversion->detalle = $validated['detalle'];
        $inversion->monto = $validated['monto'];
        $inversion->id_met_pago = $validated['id_met_pago'];
        $inversion->fecha_inversion = $validated['fecha_inversion'];
        $inversion->save();

        return redirect()->route('inversiones.index')
            ->with('success', 'Inversión registrada correctamente.');
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit(string $id)
    {
        $inversion = FactInversion::findOrFail($id);
        $metodos = DimMetPago::orderBy('id_met_pago')->get();

        return view('inversiones.edit', compact('inversion', 'metodos'));
    }

    /**
     * Actualizar inversión
     */
    public function update(Request $request, string $id)
    {
        $inversion = FactInversion::findOrFail($id);

        $validated = $request->validate([
            'detalle'           => 'required|string|max:255',
            'monto'             => 'required|numeric|min:0|max:999999.99',
            'id_met_pago'       => 'required|exists:dim_met_pago,id_met_pago',
            'fecha_inversion'   => 'required|date'
        ]);

        $inversion->detalle = $validated['detalle'];
        $inversion->monto = $validated['monto'];
        $inversion->id_met_pago = $validated['id_met_pago'];
        $inversion->fecha_inversion = $validated['fecha_inversion'];
        $inversion->save();

        return redirect()->route('inversiones.index')
            ->with('success', 'Inversión actualizada correctamente.');
    }

    /**
     * Eliminar inversión
     */
    public function destroy(string $id)
    {
        $inversion = FactInversion::findOrFail($id);
        $inversion->delete();

        return redirect()->route('inversiones.index')
            ->with('success', 'Inversión eliminada correctamente.');
    }

    /**
     * Eliminar múltiples inversiones
     */
    public function destroyMultiple(Request $request)
    {
        $ids = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:fact_inversiones,id_inversion'
        ])['ids'];

        FactInversion::whereIn('id_inversion', $ids)->delete();

        return redirect()->route('inversiones.index')
            ->with('success', 'Inversiones eliminadas correctamente.');
    }
}