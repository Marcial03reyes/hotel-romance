<?php

namespace App\Http\Controllers;

use App\Models\FactPagoProd;
use App\Models\DimProductoBodega;
use App\Models\DimMetPago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\TurnoCerrado;

class FactPagoProdController extends Controller
{
    /**
     * Mostrar listado de ventas de bodega con filtros
     */
    public function index(Request $request)
    {
        $query = FactPagoProd::with(['producto', 'metodoPago'])
            ->ventasBodega() // Solo ventas sin cliente
            ->orderByDesc('fecha_venta')
            ->orderByDesc('turno')
            ->orderByDesc('id_compra');

        // Filtro de fecha (mantener existente)
        $filtro = $request->get('filtro', 'todos');
        
        switch ($filtro) {
            case 'hoy':
                $query->whereDate('fecha_venta', today());
                break;
            case 'semana':
                $query->whereBetween('fecha_venta', [
                    now()->startOfWeek(Carbon::SUNDAY),
                    now()->endOfWeek(Carbon::SATURDAY)
                ]);
                break;
            case 'personalizado':
                if ($request->fecha_inicio) {
                    $query->whereDate('fecha_venta', '>=', $request->fecha_inicio);
                }
                if ($request->fecha_fin) {
                    $query->whereDate('fecha_venta', '<=', $request->fecha_fin);
                }
                break;
        }

        // Filtro de turno
        if ($request->has('turno') && $request->turno !== '') {
            $query->porTurno($request->turno);
        }
        
        // NUEVO: Filtro de tipo (venta normal o consumo interno)
        if ($request->has('tipo') && $request->tipo !== '') {
            if ($request->tipo === 'consumo_interno') {
                $query->consumoInterno();
            } elseif ($request->tipo === 'ventas') {
                $query->ventasReales();
            }
        }

        $ventas = $query->get();

        // Estadísticas mejoradas
        $estadisticas = [
            'total_ventas' => $ventas->where('precio_unitario', '>', 0)->count(),
            'total_consumo_interno' => $ventas->where('precio_unitario', '=', 0)->count(),
            'total_registros' => $ventas->count(),
            'total_ingresos' => $ventas->sum('total'),
            'ingresos_dia' => $ventas->where('turno', 0)->sum('total'),
            'ingresos_noche' => $ventas->where('turno', 1)->sum('total'),
            'con_comprobante' => $ventas->where('comprobante', 'SI')->count(),
            'unidades_vendidas' => $ventas->where('precio_unitario', '>', 0)->sum('cantidad'),
            'unidades_consumo_interno' => $ventas->where('precio_unitario', '=', 0)->sum('cantidad'),
            'unidades_totales' => $ventas->sum('cantidad'),
        ];

        return view('pagos-productos.index', compact('ventas', 'estadisticas'));
    }

    /**
     * Mostrar formulario de crear nueva venta
     */
    public function create()
    {
        $productos = DimProductoBodega::orderBy('nombre')->get();
        $metodos = DimMetPago::orderBy('id_met_pago')->get();
        
        return view('pagos-productos.create', compact('productos', 'metodos'));
    }

