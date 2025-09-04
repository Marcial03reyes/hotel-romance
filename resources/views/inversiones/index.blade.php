@extends('layouts.app')

@section('title', 'Inversiones - Hotel Romance')

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

    .form-container {
        background: linear-gradient(135deg, #f4f8fc 0%, #e8f2ff 100%);
    }
    
    .btn-romance {
        background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
        transition: all 0.3s ease;
        color: white;
    }
    
    .btn-romance:hover {
        background: linear-gradient(135deg, var(--secondary-color), var(--accent-color));
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(136, 166, 211, 0.3);
    }
    
    .btn-secondary {
        background: #f3f4f6;
        color: #374151;
        border: 1px solid #d1d5db;
        transition: all 0.3s ease;
    }
    
    .btn-secondary:hover {
        background: #e5e7eb;
        border-color: #9ca3af;
    }

    .btn-edit {
        background: var(--tertiary-color);
        color: var(--accent-color);
        border: 1px solid var(--light-blue);
        transition: all 0.3s ease;
    }

    .btn-edit:hover:not(.pointer-events-none) {
        background: var(--primary-color);
        color: white;
        transform: translateY(-1px);
    }

    .btn-delete {
        background: #fee2e2;
        color: #dc2626;
        border: 1px solid #fecaca;
        transition: all 0.3s ease;
    }

    .btn-delete:hover:not(.pointer-events-none) {
        background: #dc2626;
        color: white;
        transform: translateY(-1px);
    }
    
    .table-container {
        background: white;
        border-radius: 0.75rem;
        padding: 1.5rem;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        border: 1px solid #e5e7eb;
    }
    
    .badge-info {
        background: linear-gradient(135deg, var(--tertiary-color), var(--light-blue));
        color: var(--accent-color);
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    /* Iconos con color azul */
    .icon-azul {
        color: var(--secondary-color);
    }

    /* Enlaces de navegación */
    .nav-link:hover {
        color: var(--secondary-color);
    }

    /* Checkbox personalizado */
    .checkbox-azul {
        accent-color: var(--primary-color);
        transform: scale(1.2);
    }

    /* Header de tabla con tema azul */
    .table-header {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border-bottom: 2px solid var(--light-blue);
    }

    .table-header th {
        color: var(--accent-color);
        font-weight: 600;
        font-size: 0.875rem;
    }

    /* Filas de tabla */
    .table-row:hover {
        background-color: #f8fafc;
    }

    /* Información adicional con tema azul */
    .info-box {
        background-color: #f0f7ff;
        border-color: var(--light-blue);
    }

    .info-text {
        color: var(--accent-color);
    }

    .info-icon {
        color: var(--secondary-color);
    }

    /* Mensaje de éxito personalizado */
    .success-message {
        background: linear-gradient(135deg, #ecfdf5 0%, #f0fdf4 100%);
        border-color: #86efac;
        color: #166534;
    }

    /* Estadísticas */
    .stats-card {
        background: linear-gradient(135deg, var(--light-blue) 0%, #e0f2fe 100%);
        border: 1px solid var(--tertiary-color);
    }

    .stats-icon {
        background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
    }
</style>

<div class="container mx-auto py-6 px-4">
    
    <!-- Header con información de inversiones -->
    <div class="mb-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">
                    <i class='bx bx-trending-up mr-2 icon-azul'></i>
                    Gestión de Inversiones
                </h1>
                <p class="text-gray-600">Administra y controla todas las inversiones de la empresa</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('inversiones.create') }}"
                   class="btn-romance px-6 py-3 rounded-lg font-medium shadow-lg">
                    <i class='bx bx-plus mr-2'></i>
                    Agregar Inversión
                </a>
            </div>
        </div>
        
        <div class="flex items-center space-x-4 text-sm text-gray-600">
            <a href="{{ route('dashboard') }}" 
               class="inline-flex items-center nav-link transition-colors">
                <i class='bx bx-arrow-back mr-1'></i>
                Volver al dashboard
            </a>
            <span>•</span>
            <span class="text-gray-500">
                <i class='bx bx-info-circle mr-1'></i>
                Usa los botones de cada fila para editar o eliminar inversiones
            </span>
        </div>
    </div>

    <!-- Estadísticas rápidas -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="stats-card p-4 rounded-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total de Inversiones</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $inversiones->count() }}</p>
                </div>
                <div class="stats-icon w-12 h-12 rounded-full flex items-center justify-center text-white">
                    <i class='bx bx-trending-up text-xl'></i>
                </div>
            </div>
        </div>
        
        <div class="stats-card p-4 rounded-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Monto Total</p>
                    <p class="text-2xl font-bold text-gray-800">S/ {{ number_format($inversiones->sum('monto'), 2) }}</p>
                </div>
                <div class="stats-icon w-12 h-12 rounded-full flex items-center justify-center text-white">
                    <i class='bx bx-money text-xl'></i>
                </div>
            </div>
        </div>
        
        <div class="stats-card p-4 rounded-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Monto Este Mes</p>
                    <p class="text-2xl font-bold text-gray-800">S/ {{ number_format($inversiones->where('fecha_inversion', '>=', now()->startOfMonth())->sum('monto'), 2) }}</p>
                </div>
                <div class="stats-icon w-12 h-12 rounded-full flex items-center justify-center text-white">
                    <i class='bx bx-calendar text-xl'></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Mensaje de éxito -->
    @if(session('success'))
        <div class="success-message rounded-lg border p-4 mb-6 shadow-sm">
            <div class="flex items-center">
                <i class='bx bx-check-circle mr-2 text-lg'></i>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <!-- Tabla de inversiones -->
    <div class="table-container">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                <i class='bx bx-list-ul mr-2 icon-azul'></i>
                Lista de Inversiones
            </h2>
            <span class="badge-info">
                <i class='bx bx-info-circle mr-1'></i>
                {{ $inversiones->count() }} registro(s) total(es)
            </span>
        </div>
        
        <div class="overflow-x-auto rounded-xl border border-gray-200">
            <table class="min-w-full text-sm">
                <thead class="table-header">
                <tr>
                    <th class="px-6 py-4 text-left">
                        <i class='bx bx-tag mr-1'></i>
                        Detalle
                    </th>
                    <th class="px-6 py-4 text-left">
                        <i class='bx bx-money mr-1'></i>
                        Monto
                    </th>
                    <th class="px-6 py-4 text-left">
                        <i class='bx bx-credit-card mr-1'></i>
                        Método de Pago
                    </th>
                    <th class="px-6 py-4 text-left">
                        <i class='bx bx-calendar mr-1'></i>
                        Fecha
                    </th>
                    <th class="px-6 py-4 text-right">
                        <i class='bx bx-cog mr-1'></i>
                        Acciones
                    </th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                @forelse($inversiones as $inversion)
                    <tr class="table-row transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <i class='bx bx-trending-up mr-2 text-gray-400'></i>
                                <span class="font-medium text-gray-900">{{ $inversion->detalle }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-semibold text-emerald-600">S/ {{ number_format($inversion->monto, 2) }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $inversion->met_pago }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center text-gray-600">
                                <i class='bx bx-time mr-1'></i>
                                {{ \Illuminate\Support\Carbon::parse($inversion->fecha_inversion)->format('d/m/Y') }}
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <a href="{{ route('inversiones.edit', $inversion->id_inversion) }}" 
                               class="inline-flex items-center rounded-lg px-3 py-1.5 text-xs font-medium bg-blue-100 text-blue-800 hover:bg-blue-200 transition-all">
                                <i class='bx bx-edit mr-1'></i>
                                Editar
                            </a>
                            
                            <form action="{{ route('inversiones.destroy', $inversion->id_inversion) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        onclick="return confirm('¿Estás seguro de eliminar esta inversión?')"
                                        class="inline-flex items-center rounded-lg px-3 py-1.5 text-xs font-medium bg-red-100 text-red-800 hover:bg-red-200 transition-all">
                                    <i class='bx bx-trash mr-1'></i>
                                    Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <i class='bx bx-trending-up text-4xl text-gray-300 mb-2'></i>
                                <span class="text-gray-500 font-medium">No hay inversiones registradas</span>
                                <span class="text-gray-400 text-sm">Comienza agregando tu primera inversión</span>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Información adicional -->
    <div class="mt-8 p-4 info-box border rounded-lg">
        <div class="flex items-start">
            <i class='bx bx-info-circle info-icon text-lg mr-2 mt-1'></i>
            <div class="text-sm info-text">
                <p class="font-medium mb-1">Información importante:</p>
                <ul class="list-disc list-inside space-y-1">
                    <li>Las inversiones se ordenan por fecha de registro de más reciente a más antiguo</li>
                    <li>Puedes editar o eliminar inversiones individualmente usando los botones de la columna "Acciones"</li>
                    <li>Todos los montos se muestran en soles peruanos (S/)</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@endsection