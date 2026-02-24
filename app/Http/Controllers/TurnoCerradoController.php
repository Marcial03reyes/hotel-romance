<?php

namespace App\Http\Controllers;

use App\Models\TurnoCerrado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TurnoCerradoController extends Controller
{
    // Listar turnos cerrados
    public function index(Request $request)
    {
        $query = TurnoCerrado::with('usuario')->orderByDesc('fecha')->orderBy('turno');

        if ($request->filled('fecha')) {
            $query->where('fecha', $request->fecha);
        }

        $turnosCerrados = $query->get();

        return view('turnos-cerrados.index', compact('turnosCerrados'));
    }

    // Cerrar un turno
    public function store(Request $request)
    {
        $request->validate([
            'fecha' => 'required|date',
            'turno' => 'required|in:0,1',
            'observacion' => 'nullable|string|max:500',
        ]);

        if (TurnoCerrado::estaCerrado($request->fecha, $request->turno)) {
            return back()->withErrors(['error' => 'Ese turno ya está cerrado.']);
        }

        TurnoCerrado::cerrar(
            $request->fecha,
            $request->turno,
            Auth::id(),
            $request->observacion
        );

        $turnoNombre = $request->turno == 0 ? 'DÍA' : 'NOCHE';
        $fecha = \Carbon\Carbon::parse($request->fecha)->format('d/m/Y');

        return back()->with('success', "Turno {$turnoNombre} del {$fecha} cerrado correctamente.");
    }

    // Reabrir un turno
    public function destroy(Request $request)
    {
        $request->validate([
            'fecha' => 'required|date',
            'turno' => 'required|in:0,1',
        ]);

        $reabierto = TurnoCerrado::reabrir($request->fecha, $request->turno);

        if (!$reabierto) {
            return back()->withErrors(['error' => 'No se encontró ese turno cerrado.']);
        }

        $turnoNombre = $request->turno == 0 ? 'DÍA' : 'NOCHE';
        $fecha = \Carbon\Carbon::parse($request->fecha)->format('d/m/Y');

        return back()->with('success', "Turno {$turnoNombre} del {$fecha} reabierto correctamente.");
    }
}