@extends('layouts.app')

@section('title', 'Cuadre Caja - Hotel Romance')

@section('content')

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Selector de fechas múltiples -->
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 mb-6">
            <form method="GET" action="{{ route('cuadre-caja.index') }}" id="fechasForm">
                <div class="flex flex-wrap items-center gap-4 mb-4">
                    <div class="flex-1">
                        <label for="fechas" class="block text-sm font-medium text-gray-700 mb-2">
                            Seleccionar Fechas (múltiples días)
                        </label>
                        <div class="relative">
                            <input type="text" id="fechasInput" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Haga clic para seleccionar fechas..." readonly>
                            <div class="absolute right-3 top-1/2 transform -translate-y-1/2">
                                <i class='bx bx-calendar text-gray-400'></i>
                            </div>
                        </div>
                        <!-- Campos ocultos para enviar fechas seleccionadas -->
                        <div id="fechasHiddenInputs">
                            @foreach($fechasSeleccionadas as $fecha)
                                <input type="hidden" name="fechas[]" value="{{ $fecha }}">
                            @endforeach
                        </div>
                    </div>
                    <div class="pt-6">
                        <button type="submit" 
                            class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 font-medium">
                            <i class='bx bx-search mr-2'></i>
                            Consultar
                        </button>
                    </div>
                </div>
                
                <!-- Fechas seleccionadas -->
                @if(count($fechasSeleccionadas) > 0)
                    <div class="mt-4">
                        <span class="text-sm font-medium text-gray-700">Fechas seleccionadas:</span>
                        <div class="flex flex-wrap gap-2 mt-2">
                            @foreach($fechasSeleccionadas as $fecha)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </form>
        </div>

        <!-- Matriz de datos por días -->
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-xl font-bold text-center mb-6 text-gray-800">CUADRE DE CAJA POR DÍAS</h3>
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse border border-gray-300 text-sm">
                        <thead>
                            <tr class="bg-gray-100">
                                <!-- Fecha -->
                                <th rowspan="2" class="border border-gray-300 px-3 py-3 text-center font-bold bg-gray-50">FECHA</th>
                                <!-- Método -->
                                <th rowspan="2" class="border border-gray-300 px-3 py-3 text-center font-bold bg-gray-50">MÉTODO</th>
                                <!-- Hotel -->
                                <th colspan="2" class="border border-gray-300 px-3 py-2 text-center font-bold bg-blue-100">HOTEL</th>
                                <!-- Bodega -->
                                <th colspan="2" class="border border-gray-300 px-3 py-2 text-center font-bold bg-green-100">BODEGA</th>
                                <!-- Gasto -->
                                <th colspan="2" class="border border-gray-300 px-3 py-2 text-center font-bold bg-red-100">GASTO</th>
                                <!-- Columnas auxiliares -->
                                <th rowspan="2" class="border border-gray-300 px-3 py-3 text-center font-bold bg-yellow-100">DÍA<br>(Efectivo)</th>
                                <th rowspan="2" class="border border-gray-300 px-3 py-3 text-center font-bold bg-yellow-100">NOCHE<br>(Efectivo)</th>
                            </tr>
                            <tr class="bg-gray-50">
                                <!-- Hotel turnos -->
                                <th class="border border-gray-300 px-3 py-2 text-center font-semibold bg-blue-50">DÍA</th>
                                <th class="border border-gray-300 px-3 py-2 text-center font-semibold bg-blue-50">NOCHE</th>
                                <!-- Bodega turnos -->
                                <th class="border border-gray-300 px-3 py-2 text-center font-semibold bg-green-50">DÍA</th>
                                <th class="border border-gray-300 px-3 py-2 text-center font-semibold bg-green-50">NOCHE</th>
                                <!-- Gasto turnos -->
                                <th class="border border-gray-300 px-3 py-2 text-center font-semibold bg-red-50">DÍA</th>
                                <th class="border border-gray-300 px-3 py-2 text-center font-semibold bg-red-50">NOCHE</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($datosPorDias as $fecha => $datos)
                                <!-- Efectivo -->
                                <tr class="hover:bg-gray-50">
                                    <td rowspan="3" class="border border-gray-300 px-3 py-4 text-center font-medium bg-gray-50">
                                        {{ $datos['fecha']->format('d-M') }}
                                    </td>
                                    <td class="border border-gray-300 px-3 py-2 font-medium">EFECTIVO</td>
                                    <!-- Hotel -->
                                    <td class="border border-gray-300 px-3 py-2 text-center">
                                        {{ number_format($datos['hotel']['dia']['efectivo'], 0) }}
                                    </td>
                                    <td class="border border-gray-300 px-3 py-2 text-center">
                                        {{ number_format($datos['hotel']['noche']['efectivo'], 0) }}
                                    </td>
                                    <!-- Bodega -->
                                    <td class="border border-gray-300 px-3 py-2 text-center">
                                        {{ number_format($datos['bodega']['dia']['efectivo'], 0) }}
                                    </td>
                                    <td class="border border-gray-300 px-3 py-2 text-center">
                                        {{ number_format($datos['bodega']['noche']['efectivo'], 0) }}
                                    </td>
                                    <!-- Gastos -->
                                    <td class="border border-gray-300 px-3 py-2 text-center text-red-600">
                                        {{ number_format($datos['gastos']['dia']['efectivo'], 0) }}
                                    </td>
                                    <td class="border border-gray-300 px-3 py-2 text-center text-red-600">
                                        {{ number_format($datos['gastos']['noche']['efectivo'], 0) }}
                                    </td>
                                    <!-- Columnas auxiliares (efectivo neto) -->
                                    @php
                                        $efectivoDia = $datos['hotel']['dia']['efectivo'] + $datos['bodega']['dia']['efectivo'] - $datos['gastos']['dia']['efectivo'];
                                        $efectivoNoche = $datos['hotel']['noche']['efectivo'] + $datos['bodega']['noche']['efectivo'] - $datos['gastos']['noche']['efectivo'];
                                    @endphp
                                    <td rowspan="3" class="border border-gray-300 px-3 py-2 text-center font-bold {{ $efectivoDia >= 0 ? 'text-green-600 bg-yellow-50' : 'text-red-600 bg-yellow-50' }}">
                                        {{ number_format($efectivoDia, 0) }}
                                    </td>
                                    <td rowspan="3" class="border border-gray-300 px-3 py-2 text-center font-bold {{ $efectivoNoche >= 0 ? 'text-green-600 bg-yellow-50' : 'text-red-600 bg-yellow-50' }}">
                                        {{ number_format($efectivoNoche, 0) }}
                                    </td>
                                </tr>

                                <!-- Yape/Plin -->
                                <tr class="hover:bg-gray-50">
                                    <td class="border border-gray-300 px-3 py-2 font-medium">YAPE/PLIN</td>
                                    <!-- Hotel -->
                                    <td class="border border-gray-300 px-3 py-2 text-center">
                                        {{ number_format($datos['hotel']['dia']['yape_plin'], 0) }}
                                    </td>
                                    <td class="border border-gray-300 px-3 py-2 text-center">
                                        {{ number_format($datos['hotel']['noche']['yape_plin'], 0) }}
                                    </td>
                                    <!-- Bodega -->
                                    <td class="border border-gray-300 px-3 py-2 text-center">
                                        {{ number_format($datos['bodega']['dia']['yape_plin'], 0) }}
                                    </td>
                                    <td class="border border-gray-300 px-3 py-2 text-center">
                                        {{ number_format($datos['bodega']['noche']['yape_plin'], 0) }}
                                    </td>
                                    <!-- Gastos -->
                                    <td class="border border-gray-300 px-3 py-2 text-center text-red-600">
                                        {{ number_format($datos['gastos']['dia']['yape_plin'], 0) }}
                                    </td>
                                    <td class="border border-gray-300 px-3 py-2 text-center text-red-600">
                                        {{ number_format($datos['gastos']['noche']['yape_plin'], 0) }}
                                    </td>
                                </tr>

                                <!-- Tarjeta -->
                                <tr class="hover:bg-gray-50 border-b-2 border-gray-400">
                                    <td class="border border-gray-300 px-3 py-2 font-medium">TARJETA</td>
                                    <!-- Hotel -->
                                    <td class="border border-gray-300 px-3 py-2 text-center">
                                        {{ number_format($datos['hotel']['dia']['tarjeta'], 0) }}
                                    </td>
                                    <td class="border border-gray-300 px-3 py-2 text-center">
                                        {{ number_format($datos['hotel']['noche']['tarjeta'], 0) }}
                                    </td>
                                    <!-- Bodega -->
                                    <td class="border border-gray-300 px-3 py-2 text-center">
                                        {{ number_format($datos['bodega']['dia']['tarjeta'], 0) }}
                                    </td>
                                    <td class="border border-gray-300 px-3 py-2 text-center">
                                        {{ number_format($datos['bodega']['noche']['tarjeta'], 0) }}
                                    </td>
                                    <!-- Gastos -->
                                    <td class="border border-gray-300 px-3 py-2 text-center text-red-600">
                                        {{ number_format($datos['gastos']['dia']['tarjeta'], 0) }}
                                    </td>
                                    <td class="border border-gray-300 px-3 py-2 text-center text-red-600">
                                        {{ number_format($datos['gastos']['noche']['tarjeta'], 0) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- RESUMEN FINAL EN MATRIZ -->
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mt-8">
            <div class="p-6">
                <h3 class="text-xl font-bold text-center mb-6 text-gray-800">RESUMEN FINAL</h3>
                <div class="flex justify-center">
                    <div class="w-full max-w-2xl">
                        <table class="w-full border-collapse border border-gray-300 text-lg">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="border border-gray-300 px-6 py-4 text-left font-bold"></th>
                                    <th class="border border-gray-300 px-6 py-4 text-center font-bold">HOTEL</th>
                                    <th class="border border-gray-300 px-6 py-4 text-center font-bold">BODEGA</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="border border-gray-300 px-6 py-4 font-bold">EFECTIVO</td>
                                    <td class="border border-gray-300 px-6 py-4 text-center {{ $resumenFinal['hotel']['efectivo'] >= 0 ? 'text-green-600' : 'text-red-600' }} font-bold">
                                        S/ {{ number_format($resumenFinal['hotel']['efectivo'], 0, ',', ',') }}
                                    </td>
                                    <td class="border border-gray-300 px-6 py-4 text-center {{ $resumenFinal['bodega']['efectivo'] >= 0 ? 'text-green-600' : 'text-red-600' }} font-bold">
                                        S/ {{ number_format($resumenFinal['bodega']['efectivo'], 0, ',', ',') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="border border-gray-300 px-6 py-4 font-bold">CUENTA</td>
                                    <td class="border border-gray-300 px-6 py-4 text-center {{ $resumenFinal['hotel']['cuenta'] >= 0 ? 'text-green-600' : 'text-red-600' }} font-bold">
                                        S/ {{ number_format($resumenFinal['hotel']['cuenta'], 0, ',', ',') }}
                                    </td>
                                    <td class="border border-gray-300 px-6 py-4 text-center {{ $resumenFinal['bodega']['cuenta'] >= 0 ? 'text-green-600' : 'text-red-600' }} font-bold">
                                        S/ {{ number_format($resumenFinal['bodega']['cuenta'], 0, ',', ',') }}
                                    </td>
                                </tr>
                                <tr class="bg-blue-100 font-bold text-xl">
                                    <td class="border border-gray-300 px-6 py-4 text-blue-800">TOTAL</td>
                                    <td colspan="2" class="border border-gray-300 px-6 py-4 text-center text-2xl font-bold {{ $resumenFinal['totales']['total'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        S/ {{ number_format($resumenFinal['totales']['total'], 0, ',', ',') }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Información adicional -->
                <div class="mt-6 text-center">
                    <div class="inline-flex items-center px-4 py-2 bg-yellow-100 border border-yellow-300 rounded-lg">
                        <i class='bx bx-info-circle text-yellow-600 mr-2'></i>
                        <span class="text-sm text-yellow-800">
                            Las columnas DÍA y NOCHE muestran el efectivo neto para cuadre de caja
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Flatpickr CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<!-- Flatpickr JS -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://npmcdn.com/flatpickr/dist/l10n/es.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar Flatpickr para selección múltiple de fechas
    const fechasInput = document.getElementById('fechasInput');
    const fechasHiddenInputs = document.getElementById('fechasHiddenInputs');
    
    // Fechas actualmente seleccionadas
    const fechasSeleccionadas = @json($fechasSeleccionadas);
    
    const fp = flatpickr(fechasInput, {
        mode: "multiple",
        dateFormat: "Y-m-d",
        defaultDate: fechasSeleccionadas,
        locale: "es",
        maxDate: "today",
        onClose: function(selectedDates, dateStr) {
            // Actualizar el texto del input
            if (selectedDates.length > 0) {
                const fechasTexto = selectedDates.map(fecha => 
                    fecha.toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' })
                ).join(', ');
                fechasInput.value = fechasTexto;
            } else {
                fechasInput.value = '';
            }
            
            // Actualizar campos ocultos
            fechasHiddenInputs.innerHTML = '';
            selectedDates.forEach(fecha => {
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'fechas[]';
                hiddenInput.value = fecha.toISOString().split('T')[0];
                fechasHiddenInputs.appendChild(hiddenInput);
            });
        }
    });
    
    // Establecer texto inicial
    if (fechasSeleccionadas.length > 0) {
        const fechasTexto = fechasSeleccionadas.map(fecha => {
            const date = new Date(fecha + 'T00:00:00');
            return date.toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' });
        }).join(', ');
        fechasInput.value = fechasTexto;
    }
});
</script>

@endsection