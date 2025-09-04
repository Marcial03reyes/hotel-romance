@extends('layouts.app')

@section('title', 'Dashboard - Hotel Romance')

@section('content')
<!-- Estadísticas principales -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <!-- Clientes Hoy -->
    <div class="bg-white rounded-lg border border-gray-100 p-6 shadow-md shadow-black/5">
        <div class="flex justify-between mb-6">
            <div>
                <div class="flex items-center mb-1">
                    <div class="text-2xl font-semibold">
                        {{ \App\Models\FactRegistroCliente::whereDate('fecha_ingreso', today())->count() }}
                    </div>
                    <div class="p-1 rounded" style="background: rgba(136, 166, 211, 0.1); color: #6B8CC7;">
                        <span class="text-[12px] font-semibold leading-none">nuevos</span>
                    </div>
                </div>
                <div class="text-sm font-medium text-gray-400">Clientes Hoy</div>
            </div>
            <div class="w-12 h-12 rounded-full flex items-center justify-center text-white" style="background: linear-gradient(135deg, #88A6D3, #6B8CC7);">
                <i class='bx bx-user text-xl'></i>
            </div>
        </div>
        <a href="{{ route('clientes.index') }}" class="font-medium text-sm" style="color: #6B8CC7;" onmouseover="this.style.color='#4A73B8'" onmouseout="this.style.color='#6B8CC7'">
            Ver Clientes →
        </a>
    </div>

    <!-- Ingresos del Día -->
    <div class="bg-white rounded-lg border border-gray-100 p-6 shadow-md shadow-black/5">
        <div class="flex justify-between mb-6">
            <div>
                <div class="flex items-center mb-1">
                    <div class="text-2xl font-semibold">
                        S/{{ number_format(\App\Models\FactPagoHab::whereHas('estadia', function($query) {
                            $query->whereDate('fecha_ingreso', today());
                        })->sum('monto'), 2) }}
                    </div>
                </div>
                <div class="text-sm font-medium text-gray-400">Ingresos Hoy</div>
            </div>
            <div class="w-12 h-12 rounded-full flex items-center justify-center text-white" style="background: linear-gradient(135deg, #A5BFDB, #88A6D3);">
                <i class='bx bx-dollar text-xl'></i>
            </div>
        </div>
        <a href="{{ route('registros.index') }}" class="font-medium text-sm" style="color: #88A6D3;" onmouseover="this.style.color='#4A73B8'" onmouseout="this.style.color='#88A6D3'">
            Ver Detalle →
        </a>
    </div>

    <!-- Gastos del Mes -->
    <div class="bg-white rounded-lg border border-gray-100 p-6 shadow-md shadow-black/5">
        <div class="flex justify-between mb-6">
            <div>
                <div class="flex items-center mb-1">
                    <div class="text-2xl font-semibold">
                        @php
                            $gastosGenerales = \App\Models\FactGastoGeneral::whereMonth('fecha_gasto', now()->month)
                                ->whereYear('fecha_gasto', now()->year)->sum('monto');
                            
                            $gastosFijos = \App\Models\FactPagoGastoFijo::whereMonth('fecha_pago', now()->month)
                                ->whereYear('fecha_pago', now()->year)->sum('monto_pagado');
                            
                            $totalGastosMes = $gastosGenerales + $gastosFijos;
                        @endphp
                        S/{{ number_format($totalGastosMes, 2) }}
                    </div>
                    
                    {{-- Mostrar desglose en tooltip --}}
                    @if($gastosFijos > 0)
                    <div class="ml-2 text-xs text-gray-500">
                        <div title="Gastos Generales: S/{{ number_format($gastosGenerales, 2) }} | Servicios Fijos: S/{{ number_format($gastosFijos, 2) }}">
                            <i class='bx bx-info-circle'></i>
                        </div>
                    </div>
                    @endif
                </div>
                <div class="text-sm font-medium text-gray-400">
                    Gastos del Mes
                    @if($gastosFijos > 0)
                        <span class="text-xs">(incluye servicios fijos)</span>
                    @endif
                </div>
            </div>
            <div class="w-12 h-12 rounded-full flex items-center justify-center text-white" style="background: linear-gradient(135deg, #C8D7ED, #A5BFDB);">
                <i class='bx bx-wallet text-xl'></i>
            </div>
        </div>
        <div class="flex justify-between items-center">
            <a href="{{ route('gastos.index') }}" class="font-medium text-sm mr-2" style="color: #A5BFDB;" onmouseover="this.style.color='#4A73B8'" onmouseout="this.style.color='#A5BFDB'">
                Ver Gastos →
            </a>
            @if($gastosFijos > 0)
            <a href="{{ route('gastos-fijos.index') }}" class="font-medium text-sm text-blue-600 hover:text-blue-800">
                Servicios →
            </a>
            @endif
        </div>
    </div>

    <!-- Monto Boletas del Mes -->
    <div class="bg-white rounded-lg border border-gray-100 p-6 shadow-md shadow-black/5">
        <div class="flex justify-between mb-6">
            <div>
                <div class="flex items-center mb-1">
                    <div class="text-2xl font-semibold">
                        S/{{ number_format(\App\Models\FactPagoHab::whereHas('estadia', function($query) {
                            $query->whereMonth('fecha_ingreso', now()->month)
                                  ->whereYear('fecha_ingreso', now()->year);
                        })->where('boleta', 'SI')->sum('monto'), 2) }}
                    </div>
                    <div class="p-1 rounded ml-2" style="background: rgba(74, 115, 184, 0.1); color: #4A73B8;">
                        <span class="text-[12px] font-semibold leading-none">
                            {{ \App\Models\FactPagoHab::whereHas('estadia', function($query) {
                                $query->whereMonth('fecha_ingreso', now()->month)
                                      ->whereYear('fecha_ingreso', now()->year);
                            })->where('boleta', 'SI')->count() }}
                        </span>
                    </div>
                </div>
                <div class="text-sm font-medium text-gray-400">Monto Boletas del Mes</div>
            </div>
            <div class="w-12 h-12 rounded-full flex items-center justify-center text-white" style="background: linear-gradient(135deg, #6B8CC7, #4A73B8);">
                <i class='bx bx-receipt text-xl'></i>
            </div>
        </div>
        <span class="font-medium text-sm" style="color: #4A73B8;">
            Total boletas emitidas
        </span>
    </div>
