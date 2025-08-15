<?php

namespace App\Http\Controllers;

use App\Models\FactHorario;
use App\Models\FactTrabajador;
use Illuminate\Http\Request;

class FactHorarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Obtener todos los trabajadores con sus horarios
        $trabajadores = FactTrabajador::with(['horariosActivos' => function($query) {
            $query->orderByRaw("FIELD(dia_semana, 'lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo')");
        }])->orderBy('nombre_apellido')->get();

        // Obtener trabajadores sin horarios asignados
        $trabajadoresSinHorarios = FactTrabajador::whereDoesntHave('horariosActivos')->get();

        return view('horarios.index', compact('trabajadores', 'trabajadoresSinHorarios'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $trabajadores = FactTrabajador::orderBy('nombre_apellido')->get();
        $diasSemana = [
            'lunes' => 'Lunes',
            'martes' => 'Martes',
            'miercoles' => 'Miércoles',
            'jueves' => 'Jueves',
            'viernes' => 'Viernes',
            'sabado' => 'Sábado',
            'domingo' => 'Domingo'
        ];

        return view('horarios.create', compact('trabajadores', 'diasSemana'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'DNI' => 'required|exists:fact_trabajadores,DNI',
            'dia_semana' => 'required|in:lunes,martes,miercoles,jueves,viernes,sabado,domingo',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
        ]);

        // Verificar si ya existe un horario para este trabajador en este día
        $horarioExistente = FactHorario::where('DNI', $validated['DNI'])
            ->where('dia_semana', $validated['dia_semana'])
            ->where('activo', true)
            ->first();

        if ($horarioExistente) {
            return back()->withErrors(['dia_semana' => 'Ya existe un horario activo para este trabajador en este día.'])->withInput();
        }

        FactHorario::create($validated);

        return redirect()->route('horarios.index')->with('success', 'Horario creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(FactHorario $horario)
    {
        return view('horarios.show', compact('horario'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FactHorario $horario)
    {
        $trabajadores = FactTrabajador::orderBy('nombre_apellido')->get();
        $diasSemana = [
            'lunes' => 'Lunes',
            'martes' => 'Martes',
            'miercoles' => 'Miércoles',
            'jueves' => 'Jueves',
            'viernes' => 'Viernes',
            'sabado' => 'Sábado',
            'domingo' => 'Domingo'
        ];

        return view('horarios.edit', compact('horario', 'trabajadores', 'diasSemana'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FactHorario $horario)
    {
        $validated = $request->validate([
            'DNI' => 'required|exists:fact_trabajadores,DNI',
            'dia_semana' => 'required|in:lunes,martes,miercoles,jueves,viernes,sabado,domingo',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
            'activo' => 'boolean'
        ]);

        // Verificar si ya existe otro horario para este trabajador en este día (excluyendo el actual)
        $horarioExistente = FactHorario::where('DNI', $validated['DNI'])
            ->where('dia_semana', $validated['dia_semana'])
            ->where('activo', true)
            ->where('id', '!=', $horario->id)
            ->first();

        if ($horarioExistente) {
            return back()->withErrors(['dia_semana' => 'Ya existe un horario activo para este trabajador en este día.'])->withInput();
        }

        $horario->update($validated);

        return redirect()->route('horarios.index')->with('success', 'Horario actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FactHorario $horario)
    {
        $horario->delete();
        return redirect()->route('horarios.index')->with('success', 'Horario eliminado correctamente.');
    }

    /**
     * Asignar horarios masivos a un trabajador
     */
    public function asignarHorarioCompleto(Request $request)
    {
        $validated = $request->validate([
            'DNI' => 'required|exists:fact_trabajadores,DNI',
            'dias_seleccionados' => 'required|array|min:1',
            'dias_seleccionados.*' => 'in:lunes,martes,miercoles,jueves,viernes,sabado,domingo',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
        ]);

        $errores = [];
        $creados = 0;

        foreach ($validated['dias_seleccionados'] as $dia) {
            // Verificar si ya existe un horario para este día
            $horarioExistente = FactHorario::where('DNI', $validated['DNI'])
                ->where('dia_semana', $dia)
                ->where('activo', true)
                ->first();

            if (!$horarioExistente) {
                FactHorario::create([
                    'DNI' => $validated['DNI'],
                    'dia_semana' => $dia,
                    'hora_inicio' => $validated['hora_inicio'],
                    'hora_fin' => $validated['hora_fin'],
                ]);
                $creados++;
            } else {
                $errores[] = $dia;
            }
        }

        $mensaje = "Se crearon {$creados} horarios correctamente.";
        if (!empty($errores)) {
            $mensaje .= " Los días " . implode(', ', $errores) . " ya tenían horarios asignados.";
        }

        return redirect()->route('horarios.index')->with('success', $mensaje);
    }
}