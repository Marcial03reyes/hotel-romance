<?php

namespace App\Http\Controllers;

use App\Models\DimRegistroCliente;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;

class DimRegistroClienteController extends Controller
{
    public function index()
    {
        $clientes = DimRegistroCliente::orderBy('nombre_apellido')->get();
        
        \Log::info('Clientes count: ' . $clientes->count());
        
        return view('clientes.index', compact('clientes'));
    }

    public function create()
    {
        return view('clientes.create');
    }

    public function store(Request $request)
    {
        // Detectar si es una petición AJAX (desde el formulario de registro)
        if ($request->wantsJson() || $request->ajax()) {
            return $this->storeAjax($request);
        }
        
        // Validación normal para formulario web
        $validated = $request->validate([
            'doc_identidad'     => 'required|string|max:20|unique:dim_registro_clientes,doc_identidad',
            'nombre_apellido'   => 'required|string|max:100',
            'estado_civil'      => 'nullable|string|max:20',
            'fecha_nacimiento'  => 'nullable|date|before:today',
            'lugar_nacimiento'  => 'nullable|string|max:100',
            'nacionalidad' => 'nullable|string|max:50',
            'sexo'              => 'nullable|in:M,F',                    
            'profesion_ocupacion' => 'nullable|string|max:100',
        ]);

        $c = new DimRegistroCliente();
        $c->doc_identidad   = $validated['doc_identidad'];
        $c->nombre_apellido = $validated['nombre_apellido'];
        $c->estado_civil    = $validated['estado_civil'] ?? null;
        $c->fecha_nacimiento = $validated['fecha_nacimiento'] ?? null;
        $c->lugar_nacimiento = $validated['lugar_nacimiento'] ?? null;
        $c->nacionalidad = $validated['nacionalidad'] ?? null;
        $c->sexo = $validated['sexo'] ?? null;                          
        $c->profesion_ocupacion = $validated['profesion_ocupacion'] ?? null;
        $c->save();

        return redirect()->route('clientes.index')->with('success', 'Cliente creado correctamente.');
    }

    /**
     * Crear cliente vía AJAX desde el formulario de registro
     */
    private function storeAjax(Request $request)
    {
        // Validar datos
        $validator = Validator::make($request->all(), [
            'doc_identidad'     => 'required|string|max:20|unique:dim_registro_clientes,doc_identidad',
            'nombre_apellido'   => 'required|string|min:3|max:100',
            'estado_civil'      => 'nullable|string|max:20',
            'fecha_nacimiento'  => 'nullable|date|before:today',
            'lugar_nacimiento'  => 'nullable|string|max:100',
            'nacionalidad' => 'nullable|string|max:50',
            'sexo'              => 'nullable|in:M,F',                   
            'profesion_ocupacion' => 'nullable|string|max:100', 
        ], [
            'doc_identidad.required' => 'El documento de identidad es obligatorio',
            'doc_identidad.unique' => 'Ya existe un cliente con este documento',
            'nombre_apellido.required' => 'El nombre y apellido es obligatorio',
            'nombre_apellido.min' => 'El nombre debe tener al menos 3 caracteres',
            'nombre_apellido.max' => 'El nombre no puede exceder 100 caracteres',
            'fecha_nacimiento.date' => 'La fecha de nacimiento debe ser una fecha válida',
            'fecha_nacimiento.before' => 'La fecha de nacimiento debe ser anterior a hoy',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'ok' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Crear el cliente
            $cliente = new DimRegistroCliente();
            $cliente->doc_identidad = trim($request->doc_identidad);
            $cliente->nombre_apellido = trim($request->nombre_apellido);
            $cliente->estado_civil = $request->estado_civil ?? null;
            $cliente->fecha_nacimiento = $request->fecha_nacimiento ?? null;
            $cliente->lugar_nacimiento = $request->lugar_nacimiento ?? null;
            $cliente->nacionalidad = $request->nacionalidad ?? null;
            $cliente->sexo = $request->sexo ?? null;                      
            $cliente->profesion_ocupacion = $request->profesion_ocupacion ?? null;
            $cliente->save();

            return response()->json([
                'ok' => true,
                'message' => 'Cliente creado exitosamente',
                'cliente' => [
                    'doc_identidad' => $cliente->doc_identidad,
                    'nombre_apellido' => $cliente->nombre_apellido
                ]
            ], 201);

        } catch (QueryException $e) {
            // Error de base de datos (posiblemente documento duplicado)
            return response()->json([
                'ok' => false,
                'message' => 'Error al crear el cliente: El documento ya existe'
            ], 409);

        } catch (\Exception $e) {
            return response()->json([
                'ok' => false,
                'message' => 'Error interno del servidor'
            ], 500);
        }
    }

    public function edit(string $id)
    {
        $cliente = DimRegistroCliente::findOrFail($id);
        return view('clientes.edit', compact('cliente'));
    }

    public function update(Request $request, string $id)
    {
        $cliente = DimRegistroCliente::findOrFail($id);

        $request->validate([
            'nombre_apellido'   => 'required|string|max:100',
            'estado_civil'      => 'nullable|string|max:20',
            'fecha_nacimiento'  => 'nullable|date|before:today',
            'lugar_nacimiento'  => 'nullable|string|max:100',
            'nacionalidad' => 'nullable|string|max:50',
            'sexo'              => 'nullable|in:M,F',                    
            'profesion_ocupacion' => 'nullable|string|max:100',
        ]);

        $cliente->nombre_apellido = $request->input('nombre_apellido');
        $cliente->estado_civil = $request->input('estado_civil');
        $cliente->fecha_nacimiento = $request->input('fecha_nacimiento');
        $cliente->lugar_nacimiento = $request->input('lugar_nacimiento');
        $cliente->nacionalidad = $request->input('nacionalidad');
        $cliente->sexo = $request->input('sexo');                       
        $cliente->profesion_ocupacion = $request->input('profesion_ocupacion');
        $cliente->save();
        
        return redirect()->route('clientes.index')->with('success', 'Cliente actualizado correctamente.');
    }

    public function destroy(string $id)
    {
        $cliente = DimRegistroCliente::findOrFail($id);

        try {
            $cliente->delete();
        } catch (QueryException $e) {
            return back()->withErrors('No se puede eliminar porque el cliente tiene registros relacionados.');
        }

        return redirect()
            ->route('clientes.index')
            ->with('success', 'Cliente eliminado correctamente.');
    }
}