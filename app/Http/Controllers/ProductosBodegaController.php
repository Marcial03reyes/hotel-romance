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
            $productos = DB::table('dim_productos_bodega as dpb')
                ->leftJoin(DB::raw('(
                    SELECT 
                        id_prod_bod,
                        SUM(cantidad) as unidades_compradas,
                        SUM(cantidad * precio_unitario) as inversion_total,
                        COUNT(*) as total_compras,
                        MAX(fecha_compra) as ultima_compra
                    FROM fact_compra_bodega
                    GROUP BY id_prod_bod
                ) as compras'), 'dpb.id_prod_bod', '=', 'compras.id_prod_bod')
                ->leftJoin(DB::raw('(
                    SELECT 
                        id_prod_bod,
                        SUM(CASE WHEN cantidad > 0 THEN cantidad ELSE 0 END) as unidades_vendidas
                    FROM fact_pago_prod
                    WHERE id_estadia IS NULL
                    GROUP BY id_prod_bod
                ) as ventas'), 'dpb.id_prod_bod', '=', 'ventas.id_prod_bod')
                ->select(
                    'dpb.id_prod_bod',
                    'dpb.nombre',
                    'dpb.precio_actual',
                    'dpb.stock_inicial',
                    DB::raw('COALESCE(compras.unidades_compradas, 0) as unidades_compradas'),
                    DB::raw('COALESCE(ventas.unidades_vendidas, 0) as unidades_vendidas'),
                    DB::raw('dpb.stock_inicial + COALESCE(compras.unidades_compradas, 0) - COALESCE(ventas.unidades_vendidas, 0) as stock'),
                    DB::raw('COALESCE(compras.inversion_total, 0) as inversion_total'),
                    DB::raw('COALESCE(compras.total_compras, 0) as total_compras'),
                    'compras.ultima_compra'
                )
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
                SUM(CASE WHEN cantidad > 0 THEN cantidad ELSE 0 END) as total_vendido,
                SUM(cantidad * precio_unitario) as ingresos_totales,
                MIN(fecha_venta) as primera_venta,
                MAX(fecha_venta) as ultima_venta
                ')
                ->first();
            
            $totalVendido = $ventas->total_vendido ?? 0;
            $ingresosTotales = $ventas->ingresos_totales ?? 0;
            
            // Stock actual
            $stockActual = $totalComprado - $totalVendido;
            
            // === ROTACIÓN MENSUAL MEJORADA (Días que durará el stock) ===
            // Inicializar la variable
            $rotacionMensual = null;
            
            // Obtener ventas de diferentes períodos para mejor análisis
            $ventasUltimos30Dias = DB::table('fact_pago_prod')
                ->where('id_prod_bod', $id)
                ->whereNull('id_estadia')
                ->where('fecha_venta', '>=', now()->subDays(30))
                ->sum('cantidad');
            
            $ventasUltimos90Dias = DB::table('fact_pago_prod')
                ->where('id_prod_bod', $id)
                ->whereNull('id_estadia')
                ->where('fecha_venta', '>=', now()->subDays(90))
                ->sum('cantidad');
            
            // Calcular días de cobertura con lógica mejorada
            if ($stockActual == 0) {
                $rotacionMensual = 0; // Sin stock
            } elseif ($ventasUltimos30Dias > 0) {
                // Prioridad 1: Usar ventas de los últimos 30 días (más preciso)
                $ventasDiariasPromedio = $ventasUltimos30Dias / 30;
                $rotacionMensual = round($stockActual / $ventasDiariasPromedio);
            } elseif ($ventasUltimos90Dias > 0) {
                // Prioridad 2: Usar ventas de los últimos 90 días
                $ventasDiariasPromedio = $ventasUltimos90Dias / 90;
                $rotacionMensual = round($stockActual / $ventasDiariasPromedio);
            } elseif ($totalVendido > 0 && $ventas->primera_venta) {
                // Prioridad 3: Usar todo el histórico
                $diasTranscurridos = now()->diffInDays($ventas->primera_venta);
                if ($diasTranscurridos > 0) {
                    $ventasDiariasPromedio = $totalVendido / $diasTranscurridos;
                    $rotacionMensual = round($stockActual / $ventasDiariasPromedio);
                }
            } else {
                // Si no hay ventas, mostrar "N/A" o un valor alto
                $rotacionMensual = $stockActual > 0 ? 'Sin ventas' : 0;
            }
            
            // === GANANCIA MENSUAL PROMEDIO MEJORADA ===
            // Inicializar la variable
            $gananciaMensual = null;
            
            // Calcular basado en ventas de los últimos 3 meses para mayor precisión
            $hace3Meses = now()->subMonths(3)->startOfDay();
            
            $ventasUltimos3Meses = DB::table('fact_pago_prod')
                ->where('id_prod_bod', $id)
                ->whereNull('id_estadia')
                ->where('fecha_venta', '>=', $hace3Meses)
                ->selectRaw('
                    SUM(cantidad) as cantidad_vendida,
                    SUM(cantidad * precio_unitario) as ingresos
                ')
                ->first();
            
            // Contar meses distintos con ventas
            $mesesConVentas = DB::table('fact_pago_prod')
                ->where('id_prod_bod', $id)
                ->whereNull('id_estadia')
                ->where('fecha_venta', '>=', $hace3Meses)
                ->selectRaw('COUNT(DISTINCT DATE_FORMAT(fecha_venta, "%Y-%m")) as meses')
                ->value('meses');
            
            if ($ventasUltimos3Meses && $ventasUltimos3Meses->cantidad_vendida > 0) {
                // Precio promedio de venta en los últimos 3 meses
                $precioPromedioVentaReciente = $ventasUltimos3Meses->ingresos / $ventasUltimos3Meses->cantidad_vendida;
                
                // Obtener el costo promedio de las compras más recientes
                $comprasRecientes = $historialCompras
                    ->sortByDesc('fecha_compra')
                    ->take(5); // Últimas 5 compras
                
                if ($comprasRecientes->count() > 0) {
                    $cantidadComprasRecientes = $comprasRecientes->sum('cantidad');
                    $inversionComprasRecientes = $comprasRecientes->sum(function($c) {
                        return $c->cantidad * $c->precio_unitario;
                    });
                    
                    $costoPromedioReciente = $cantidadComprasRecientes > 0 ? 
                        $inversionComprasRecientes / $cantidadComprasRecientes : 
                        $precioPromedioCompra;
                } else {
                    // Si no hay compras recientes, usar el promedio general
                    $costoPromedioReciente = $precioPromedioCompra;
                }
                
                // Calcular ganancia por unidad
                $gananciaPorUnidad = $precioPromedioVentaReciente - $costoPromedioReciente;
                
                // Calcular ganancia total y promedio mensual
                $gananciaTotal3Meses = $gananciaPorUnidad * $ventasUltimos3Meses->cantidad_vendida;
                
                // Dividir entre meses con ventas (no solo 3)
                $divisorMeses = max(1, $mesesConVentas); // Mínimo 1 para evitar división por 0
                $gananciaMensual = $gananciaTotal3Meses / $divisorMeses;
                
            } elseif ($totalVendido > 0 && $ventas->primera_venta) {
                // Fallback: usar todo el histórico si no hay ventas recientes
                $precioPromedioVenta = $ingresosTotales / $totalVendido;
                $gananciaTotal = ($precioPromedioVenta - $precioPromedioCompra) * $totalVendido;
                
                $mesesTranscurridos = max(1, now()->diffInMonths($ventas->primera_venta));
                $gananciaMensual = $gananciaTotal / $mesesTranscurridos;
                
            } else {
                // No hay ventas, ganancia es 0
                $gananciaMensual = 0;
            }
            
            return view('productos-bodega.historial', compact(
                'producto', 
                'historialCompras', 
                'totalComprado', 
                'inversionTotal',
                'precioPromedioCompra',
                'totalVendido',
                'stockActual',
                'rotacionMensual',      // Variable correctamente definida
                'gananciaMensual'       // Variable correctamente definida
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
            ],

            'stock_inicial' => [  
                'nullable',
                'integer',
                'min:0',
                'max:9999'
            ],
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
                'precio_actual' => $request->precio_actual,
                'stock_inicial' => $request->stock_inicial ?? 0
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
                'precio_actual' => $request->precio_actual,
                'stock_inicial' => $request->stock_inicial ?? $producto->stock_inicial
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