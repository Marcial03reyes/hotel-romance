@extends('layouts.app')

@section('title', 'Productos Bodega - Hotel Romance')

@section('content')

<style>
    /* Paleta de colores azul Hotel Romance */
    :root {
        --primary-color: #88A6D3;
        --secondary-color: #6B8CC7;
        --tertiary-color: #A5BFDB;
        --accent-color: #4A73B8;
        --light-blue: #C8D7ED;
        --sidebar-bg: #f4f8fc;
        --gradient-start: #88A6D3;
        --gradient-end: #6B8CC7;
    }
    
    .table-container {
        background: linear-gradient(135deg, var(--sidebar-bg) 0%, var(--light-blue) 100%);
    }
    
    .search-input:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(136, 166, 211, 0.1);
    }
    
    .btn-romance {
        background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
        color: white;
        transition: all 0.3s ease;
    }
    
    .btn-romance:hover {
        background: linear-gradient(135deg, var(--secondary-color), var(--accent-color));
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(136, 166, 211, 0.3);
        color: white;
    }
    
    .table-row:hover {
        background-color: var(--sidebar-bg);
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(136, 166, 211, 0.1);
        transition: all 0.2s ease;
        cursor: pointer;
    }
    
    .badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
    }
    
    .badge-stock-alto {
        background-color: #dcfce7;
        color: #166534;
    }
    
    .badge-stock-medio {
        background-color: #fef3c7;
        color: #92400e;
    }
    
    .badge-stock-bajo {
        background-color: #fee2e2;
        color: #991b1b;
    }
    
    .badge-producto {
        background-color: var(--light-blue);
        color: var(--accent-color);
    }
    
    .stats-card {
        background: linear-gradient(135deg, var(--tertiary-color), var(--primary-color));
        color: white;
    }
</style>

<div class="container mx-auto py-6 px-4">
    
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Productos de Bodega</h1>
            <p class="text-gray-600">Control de inventario y stock del Hotel Romance</p>
        </div>
        <a href="{{ route('productos-bodega.create-producto') }}"
           class="btn-romance text-white px-6 py-3 rounded-lg font-medium shadow-lg">
            <i class='bx bx-plus mr-2'></i>
            Nuevo producto
        </a>
    </div>

    <!-- Mensajes de éxito/error -->
    @if(session('success'))
        <div class="rounded-lg border border-green-300 bg-green-50 p-4 text-green-800 mb-6 shadow-sm">
            <div class="flex items-center">
                <i class='bx bx-check-circle mr-2'></i>
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="rounded-lg border border-red-300 bg-red-50 p-4 text-red-800 mb-6 shadow-sm">
            <div class="flex items-center mb-2">
                <i class='bx bx-error-circle mr-2'></i>
                Se encontraron errores:
            </div>
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li class="text-sm">{{ $error }}</li>
                @endforeach
            </ul>
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

    <!-- Tabla -->
    <div class="table-container rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead class="bg-gradient-to-r from-blue-600 to-blue-800 text-white sticky top-0 z-10" style="background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider">Producto</th>
                        <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider">Unidades Compradas</th>
                        <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider">Unidades Vendidas</th>
                        <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider">Stock Actual</th>
                        <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200" id="tableBody">
                    @forelse($productos as $producto)
                        <tr class="table-row border-l-4 border-transparent hover:border-blue-400" 
                            data-search="{{ strtolower($producto->nombre) }}" 
                            onclick="window.location.href='{{ route('productos-bodega.historial', $producto->id_prod_bod) }}'"
                            style="--hover-border-color: var(--primary-color);">
                            
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="badge badge-producto font-medium">{{ $producto->nombre }}</span>
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $producto->unidades_compradas }}</div>
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $producto->unidades_vendidas }}</div>
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $stock = $producto->stock;
                                    $badgeClass = 'badge-stock-alto';
                                    if ($stock <= 5) $badgeClass = 'badge-stock-bajo';
                                    elseif ($stock <= 15) $badgeClass = 'badge-stock-medio';
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ $stock }} unidades</span>
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium" onclick="event.stopPropagation();">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('productos-bodega.historial', $producto->id_prod_bod) }}"
                                       class="inline-flex items-center bg-blue-100 text-blue-700 px-3 py-1 text-xs rounded-full hover:bg-blue-200 transition-colors">
                                        <i class='bx bx-history mr-1'></i>
                                        Historial
                                    </a>

                                    <!-- BOTÓN EDITAR (NUEVO) -->
                                    <a href="{{ route('productos-bodega.edit-producto', $producto->id_prod_bod) }}"
                                        class="inline-flex items-center bg-yellow-100 text-yellow-700 px-3 py-1 text-xs rounded-full hover:bg-yellow-200 transition-colors"
                                        title="Editar nombre del producto">
                                        <i class='bx bx-edit mr-1'></i>
                                        Editar
                                    </a>
                                    
                                    <a href="{{ route('productos-bodega.create-compra', $producto->id_prod_bod) }}"
                                       class="inline-flex items-center bg-green-100 text-green-700 px-3 py-1 text-xs rounded-full hover:bg-green-200 transition-colors">
                                        <i class='bx bx-plus mr-1'></i>
                                        Comprar
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <i class='bx bx-package text-6xl text-gray-300 mb-4'></i>
                                    <span class="text-lg">No hay productos registrados aún.</span>
                                    <p class="text-sm mt-2">Comienza agregando tu primer producto.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
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
    
    // Agregar efecto hover personalizado para las filas
    const tableRows = document.querySelectorAll('.table-row');
    tableRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.borderLeftColor = 'var(--primary-color)';
        });
        
        row.addEventListener('mouseleave', function() {
            this.style.borderLeftColor = 'transparent';
        });
    });
});
</script>

@endsection