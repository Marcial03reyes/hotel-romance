<?php

use App\Http\Controllers\ProductosBodegaController;
use App\Http\Controllers\DimProductoHotelController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DimRegistroClienteController;
use App\Http\Controllers\FactRegistroClienteController;
use App\Http\Controllers\FactTrabajadorController;
use App\Http\Controllers\DimProductoBodegaController;
use App\Http\Controllers\DimMetPagoController;
use App\Http\Controllers\DimTipoGastoController;
use App\Http\Controllers\FactCompraInternaController;
use App\Http\Controllers\FactPagoHabController;
use App\Http\Controllers\FactPagoProdController;
use App\Http\Controllers\FactHorarioController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GraficaController;
use App\Http\Controllers\FactSunatController;
use App\Http\Controllers\FactInversionController;
use App\Http\Controllers\CuadreCajaController;
use App\Http\Controllers\GastosFijosController;
use App\Http\Controllers\FactGastoGeneralController;

/*
|--------------------------------------------------------------------------
| RUTA PRINCIPAL - Redirección automática
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return Auth::check() 
        ? redirect()->route('dashboard') 
        : redirect()->route('login');
})->name('home');

/*
|--------------------------------------------------------------------------
| RUTAS DE AUTENTICACIÓN (Solo para invitados)
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
});

// Logout (requiere autenticación)
Route::post('logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| RUTAS PARA TODOS LOS USUARIOS AUTENTICADOS
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    
    /*
    |----------------------------------------------------------------------
    | DASHBOARD - ✅ USAR CONTROLADOR
    |----------------------------------------------------------------------
    */
    Route::get('dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    
    // APIs para datos dinámicos del dashboard
    Route::get('api/dashboard/ingresos-turno', [App\Http\Controllers\DashboardController::class, 'getIngresosTurnoDinamico'])
        ->name('api.dashboard.ingresos-turno');
    Route::get('api/dashboard/ocupacion', [App\Http\Controllers\DashboardController::class, 'getOcupacionDinamica'])
        ->name('api.dashboard.ocupacion');

    /*
    |----------------------------------------------------------------------
    | GESTIÓN DE HOTEL - ACCESO PARA TODOS
    |----------------------------------------------------------------------
    */
    
    // ✅ REGISTRO DE HABITACIONES (Estadías) - TODOS pueden acceder
    Route::prefix('registros')->name('registros.')->group(function () {
        // ✅ LOOKUP cliente (DENTRO del grupo, más organizado)
        Route::get('lookup-cliente', [FactRegistroClienteController::class, 'lookupCliente'])
             ->name('lookup-cliente');
        
        // ✅ RUTAS BÁSICAS DE REGISTRO
        Route::get('/', [FactRegistroClienteController::class, 'index'])->name('index');
        Route::get('create', [FactRegistroClienteController::class, 'create'])->name('create');
        Route::post('/', [FactRegistroClienteController::class, 'store'])->name('store');
        Route::get('{id}/edit', [FactRegistroClienteController::class, 'edit'])->name('edit');
        Route::put('{id}', [FactRegistroClienteController::class, 'update'])->name('update');
        
        // ✅ ELIMINAR ESTADÍA (AL FINAL para evitar conflictos)
        Route::delete('{id}', [FactRegistroClienteController::class, 'destroy'])->name('destroy');
    });
    
    // ✅ CLIENTES - TODOS pueden acceder
    Route::resource('clientes', DimRegistroClienteController::class);
    Route::post('/clientes/store-ajax', [FactRegistroClienteController::class, 'storeCliente'])
        ->name('clientes.store-ajax');

    /*
    |----------------------------------------------------------------------
    | CONFIGURACIÓN DE PERFIL - TODOS pueden acceder
    |----------------------------------------------------------------------
    */
    
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('show');
        Route::patch('/name', [ProfileController::class, 'updateName'])->name('update.name');
        Route::patch('/password', [ProfileController::class, 'updatePassword'])->name('update.password');
    });

    /*
    |----------------------------------------------------------------------
    | API ENDPOINTS - TODOS pueden acceder
    |----------------------------------------------------------------------
    */
    
    Route::get('api/clientes/lookup', function(Request $request) {
        $doc = $request->query('doc');
        
        if (!$doc) {
            return response()->json(['ok' => false, 'message' => 'No document provided']);
        }
        
        $cliente = \App\Models\DimRegistroCliente::where('doc_identidad', $doc)->first();
        
        if ($cliente) {
            return response()->json([
                'ok' => true,
                'nombre_apellido' => $cliente->nombre_apellido
            ]);
        }
        
        return response()->json(['ok' => false, 'message' => 'Cliente no encontrado']);
    })->name('clientes.lookup');
});