    /**
     * Guardar nueva venta de bodega
     */
    public function store(Request $request)
    {
        $rules = [
            'fecha_venta' => 'required|date',
            'turno' => 'required|in:0,1',
            'id_prod_bod' => 'required|exists:dim_productos_bodega,id_prod_bod',
            'cantidad' => 'required|integer|min:1|max:9999',
            'es_consumo_interno' => 'sometimes|boolean',
        ];

        // Validación condicional
        if (!$request->has('es_consumo_interno') || !$request->es_consumo_interno) {
            $rules['monto_total'] = 'required|numeric|min:0';
            $rules['pagos.*.id_met_pago'] = 'nullable|exists:dim_met_pago,id_met_pago';
            $rules['pagos.*.monto'] = 'nullable|numeric|min:0';
            $rules['monto_individual'] = 'nullable|numeric|min:0';
            $rules['id_met_pago'] = 'nullable|exists:dim_met_pago,id_met_pago';
            $rules['comprobante'] = 'required|in:SI,NO';
        }

        $request->validate($rules);

        // Verificar si el turno está cerrado
        if (TurnoCerrado::estaCerrado($request->fecha_venta, $request->turno)) {
            return back()
                ->withInput()
                ->withErrors(['turno' => 'TURNO CERRADO: No se pueden registrar datos para la fecha ' . 
                    \Carbon\Carbon::parse($request->fecha_venta)->format('d/m/Y') . 
                    ' turno ' . ($request->turno == 0 ? 'DÍA' : 'NOCHE') . '. Contacte al administrador.']);
        }

        DB::beginTransaction();
        try {
            $producto = DimProductoBodega::findOrFail($request->id_prod_bod);
            $esConsumoInterno = $request->has('es_consumo_interno') && $request->es_consumo_interno;

            if ($esConsumoInterno) {
                // Consumo interno: un solo registro con precio 0
                $venta = new FactPagoProd();
                $venta->id_estadia = null;
                $venta->fecha_venta = $request->fecha_venta;
                $venta->turno = $request->turno;
                $venta->id_prod_bod = $request->id_prod_bod;
                $venta->cantidad = $request->cantidad;
                $venta->precio_unitario = 0;
                $venta->comprobante = 'NO';
                $venta->id_met_pago = 99;
                $venta->save();
                
            } else {
                // Venta normal: múltiples registros por método de pago
                $pagos = $request->input('pagos', []);
                
                // Si viene del formulario simple (compatibilidad) - ESTE DEBE SER EL PRIMERO
                if ($request->has('id_met_pago') && ($request->has('monto_individual') || $request->has('monto_total'))) {
                    array_unshift($pagos, [  // ✅ CAMBIAR: usar unshift para que sea el primero
                        'id_met_pago' => $request->id_met_pago,
                        'monto' => $request->monto_individual ?? $request->monto_total
                    ]);
                }
                
                $esPrimerPago = true;
                
                foreach ($pagos as $pagoData) {
                    if (!empty($pagoData['id_met_pago']) && !empty($pagoData['monto'])) {
                        $venta = new FactPagoProd();
                        $venta->id_estadia = null;
                        $venta->fecha_venta = $request->fecha_venta;
                        $venta->turno = $request->turno;
                        $venta->id_prod_bod = $request->id_prod_bod;
                        $venta->id_met_pago = $pagoData['id_met_pago'];
                        $venta->comprobante = $request->comprobante;
                        
                        if ($esPrimerPago) {
                            // ✅ PRIMER MÉTODO: cantidad real, precio por unidad
                            $venta->cantidad = $request->cantidad;
                            $venta->precio_unitario = $pagoData['monto'] / $request->cantidad;
                            $esPrimerPago = false;
                        } else {
                            // ✅ MÉTODOS ADICIONALES: cantidad -1, monto completo negativo
                            $venta->cantidad = -1;
                            $venta->precio_unitario = -$pagoData['monto'];
                        }
                        
                        $venta->save();
                    }
                }
            }
            DB::commit();
            
            $mensaje = $esConsumoInterno ? 
                'Consumo interno registrado correctamente.' : 
                'Venta registrada correctamente.';

            return redirect()->route('pagos-productos.index')->with('success', $mensaje);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors('Error al registrar: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Mostrar formulario de editar venta
     */
    public function edit($id)
    {
        $venta = FactPagoProd::with(['producto', 'metodoPago'])->findOrFail($id);
        $productos = DimProductoBodega::orderBy('nombre')->get();
        $metodos = DimMetPago::orderBy('id_met_pago')->get();

        return view('pagos-productos.edit', compact('venta', 'productos', 'metodos'));
    }

    /**
     * Actualizar venta existente
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'fecha_venta' => 'required|date',
            'turno' => 'required|in:0,1',
            'id_prod_bod' => 'required|exists:dim_productos_bodega,id_prod_bod',
            'cantidad' => 'required|integer|min:1|max:9999',
            'id_met_pago' => 'required|exists:dim_met_pago,id_met_pago',
            'comprobante' => 'required|in:SI,NO',
        ]);

        // Verificar si el turno está cerrado
        if (TurnoCerrado::estaCerrado($request->fecha_venta, $request->turno)) {
            return back()
                ->withInput()
                ->withErrors(['turno' => 'TURNO CERRADO: No se pueden modificar datos para la fecha ' . 
                    \Carbon\Carbon::parse($request->fecha_venta)->format('d/m/Y') . 
                    ' turno ' . ($request->turno == 0 ? 'DÍA' : 'NOCHE') . '. Contacte al administrador.']);
        }

        try {
            $venta = FactPagoProd::findOrFail($id);
            
            // Si cambió el producto, actualizar el precio
            if ($venta->id_prod_bod != $request->id_prod_bod) {
                $producto = DimProductoBodega::findOrFail($request->id_prod_bod);
                $venta->precio_unitario = $producto->precio_actual;
            }

            $venta->fecha_venta = $request->fecha_venta;
            $venta->turno = $request->turno;
            $venta->id_prod_bod = $request->id_prod_bod;
            $venta->cantidad = $request->cantidad;
            $venta->id_met_pago = $request->id_met_pago;
            $venta->comprobante = $request->comprobante;
            $venta->save();

            return redirect()->route('pagos-productos.index')
                ->with('success', 'Venta actualizada correctamente.');

        } catch (\Exception $e) {
            return back()->withErrors('Error al actualizar venta: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Eliminar venta
     */
    public function destroy($id)
    {
        try {
            $venta = FactPagoProd::findOrFail($id);
            if (TurnoCerrado::estaCerrado($venta->fecha_venta, $venta->turno)) {
                return back()->withErrors(['error' => 'TURNO CERRADO: No se puede eliminar este registro. Contacte al administrador.']);
            }
            $productoNombre = $venta->producto_nombre;
            $venta->delete();

            return redirect()->route('pagos-productos.index')
                ->with('success', "Venta eliminada: {$productoNombre}");

        } catch (\Exception $e) {
            return back()->withErrors('Error al eliminar venta: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Método opcional - puedes implementar una vista detallada si lo necesitas
        $venta = FactPagoProd::with(['producto', 'metodoPago'])->findOrFail($id);
        return view('pagos-productos.show', compact('venta'));
    }

    public function getPrecioProducto($id)
    {
        $producto = DimProductoBodega::findOrFail($id);
        return response()->json([
            'precio_actual' => $producto->precio_actual
        ]);
    }
}