</div>

<!-- Resumen de Trabajadores y Gráfico -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

    @php
    // Obtener ingresos por turno de la semana actual
    $inicioSemana = now()->startOfWeek();
    $finSemana = now()->endOfWeek();

    $ingresosPorTurno = DB::table('fact_pago_hab as fph')
        ->join('fact_registro_clientes as frc', 'frc.id_estadia', '=', 'fph.id_estadia')
        ->whereBetween('frc.fecha_ingreso', [$inicioSemana, $finSemana])
        ->selectRaw('
            DAYOFWEEK(frc.fecha_ingreso) as dia_semana,
            frc.turno,
            SUM(fph.monto) as total_ingresos
        ')
        ->groupBy('dia_semana', 'frc.turno')
        ->get();

    // Organizar datos para el gráfico
    $datosTurnos = [];
    $diasSemana = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];

    // Mapeo de números a nombres de turnos (ajusta según tus valores)
    $mapeoTurnos = [
        0 => 'Día',
        1 => 'Noche'
    ];

    foreach($mapeoTurnos as $numTurno => $nombreTurno) {
        $datosTurnos[$nombreTurno] = array_fill(0, 7, 0);
    }

    foreach($ingresosPorTurno as $ingreso) {
        $indiceDia = $ingreso->dia_semana - 1;
        $nombreTurno = $mapeoTurnos[$ingreso->turno] ?? 'Desconocido';
        
        if(isset($datosTurnos[$nombreTurno])) {
            $datosTurnos[$nombreTurno][$indiceDia] = (float) $ingreso->total_ingresos;
        }
    }
    @endphp
    
    <!-- Gráfico de Ingresos por Turno -->
    <div class="bg-white border border-gray-100 shadow-md shadow-black/5 p-6 rounded-lg">
        <div class="flex justify-between mb-4 items-start">
            <div class="font-semibold text-lg">Ingresos por Turno</div>
            <select class="text-sm border border-gray-200 rounded px-3 py-1" style="border-color: #C8D7ED;">
                <option>Esta Semana</option>
                <option>Semana Anterior</option>
                <option>Este Mes</option>
            </select>
        </div>
        <div class="mt-4">
            <canvas id="turnos-chart"></canvas>
        </div>
    </div>

    <!-- Gráfico de Ocupación -->
    <div class="bg-white border border-gray-100 shadow-md shadow-black/5 p-6 rounded-lg">
        <div class="flex justify-between mb-4 items-start">
            <div class="font-semibold text-lg">Ocupación Semanal</div>
            <select class="text-sm border border-gray-200 rounded px-3 py-1" style="border-color: #C8D7ED;">
                <option>Esta Semana</option>
                <option>Última Semana</option>
                <option>Este Mes</option>
            </select>
        </div>
        <div class="mt-4">
            <canvas id="occupancy-chart"></canvas>
        </div>
    </div>