/*
|--------------------------------------------------------------------------
| RUTAS SOLO PARA ADMINISTRADORES
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin'])->group(function () {
    
    /*
    |----------------------------------------------------------------------
    | GESTIÓN FINANCIERA - SOLO ADMINISTRADORES
    |----------------------------------------------------------------------
    */
    
    // GASTOS GENERALES
    Route::prefix('gastos')->name('gastos.')->group(function () {
        Route::get('/', [FactGastoGeneralController::class, 'index'])->name('index');
        Route::get('create', [FactGastoGeneralController::class, 'create'])->name('create');
        Route::post('/', [FactGastoGeneralController::class, 'store'])->name('store');
        Route::get('{id}/edit', [FactGastoGeneralController::class, 'edit'])->name('edit');
        Route::put('{id}', [FactGastoGeneralController::class, 'update'])->name('update');
        Route::delete('{id}', [FactGastoGeneralController::class, 'destroy'])->name('destroy');
        
        // Eliminación múltiple
        Route::delete('multiple', [FactGastoGeneralController::class, 'destroyMultiple'])->name('destroy.multiple');
    });
    
    // GASTOS FIJOS 
    Route::prefix('gastos-fijos')->name('gastos-fijos.')->group(function () {
        // Lista principal
        Route::get('/', [GastosFijosController::class, 'index'])->name('index');
        
        // Crear nuevo servicio
        Route::get('create', [GastosFijosController::class, 'create'])->name('create');
        Route::post('/', [GastosFijosController::class, 'store'])->name('store');
        
        // Editar servicio
        Route::get('{id}/edit', [GastosFijosController::class, 'edit'])->name('edit');
        Route::put('{id}', [GastosFijosController::class, 'update'])->name('update');
        Route::delete('{id}', [GastosFijosController::class, 'destroy'])->name('destroy');
        
        // Historial de pagos
        Route::get('{id}/historial', [GastosFijosController::class, 'historial'])->name('historial');
        
        // Registrar nuevo pago
        Route::get('{id}/pago/create', [GastosFijosController::class, 'createPago'])->name('create-pago');
        Route::post('{id}/pago', [GastosFijosController::class, 'storePago'])->name('store-pago');
        
        // Editar/eliminar pago específico
        Route::get('{id}/pago/{pagoId}/edit', [GastosFijosController::class, 'editPago'])->name('edit-pago');
        Route::put('{id}/pago/{pagoId}', [GastosFijosController::class, 'updatePago'])->name('update-pago');
        Route::delete('{id}/pago/{pagoId}', [GastosFijosController::class, 'destroyPago'])->name('destroy-pago');
        
        // Ver comprobante
        Route::get('{id}/pago/{pagoId}/comprobante', [GastosFijosController::class, 'verComprobante'])->name('ver-comprobante');
    });

    // COMPROBANTES SUNAT
    Route::prefix('sunat')->name('sunat.')->group(function () {
        Route::get('/', [FactSunatController::class, 'index'])->name('index');
        Route::get('create', [FactSunatController::class, 'create'])->name('create');
        Route::post('/', [FactSunatController::class, 'store'])->name('store');
        Route::get('{id}/edit', [FactSunatController::class, 'edit'])->name('edit');
        Route::put('{id}', [FactSunatController::class, 'update'])->name('update');
        Route::delete('{id}', [FactSunatController::class, 'destroy'])->name('destroy');
        
        // Funcionalidades adicionales
        Route::delete('multiple', [FactSunatController::class, 'destroyMultiple'])->name('destroy.multiple');
        Route::get('{id}/archivo', [FactSunatController::class, 'showArchivo'])->name('archivo');
        Route::get('{id}/archivo/download', [FactSunatController::class, 'downloadArchivo'])->name('archivo.download');
    });

    // INVERSIONES 
    Route::prefix('inversiones')->name('inversiones.')->group(function () {
        Route::get('/', [FactInversionController::class, 'index'])->name('index');
        Route::get('create', [FactInversionController::class, 'create'])->name('create');
        Route::post('/', [FactInversionController::class, 'store'])->name('store');
        Route::get('{id}/edit', [FactInversionController::class, 'edit'])->name('edit');
        Route::put('{id}', [FactInversionController::class, 'update'])->name('update');
        Route::delete('{id}', [FactInversionController::class, 'destroy'])->name('destroy');
        
        // Eliminación múltiple
        Route::delete('multiple', [FactInversionController::class, 'destroyMultiple'])->name('destroy.multiple');
    });

    // CUADRE DE CAJA 
    Route::get('/cuadre-caja', [App\Http\Controllers\CuadreCajaController::class, 'index'])->name('cuadre-caja.index');

    // PAGOS DE HABITACIÓN
    Route::resource('pagos-habitacion', FactPagoHabController::class);
    
    // PAGOS DE PRODUCTOS
    Route::resource('pagos-productos', FactPagoProdController::class);

    Route::get('api/productos/{id}/precio', [FactPagoProdController::class, 'getPrecioProducto']);

    /*
    |----------------------------------------------------------------------
    | ADMINISTRACIÓN - SOLO ADMINISTRADORES
    |----------------------------------------------------------------------
    */
    
    // TRABAJADORES
    Route::resource('trabajadores', FactTrabajadorController::class)
        ->parameters(['trabajadores' => 'dni']);
    
    // HORARIO TRABAJADORES
    Route::resource('horarios', FactHorarioController::class);
    Route::post('horarios/asignar-completo', [FactHorarioController::class, 'asignarHorarioCompleto'])->name('horarios.asignar-completo');
    
    /*
    |----------------------------------------------------------------------
    | PRODUCTOS - SOLO ADMINISTRADORES
    |----------------------------------------------------------------------
    */

    // PRODUCTOS BODEGA (Completo con ProductosBodegaController)
    Route::prefix('productos-bodega')->name('productos-bodega.')->group(function () {
        // Lista principal de productos
        Route::get('/', [ProductosBodegaController::class, 'index'])->name('index');
        
        // Crear nuevo producto
        Route::get('producto/create', [ProductosBodegaController::class, 'createProducto'])->name('create-producto');
        Route::post('producto', [ProductosBodegaController::class, 'storeProducto'])->name('store-producto');
        
        // Editar producto
        Route::get('{id}/edit', [ProductosBodegaController::class, 'editProducto'])->name('edit-producto');
        Route::put('{id}', [ProductosBodegaController::class, 'updateProducto'])->name('update-producto');
        Route::delete('{id}', [ProductosBodegaController::class, 'destroyProducto'])->name('destroy-producto');
        
        // Historial de compras de un producto
        Route::get('{id}/historial', [ProductosBodegaController::class, 'historial'])->name('historial');
        
        // Registrar nueva compra
        Route::get('{id}/compra/create', [ProductosBodegaController::class, 'createCompra'])->name('create-compra');
        Route::post('{id}/compra', [ProductosBodegaController::class, 'storeCompra'])->name('store-compra');
        
        // Editar/eliminar compra específica
        Route::get('{id}/compra/{compraId}/edit', [ProductosBodegaController::class, 'editCompra'])->name('edit-compra');
        Route::put('{id}/compra/{compraId}', [ProductosBodegaController::class, 'updateCompra'])->name('update-compra');
        Route::delete('{id}/compra/{compraId}', [ProductosBodegaController::class, 'destroyCompra'])->name('destroy-compra');
    });
    
    // PRODUCTOS HOTEL (Completo con DimProductoHotelController)
    Route::prefix('productos-hotel')->name('productos-hotel.')->group(function () {
        // Lista principal de productos
        Route::get('/', [DimProductoHotelController::class, 'index'])->name('index');
        
        // Crear nuevo producto
        Route::get('producto/create', [DimProductoHotelController::class, 'createProducto'])->name('create-producto');
        Route::post('producto', [DimProductoHotelController::class, 'storeProducto'])->name('store-producto');
        
        // Editar producto
        Route::get('{id}/edit', [DimProductoHotelController::class, 'editProducto'])->name('edit-producto');
        Route::put('{id}', [DimProductoHotelController::class, 'updateProducto'])->name('update-producto');
        Route::delete('{id}', [DimProductoHotelController::class, 'destroyProducto'])->name('destroy-producto');
        
        // Historial de compras de un producto
        Route::get('{id}/historial', [DimProductoHotelController::class, 'historial'])->name('historial');
        
        // Registrar nueva compra
        Route::get('{id}/compra/create', [DimProductoHotelController::class, 'createCompra'])->name('create-compra');
        Route::post('{id}/compra', [DimProductoHotelController::class, 'storeCompra'])->name('store-compra');
        
        // Editar/eliminar compra específica
        Route::get('{id}/compra/{compraId}/edit', [DimProductoHotelController::class, 'editCompra'])->name('edit-compra');
        Route::put('{id}/compra/{compraId}', [DimProductoHotelController::class, 'updateCompra'])->name('update-compra');
        Route::delete('{id}/compra/{compraId}', [DimProductoHotelController::class, 'destroyCompra'])->name('destroy-compra');
    });
    
    // COMPRAS INTERNAS (Para mantener compatibilidad con rutas existentes)
    Route::resource('compras-internas', FactCompraInternaController::class);

    /*
    |----------------------------------------------------------------------
    | CONFIGURACIÓN Y CATÁLOGOS - SOLO ADMINISTRADORES
    |----------------------------------------------------------------------
    */
    
    // MÉTODOS DE PAGO
    Route::resource('metodos-pago', DimMetPagoController::class);
    
    // TIPOS DE GASTO
    Route::resource('tipos-gasto', DimTipoGastoController::class);

    // GRÁFICAS Y REPORTES
    Route::get('/graficas', [GraficaController::class, 'index'])->name('graficas.index');

    /*
    |----------------------------------------------------------------------
    | API ENDPOINTS PARA ADMINISTRADORES
    |----------------------------------------------------------------------
    */

    // API para estadísticas de productos de bodega
    Route::get('api/productos-bodega/{id}/stats', [ProductosBodegaController::class, 'getProductoStats'])->name('api.productos-bodega.stats');
    
    // API para búsqueda de productos de bodega
    Route::get('api/productos-bodega/search', [ProductosBodegaController::class, 'searchProductos'])->name('api.productos-bodega.search');

    // API para estadísticas de productos de hotel
    Route::get('api/productos-hotel/{id}/stats', [DimProductoHotelController::class, 'getProductoStats'])->name('api.productos-hotel.stats');
    
    // API para búsqueda de productos de hotel
    Route::get('api/productos-hotel/search', [DimProductoHotelController::class, 'searchProductos'])->name('api.productos-hotel.search');
});

// FALLBACK PARA RUTAS NO ENCONTRADAS
Route::fallback(function () {
    return Auth::check() 
        ? redirect()->route('dashboard')->with('error', 'Página no encontrada') 
        : redirect()->route('login');
});