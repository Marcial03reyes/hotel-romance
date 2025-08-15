<?php

namespace App\Http\Controllers;

use App\Models\FactTrabajador;
use Illuminate\Http\Request;

class FactTrabajadorController extends Controller
{
    public function index()
    {
        $trabajadores = FactTrabajador::orderBy('nombre_apellido')->get();
        return view('trabajadores.index', compact('trabajadores'));
    }

    public function create()
    {
        return view('trabajadores.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'DNI'             => 'required|string|max:20|unique:fact_trabajadores,DNI',
            'nombre_apellido' => 'required|string|max:100',
            'sueldo'          => 'required|numeric|min:0|max:9999.99',
            'Fecha_inicio'    => 'required|date',
            'Telef'           => 'required|string|max:9',
        ]);

        // estilo “plan B” sin mass assignment si prefieres
        $t = new FactTrabajador();
        $t->DNI             = $validated['DNI'];
        $t->nombre_apellido = $validated['nombre_apellido'];
        $t->sueldo          = $validated['sueldo'];
        $t->Fecha_inicio    = $validated['Fecha_inicio'];
        $t->Telef           = $validated['Telef'];
        $t->save();

        return redirect()->route('trabajadores.index')->with('success', 'Trabajador creado correctamente.');
    }

    public function edit(string $dni)
    {
        $trabajador = FactTrabajador::findOrFail($dni);
        return view('trabajadores.edit', compact('trabajador'));
    }

    public function update(Request $request, string $dni)
    {
        $trabajador = FactTrabajador::findOrFail($dni);

        $validated = $request->validate([
            'nombre_apellido' => 'required|string|max:100',
            'sueldo'          => 'required|numeric|min:0|max:9999.99',
            'Fecha_inicio'    => 'required|date',
            'Telef'           => 'required|string|max:9',
        ]);

        // “plan B” para evitar mass assignment
        $trabajador->nombre_apellido = $validated['nombre_apellido'];
        $trabajador->sueldo          = $validated['sueldo'];
        $trabajador->Fecha_inicio    = $validated['Fecha_inicio'];
        $trabajador->Telef           = $validated['Telef'];
        $trabajador->save();

        return redirect()->route('trabajadores.index')->with('success', 'Trabajador actualizado correctamente.');
    }

    public function destroy(string $dni)
    {
        $trabajador = FactTrabajador::findOrFail($dni);
        $trabajador->delete();

        return redirect()->route('trabajadores.index')->with('success', 'Trabajador eliminado correctamente.');
    }
}
