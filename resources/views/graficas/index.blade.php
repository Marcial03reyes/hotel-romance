@extends('layouts.app')

@section('title', 'Gráficas y Análisis - Hotel Romance')

@section('content')
<!-- Header -->
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-2">Análisis Financiero</h1>
    <p class="text-gray-600">Resumen de ingresos y gastos del hotel y bodega</p>
</div>

<!-- Botones de Filtro -->
<div class="mb-6">
    <div class="flex space-x-4">
        <button onclick="showSection('hotel')" 
                id="btn-hotel" 
                class="px-6 py-3 bg-emerald-600 text-white rounded-lg font-semibold hover:bg-emerald-700 transition-colors">
            🏨 HOTEL
        </button>
        <button onclick="showSection('bodega')" 
                id="btn-bodega" 
                class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg font-semibold hover:bg-gray-300 transition-colors">
            📦 BODEGA
        </button>
        <button onclick="showSection('ambos')" 
                id="btn-ambos" 
                class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg font-semibold hover:bg-gray-300 transition-colors">
            📊 AMBOS
        </button>
    </div>
</div>

<!-- Sección HOTEL -->
<div id="section-hotel">
    <!-- Gráfico HOTEL -->
    <div class="bg-white border border-gray-100 shadow-md shadow-black/5 p-6 rounded-lg mb-8">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h3 class="text-2xl font-bold text-gray-900">🏨 HOTEL</h3>
                <p class="text-sm text-gray-600">Ingresos - Gastos</p>
            </div>
            <div class="flex space-x-4 text-sm">
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-emerald-500 rounded-full mr-2"></div>
                    <span class="text-gray-600">Ingresos</span>
                </div>
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-red-500 rounded-full mr-2"></div>
                    <span class="text-gray-600">Gastos</span>
                </div>
            </div>
        </div>
        <div class="h-80">
            <canvas id="hotel-chart"></canvas>
        </div>
    </div>

    <!-- Tabla de Gastos HOTEL -->
    <div class="bg-white border border-gray-100 shadow-md shadow-black/5 rounded-lg mb-8">
        <div class="p-6 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-900">🏨 Gastos Mensuales - HOTEL</h3>
            <p class="text-sm text-gray-600 mt-1">Desglose de gastos por categoría</p>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="text-left py-4 px-6 font-semibold text-gray-700 text-sm uppercase tracking-wide">
                            Descripción
                        </th>
                        @foreach($gastosMensualesHotel['meses'] as $mesNum => $mesNombre)
                        <th class="text-center py-4 px-6 font-semibold text-gray-700 text-sm uppercase tracking-wide">
                            {{ $mesNombre }}
                        </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($gastosMensualesHotel['gastosPorTipo'] as $tipoGasto => $mesesData)
                    <tr class="hover:bg-gray-50">
                        <td class="py-4 px-6 text-sm font-medium text-gray-900">
                            {{ $tipoGasto }}
                        </td>
                        @foreach($gastosMensualesHotel['meses'] as $mesNum => $mesNombre)
                        <td class="py-4 px-6 text-center text-sm text-gray-700">
                            @if(isset($mesesData[$mesNum]))
                                S/ {{ number_format($mesesData[$mesNum], 2) }}
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        @endforeach
                    </tr>
                    @endforeach
                    
                    <!-- Fila de totales -->
                    <tr class="bg-emerald-50 font-semibold">
                        <td class="py-4 px-6 text-sm text-gray-900 font-bold">
                            Total HOTEL
                        </td>
                        @foreach($gastosMensualesHotel['meses'] as $mesNum => $mesNombre)
                        <td class="py-4 px-6 text-center text-sm text-emerald-700 font-bold">
                            S/ {{ number_format($gastosMensualesHotel['totalesPorMes'][$mesNum] ?? 0, 2) }}
                        </td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Sección BODEGA -->
