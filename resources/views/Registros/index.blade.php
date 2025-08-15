@extends('layouts.app')

@section('title', 'Registros - Hotel Romance')

@section('content')

<style>
    /* Paleta de colores azul Hotel Romance */
    :root {
        --primary-color: #88A6D3;      /* Azul principal */
        --secondary-color: #6B8CC7;    /* Azul secundario más oscuro */
        --tertiary-color: #A5BFDB;     /* Azul terciario más claro */
        --accent-color: #4A73B8;       /* Azul de acento oscuro */
        --light-blue: #C8D7ED;         /* Azul muy claro */
        --sidebar-bg: #f4f8fc;         /* Fondo sidebar azul muy suave */
        --hover-bg: #88A6D3;           /* Color hover */
        --gradient-start: #88A6D3;     /* Inicio gradiente */
        --gradient-end: #6B8CC7;       /* Fin gradiente */
    }

    .table-container {
        background: linear-gradient(135deg, #f4f8fc 0%, #e8f2ff 100%);
    }
    
    .search-input:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(136, 166, 211, 0.1);
    }
    
    .btn-romance {
        background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
        transition: all 0.3s ease;
    }
    
    .btn-romance:hover {
        background: linear-gradient(135deg, var(--secondary-color), var(--accent-color));
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(136, 166, 211, 0.3);
    }
    
    .table-row:hover {
        background-color: #f4f8fc;
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(136, 166, 211, 0.1);
        transition: all 0.2s ease;
    }
    
    .badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
    }
    
    .badge-habitacion {
        background-color: #e8f2ff;
        color: var(--accent-color);
    }
    
    .badge-si {
        background-color: #dcfce7;
        color: #166534;
    }
    
    .badge-no {
        background-color: #f3f4f6;
        color: #374151;
    }

    /* Estadísticas con colores azules */
    .stats-total {
        color: var(--secondary-color);
    }

    .stats-boleta {
        color: var(--accent-color);
    }

    /* Checkbox personalizado */
    .checkbox-romance {
        accent-color: var(--primary-color);
    }

    /* Barra de acciones */
    .action-bar-edit {
        background-color: var(--primary-color);
    }

    .action-bar-edit:hover {
        background-color: var(--secondary-color);
    }

    /* Tabla header gradiente azul */
    .table-header {
        background: linear-gradient(135deg, var(--secondary-color), var(--accent-color));
    }

    /* Hover en filas con borde azul */
    .table-row:hover {
        border-left-color: var(--primary-color) !important;
    }
</style>

