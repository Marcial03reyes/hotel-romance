<?php

namespace App\Http\Controllers;

use App\Models\FactPagoProd;
use App\Models\DimProductoBodega;
use App\Models\DimMetPago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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

        // Filtro de fecha
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

        $ventas = $query->get();

        // Estadísticas
        $estadisticas = [
            'total_ventas' => $ventas->count(),
            'total_ingresos' => $ventas->sum('total'),
            'ingresos_dia' => $ventas->where('turno', 0)->sum('total'),
            'ingresos_noche' => $ventas->where('turno', 1)->sum('total'),
            'con_comprobante' => $ventas->where('comprobante', 'SI')->count(),
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
        $request->validate([
            'fecha_venta' => 'required|date',
            'turno' => 'required|in:0,1',
            'id_prod_bod' => 'required|exists:dim_productos_bodega,id_prod_bod',
            'cantidad' => 'required|integer|min:1|max:9999',
            'id_met_pago' => 'required|exists:dim_met_pago,id_met_pago',
            'comprobante' => 'required|in:SI,NO',
        ]);

        try {
            // Obtener el precio actual del producto
            $producto = DimProductoBodega::findOrFail($request->id_prod_bod);

            $venta = new FactPagoProd();
            $venta->id_estadia = null; // Venta de bodega sin cliente
            $venta->fecha_venta = $request->fecha_venta;
            $venta->turno = $request->turno;
            $venta->id_prod_bod = $request->id_prod_bod;
            $venta->cantidad = $request->cantidad;
            $venta->precio_unitario = $producto->precio_actual; // Tomar precio actual del producto
            $venta->id_met_pago = $request->id_met_pago;
            $venta->comprobante = $request->comprobante;
            $venta->save();

            return redirect()->route('pagos-productos.index')
                ->with('success', 'Venta registrada correctamente.');

        } catch (\Exception $e) {
            return back()->withErrors('Error al registrar venta: ' . $e->getMessage())->withInput();
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