<div id="section-bodega" style="display: none;">
    <!-- Gráfico BODEGA -->
    <div class="bg-white border border-gray-100 shadow-md shadow-black/5 p-6 rounded-lg mb-8">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h3 class="text-2xl font-bold text-gray-900">📦 BODEGA</h3>
                <p class="text-sm text-gray-600">Ingresos - Gastos</p>
            </div>
            <div class="flex space-x-4 text-sm">
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-blue-500 rounded-full mr-2"></div>
                    <span class="text-gray-600">Ingresos</span>
                </div>
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-orange-500 rounded-full mr-2"></div>
                    <span class="text-gray-600">Gastos</span>
                </div>
            </div>
        </div>
        <div class="h-80">
            <canvas id="bodega-chart"></canvas>
        </div>
    </div>

    <!-- Tabla de Gastos BODEGA -->
    <div class="bg-white border border-gray-100 shadow-md shadow-black/5 rounded-lg mb-8">
        <div class="p-6 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-900">📦 Gastos Mensuales - BODEGA</h3>
            <p class="text-sm text-gray-600 mt-1">Gastos en compras de productos para inventario</p>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="text-left py-4 px-6 font-semibold text-gray-700 text-sm uppercase tracking-wide">
                            Producto
                        </th>
                        @foreach($gastosMensualesBodega['meses'] as $mesNum => $mesNombre)
                        <th class="text-center py-4 px-6 font-semibold text-gray-700 text-sm uppercase tracking-wide">
                            {{ $mesNombre }}
                        </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($gastosMensualesBodega['gastosPorTipo'] as $producto => $mesesData)
                    <tr class="hover:bg-gray-50">
                        <td class="py-4 px-6 text-sm font-medium text-gray-900">
                            {{ $producto }}
                        </td>
                        @foreach($gastosMensualesBodega['meses'] as $mesNum => $mesNombre)
                        <td class="py-4 px-6 text-center text-sm text-gray-700">
                            @if(isset($mesesData[$mesNum]))
                                S/ {{ number_format($mesesData[$mesNum], 2) }}
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        @endforeach
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ count($gastosMensualesBodega['meses']) + 1 }}" class="py-8 px-6 text-center text-gray-500">
                            No hay compras registradas en este período
                        </td>
                    </tr>
                    @endforelse
                    
                    <!-- Fila de totales -->
                    <tr class="bg-blue-50 font-semibold">
                        <td class="py-4 px-6 text-sm text-gray-900 font-bold">
                            Total BODEGA
                        </td>
                        @foreach($gastosMensualesBodega['meses'] as $mesNum => $mesNombre)
                        <td class="py-4 px-6 text-center text-sm text-blue-700 font-bold">
                            S/ {{ number_format($gastosMensualesBodega['totalesPorMes'][$mesNum] ?? 0, 2) }}
                        </td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Sección AMBOS -->