<div class="container mx-auto py-6 px-4">
    
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Registro de Habitaciones</h1>
            <p class="text-gray-600">Gestiona los registros de huéspedes del Hotel Romance</p>
        </div>
        <a href="{{ route('registros.create') }}"
           class="btn-romance text-white px-6 py-3 rounded-lg font-medium shadow-lg">
            <i class='bx bx-plus mr-2'></i>
            Agregar registro
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

    <!-- Barra de búsqueda y estadísticas -->
    <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Búsqueda -->
        <div class="md:col-span-2">
            <div class="relative">
                <input type="search" id="searchInput"
                    class="search-input w-full pl-10 pr-4 py-3 rounded-lg border-2 border-gray-200 focus:outline-none transition-all"
                    placeholder="Buscar por nombre, documento, habitación...">
                <div class="absolute top-0 left-0 inline-flex items-center p-3">
                    <i class='bx bx-search text-gray-400'></i>
                </div>
            </div>
        </div>
        
        <!-- Estadísticas rápidas -->
        <div class="bg-white rounded-lg border p-4 text-center">
            <div class="text-2xl font-bold stats-total">{{ count($registros) }}</div>
            <div class="text-sm text-gray-600">Total Registros</div>
        </div>
        <div class="bg-white rounded-lg border p-4 text-center">
            <div class="text-2xl font-bold stats-boleta">{{ $registros->where('boleta', 'SI')->count() }}</div>
            <div class="text-sm text-gray-600">Con Boleta</div>
        </div>
    </div>

    <!-- Tabla -->
    <div class="table-container rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto" style="max-height: 600px; overflow-y: auto;">
            <table class="min-w-full bg-white">
                <thead class="table-header text-white sticky top-0 z-10">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider">
                            <input type="checkbox" id="selectAll" class="checkbox-romance rounded border-gray-300 focus:ring-2 focus:ring-white">
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider">Hora</th>
                        <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider">Fecha</th>
                        <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider">Nombre</th>
                        <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider">Documento</th>
                        <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider">Habitación</th>
                        <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider">Tarifa</th>
                        <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider">Método</th>
                        <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider">Boleta</th>
                        <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider">Acciones</th> 
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200" id="tableBody">
                    @forelse($registros as $r)
                        <tr class="table-row border-l-4 border-transparent hover:border-blue-400" data-search="{{ strtolower($r->nombre_apellido . ' ' . $r->doc_identidad . ' ' . $r->habitacion . ' ' . $r->metodo_pago) }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="checkbox" class="row-checkbox checkbox-romance rounded border-gray-300 focus:ring-2" value="{{ $r->id_estadia }}">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $r->hora_ingreso }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ \Illuminate\Support\Carbon::parse($r->fecha_ingreso)->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $r->nombre_apellido }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 font-mono">
                                {{ $r->doc_identidad }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="badge badge-habitacion">{{ $r->habitacion }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                S/ {{ number_format($r->tarifa ?? 0, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $r->metodo_pago }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="badge {{ $r->boleta === 'SI' ? 'badge-si' : 'badge-no' }}">
                                    {{ $r->boleta }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                @if(($r->consumo_count ?? 0) > 0)
                                    <a href="{{ route('registros.consumo', $r->id_estadia) }}"
                                       class="inline-flex items-center bg-blue-100 text-blue-700 px-3 py-1 text-xs rounded-full hover:bg-blue-200 transition-colors">
                                        <i class='bx bx-receipt mr-1'></i>
                                        Ver consumo ({{ $r->consumo_count }})
                                    </a>
                                @endif
                                {{-- Si no hay consumo, la celda queda vacía --}}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <i class='bx bx-inbox text-6xl text-gray-300 mb-4'></i>
                                    <span class="text-lg">No hay registros disponibles.</span>
                                    <p class="text-sm mt-2">Comienza agregando tu primer registro de huésped.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Barra de acciones para elementos seleccionados -->
    <div id="actionBar" class="hidden fixed bottom-6 left-1/2 transform -translate-x-1/2 bg-white rounded-lg shadow-xl border px-6 py-4 z-50">
        <div class="flex items-center space-x-4">
            <span id="selectedCount" class="text-sm font-medium text-gray-700"></span>
            <button id="editSelected" class="action-bar-edit hover:action-bar-edit text-white px-4 py-2 rounded text-sm transition-colors">
                Editar
            </button>
            <button id="deleteSelected" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded text-sm transition-colors">
                Eliminar
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const tableBody = document.getElementById('tableBody');
    const selectAllCheckbox = document.getElementById('selectAll');
    const actionBar = document.getElementById('actionBar');
    const selectedCount = document.getElementById('selectedCount');
    
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
    
    // Selección múltiple
    function updateActionBar() {
        const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
        if (checkedBoxes.length > 0) {
            actionBar.classList.remove('hidden');
            selectedCount.textContent = `${checkedBoxes.length} elemento(s) seleccionado(s)`;
        } else {
            actionBar.classList.add('hidden');
        }
    }
    
    // Seleccionar todo
    selectAllCheckbox.addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.row-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateActionBar();
    });
    
    // Selección individual
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('row-checkbox')) {
            updateActionBar();
            
            // Actualizar el checkbox "seleccionar todo"
            const allCheckboxes = document.querySelectorAll('.row-checkbox');
            const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
            selectAllCheckbox.checked = allCheckboxes.length === checkedBoxes.length;
        }
    });
    
    // Editar seleccionados
    document.getElementById('editSelected').addEventListener('click', function() {
        const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
        if (checkedBoxes.length === 1) {
            window.location.href = `/registros/${checkedBoxes[0].value}/edit`;
        } else {
            alert('Selecciona solo un registro para editar.');
        }
    });
    
    // Eliminar seleccionados
    document.getElementById('deleteSelected').addEventListener('click', function() {
        const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
        if (checkedBoxes.length > 0) {
            if (confirm(`¿Eliminar ${checkedBoxes.length} registro(s) seleccionado(s)?`)) {
                // Aquí podrías implementar eliminación múltiple
                alert('Funcionalidad de eliminación múltiple por implementar.');
            }
        }
    });
});
</script>

@endsection