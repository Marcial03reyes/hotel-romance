<?php

namespace App\Http\Controllers;

use App\Models\DimProductoBodega;
use App\Models\FactCompraBodega;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProductosBodegaController extends Controller
{
    /**
     * Mostrar la vista principal con la tabla de productos y sus estadísticas
     * URL: /productos-bodega
     */
    public function index()
    {
        try {
            // Obtener productos con estadísticas calculadas
            $productos = DB::table('dim_productos_bodega as dpb')
                ->leftJoin('fact_compra_bodega as fcb', 'dpb.id_prod_bod', '=', 'fcb.id_prod_bod')
                ->leftJoin('fact_pago_prod as fpp', function($join) {
                    $join->on('dpb.id_prod_bod', '=', 'fpp.id_prod_bod')
                        ->whereNull('fpp.id_estadia'); // Solo ventas de bodega
                })
                ->select(
                    'dpb.id_prod_bod',
                    'dpb.nombre',
                    'dpb.precio_actual',
                    DB::raw('COALESCE(SUM(fcb.cantidad), 0) as unidades_compradas'),
                    DB::raw('COALESCE(SUM(fpp.cantidad), 0) as unidades_vendidas'),
                    DB::raw('COALESCE(SUM(fcb.cantidad), 0) - COALESCE(SUM(fpp.cantidad), 0) as stock'),
                    DB::raw('COALESCE(SUM(fcb.cantidad * fcb.precio_unitario), 0) as inversion_total'),
                    DB::raw('COUNT(DISTINCT fcb.id_compra_bodega) as total_compras'),
                    DB::raw('MAX(fcb.fecha_compra) as ultima_compra')
                )
                ->groupBy('dpb.id_prod_bod', 'dpb.nombre', 'dpb.precio_actual')
                ->orderBy('dpb.nombre')
                ->get();
            
            return view('productos-bodega.index', compact('productos'));
            
        } catch (\Exception $e) {
            return back()->withErrors('Error al cargar los productos: ' . $e->getMessage());
        }
    }

    /**
     * Ver historial completo de compras de un producto específico
     * URL: /productos-bodega/{id}/historial
     */
    public function historial($id)
    {
        try {
            // Buscar el producto o fallar con 404
            $producto = DimProductoBodega::findOrFail($id);
            
            // Obtener todas las compras de este producto ordenadas por fecha más reciente
            $historialCompras = FactCompraBodega::where('id_prod_bod', $id)
                ->orderByDesc('fecha_compra')
                ->orderByDesc('id_compra_bodega')
                ->get();
            
            // Calcular estadísticas totales de COMPRAS
            $totalComprado = $historialCompras->sum('cantidad');
            
            $inversionTotal = $historialCompras->sum(function($compra) {
                return $compra->cantidad * $compra->precio_unitario;
            });
            
            // Precio promedio de COMPRA
            $precioPromedioCompra = $totalComprado > 0 ? $inversionTotal / $totalComprado : 0;
            
            // Obtener estadísticas de VENTAS
            $ventas = DB::table('fact_pago_prod')
                ->where('id_prod_bod', $id)
                ->whereNull('id_estadia')
                ->selectRaw('
                    SUM(cantidad) as total_vendido,
                    SUM(cantidad * precio_unitario) as ingresos_totales,
                    MIN(fecha_venta) as primera_venta,
                    MAX(fecha_venta) as ultima_venta
                ')
                ->first();
            
            $totalVendido = $ventas->total_vendido ?? 0;
            $ingresosTotales = $ventas->ingresos_totales ?? 0;
            
            // Stock actual
            $stockActual = $totalComprado - $totalVendido;
            
            // === NUEVOS CÁLCULOS ===
            
            // 1. ROTACIÓN MENSUAL (Días que dura el stock)
            $rotacionMensual = null;
            if ($totalVendido > 0 && $ventas->primera_venta) {
                $diasTranscurridos = now()->diffInDays($ventas->primera_venta);
                if ($diasTranscurridos > 0) {
                    $ventasDiarias = $totalVendido / $diasTranscurridos;
                    $rotacionMensual = $ventasDiarias > 0 ? round($stockActual / $ventasDiarias) : 'N/A';
                }
            }
            
            // 2. GANANCIA MENSUAL PROMEDIO
            $gananciaMensual = null;
            if ($totalVendido > 0 && $ventas->primera_venta) {
                // Precio promedio de venta
                $precioPromedioVenta = $ingresosTotales / $totalVendido;
                
                // Ganancia total
                $gananciaTotal = ($precioPromedioVenta - $precioPromedioCompra) * $totalVendido;
                
                // Convertir a ganancia mensual
                $mesesTranscurridos = max(1, now()->diffInMonths($ventas->primera_venta));
                $gananciaMensual = $gananciaTotal / $mesesTranscurridos;
            }
            
            return view('productos-bodega.historial', compact(
                'producto', 
                'historialCompras', 
                'totalComprado', 
                'inversionTotal',
                'precioPromedioCompra',
                'totalVendido',
                'stockActual',
                'rotacionMensual',      // NUEVO
                'gananciaMensual'       // NUEVO
            ));
            
        } catch (\Exception $e) {
            return back()->withErrors('Error al cargar el historial: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar formulario para registrar nueva compra de un producto
     * URL: /productos-bodega/{id}/compra/create
     */
    public function createCompra($id)
    {
        try {
            // Verificar que el producto existe
            $producto = DimProductoBodega::findOrFail($id);
            
            // Obtener la fecha actual para el formulario
            $fechaActual = now()->format('Y-m-d');
            
            return view('productos-bodega.create-compra', compact('producto', 'fechaActual'));
            
        } catch (\Exception $e) {
            return back()->withErrors('Error al cargar el formulario: ' . $e->getMessage());
        }
    }

    /**
     * Guardar nueva compra en la base de datos
     * URL: POST /productos-bodega/{id}/compra
     */
    public function storeCompra(Request $request, $id)
    {
        // Verificar que el producto existe
        $producto = DimProductoBodega::findOrFail($id);

        // Validar los datos del formulario
        $validator = Validator::make($request->all(), [
            'cantidad' => [
                'required',
                'integer',
                'min:1',
                'max:9999'
            ],
            'precio_unitario' => [
                'required',
                'numeric',
                'min:0.01',
                'max:99999.99'
            ],
            'fecha_compra' => [
                'required',
                'date',
                'before_or_equal:' . now()->format('Y-m-d')
            ],
            'proveedor' => [
                'nullable',
                'string',
                'max:255'
            ]
        ], [
            // Mensajes personalizados
            'cantidad.required' => 'La cantidad es obligatoria',
            'cantidad.integer' => 'La cantidad debe ser un número entero',
            'cantidad.min' => 'La cantidad debe ser al menos 1',
            'cantidad.max' => 'La cantidad no puede exceder 9999',
            'precio_unitario.required' => 'El precio unitario es obligatorio',
            'precio_unitario.numeric' => 'El precio debe ser un número válido',
            'precio_unitario.min' => 'El precio debe ser mayor a 0',
            'precio_unitario.max' => 'El precio no puede exceder S/ 99,999.99',
            'fecha_compra.required' => 'La fecha de compra es obligatoria',
            'fecha_compra.date' => 'La fecha debe ser válida',
            'fecha_compra.before_or_equal' => 'La fecha no puede ser futura',
            'proveedor.max' => 'El nombre del proveedor es muy largo'
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Iniciar transacción para seguridad
            DB::beginTransaction();

            // Crear el registro de compra
            $compra = FactCompraBodega::create([
                'id_prod_bod' => $id,
                'cantidad' => $request->cantidad,
                'precio_unitario' => $request->precio_unitario,
                'fecha_compra' => $request->fecha_compra,
                'proveedor' => $request->proveedor ?: null
            ]);

            // Confirmar transacción
            DB::commit();

            // Calcular total de la compra para el mensaje
            $totalCompra = $request->cantidad * $request->precio_unitario;

            return redirect()
                ->route('productos-bodega.historial', $id)
                ->with('success', "Compra registrada exitosamente. {$request->cantidad} unidades por S/ " . number_format($totalCompra, 2));

        } catch (\Exception $e) {
            // Revertir transacción en caso de error
            DB::rollback();
            
            return back()
                ->withErrors('Error al registrar la compra: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Mostrar formulario para crear nuevo producto
     * URL: /productos-bodega/producto/create
     */
    public function createProducto()
    {
        return view('productos-bodega.create-producto');
    }

    /**
     * Guardar nuevo producto en la base de datos
     * URL: POST /productos-bodega/producto
     */
    public function storeProducto(Request $request)
    {
        // Validar datos del formulario
        $validator = Validator::make($request->all(), [
            'nombre' => [
                'required',
                'string',
                'max:50',
                'unique:dim_productos_bodega,nombre'
            ]
        ], [
            'nombre.required' => 'El nombre del producto es obligatorio',
            'nombre.max' => 'El nombre no puede exceder 50 caracteres',
            'nombre.unique' => 'Ya existe un producto con este nombre'
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Crear el producto usando el modelo con $fillable
            $producto = DimProductoBodega::create([
                'nombre' => trim($request->nombre),
                'precio_actual' => $request->precio_actual
            ]);

            return redirect()
                ->route('productos-bodega.index')
                ->with('success', "Producto '{$producto->nombre}' creado exitosamente");

        } catch (\Exception $e) {
            return back()
                ->withErrors('Error al crear el producto: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Eliminar una compra específica del historial
     * URL: DELETE /productos-bodega/{id}/compra/{compraId}
     */
    public function destroyCompra($id, $compraId)
    {
        try {
            // Verificar que el producto existe
            $producto = DimProductoBodega::findOrFail($id);
            
            // Buscar la compra específica que pertenece a este producto
            $compra = FactCompraBodega::where('id_compra_bodega', $compraId)
                                      ->where('id_prod_bod', $id)
                                      ->firstOrFail();
            
            // Guardar datos para el mensaje antes de eliminar
            $cantidad = $compra->cantidad;
            $fechaCompra = $compra->fecha_compra;
            
            // Eliminar la compra
            $compra->delete();

            return redirect()
                ->route('productos-bodega.historial', $id)
                ->with('success', "Compra eliminada: {$cantidad} unidades del {$fechaCompra}");

        } catch (\Exception $e) {
            return back()->withErrors('Error al eliminar la compra: ' . $e->getMessage());
        }
    }

    /**
     * Editar una compra específica
     * URL: /productos-bodega/{id}/compra/{compraId}/edit
     */
    public function editCompra($id, $compraId)
    {
        try {
            $producto = DimProductoBodega::findOrFail($id);
            
            $compra = FactCompraBodega::where('id_compra_bodega', $compraId)
                                      ->where('id_prod_bod', $id)
                                      ->firstOrFail();
            
            return view('productos-bodega.edit-compra', compact('producto', 'compra'));
            
        } catch (\Exception $e) {
            return back()->withErrors('Error al cargar la compra: ' . $e->getMessage());
        }
    }

    /**
     * Actualizar una compra específica
     * URL: PUT /productos-bodega/{id}/compra/{compraId}
     */
    public function updateCompra(Request $request, $id, $compraId)
    {
        // Verificar que el producto y la compra existen
        $producto = DimProductoBodega::findOrFail($id);
                    $compra = FactCompraBodega::where('id_compra_bodega', $compraId)
                                      ->where('id_prod_bod', $id)
                                      ->firstOrFail();

        // Validar (mismas reglas que store)
        $validator = Validator::make($request->all(), [
            'cantidad' => 'required|integer|min:1|max:9999',
            'precio_unitario' => 'required|numeric|min:0.01|max:99999.99',
            'fecha_compra' => 'required|date|before_or_equal:' . now()->format('Y-m-d'),
            'proveedor' => 'nullable|string|max:255'
        ], [
            'cantidad.required' => 'La cantidad es obligatoria',
            'cantidad.integer' => 'La cantidad debe ser un número entero',
            'cantidad.min' => 'La cantidad debe ser al menos 1',
            'cantidad.max' => 'La cantidad no puede exceder 9999',
            'precio_unitario.required' => 'El precio unitario es obligatorio',
            'precio_unitario.numeric' => 'El precio debe ser un número válido',
            'precio_unitario.min' => 'El precio debe ser mayor a 0',
            'precio_unitario.max' => 'El precio no puede exceder S/ 99,999.99',
            'fecha_compra.required' => 'La fecha de compra es obligatoria',
            'fecha_compra.date' => 'La fecha debe ser válida',
            'fecha_compra.before_or_equal' => 'La fecha no puede ser futura',
            'proveedor.max' => 'El nombre del proveedor es muy largo'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            // Actualizar la compra
            $compra->update([
                'cantidad' => $request->cantidad,
                'precio_unitario' => $request->precio_unitario,
                'fecha_compra' => $request->fecha_compra,
                'proveedor' => $request->proveedor ?: null
            ]);

            DB::commit();

            return redirect()
                ->route('productos-bodega.historial', $id)
                ->with('success', 'Compra actualizada exitosamente');

        } catch (\Exception $e) {
            DB::rollback();
            return back()
                ->withErrors('Error al actualizar la compra: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Eliminar un producto (solo si no tiene compras ni ventas registradas)
     * URL: DELETE /productos-bodega/{id}
     */
    public function destroyProducto($id)
    {
        try {
            $producto = DimProductoBodega::findOrFail($id);
            
            // Verificar que no tenga compras registradas
            $tieneCompras = FactCompraBodega::where('id_prod_bod', $id)->exists();
            
            // Verificar que no tenga ventas registradas
            $tieneVentas = DB::table('fact_pago_prod')
                ->where('id_prod_bod', $id)
                ->whereNull('id_estadia') // Solo ventas de bodega
                ->exists();
            
            if ($tieneCompras || $tieneVentas) {
                return back()->withErrors('No se puede eliminar el producto porque tiene compras o ventas registradas.');
            }
            
            $nombreProducto = $producto->nombre;
            $producto->delete();
            
            return redirect()
                ->route('productos-bodega.index')
                ->with('success', "Producto '{$nombreProducto}' eliminado exitosamente");
                
        } catch (\Exception $e) {
            return back()->withErrors('Error al eliminar el producto: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar formulario para editar un producto
     * URL: /productos-bodega/{id}/edit
     */
    public function editProducto($id)
    {
        try {
            $producto = DimProductoBodega::findOrFail($id);
            return view('productos-bodega.edit-producto', compact('producto'));
        } catch (\Exception $e) {
            return back()->withErrors('Error al cargar el producto: ' . $e->getMessage());
        }
    }

    /**
     * Actualizar un producto
     * URL: PUT /productos-bodega/{id}
     */
    public function updateProducto(Request $request, $id)
    {
        $producto = DimProductoBodega::findOrFail($id);

        // Validar datos del formulario
        $validator = Validator::make($request->all(), [
            'nombre' => [
                'required',
                'string',
                'max:50',
                'unique:dim_productos_bodega,nombre,' . $id . ',id_prod_bod'
            ],
            'precio_actual' => [
                'required',
                'numeric',
                'min:0.01',
                'max:9999.99'
            ]
        ], [
            'nombre.required' => 'El nombre del producto es obligatorio',
            'nombre.max' => 'El nombre no puede exceder 50 caracteres',
            'nombre.unique' => 'Ya existe un producto con este nombre',
            'precio_actual.required' => 'El precio es obligatorio',
            'precio_actual.numeric' => 'El precio debe ser un número válido',
            'precio_actual.min' => 'El precio debe ser mayor a 0',
            'precio_actual.max' => 'El precio no puede exceder S/ 9,999.99'
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Actualizar el producto
            $producto->update([
                'nombre' => trim($request->nombre),
                'precio_actual' => $request->precio_actual
            ]);

            return redirect()
                ->route('productos-bodega.index')
                ->with('success', "Producto actualizado exitosamente");

        } catch (\Exception $e) {
            return back()
                ->withErrors('Error al actualizar el producto: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * API endpoint para obtener estadísticas de un producto (opcional)
     * URL: /api/productos-bodega/{id}/stats
     */
    public function getProductoStats($id)
    {
        try {
            $producto = DimProductoBodega::findOrFail($id);
            
            // Obtener estadísticas de compras
            $compras = FactCompraBodega::where('id_prod_bod', $id)->get();
            $unidadesCompradas = $compras->sum('cantidad');
            $inversionTotal = $compras->sum(function($compra) {
                return $compra->cantidad * $compra->precio_unitario;
            });
            
            // Obtener estadísticas de ventas
            $unidadesVendidas = DB::table('fact_pago_prod')
                ->where('id_prod_bod', $id)
                ->whereNull('id_estadia') // Solo ventas de bodega
                ->sum('cantidad');
            
            $stats = [
                'nombre' => $producto->nombre,
                'unidades_compradas' => $unidadesCompradas,
                'unidades_vendidas' => $unidadesVendidas,
                'stock_actual' => $unidadesCompradas - $unidadesVendidas,
                'total_compras' => $compras->count(),
                'inversion_total' => $inversionTotal,
                'ultima_compra' => $compras->sortByDesc('fecha_compra')->first()?->fecha_compra
            ];
            
            return response()->json($stats);
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    /**
     * Buscar productos por nombre (para AJAX)
     * URL: /api/productos-bodega/search
     */
    public function searchProductos(Request $request)
    {
        try {
            $query = $request->get('q', '');
            
            $productos = DimProductoBodega::where('nombre', 'LIKE', "%{$query}%")
                ->orderBy('nombre')
                ->limit(10)
                ->get(['id_prod_bod', 'nombre']);
            
            return response()->json($productos);
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}