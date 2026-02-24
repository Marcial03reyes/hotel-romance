@extends('layouts.app')

@section('title', 'Historial - ' . $producto->nombre)

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumb -->
        <nav class="flex mb-6" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                <li class="inline-flex items-center">
                    <a href="{{ route('productos-bodega.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                        <i class='bx bx-package mr-2'></i>
                        Productos Bodega
                    </a>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class='bx bx-chevron-right text-gray-400'></i>
                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">{{ $producto->nombre }}</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Header con estadísticas -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $producto->nombre }}</h1>
                    <p class="text-gray-600 mt-1">Historial de compras y movimientos</p>
                </div>
                <div class="flex space-x-3">
                    <!-- BOTÓN que abre el modal -->
                    @if(auth()->user()->role === 'admin')
                    <button onclick="openStockModal()" 
                            class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-purple-700 transition-colors">
                        <i class='bx bx-edit mr-2'></i>
                        Ajustar Stock Inicial
                    </button>
                    @endif

                    <a href="{{ route('productos-bodega.create-compra', $producto->id_prod_bod) }}" 
                       class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-green-700 transition-colors">
                        <i class='bx bx-plus mr-2'></i>
                        Registrar Compra
                    </a>
                    <a href="{{ route('productos-bodega.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        <i class='bx bx-arrow-back mr-2'></i>
                        Volver
                    </a>
                </div>
            </div>

            <!-- Estadísticas en tarjetas -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Stock Actual -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-blue-600">Stock Actual</p>
                            <p class="text-2xl font-bold text-blue-900">{{ $stockActual }}</p>
                            <p class="text-xs text-blue-600 mt-1">unidades disponibles</p>
                        </div>
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class='bx bx-package text-blue-600 text-xl'></i>
                        </div>
                    </div>
                </div>
                
                <!-- Rotación Mensual -->
                <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-orange-600">Rotación</p>
                            @if($rotacionMensual !== null)
                                <p class="text-2xl font-bold text-orange-900">{{ $rotacionMensual }}</p>
                                <p class="text-xs text-orange-600 mt-1">días de stock</p>
                            @else
                                <p class="text-lg font-medium text-orange-900">Sin datos</p>
                                <p class="text-xs text-orange-600 mt-1">aún no hay ventas</p>
                            @endif
                        </div>
                        <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                            <i class='bx bx-time text-orange-600 text-xl'></i>
                        </div>
                    </div>
                </div>
                
                <!-- Ganancia Mensual Promedio -->
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-green-600">Ganancia Mensual</p>
                            @if($gananciaMensual !== null)
                                <p class="text-2xl font-bold text-green-900">S/ {{ number_format($gananciaMensual, 2) }}</p>
                                <p class="text-xs text-green-600 mt-1">promedio mensual</p>
                            @else
                                <p class="text-lg font-medium text-green-900">Sin datos</p>
                                <p class="text-xs text-green-600 mt-1">aún no hay ventas</p>
                            @endif
                        </div>
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class='bx bx-dollar text-green-600 text-xl'></i>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>

        <!-- Alertas -->
        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class='bx bx-check-circle text-green-400 text-xl'></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class='bx bx-error text-red-400 text-xl'></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800">{{ $errors->first() }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Tabla de historial de compras -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Historial de Compras</h3>
                <p class="text-sm text-gray-500">Registro cronológico de todas las compras realizadas</p>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Fecha
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Cantidad
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Precio Unitario
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Total
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Proveedor
                            </th>

                            @if(auth()->user()->role === 'admin')
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Acciones
                            </th>
                            @endif

                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($historialCompras as $compra)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $compra->fecha_compra->format('d/m/Y') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $compra->cantidad }} unidades
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">S/ {{ number_format($compra->precio_unitario, 2) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        S/ {{ number_format($compra->cantidad * $compra->precio_unitario, 2) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $compra->proveedor ?: 'No especificado' }}
                                    </div>
                                </td>

                                @if(auth()->user()->role === 'admin')
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        <a href="{{ route('productos-bodega.edit-compra', [$producto->id_prod_bod, $compra->id_compra_bodega]) }}" 
                                           class="text-yellow-600 hover:text-yellow-900 transition-colors"
                                           title="Editar compra">
                                            <i class='bx bx-edit text-lg'></i>
                                        </a>
                                        <form method="POST" action="{{ route('productos-bodega.destroy-compra', [$producto->id_prod_bod, $compra->id_compra_bodega]) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="text-red-600 hover:text-red-900 transition-colors"
                                                    title="Eliminar compra"
                                                    onclick="return confirm('¿Estás seguro de eliminar esta compra?')">
                                                <i class='bx bx-trash text-lg'></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="text-gray-500">
                                        <i class='bx bx-receipt text-4xl mb-4'></i>
                                        <p class="text-lg font-medium">No hay compras registradas</p>
                                        <p class="text-sm">Comienza registrando la primera compra de este producto</p>
                                        <a href="{{ route('productos-bodega.create-compra', $producto->id_prod_bod) }}" 
                                           class="inline-flex items-center mt-4 px-4 py-2 bg-green-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-green-700 transition-colors">
                                            <i class='bx bx-plus mr-2'></i>
                                            Registrar Primera Compra
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- MODAL (agregar al final de la vista) -->
@if(auth()->user()->role === 'admin')
<div id="stockModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <h3 class="text-lg font-medium mb-4">Ajustar Stock Inicial</h3>
            
            <form method="POST" action="{{ route('productos-bodega.update-producto', $producto->id_prod_bod) }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="nombre" value="{{ $producto->nombre }}">
                <input type="hidden" name="precio_actual" value="{{ $producto->precio_actual }}">
                
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Stock Inicial</label>
                    <input type="number" name="stock_inicial" value="{{ $producto->stock_inicial }}" 
                           class="w-full px-3 py-2 border rounded-lg" min="0" max="9999" required>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeStockModal()" 
                            class="px-4 py-2 text-gray-600 border rounded-lg">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-purple-600 text-white rounded-lg">
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>

function openStockModal() { document.getElementById('stockModal').classList.remove('hidden'); }
function closeStockModal() { document.getElementById('stockModal').classList.add('hidden'); }
</script>
@endif
@endsection