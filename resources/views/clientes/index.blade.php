@extends('layouts.app')

@section('title', 'Clientes - Hotel Romance')

@section('content')

<style>
    /* Paleta de colores azul Hotel Romance */
    :root {
        --primary-color: #88A6D3; /* Azul principal */
        --secondary-color: #6B8CC7; /* Azul secundario más oscuro */
        --tertiary-color: #A5BFDB; /* Azul terciario más claro */
        --accent-color: #4A73B8; /* Azul de acento oscuro */
        --light-blue: #C8D7ED; /* Azul muy claro */
        --sidebar-bg: #f4f8fc; /* Fondo sidebar azul muy suave */
        --hover-bg: #88A6D3; /* Color hover */
        --gradient-start: #88A6D3; /* Inicio gradiente */
        --gradient-end: #6B8CC7; /* Fin gradiente */
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
        transition: all 0.3s ease;
    }
    
    .btn-romance:hover {
        background: linear-gradient(135deg, var(--secondary-color), var(--accent-color));
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(136, 166, 211, 0.3);
    }
    
    .table-row:hover {
        background-color: var(--sidebar-bg);
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
    
    .badge-documento {
        background-color: var(--light-blue);
        color: var(--accent-color);
    }
    
    .badge-activo {
        background-color: #dcfce7;
        color: #166534;
    }
    
    .badge-nuevo {
        background-color: #fef3c7;
        color: #92400e;
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
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Gestión de Clientes</h1>
            <p class="text-gray-600">Administra la información de los clientes del Hotel Romance</p>
        </div>
        <a href="{{ route('clientes.create') }}"
           class="btn-romance text-white px-6 py-3 rounded-lg font-medium shadow-lg">
            <i class='bx bx-user-plus mr-2'></i>
            Agregar cliente
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
    <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Búsqueda -->
        <div class="md:col-span-2">
            <div class="relative">
                <input type="search" id="searchInput"
                    class="search-input w-full pl-10 pr-4 py-3 rounded-lg border-2 border-gray-200 focus:outline-none transition-all"
                    placeholder="Buscar por nombre o documento...">
                <div class="absolute top-0 left-0 inline-flex items-center p-3">
                    <i class='bx bx-search text-gray-400'></i>
                </div>
            </div>
        </div>
        
        <!-- Estadísticas rápidas -->
        <div class="stats-card rounded-lg border p-4 text-center shadow-lg">
            <div class="text-2xl font-bold">{{ count($clientes) }}</div>
            <div class="text-sm opacity-90">Total Clientes</div>
        </div>
    </div>

    <!-- Tabla -->
    <div class="table-container rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto" style="max-height: 600px; overflow-y: auto;">
            <table class="min-w-full bg-white">
                <thead class="bg-gradient-to-r from-blue-600 to-blue-800 text-white sticky top-0 z-10" style="background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider">Acciones</th>
                        <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider">Documento</th>
                        <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider">Nombre y Apellido</th>
                        <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider">Sexo</th>
                        <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider">Nacionalidad</th>
                        <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider">Estado Civil</th>
                        <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider">Fecha Nacimiento</th>
                        <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider">Lugar Nacimiento</th>
                        <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider">Profesión</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200" id="tableBody">
                    @forelse($clientes as $c)
                        <tr class="table-row border-l-4 border-transparent hover:border-blue-400" data-search="{{ strtolower($c->nombre_apellido . ' ' . $c->doc_identidad . ' ' . $c->sexo . ' ' . $c->nacionalidad . ' ' . $c->profesion_ocupacion) }}" style="--hover-border-color: var(--primary-color);">
                            <!-- ACCIONES (primera columna) -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('clientes.edit', $c->doc_identidad) }}"
                                    class="inline-flex items-center bg-yellow-100 text-yellow-700 px-3 py-1 text-xs rounded-full hover:bg-yellow-200 transition-colors">
                                        <i class='bx bx-edit mr-1'></i>
                                        Editar
                                    </a>
                                    
                                    <form action="{{ route('clientes.destroy', $c->doc_identidad) }}" method="POST" class="inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center bg-red-100 text-red-700 px-3 py-1 text-xs rounded-full hover:bg-red-200 transition-colors delete-button"
                                                data-cliente="{{ $c->nombre_apellido ?: $c->doc_identidad }}">
                                            <i class='bx bx-trash mr-1'></i>
                                            Borrar
                                        </button>
                                    </form>
                                </div>
                            </td>
                            
                            <!-- DOCUMENTO -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="badge badge-documento font-mono">{{ $c->doc_identidad }}</span>
                            </td>
                            
                            <!-- NOMBRE -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $c->nombre_apellido ?: 'Sin nombre' }}</div>
                            </td>
                            
                            <!-- SEXO (nueva columna) -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $c->sexo ?? '-' }}
                            </td>
                            
                            <!-- NACIONALIDAD (nueva columna) -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $c->nacionalidad ?? '-' }}
                            </td>
                            
                            <!-- ESTADO CIVIL -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $c->estado_civil ?? '-' }}
                            </td>
                            
                            <!-- FECHA NACIMIENTO -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $c->fecha_nacimiento ? \Carbon\Carbon::parse($c->fecha_nacimiento)->format('d/m/Y') : '-' }}
                            </td>
                            
                            <!-- LUGAR NACIMIENTO -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $c->lugar_nacimiento ?? '-' }}
                            </td>
                            
                            <!-- PROFESIÓN (nueva columna) -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $c->profesion_ocupacion ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <i class='bx bx-group text-6xl text-gray-300 mb-4'></i>
                                    <span class="text-lg">No hay clientes registrados aún.</span>
                                    <p class="text-sm mt-2">Comienza agregando tu primer cliente.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Nota: Barra de acciones removida ya que no hay selección múltiple -->
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
    
    // Confirmación para eliminar individual
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('delete-button') || e.target.closest('.delete-button')) {
            e.preventDefault();
            const button = e.target.classList.contains('delete-button') ? e.target : e.target.closest('.delete-button');
            const clienteName = button.getAttribute('data-cliente');
            const form = button.closest('.delete-form');
            
            if (confirm(`¿Eliminar al cliente ${clienteName}?`)) {
                form.submit();
            }
        }
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