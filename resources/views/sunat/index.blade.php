@extends('layouts.app')

@section('title', 'SUNAT - Hotel Romance')

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

    .btn-filter {
        background: var(--light-blue);
        color: var(--accent-color);
        border: 1px solid var(--tertiary-color);
        transition: all 0.3s ease;
    }

    .btn-filter:hover {
        background: var(--primary-color);
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

    .badge-boleta {
        background: linear-gradient(135deg, #dbeafe, #bfdbfe);
        color: #1e40af;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .badge-factura {
        background: linear-gradient(135deg, #dcfce7, #bbf7d0);
        color: #166534;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .badge-ninguno {
        background: linear-gradient(135deg, #f3f4f6, #e5e7eb);
        color: #6b7280;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .codigo-comprobante {
        background: #f8fafc;
        color: var(--accent-color);
        padding: 0.25rem 0.5rem;
        border-radius: 0.375rem;
        font-family: 'Courier New', monospace;
        font-size: 0.8rem;
        border: 1px solid var(--light-blue);
    }

    /* Iconos con color azul */
    .icon-azul {
        color: var(--secondary-color);
    }

    /* Enlaces de navegación */
    .nav-link:hover {
        color: var(--secondary-color);
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

    /* Filtros */
    .filter-container {
        background: white;
        border-radius: 0.75rem;
        padding: 1.25rem;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        border: 1px solid #e5e7eb;
        margin-bottom: 1.5rem;
    }
</style>

<div class="container mx-auto py-6 px-4">
    
    <!-- Header con información de SUNAT -->
    <div class="mb-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">
                    <i class='bx bx-receipt mr-2 icon-azul'></i>
                    Comprobantes SUNAT
                </h1>
                <p class="text-gray-600">Administra y controla todos los comprobantes fiscales</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('sunat.create') }}"
                   class="btn-romance px-6 py-3 rounded-lg font-medium shadow-lg">
                    <i class='bx bx-plus mr-2'></i>
                    Agregar Comprobante
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
                Usa los filtros para buscar comprobantes específicos
            </span>
        </div>
    </div>

    <!-- Filtros -->
    <div class="filter-container">
        <form method="GET" action="{{ route('sunat.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class='bx bx-calendar mr-1'></i>
                    Filtrar por Mes
                </label>
                <select name="mes" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Todos los meses</option>
                    @foreach($mesesConDatos as $mes)
                        @php
                            $valorMes = sprintf('%04d-%02d', $mes->anio, $mes->mes);
                            $nombresMeses = [
                                1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
                                5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
                                9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
                            ];
                            $nombreMes = $nombresMeses[$mes->mes] . ' ' . $mes->anio;
                        @endphp
                        <option value="{{ $valorMes }}" {{ request('mes') == $valorMes ? 'selected' : '' }}>
                            {{ $nombreMes }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class='bx bx-file mr-1'></i>
                    Tipo de Comprobante
                </label>
                <select name="tipo" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Todos los tipos</option>
                    <option value="BOLETA" {{ request('tipo') == 'BOLETA' ? 'selected' : '' }}>BOLETA</option>
                    <option value="FACTURA" {{ request('tipo') == 'FACTURA' ? 'selected' : '' }}>FACTURA</option>
                    <option value="NINGUNO" {{ request('tipo') == 'NINGUNO' ? 'selected' : '' }}>NINGUNO</option>
                </select>
            </div>

            <div class="flex space-x-2">
                <button type="submit" class="btn-filter px-4 py-2 rounded-lg font-medium flex items-center">
                    <i class='bx bx-search mr-2'></i>
                    Filtrar
                </button>
                <a href="{{ route('sunat.index') }}" class="btn-secondary px-4 py-2 rounded-lg font-medium flex items-center">
                    <i class='bx bx-x mr-2'></i>
                    Limpiar
                </a>
            </div>
        </form>
    </div>

    <!-- Estadísticas rápidas -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="stats-card p-4 rounded-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Comprobantes</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $comprobantes->count() }}</p>
                </div>
                <div class="stats-icon w-12 h-12 rounded-full flex items-center justify-center text-white">
                    <i class='bx bx-receipt text-xl'></i>
                </div>
            </div>
        </div>
        
        <div class="stats-card p-4 rounded-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Monto Total</p>
                    <p class="text-2xl font-bold text-gray-800">S/ {{ number_format($comprobantes->sum('monto'), 2) }}</p>
                </div>
                <div class="stats-icon w-12 h-12 rounded-full flex items-center justify-center text-white">
                    <i class='bx bx-money text-xl'></i>
                </div>
            </div>
        </div>
        
        <div class="stats-card p-4 rounded-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Boletas</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $comprobantes->where('tipo_comprobante', 'BOLETA')->count() }}</p>
                </div>
                <div class="stats-icon w-12 h-12 rounded-full flex items-center justify-center text-white">
                    <i class='bx bx-file text-xl'></i>
                </div>
            </div>
        </div>
        
        <div class="stats-card p-4 rounded-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Facturas</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $comprobantes->where('tipo_comprobante', 'FACTURA')->count() }}</p>
                </div>
                <div class="stats-icon w-12 h-12 rounded-full flex items-center justify-center text-white">
                    <i class='bx bx-file-blank text-xl'></i>
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

    <!-- Tabla de comprobantes -->
    <div class="table-container">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                <i class='bx bx-list-ul mr-2 icon-azul'></i>
                Lista de Comprobantes
            </h2>
            <span class="badge-info">
                <i class='bx bx-info-circle mr-1'></i>
                {{ $comprobantes->count() }} registro(s) total(es)
            </span>
        </div>
        
        <div class="overflow-x-auto rounded-xl border border-gray-200">
            <table class="min-w-full text-sm">
                <thead class="table-header">
                <tr>
                    <th class="px-6 py-4 text-left">
                        <i class='bx bx-receipt mr-1'></i>
                        Tipo Comprobante
                    </th>
                    <th class="px-6 py-4 text-left">
                        <i class='bx bx-barcode mr-1'></i>
                        Código
                    </th>
                    <th class="px-6 py-4 text-left">
                        <i class='bx bx-money mr-1'></i>
                        Monto
                    </th>
                    <th class="px-6 py-4 text-left">
                        <i class='bx bx-calendar mr-1'></i>
                        Fecha
                    </th>
                    <th class="px-6 py-4 text-left">
                        <i class='bx bx-file-blank mr-1'></i>
                        Archivo
                    </th>
                    <th class="px-6 py-4 text-right">
                        <i class='bx bx-cog mr-1'></i>
                        Acciones
                    </th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                @forelse($comprobantes as $comprobante)
                    <tr class="table-row transition-colors">
                        <td class="px-6 py-4">
                            @if($comprobante->tipo_comprobante == 'BOLETA')
                                <span class="badge-boleta">
                                    <i class='bx bx-receipt mr-1'></i>
                                    BOLETA
                                </span>
                            @elseif($comprobante->tipo_comprobante == 'FACTURA')
                                <span class="badge-factura">
                                    <i class='bx bx-file-blank mr-1'></i>
                                    FACTURA
                                </span>
                            @else
                                <span class="badge-ninguno">
                                    <i class='bx bx-x mr-1'></i>
                                    NINGUNO
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($comprobante->codigo_comprobante)
                                <span class="codigo-comprobante">
                                    <i class='bx bx-barcode mr-1'></i>
                                    {{ $comprobante->codigo_comprobante }}
                                </span>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-semibold text-green-600">S/ {{ number_format($comprobante->monto, 2) }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center text-gray-600">
                                <i class='bx bx-time mr-1'></i>
                                {{ \Illuminate\Support\Carbon::parse($comprobante->fecha_comprobante)->format('d/m/Y') }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($comprobante->archivo_comprobante)
                                <a href="{{ route('sunat.archivo', $comprobante->id_sunat) }}" 
                                   class="inline-flex items-center rounded-lg px-3 py-1.5 text-xs font-medium bg-green-100 text-green-800 hover:bg-green-200 transition-all">
                                    <i class='bx bx-file-blank mr-1'></i>
                                    Ver archivo
                                </a>
                            @else
                                <span class="inline-flex items-center rounded-lg px-3 py-1.5 text-xs font-medium bg-gray-100 text-gray-500">
                                    <i class='bx bx-x mr-1'></i>
                                    Sin archivo
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <a href="{{ route('sunat.edit', $comprobante->id_sunat) }}" 
                               class="inline-flex items-center rounded-lg px-3 py-1.5 text-xs font-medium bg-blue-100 text-blue-800 hover:bg-blue-200 transition-all">
                                <i class='bx bx-edit mr-1'></i>
                                Editar
                            </a>
                            
                            <form action="{{ route('sunat.destroy', $comprobante->id_sunat) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        onclick="return confirm('¿Estás seguro de eliminar este comprobante?')"
                                        class="inline-flex items-center rounded-lg px-3 py-1.5 text-xs font-medium bg-red-100 text-red-800 hover:bg-red-200 transition-all">
                                    <i class='bx bx-trash mr-1'></i>
                                    Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <i class='bx bx-receipt text-4xl text-gray-300 mb-2'></i>
                                <span class="text-gray-500 font-medium">No hay comprobantes registrados</span>
                                <span class="text-gray-400 text-sm">Comienza agregando tu primer comprobante</span>
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
                    <li>Los comprobantes se ordenan por fecha de más reciente a más antiguo</li>
                    <li>Puedes filtrar por mes y tipo de comprobante para encontrar registros específicos</li>
                    <li>Los archivos en verde indican que tienen archivo adjunto</li>
                    <li>Puedes editar o eliminar comprobantes individualmente usando los botones de "Acciones"</li>
                    <li>Todos los montos se muestran en soles peruanos (S/)</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@endsection