<div id="section-ambos" style="display: none;">
    <!-- Gráficos lado a lado -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Gráfico HOTEL -->
        <div class="bg-white border border-gray-100 shadow-md shadow-black/5 p-6 rounded-lg">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-xl font-bold text-gray-900">🏨 HOTEL</h3>
                    <p class="text-sm text-gray-600">Ingresos - Gastos</p>
                </div>
                <div class="flex space-x-4 text-sm">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-emerald-500 rounded-full mr-2"></div>
                        <span class="text-gray-600">Ingresos</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-red-500 rounded-full mr-2"></div>
                        <span class="text-gray-600">Gastos</span>
                    </div>
                </div>
            </div>
            <div class="h-80">
                <canvas id="hotel-chart-small"></canvas>
            </div>
        </div>

        <!-- Gráfico BODEGA -->
        <div class="bg-white border border-gray-100 shadow-md shadow-black/5 p-6 rounded-lg">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-xl font-bold text-gray-900">📦 BODEGA</h3>
                    <p class="text-sm text-gray-600">Ingresos - Gastos</p>
                </div>
                <div class="flex space-x-4 text-sm">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-blue-500 rounded-full mr-2"></div>
                        <span class="text-gray-600">Ingresos</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-orange-500 rounded-full mr-2"></div>
                        <span class="text-gray-600">Gastos</span>
                    </div>
                </div>
            </div>
            <div class="h-80">
                <canvas id="bodega-chart-small"></canvas>
            </div>
        </div>
    </div>

    <!-- Tabla Comparativa -->
    <div class="bg-white border border-gray-100 shadow-md shadow-black/5 rounded-lg">
        <div class="p-6 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-900">📊 Resumen Comparativo</h3>
            <p class="text-sm text-gray-600 mt-1">Comparación de gastos entre Hotel y Bodega</p>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="text-left py-4 px-6 font-semibold text-gray-700 text-sm uppercase tracking-wide">
                            Área
                        </th>
                        @foreach($gastosMensualesHotel['meses'] as $mesNum => $mesNombre)
                        <th class="text-center py-4 px-6 font-semibold text-gray-700 text-sm uppercase tracking-wide">
                            {{ $mesNombre }}
                        </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr class="hover:bg-emerald-50">
                        <td class="py-4 px-6 text-sm font-bold text-emerald-700">
                            🏨 Total HOTEL
                        </td>
                        @foreach($gastosMensualesHotel['meses'] as $mesNum => $mesNombre)
                        <td class="py-4 px-6 text-center text-sm text-emerald-700 font-semibold">
                            S/ {{ number_format($gastosMensualesHotel['totalesPorMes'][$mesNum] ?? 0, 2) }}
                        </td>
                        @endforeach
                    </tr>
                    <tr class="hover:bg-blue-50">
                        <td class="py-4 px-6 text-sm font-bold text-blue-700">
                            📦 Total BODEGA
                        </td>
                        @foreach($gastosMensualesBodega['meses'] as $mesNum => $mesNombre)
                        <td class="py-4 px-6 text-center text-sm text-blue-700 font-semibold">
                            S/ {{ number_format($gastosMensualesBodega['totalesPorMes'][$mesNum] ?? 0, 2) }}
                        </td>
                        @endforeach
                    </tr>
                    <tr class="bg-gray-100 font-bold">
                        <td class="py-4 px-6 text-sm text-gray-900 font-bold">
                            📊 TOTAL GENERAL
                        </td>
                        @foreach($gastosMensualesHotel['meses'] as $mesNum => $mesNombre)
                        <td class="py-4 px-6 text-center text-sm text-gray-900 font-bold">
                            S/ {{ number_format(($gastosMensualesHotel['totalesPorMes'][$mesNum] ?? 0) + ($gastosMensualesBodega['totalesPorMes'][$mesNum] ?? 0), 2) }}
                        </td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Datos desde el backend
const datosHotel = @json($datosGraficos['hotel']);
const datosBodega = @json($datosGraficos['bodega']);

// Función para mostrar el período completo (ya viene formateado del backend como "Ago-Sep")
function extraerMesAbreviado(nombreCompleto) {
    // Retornar el nombre tal como viene del backend (formato "Ago-Sep")
    return nombreCompleto;
}

// Preparar datos para gráfico HOTEL
const labelsHotel = datosHotel.map(item => extraerMesAbreviado(item.nombre));
const ingresosHotel = datosHotel.map(item => item.ingresos);
const gastosHotel = datosHotel.map(item => item.gastos);

// Preparar datos para gráfico BODEGA
const labelsBodega = datosBodega.map(item => extraerMesAbreviado(item.nombre));
const ingresosBodega = datosBodega.map(item => item.ingresos);
const gastosBodega = datosBodega.map(item => item.gastos);

// Configuración común
const commonOptions = {
    responsive: true,
    maintainAspectRatio: false,
    interaction: {
        intersect: false,
        mode: 'index'
    },
    plugins: {
        legend: {
            display: false
        },
        tooltip: {
            backgroundColor: 'rgba(255, 255, 255, 0.95)',
            titleColor: '#374151',
            bodyColor: '#374151',
            borderColor: '#e5e7eb',
            borderWidth: 1,
            cornerRadius: 8,
            displayColors: true,
            callbacks: {
                label: function(context) {
                    return `${context.dataset.label}: S/ ${context.parsed.y.toLocaleString()}`;
                }
            }
        }
    },
    scales: {
        x: {
            grid: {
                display: false
            },
            ticks: {
                color: '#6b7280',
                maxRotation: 0,
                minRotation: 0
            }
        },
        y: {
            grid: {
                color: '#f3f4f6'
            },
            ticks: {
                color: '#6b7280',
                callback: function(value) {
                    return 'S/ ' + value.toLocaleString();
                }
            }
        }
    }
};

