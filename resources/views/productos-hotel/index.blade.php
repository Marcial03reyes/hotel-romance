@extends('layouts.app')

@section('title', 'Productos Hotel')

@section('content')

<style>
    .search-input:focus {
        border-color: #88A6D3;
        box-shadow: 0 0 0 3px rgba(233, 134, 114, 0.1);
    }
</style>

<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Productos de Hotel</h1>
                    <p class="text-gray-600 mt-1">Gestiona los productos internos del hotel (limpieza, mantenimiento, etc.)</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('productos-hotel.create-producto') }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700 transition-colors">
                        <i class='bx bx-plus mr-2'></i>
                        Nuevo Producto
                    </a>
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

        <!-- Barra de búsqueda -->
        <div class="mb-6">
            <div class="relative max-w-md">
                <input type="search" id="searchInput"
                    class="search-input w-full pl-10 pr-4 py-3 rounded-lg border-2 border-gray-200 focus:outline-none transition-all"
                    placeholder="Buscar producto...">
                <div class="absolute top-0 left-0 inline-flex items-center p-3">
                    <i class='bx bx-search text-gray-400'></i>
                </div>
            </div>
        </div>

        <!-- Tabla de Productos -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gradient-to-r from-blue-600 to-blue-800 text-white sticky top-0 z-10" style="background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider">Producto</th>
                            <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider">Frecuencia de Compra</th>
                            <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider">Total Comprado</th>
                            <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider">Inversión Total</th>
                            <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider">Última Compra</th>
                            <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="tableBody">
                        @forelse($productos as $producto)
                            <tr class="hover:bg-gray-50" data-search="{{ strtolower($producto->nombre) }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $producto->nombre }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        @if($producto->frecuencia_compra)
                                            <span class="font-medium">{{ $producto->frecuencia_compra }}</span> días
                                            <div class="text-xs text-gray-500">promedio</div>
                                        @else
                                            <span class="text-gray-400">No calculado</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        <span class="font-medium">{{ $producto->total_comprado }}</span> unidades
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        S/ {{ number_format($producto->inversion_total, 2) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        @if($producto->ultima_compra)
                                            {{ \Carbon\Carbon::parse($producto->ultima_compra)->format('d/m/Y') }}
                                        @else
                                            <span class="text-gray-400">Sin compras</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        <a href="{{ route('productos-hotel.historial', $producto->id_prod_hotel) }}" 
                                           class="inline-flex items-center bg-blue-100 text-blue-700 px-3 py-1 text-xs rounded-full hover:bg-blue-200 transition-colors" title="Ver historial">
                                            <i class='bx bx-history mr-1'></i>
                                            Historial
                                        </a>
                                        <a href="{{ route('productos-hotel.create-compra', $producto->id_prod_hotel) }}" 
                                           class="inline-flex items-center px-2 py-1 bg-green-100 text-green-700 text-xs font-medium rounded-md hover:bg-green-200 transition-colors"
                                           title="Registrar compra">
                                            <i class='bx bx-plus mr-1'></i>
                                            Comprar
                                        </a>
                                        <!-- Editar Producto -->
                                        <a href="{{ route('productos-hotel.edit-producto', $producto->id_prod_hotel) }}" 
                                            class="inline-flex items-center px-2 py-1 bg-yellow-100 text-yellow-700 text-xs font-medium rounded-md hover:bg-yellow-200 transition-colors"
                                            title="Editar producto">
                                                <i class='bx bx-edit mr-1'></i>
                                                Editar
                                        </a>
                                        <!-- Eliminar Producto -->
                                        <form action="{{ route('productos-hotel.destroy-producto', $producto->id_prod_hotel) }}" 
                                            method="POST" style="display: inline;" 
                                            onsubmit="return confirm('¿Estás seguro de eliminar este producto? Esta acción no se puede deshacer.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="inline-flex items-center px-2 py-1 bg-red-100 text-red-700 text-xs font-medium rounded-md hover:bg-red-200 transition-colors"
                                                    title="Eliminar producto">
                                                <i class='bx bx-trash mr-1'></i>
                                                Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="text-gray-500">
                                        <i class='bx bx-cleaning text-4xl mb-4'></i>
                                        <p class="text-lg font-medium">No hay productos registrados</p>
                                        <p class="text-sm">Comienza agregando tu primer producto de hotel</p>
                                        <a href="{{ route('productos-hotel.create-producto') }}" 
                                           class="inline-flex items-center mt-4 px-4 py-2 bg-blue-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700 transition-colors">
                                            <i class='bx bx-plus mr-2'></i>
                                            Crear Primer Producto
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Información sobre frecuencias -->
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-md p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class='bx bx-info-circle text-blue-400 text-xl'></i>
                </div>
                <div class="ml-3">
                    <h4 class="text-sm font-medium text-blue-800 mb-2">Sobre la frecuencia de compra</h4>
                    <div class="text-sm text-blue-700 space-y-1">
                        <p>• La frecuencia de compra se calcula automáticamente basándose en el historial de compras</p>
                        <p>• Se requieren al menos 2 compras para calcular la frecuencia promedio</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const tableBody = document.getElementById('tableBody');
    
    // Búsqueda en tiempo real
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const rows = tableBody.querySelectorAll('tr[data-search]');
        
        rows.forEach(row => {
            const searchData = row.getAttribute('data-search');
            if (searchData.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
});
</script>

@endsection