@extends('layouts.app')

@section('title', 'Gastos variables - Hotel Romance')

@section('content')
<style>
    :root {
        --primary-color: #88A6D3;
        --secondary-color: #6B8CC7;
        --accent-color: #4A73B8;
        --tertiary-color: #A5BFDB;
        --sidebar-bg: #F8FAFC;
        --gradient-start: #88A6D3;
        --gradient-end: #6B8CC7;
    }

    .table-container {
        background: white;
        border-radius: 1rem;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(136, 166, 211, 0.1);
        border: 1px solid rgba(136, 166, 211, 0.2);
    }

    .search-input:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(136, 166, 211, 0.1);
        outline: none;
    }

    .btn-romance {
        background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
        transition: all 0.3s ease;
        color: white;
        font-weight: 600;
        box-shadow: 0 2px 8px rgba(136, 166, 211, 0.3);
    }

    .btn-romance:hover {
        background: linear-gradient(135deg, var(--secondary-color), var(--accent-color));
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(136, 166, 211, 0.4);
    }

    .stats-card {
        background: linear-gradient(135deg, var(--tertiary-color), var(--primary-color));
        color: white;
        box-shadow: 0 4px 16px rgba(136, 166, 211, 0.2);
        transition: all 0.3s ease;
    }

    .stats-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(136, 166, 211, 0.3);
    }

    .table-row {
        transition: all 0.3s ease;
        border-left: 4px solid transparent;
    }

    .table-row:hover {
        background-color: rgba(136, 166, 211, 0.05);
        border-left-color: var(--primary-color);
    }

    .badge {
        padding: 0.25rem 0.75rem;
        border-radius: 0.5rem;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .badge-tipo {
        background: rgba(136, 166, 211, 0.1);
        color: var(--accent-color);
    }

    .badge-monto {
        background: rgba(74, 115, 184, 0.1);
        color: var(--accent-color);
        font-family: 'Courier New', monospace;
    }

    .badge-fecha {
        background: rgba(165, 191, 219, 0.1);
        color: var(--secondary-color);
    }

    .badge-boleta {
        background: rgba(107, 140, 199, 0.1);
        color: var(--accent-color);
        font-family: 'Courier New', monospace;
    }
</style>

<!-- Header simple sin recuadro -->
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-800 flex items-center">
            <i class='bx bx-wallet mr-3 text-4xl'></i>
            GASTOS VARIABLES
        </h1>
        <p class="text-gray-600 mt-2">Administra y controla todos los gastos variables del hotel</p>
    </div>
    <a href="{{ route('gastos.create') }}" class="btn-romance px-6 py-3 rounded-lg text-sm font-medium flex items-center transition-all hover:scale-105">
        <i class='bx bx-plus mr-2 text-lg'></i>
        Nuevo Gasto Variable
    </a>
</div>

<!-- Filtros y estadísticas -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="md:col-span-2">
        <div class="relative">
            <input type="search" id="searchInput"
                class="search-input w-full pl-10 pr-4 py-3 rounded-lg border-2 border-gray-200 focus:outline-none transition-all"
                placeholder="Buscar por tipo, monto o fecha...">
            <div class="absolute top-0 left-0 inline-flex items-center p-3">
                <i class='bx bx-search text-gray-400'></i>
            </div>
        </div>
    </div>
    
    <!-- Estadísticas rápidas -->
    <div class="stats-card rounded-lg border p-4 text-center shadow-lg">
        <div class="text-2xl font-bold">{{ count($gastos) }}</div>
        <div class="text-sm opacity-90">Total Gastos</div>
    </div>
    
    <div class="stats-card rounded-lg border p-4 text-center shadow-lg">
        <div class="text-2xl font-bold">S/{{ number_format($gastos->sum('monto'), 2) }}</div>
        <div class="text-sm opacity-90">Monto Total</div>
    </div>
</div>

<!-- Tabla -->
<div class="table-container rounded-xl shadow-lg overflow-hidden">
    <div class="overflow-x-auto" style="max-height: 600px; overflow-y: auto;">
        <table class="min-w-full bg-white">
            <thead class="bg-gradient-to-r from-blue-600 to-blue-800 text-white sticky top-0 z-10" style="background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider">Tipo de Gasto</th>
                    <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider">Monto</th>
                    <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider">Método Pago</th>
                    <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider">Turno</th>
                    <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider">Fecha</th>
                    <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider">Comprobante</th>
                    <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider">Acciones</th> 
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200" id="tableBody">
                @forelse($gastos as $g)
                    <tr class="table-row" data-search="{{ strtolower($g->tipo . ' ' . $g->monto . ' ' . $g->fecha_gasto . ' ' . ($g->comprobante ?? '') . ' ' . ($g->turno == 0 ? 'dia' : 'noche')) }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="badge badge-tipo">{{ $g->tipo }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="badge badge-monto">S/{{ number_format($g->monto, 2) }}</span>
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $g->met_pago }}
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($g->turno == 0)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <i class='bx bx-sun mr-1'></i>
                                    Día
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <i class='bx bx-moon mr-1'></i>
                                    Noche
                                </span>
                            @endif
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="badge badge-fecha">{{ \Carbon\Carbon::parse($g->fecha_gasto)->format('d/m/Y') }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if(isset($g->comprobante) && $g->comprobante)
                                <span class="badge badge-boleta">{{ $g->comprobante }}</span>
                            @else
                                <span class="text-gray-400 text-sm">Sin código</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('gastos.edit', $g->id_gasto) }}"
                                   class="inline-flex items-center bg-yellow-100 text-yellow-700 px-3 py-1 text-xs rounded-full hover:bg-yellow-200 transition-colors">
                                    <i class='bx bx-edit mr-1'></i>
                                    Editar
                                </a>
                                
                                <form action="{{ route('gastos.destroy', $g->id_gasto) }}" method="POST" class="inline delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center bg-red-100 text-red-700 px-3 py-1 text-xs rounded-full hover:bg-red-200 transition-colors delete-button"
                                            data-gasto="{{ $g->tipo }}">
                                        <i class='bx bx-trash mr-1'></i>
                                        Eliminar
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                            <i class='bx bx-wallet text-4xl mb-2 block'></i>
                            No hay gastos variables registrados
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Funcionalidad de búsqueda
    const searchInput = document.getElementById('searchInput');
    const tableRows = document.querySelectorAll('#tableBody tr[data-search]');

    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        
        tableRows.forEach(row => {
            const searchData = row.getAttribute('data-search');
            if (searchData.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    // Confirmación para eliminar
    document.addEventListener('click', function(e) {
        if (e.target.closest('.delete-button')) {
            const button = e.target.classList.contains('.delete-button') ? e.target : e.target.closest('.delete-button');
            const gastoName = button.getAttribute('data-gasto');
            const form = button.closest('.delete-form');
            
            if (confirm(`¿Eliminar el gasto de ${gastoName}?`)) {
                form.submit();
            }
        }
    });
    
    // Agregar efecto hover personalizado para las filas
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