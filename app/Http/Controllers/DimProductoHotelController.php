<?php

namespace App\Http\Controllers;

use App\Models\DimProductoHotel;
use App\Models\FactCompraInterna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DimProductoHotelController extends Controller
{
    /**
     * Mostrar la vista principal con la tabla de productos y sus estadísticas
     * URL: /productos-hotel
     */
    public function index()
    {
        try {
            // Obtener productos con estadísticas calculadas (usando los nombres de columna correctos)
            $productos = DB::table('dim_productos_hotel as dph')
                ->leftJoin('fact_compra_interna as fci', 'dph.id_prod_hotel', '=', 'fci.id_prod_bod')
                ->select(
                    'dph.id_prod_hotel',
                    'dph.nombre',
                    DB::raw('COALESCE(SUM(fci.cantidad), 0) as total_comprado'),
                    DB::raw('COALESCE(SUM(fci.cantidad * fci.precio_unitario), 0) as inversion_total'),
                    DB::raw('COUNT(DISTINCT fci.id_compra_interna) as total_compras'),
                    DB::raw('MAX(fci.fecha_compra) as ultima_compra'),
                    DB::raw('MIN(fci.fecha_compra) as primera_compra')
                )
                ->groupBy('dph.id_prod_hotel', 'dph.nombre')
                ->orderBy('dph.nombre')
                ->get();

            // Calcular indicadores para cada producto
            foreach ($productos as $producto) {
                $productoModel = DimProductoHotel::find($producto->id_prod_hotel);
                if ($productoModel) {
                    $producto->indicador_recompra = $productoModel->indicador_recompra;
                    $producto->frecuencia_compra = $productoModel->frecuencia_compra;
                    $producto->dias_desde_ultima = $productoModel->dias_desde_ultima_compra;
                } else {
                    $producto->indicador_recompra = [
                        'estado' => 'sin_datos',
                        'mensaje' => 'Sin datos suficientes',
                        'color' => 'gray',
                        'icono' => 'bx-question-mark'
                    ];
                    $producto->frecuencia_compra = null;
                    $producto->dias_desde_ultima = null;
                }
            }
            
            return view('productos-hotel.index', compact('productos'));
            
        } catch (\Exception $e) {
            return back()->withErrors('Error al cargar los productos: ' . $e->getMessage());
        }
    }

    /**
     * Ver historial completo de compras de un producto específico
     * URL: /productos-hotel/{id}/historial
     */
    public function historial($id)
    {
        try {
            // Buscar el producto o fallar con 404
            $producto = DimProductoHotel::findOrFail($id);
            
            // Obtener todas las compras de este producto ordenadas por fecha más reciente
            $historialCompras = FactCompraInterna::where('id_prod_bod', $id)
                ->orderByDesc('fecha_compra')
                ->orderByDesc('created_at')
                ->get();
            
            // Calcular estadísticas totales
            $totalComprado = $historialCompras->sum('cantidad');
            
            $inversionTotal = $historialCompras->sum(function($compra) {
                return $compra->cantidad * $compra->precio_unitario;
            });
            
            // Calcular precio promedio por unidad
            $precioPromedio = $totalComprado > 0 ? $inversionTotal / $totalComprado : 0;
            
            // Obtener indicadores
            $indicadorRecompra = $producto->indicador_recompra;
            $frecuenciaCompra = $producto->frecuencia_compra;
            $diasDesdeUltima = $producto->dias_desde_ultima_compra;
            
            return view('productos-hotel.historial', compact(
                'producto', 
                'historialCompras', 
                'totalComprado', 
                'inversionTotal',
                'precioPromedio',
                'indicadorRecompra',
                'frecuenciaCompra',
                'diasDesdeUltima'
            ));
            
        } catch (\Exception $e) {
            return back()->withErrors('Error al cargar el historial: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar formulario para registrar nueva compra de un producto
     * URL: /productos-hotel/{id}/compra/create
     */
    public function createCompra($id)
    {
        try {
            // Verificar que el producto existe
            $producto = DimProductoHotel::findOrFail($id);
            
            // Obtener la fecha actual para el formulario
            $fechaActual = now()->format('Y-m-d');
            
            return view('productos-hotel.create-compra', compact('producto', 'fechaActual'));
            
        } catch (\Exception $e) {
            return back()->withErrors('Error al cargar el formulario: ' . $e->getMessage());
        }
    }

    /**
     * Guardar nueva compra en la base de datos
     * URL: POST /productos-hotel/{id}/compra
     */
    public function storeCompra(Request $request, $id)
    {
        // Verificar que el producto existe
        $producto = DimProductoHotel::findOrFail($id);

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

            // Crear el registro de compra (usando el campo correcto)
            $compra = FactCompraInterna::create([
                'id_prod_bod' => $id, // Campo correcto
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
                ->route('productos-hotel.historial', $id)
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
     * URL: /productos-hotel/producto/create
     */
    public function createProducto()
    {
        return view('productos-hotel.create-producto');
    }

    /**
     * Guardar nuevo producto en la base de datos
     * URL: POST /productos-hotel/producto
     */
    public function storeProducto(Request $request)
    {
        // Validar datos del formulario
        $validator = Validator::make($request->all(), [
            'nombre' => [
                'required',
                'string',
                'max:50',
                'unique:dim_productos_hotel,nombre'
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
            $producto = DimProductoHotel::create([
                'nombre' => trim($request->nombre)
            ]);

            return redirect()
                ->route('productos-hotel.index')
                ->with('success', "Producto '{$producto->nombre}' creado exitosamente");

        } catch (\Exception $e) {
            return back()
                ->withErrors('Error al crear el producto: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Eliminar una compra específica del historial
     * URL: DELETE /productos-hotel/{id}/compra/{compraId}
     */
    public function destroyCompra($id, $compraId)
    {
        try {
            // Verificar que el producto existe
            $producto = DimProductoHotel::findOrFail($id);
            
            // Buscar la compra específica que pertenece a este producto
            $compra = FactCompraInterna::where('id_compra_interna', $compraId)
                                      ->where('id_prod_bod', $id)
                                      ->firstOrFail();
            
            // Guardar datos para el mensaje antes de eliminar
            $cantidad = $compra->cantidad;
            $fechaCompra = $compra->fecha_compra;
            
            // Eliminar la compra
            $compra->delete();

            return redirect()
                ->route('productos-hotel.historial', $id)
                ->with('success', "Compra eliminada: {$cantidad} unidades del {$fechaCompra}");

        } catch (\Exception $e) {
            return back()->withErrors('Error al eliminar la compra: ' . $e->getMessage());
        }
    }

    /**
     * Editar una compra específica
     * URL: /productos-hotel/{id}/compra/{compraId}/edit
     */
    public function editCompra($id, $compraId)
    {
        try {
            $producto = DimProductoHotel::findOrFail($id);
            
            $compra = FactCompraInterna::where('id_compra_interna', $compraId)
                                      ->where('id_prod_bod', $id)
                                      ->firstOrFail();
            
            return view('productos-hotel.edit-compra', compact('producto', 'compra'));
            
        } catch (\Exception $e) {
            return back()->withErrors('Error al cargar la compra: ' . $e->getMessage());
        }
    }

    /**
     * Actualizar una compra específica
     * URL: PUT /productos-hotel/{id}/compra/{compraId}
     */
    public function updateCompra(Request $request, $id, $compraId)
    {
        // Verificar que el producto y la compra existen
        $producto = DimProductoHotel::findOrFail($id);
        $compra = FactCompraInterna::where('id_compra_interna', $compraId)
                                  ->where('id_prod_bod', $id)
                                  ->firstOrFail();

        // Validar (mismas reglas que store)
        $validator = Validator::make($request->all(), [
            'cantidad' => 'required|integer|min:1|max:9999',
            'precio_unitario' => 'required|numeric|min:0.01|max:99999.99',
            'fecha_compra' => 'required|date|before_or_equal:' . now()->format('Y-m-d'),
            'proveedor' => 'nullable|string|max:255'
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
                ->route('productos-hotel.historial', $id)
                ->with('success', 'Compra actualizada exitosamente');

        } catch (\Exception $e) {
            DB::rollback();
            return back()
                ->withErrors('Error al actualizar la compra: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Eliminar un producto (solo si no tiene compras registradas)
     * URL: DELETE /productos-hotel/{id}
     */
    public function destroyProducto($id)
    {
        try {
            $producto = DimProductoHotel::findOrFail($id);
            
            // Verificar que no tenga compras registradas
            $tieneCompras = FactCompraInterna::where('id_prod_bod', $id)->exists();
            
            if ($tieneCompras) {
                return back()->withErrors('No se puede eliminar el producto porque tiene compras registradas.');
            }
            
            $nombreProducto = $producto->nombre;
            $producto->delete();
            
            return redirect()
                ->route('productos-hotel.index')
                ->with('success', "Producto '{$nombreProducto}' eliminado exitosamente");
                
        } catch (\Exception $e) {
            return back()->withErrors('Error al eliminar el producto: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar formulario para editar un producto
     * URL: /productos-hotel/{id}/edit
     */
    public function editProducto($id)
    {
        try {
            $producto = DimProductoHotel::findOrFail($id);
            return view('productos-hotel.edit-producto', compact('producto'));
        } catch (\Exception $e) {
            return back()->withErrors('Error al cargar el producto: ' . $e->getMessage());
        }
    }

    /**
     * Actualizar un producto
     * URL: PUT /productos-hotel/{id}
     */
    public function updateProducto(Request $request, $id)
    {
        $producto = DimProductoHotel::findOrFail($id);

        // Validar datos del formulario
        $validator = Validator::make($request->all(), [
            'nombre' => [
                'required',
                'string',
                'max:50',
                'unique:dim_productos_hotel,nombre,' . $id . ',id_prod_hotel'
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
            // Actualizar el producto
            $producto->update([
                'nombre' => trim($request->nombre)
            ]);

            return redirect()
                ->route('productos-hotel.index')
                ->with('success', "Producto actualizado exitosamente");

        } catch (\Exception $e) {
            return back()
                ->withErrors('Error al actualizar el producto: ' . $e->getMessage())
                ->withInput();
        }
    }
}