// Variables para almacenar las instancias de los gráficos
let hotelChart, bodegaChart, hotelChartSmall, bodegaChartSmall;

// Función para crear gráfico HOTEL
function createHotelChart(canvasId) {
    const ctx = document.getElementById(canvasId);
    return new Chart(ctx, {
        type: 'line',
        data: {
            labels: labelsHotel,
            datasets: [
                {
                    label: 'Ingresos',
                    data: ingresosHotel,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#10b981',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8
                },
                {
                    label: 'Gastos',
                    data: gastosHotel,
                    borderColor: '#ef4444',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#ef4444',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8
                }
            ]
        },
        options: commonOptions
    });
}

// Función para crear gráfico BODEGA
function createBodegaChart(canvasId) {
    const ctx = document.getElementById(canvasId);
    return new Chart(ctx, {
        type: 'line',
        data: {
            labels: labelsBodega,
            datasets: [
                {
                    label: 'Ingresos',
                    data: ingresosBodega,
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#3b82f6',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8
                },
                {
                    label: 'Gastos',
                    data: gastosBodega,
                    borderColor: '#f97316',
                    backgroundColor: 'rgba(249, 115, 22, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#f97316',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8
                }
            ]
        },
        options: commonOptions
    });
}

// Función para mostrar sección
function showSection(section) {
    // Ocultar todas las secciones
    document.getElementById('section-hotel').style.display = 'none';
    document.getElementById('section-bodega').style.display = 'none';
    document.getElementById('section-ambos').style.display = 'none';
    
    // Resetear estilos de botones
    document.getElementById('btn-hotel').className = 'px-6 py-3 bg-gray-200 text-gray-700 rounded-lg font-semibold hover:bg-gray-300 transition-colors';
    document.getElementById('btn-bodega').className = 'px-6 py-3 bg-gray-200 text-gray-700 rounded-lg font-semibold hover:bg-gray-300 transition-colors';
    document.getElementById('btn-ambos').className = 'px-6 py-3 bg-gray-200 text-gray-700 rounded-lg font-semibold hover:bg-gray-300 transition-colors';
    
    // Mostrar sección seleccionada y activar botón
    if (section === 'hotel') {
        document.getElementById('section-hotel').style.display = 'block';
        document.getElementById('btn-hotel').className = 'px-6 py-3 bg-emerald-600 text-white rounded-lg font-semibold hover:bg-emerald-700 transition-colors';
        // Crear gráfico si no existe
        if (!hotelChart) {
            hotelChart = createHotelChart('hotel-chart');
        }
    } else if (section === 'bodega') {
        document.getElementById('section-bodega').style.display = 'block';
        document.getElementById('btn-bodega').className = 'px-6 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition-colors';
        // Crear gráfico si no existe
        if (!bodegaChart) {
            bodegaChart = createBodegaChart('bodega-chart');
        }
    } else if (section === 'ambos') {
        document.getElementById('section-ambos').style.display = 'block';
        document.getElementById('btn-ambos').className = 'px-6 py-3 bg-purple-600 text-white rounded-lg font-semibold hover:bg-purple-700 transition-colors';
        // Crear gráficos pequeños si no existen
        setTimeout(() => {
            if (!hotelChartSmall) {
                hotelChartSmall = createHotelChart('hotel-chart-small');
            }
            if (!bodegaChartSmall) {
                bodegaChartSmall = createBodegaChart('bodega-chart-small');
            }
        }, 100);
    }
}

// Inicializar la vista por defecto (Hotel)
document.addEventListener('DOMContentLoaded', function() {
    showSection('hotel');
});
</script>
@endpush