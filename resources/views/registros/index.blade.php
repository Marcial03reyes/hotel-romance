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

    .badge-turno-dia {
        background-color: #fef3c7;
        color: #d97706;
    }

    .badge-turno-noche {
        background-color: #dbeafe;
        color: #1d4ed8;
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

    .fecha-real-indicator {
        font-size: 0.65rem;
        color: #2563eb;
        font-style: italic;
        margin-top: 2px;
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

    <!-- Barra de filtros y estadísticas -->
    <div class="mb-6 grid grid-cols-1 lg:grid-cols-12 gap-4">
        <!-- Filtros de fecha -->
        <div class="lg:col-span-7">
            <form method="GET" action="{{ route('registros.index') }}" id="filtroForm" class="flex flex-wrap gap-4 items-end">
                <!-- Botones de período rápido -->
                <div class="flex gap-2">
                    <button type="submit" name="filtro" value="hoy"
                        class="px-4 py-2 text-sm rounded-lg font-medium transition-colors duration-200 {{ request('filtro', 'todos') === 'hoy' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        Hoy
                    </button>
                    <button type="submit" name="filtro" value="semana"
                        class="px-4 py-2 text-sm rounded-lg font-medium transition-colors duration-200 {{ request('filtro') === 'semana' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        Esta Semana
                    </button>
                    <button type="button" id="personalizadoBtn"
                        class="px-4 py-2 text-sm rounded-lg font-medium transition-colors duration-200 {{ request('filtro') === 'personalizado' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        Personalizado
                    </button>
                    <button type="submit" name="filtro" value="todos"
                        class="px-4 py-2 text-sm rounded-lg font-medium transition-colors duration-200 {{ request('filtro', 'todos') === 'todos' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        Todos
                    </button>
                </div>
                
                <!-- Campos de fecha personalizada -->
                <div id="fechasPersonalizadas" class="flex gap-2 items-end {{ request('filtro') !== 'personalizado' ? 'hidden' : '' }}">
                    <div>
                        <label for="fecha_inicio" class="block text-xs font-medium text-gray-600 mb-1">Desde</label>
                        <input type="date" id="fecha_inicio" name="fecha_inicio" 
                            value="{{ request('fecha_inicio') }}"
                            class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="fecha_fin" class="block text-xs font-medium text-gray-600 mb-1">Hasta</label>
                        <input type="date" id="fecha_fin" name="fecha_fin" 
                            value="{{ request('fecha_fin') }}"
                            class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <button type="submit" name="filtro" value="personalizado"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700 transition-colors duration-200">
                        Filtrar
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Búsqueda reducida -->
        <div class="lg:col-span-3">
            <label class="block text-xs font-medium text-gray-600 mb-1">Buscar</label>
            <div class="relative">
                <input type="search" id="searchInput"
                    class="search-input w-full pl-10 pr-4 py-2 text-sm rounded-lg border-2 border-gray-200 focus:outline-none transition-all"
                    placeholder="Nombre, documento...">
                <div class="absolute top-0 left-0 inline-flex items-center p-2">
                    <i class='bx bx-search text-gray-400'></i>
                </div>
            </div>
        </div>
        
        <!-- Estadísticas compactas -->
        <div class="lg:col-span-2 grid grid-cols-2 gap-2">
            <div class="bg-white rounded-lg border p-3 text-center">
                <div class="text-lg font-bold stats-total">{{ count($registros) }}</div>
                <div class="text-xs text-gray-600">Total</div>
            </div>
            <div class="bg-white rounded-lg border p-3 text-center">
                <div class="text-lg font-bold stats-boleta">{{ $registros->where('boleta', 'SI')->count() }}</div>
                <div class="text-xs text-gray-600">C/Boleta</div>
            </div>
        </div>
    </div>

    <!-- Tabla -->
    <div class="table-container rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto" style="max-height: 600px; overflow-y: auto;">
            <table class="min-w-full bg-white">
                <thead class="table-header text-white sticky top-0 z-10">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider">Acciones</th>
                        <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider">Fecha Ingreso</th> 
                        <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider">Habitación</th>
                        <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider">Turno</th>
                        <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider">Hora Ingreso</th>
                        <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider">Hora Salida</th>
                        <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider">Nombres y Apellidos</th>
                        <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider">Documento</th>
                        <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider">Fecha Nacimiento</th>
                        <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider">E.C</th>
                        <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider">Lugar</th>
                        <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider">Precio</th>
                        <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider">Método</th>
                        <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider">Boleta</th>
                        <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider">OBS</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200" id="tableBody">


                    @forelse($registros as $r)
                        <tr class="table-row border-l-4 border-transparent hover:border-blue-400" data-search="{{ strtolower($r->nombre_apellido . ' ' . $r->doc_identidad . ' ' . $r->habitacion . ' ' . $r->metodo_pago) }}">
                            <!-- ACCIONES (primera posición) -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <!-- Botón de editar -->
                                    <a href="{{ route('registros.edit', $r->id_estadia) }}"
                                    class="inline-flex items-center bg-blue-100 text-blue-700 px-3 py-1 text-xs rounded-full hover:bg-blue-200 transition-colors">
                                        <i class='bx bx-edit mr-1'></i>
                                        Editar
                                    </a>
                                    
                                    <!-- Botón de eliminar -->
                                    <form method="POST" action="{{ route('registros.destroy', $r->id_estadia) }}" 
                                        onsubmit="return confirm('¿Estás seguro de eliminar este registro? Esta acción no se puede deshacer.')" 
                                        class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center bg-red-100 text-red-700 px-3 py-1 text-xs rounded-full hover:bg-red-200 transition-colors">
                                            <i class='bx bx-trash mr-1'></i>
                                            Eliminar
                                        </button>
                                    </form>
                                    
                                    <!-- Ver consumo (si existe) -->
                                    @if(($r->consumo_count ?? 0) > 0)
                                        <a href="{{ route('registros.consumo', $r->id_estadia) }}"
                                        class="inline-flex items-center bg-green-100 text-green-700 px-3 py-1 text-xs rounded-full hover:bg-green-200 transition-colors">
                                            <i class='bx bx-receipt mr-1'></i>
                                            Consumo ({{ $r->consumo_count }})
                                        </a>
                                    @endif
                                </div>
                            </td>

                            <!-- FECHA INGRESO --> 
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($r->fecha_ingreso_real)
                                    {{ \Carbon\Carbon::parse($r->fecha_ingreso_real)->format('d/m/Y') }}
                                @else
                                    {{ \Carbon\Carbon::parse($r->fecha_ingreso)->format('d/m/Y') }}
                                @endif
                            </td>

                            <!-- HABITACIÓN -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="badge badge-habitacion">{{ $r->habitacion }}</span>
                            </td>

                            <!-- TURNO -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if(isset($r->turno))
                                    <span class="badge {{ $r->turno == 0 ? 'badge-turno-dia' : 'badge-turno-noche' }}">
                                        <i class='bx {{ $r->turno == 0 ? "bx-sun" : "bx-moon" }} mr-1'></i>
                                        {{ $r->turno == 0 ? 'DÍA' : 'NOCHE' }}
                                    </span>
                                @else
                                    <span class="badge badge-no">-</span>
                                @endif
                            </td>

                            <!-- HORA INGRESO -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($r->hora_ingreso_real)
                                    {{ \Carbon\Carbon::createFromFormat('H:i:s', $r->hora_ingreso_real)->format('h:i A') }}
                                @else
                                    {{ \Carbon\Carbon::createFromFormat('H:i:s', $r->hora_ingreso)->format('h:i A') }}
                                @endif
                            </td>

                            <!-- HORA SALIDA -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($r->hora_salida)
                                    {{ \Carbon\Carbon::createFromFormat('H:i:s', $r->hora_salida)->format('h:i A') }}
                                @else
                                    <span class="text-gray-400 italic">Sin registrar</span>
                                @endif
                            </td>

                            <!-- NOMBRES Y APELLIDOS -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $r->nombre_apellido }}</div>
                            </td>

                            <!-- DOCUMENTO -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 font-mono">
                                {{ $r->doc_identidad }}
                            </td>

                            <!-- FECHA NACIMIENTO -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $r->fecha_nacimiento ? \Carbon\Carbon::parse($r->fecha_nacimiento)->format('d/m/Y') : '-' }}
                            </td>

                            <!-- E.C -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $r->estado_civil ?? '-' }}
                            </td>

                            <!-- LUGAR -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $r->lugar_nacimiento ?? '-' }}
                            </td>

                            <!-- PRECIO -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                S/ {{ number_format($r->precio ?? 0, 2) }}
                            </td>

                            <!-- MÉTODO -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $r->metodo_pago }}
                            </td>

                            <!-- BOLETA -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="badge {{ $r->boleta === 'SI' ? 'badge-si' : 'badge-no' }}">
                                    {{ $r->boleta }}
                                </span>
                            </td>

                            <!-- OBS -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $r->obs ? (strlen($r->obs) > 30 ? substr($r->obs, 0, 30) . '...' : $r->obs) : '-' }}
                            </td>
                        </tr>
                    
                    
                    
                    @empty
                        <tr>
                            <td colspan="15" class="px-6 py-12 text-center text-gray-500">
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
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const tableBody = document.getElementById('tableBody');
    const personalizadoBtn = document.getElementById('personalizadoBtn');
    const fechasPersonalizadas = document.getElementById('fechasPersonalizadas');
    
    // Búsqueda en tiempo real
    if (searchInput && tableBody) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = tableBody.querySelectorAll('tr[data-search]');
            
            rows.forEach(row => {
                const searchData = row.getAttribute('data-search');
                if (searchData && searchData.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
    
    // Botón personalizado para mostrar/ocultar campos de fecha
    if (personalizadoBtn && fechasPersonalizadas) {
        personalizadoBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            console.log('Botón personalizado clickeado'); // Para debug
            
            // Toggle visibility
            fechasPersonalizadas.classList.toggle('hidden');
            
            // Actualizar apariencia del botón
            if (fechasPersonalizadas.classList.contains('hidden')) {
                this.classList.remove('bg-blue-600', 'text-white');
                this.classList.add('bg-gray-100', 'text-gray-700');
                console.log('Ocultando campos de fecha');
            } else {
                this.classList.remove('bg-gray-100', 'text-gray-700');
                this.classList.add('bg-blue-600', 'text-white');
                console.log('Mostrando campos de fecha');
            }
        });
    } else {
        console.log('No se encontraron los elementos necesarios para el filtro personalizado');
        if (!personalizadoBtn) console.log('personalizadoBtn no encontrado');
        if (!fechasPersonalizadas) console.log('fechasPersonalizadas no encontrado');
    }
});
</script>

@endsection