</div>

<!-- Gráfico y Ventas de Productos -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Ventas de Productos -->
    <div class="bg-white border border-gray-100 shadow-md shadow-black/5 p-6 rounded-lg">
        <div class="font-semibold text-lg mb-4">Productos Más Vendidos (Hoy)</div>
        <div class="space-y-3">
            @php
                $productosVendidos = \App\Models\FactPagoProd::with('producto')
                    ->whereHas('estadia', function($query) {
                        $query->whereDate('fecha_ingreso', today());
                    })
                    ->selectRaw('id_prod_bod, SUM(cantidad) as total_cantidad, SUM(cantidad * precio_unitario) as total_monto')
                    ->groupBy('id_prod_bod')
                    ->orderByDesc('total_cantidad')
                    ->take(5)
                    ->get();
            @endphp
            
            @forelse($productosVendidos as $producto)
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded flex items-center justify-center mr-3" style="background: rgba(136, 166, 211, 0.1);">
                        <i class='bx bx-package' style="color: #6B8CC7;"></i>
                    </div>
                    <div>
                        <div class="text-sm font-medium">{{ $producto->producto->nombre ?? 'Producto' }}</div>
                        <div class="text-xs text-gray-500">{{ $producto->total_cantidad }} unidades</div>
                    </div>
                </div>
                <span class="text-sm font-semibold">S/{{ number_format($producto->total_monto, 2) }}</span>
            </div>
            @empty
            <div class="text-center py-4 text-gray-500">
                <i class='bx bx-package text-3xl mb-2'></i>
                <p>No hay ventas de productos hoy</p>
            </div>
            @endforelse
        </div>
        
        @if($productosVendidos->count() > 0)
        <div class="mt-4 pt-4 border-t border-gray-100">
            <div class="flex justify-between text-sm">
                <span class="font-medium">Total ventas productos:</span>
                <span class="font-bold" style="color: #6B8CC7;">
                    S/{{ number_format($productosVendidos->sum('total_monto'), 2) }}
                </span>
            </div>
        </div>
        @endif
    </div>

    <!-- Métodos de Pago del Día -->
    <div class="bg-white border border-gray-100 shadow-md shadow-black/5 p-6 rounded-lg">
        <div class="font-semibold text-lg mb-4">Métodos de Pago (Hoy)</div>
        <div class="space-y-3">
            @php
                $metodosPago = \App\Models\FactPagoHab::with('metodoPago')
                    ->whereHas('estadia', function($query) {
                        $query->whereDate('fecha_ingreso', today());
                    })
                    ->selectRaw('id_met_pago, COUNT(*) as total_transacciones, SUM(monto) as total_monto')
                    ->groupBy('id_met_pago')
                    ->orderByDesc('total_monto')
                    ->get();
                    
                $totalRecaudado = $metodosPago->sum('total_monto');
            @endphp
            
            @forelse($metodosPago as $metodo)
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded flex items-center justify-center mr-3" style="background: rgba(74, 115, 184, 0.1);">
                        @switch($metodo->metodoPago->met_pago ?? '')
                            @case('Efectivo')
                                <i class='bx bx-money' style="color: #4A73B8;"></i>
                                @break
                            @case('Yape')
                            @case('Plin')
                                <i class='bx bx-mobile' style="color: #4A73B8;"></i>
                                @break
                            @case('Tarjeta')
                                <i class='bx bx-credit-card' style="color: #4A73B8;"></i>
                                @break
                            @case('QR')
                                <i class='bx bx-qr' style="color: #4A73B8;"></i>
                                @break
                            @default
                                <i class='bx bx-dollar' style="color: #4A73B8;"></i>
                        @endswitch
                    </div>
                    <div>
                        <div class="text-sm font-medium">{{ $metodo->metodoPago->met_pago ?? 'Desconocido' }}</div>
                        <div class="text-xs text-gray-500">{{ $metodo->total_transacciones }} transacciones</div>
                    </div>
                </div>
                <span class="text-sm font-semibold">S/{{ number_format($metodo->total_monto, 2) }}</span>
            </div>
            @empty
            <div class="text-center py-4 text-gray-500">
                <i class='bx bx-money text-3xl mb-2'></i>
                <p>No hay transacciones hoy</p>
            </div>
            @endforelse
        </div>
        
        @if($metodosPago->count() > 0)
        <div class="mt-4 pt-4 border-t border-gray-100">
            <div class="flex justify-between text-sm mb-2">
                <span class="font-medium">Total recaudado:</span>
                <span class="font-bold" style="color: #4A73B8;">S/{{ number_format($totalRecaudado, 2) }}</span>
            </div>
            @if($metodosPago->count() > 0)
            <div class="text-xs text-gray-500">
                @php
                    $metodoPrincipal = $metodosPago->first();
                    $porcentaje = $totalRecaudado > 0 ? ($metodoPrincipal->total_monto / $totalRecaudado) * 100 : 0;
                @endphp
                Método preferido: {{ $metodoPrincipal->metodoPago->met_pago ?? 'N/A' }} ({{ number_format($porcentaje, 1) }}%)
            </div>
            @endif
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>

    // Gráfico de ingresos por turno
    const ctxTurnos = document.getElementById('turnos-chart');
    if (ctxTurnos) {
        new Chart(ctxTurnos, {
            type: 'bar',
            data: {
                labels: ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'],
                datasets: [
                    {
                        label: 'Mañana',
                        data: {!! json_encode(array_values($datosTurnos['Mañana'] ?? array_fill(0, 7, 0))) !!},
                        backgroundColor: 'rgba(136, 166, 211, 0.8)',
                        borderColor: '#88A6D3',
                        borderWidth: 1
                    },
                    {
                        label: 'Tarde',
                        data: {!! json_encode(array_values($datosTurnos['Tarde'] ?? array_fill(0, 7, 0))) !!},
                        backgroundColor: 'rgba(107, 140, 199, 0.8)',
                        borderColor: '#6B8CC7',
                        borderWidth: 1
                    },
                    {
                        label: 'Noche',
                        data: {!! json_encode(array_values($datosTurnos['Noche'] ?? array_fill(0, 7, 0))) !!},
                        backgroundColor: 'rgba(74, 115, 184, 0.8)',
                        borderColor: '#4A73B8',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'S/' + value.toFixed(0);
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': S/' + context.parsed.y.toFixed(2);
                            }
                        }
                    }
                }
            }
        });
    }

    // Gráfico de ocupación
    const ctx = document.getElementById('occupancy-chart');
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'],
                datasets: [{
                    label: 'Habitaciones Ocupadas',
                    data: [4, 4, 5, 3, 6, 5, 6],
                    borderWidth: 2,
                    fill: true,
                    pointBackgroundColor: '#88A6D3',
                    borderColor: '#88A6D3',
                    backgroundColor: 'rgba(136, 166, 211, 0.1)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 6,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }
</script>
@endpush