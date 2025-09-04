<?php

namespace App\Http\Controllers;

use App\Models\DimTipoGasto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DimTipoGastoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tipos = DimTipoGasto::orderBy('nombre')->get();
        return view('tipos-gasto.index', compact('tipos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('tipos-gasto.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => [
                'required',
                'string',
                'max:100',
                'unique:dim_tipo_gasto,nombre'
            ]
        ], [
            'nombre.required' => 'El nombre del tipo de gasto es obligatorio',
            'nombre.max' => 'El nombre no puede exceder 100 caracteres',
            'nombre.unique' => 'Ya existe un tipo de gasto con este nombre'
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DimTipoGasto::create([
                'nombre' => trim($request->nombre)
            ]);

            return redirect()
                ->route('tipos-gasto.index')
                ->with('success', "Tipo de gasto '{$request->nombre}' creado exitosamente");

        } catch (\Exception $e) {
            return back()
                ->withErrors('Error al crear el tipo de gasto: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $tipo = DimTipoGasto::findOrFail($id);
            return view('tipos-gasto.edit', compact('tipo'));
        } catch (\Exception $e) {
            return back()->withErrors('Error al cargar el tipo de gasto: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $tipo = DimTipoGasto::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nombre' => [
                'required',
                'string',
                'max:100',
                'unique:dim_tipo_gasto,nombre,' . $id . ',id_tipo_gasto'
            ]
        ], [
            'nombre.required' => 'El nombre del tipo de gasto es obligatorio',
            'nombre.max' => 'El nombre no puede exceder 100 caracteres',
            'nombre.unique' => 'Ya existe un tipo de gasto con este nombre'
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $tipo->update([
                'nombre' => trim($request->nombre)
            ]);

            return redirect()
                ->route('tipos-gasto.index')
                ->with('success', "Tipo de gasto actualizado exitosamente");

        } catch (\Exception $e) {
            return back()
                ->withErrors('Error al actualizar el tipo de gasto: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $tipo = DimTipoGasto::findOrFail($id);
            
            // Verificar que no tenga gastos registrados
            $tieneGastos = $tipo->gastos()->exists();
            
            if ($tieneGastos) {
                return back()->withErrors('No se puede eliminar el tipo de gasto porque tiene gastos registrados.');
            }
            
            $nombreTipo = $tipo->nombre;
            $tipo->delete();
            
            return redirect()
                ->route('tipos-gasto.index')
                ->with('success', "Tipo de gasto '{$nombreTipo}' eliminado exitosamente");
                
        } catch (\Exception $e) {
            return back()->withErrors('Error al eliminar el tipo de gasto: ' . $e->getMessage());
        }
    }
}