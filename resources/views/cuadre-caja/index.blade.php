@extends('layouts.app')

@section('title', 'Cuadre Caja - Hotel Romance')

@section('content')

<div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filtros de período -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 mb-6">
                <form method="GET" action="{{ route('cuadre-caja.index') }}" id="filtroForm">
                    <div class="flex flex-wrap items-center gap-4 mb-4">
                        <div class="flex gap-2">
                            <button type="submit" name="filtro" value="dia"
                                class="px-4 py-2 rounded-lg font-medium transition-colors duration-200 {{ $filtro === 'dia' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                                DÍA
                            </button>
                            <button type="submit" name="filtro" value="semana"
                                class="px-4 py-2 rounded-lg font-medium transition-colors duration-200 {{ $filtro === 'semana' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                                SEMANA
                            </button>
                            <button type="button" id="personalizadoBtn"
                                class="px-4 py-2 rounded-lg font-medium transition-colors duration-200 {{ $filtro === 'personalizado' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                                PERSONALIZADO
                            </button>
                        </div>
                    </div>
                    
                    <!-- Campos de fecha personalizada (ocultos por defecto) -->
                    <div id="fechasPersonalizadas" class="flex gap-4 items-center {{ $filtro !== 'personalizado' ? 'hidden' : '' }}">
                        <div>
                            <label for="fecha_inicio" class="block text-sm font-medium text-gray-700 mb-1">Fecha Inicio</label>
                            <input type="date" id="fecha_inicio" name="fecha_inicio" 
                                value="{{ $fechaInicio }}"
                                class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label for="fecha_fin" class="block text-sm font-medium text-gray-700 mb-1">Fecha Fin</label>
                            <input type="date" id="fecha_fin" name="fecha_fin" 
                                value="{{ $fechaFin }}"
                                class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div class="pt-6">
                            <button type="submit" name="filtro" value="personalizado"
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                                Consultar
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Subtítulo con período -->
            <div class="text-center mb-8">
                <h3 class="text-lg font-semibold text-gray-700">{{ $subtituloFecha }}</h3>
            </div>

            <!-- Tablas Diarias (solo para semana y personalizado) -->
            @if(($filtro === 'semana' || $filtro === 'personalizado') && isset($datosDiariosHotel))
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                    <!-- HOTEL - TABLAS DIARIAS -->
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-center mb-4 text-blue-600">HOTEL - DETALLE DIARIO</h3>
                            
                            <!-- Tabla de INGRESOS -->
                            <h4 class="text-lg font-semibold mb-3 text-blue-500">INGRESOS</h4>
                            <div class="overflow-x-auto mb-6">
                                <table class="w-full border-collapse border border-gray-300">
                                    <thead>
                                        <tr class="bg-blue-50">
                                            <th class="border border-gray-300 px-3 py-2 text-left font-semibold">Día</th>
                                            <th class="border border-gray-300 px-3 py-2 text-center font-semibold">Efectivo</th>
                                            <th class="border border-gray-300 px-3 py-2 text-center font-semibold">Yape/Plin</th>
                                            <th class="border border-gray-300 px-3 py-2 text-center font-semibold">Cta. Bancaria</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($datosDiariosHotel as $fecha => $datos)
                                        <tr>
                                            <td class="border border-gray-300 px-3 py-2 font-medium">
                                                {{ ucfirst($datos['fecha']->locale('es')->dayName) }} {{ $datos['fecha']->day }}
                                            </td>
                                            <td class="border border-gray-300 px-3 py-2 text-center">
                                                S/{{ number_format($datos['ingresos']['Efectivo'], 2) }}
                                            </td>
                                            <td class="border border-gray-300 px-3 py-2 text-center">
                                                S/{{ number_format($datos['ingresos']['Yape/Plin'], 2) }}
                                            </td>
                                            <td class="border border-gray-300 px-3 py-2 text-center">
                                                S/{{ number_format($datos['ingresos']['Cuenta Bancaria'], 2) }}
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Tabla de GASTOS -->
                            <h4 class="text-lg font-semibold mb-3 text-red-500">GASTOS</h4>
                            <div class="overflow-x-auto">
                                <table class="w-full border-collapse border border-gray-300">
                                    <thead>
                                        <tr class="bg-red-50">
                                            <th class="border border-gray-300 px-3 py-2 text-left font-semibold">Día</th>
                                            <th class="border border-gray-300 px-3 py-2 text-center font-semibold">Efectivo</th>
                                            <th class="border border-gray-300 px-3 py-2 text-center font-semibold">Yape/Plin</th>
                                            <th class="border border-gray-300 px-3 py-2 text-center font-semibold">Cta. Bancaria</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($datosDiariosHotel as $fecha => $datos)
                                        <tr>
                                            <td class="border border-gray-300 px-3 py-2 font-medium">
                                                {{ ucfirst($datos['fecha']->locale('es')->dayName) }} {{ $datos['fecha']->day }}
                                            </td>
                                            <td class="border border-gray-300 px-3 py-2 text-center">
                                                S/{{ number_format($datos['gastos']['Efectivo'], 2) }}
                                            </td>
                                            <td class="border border-gray-300 px-3 py-2 text-center">
                                                S/{{ number_format($datos['gastos']['Yape/Plin'], 2) }}
                                            </td>
                                            <td class="border border-gray-300 px-3 py-2 text-center">
                                                S/{{ number_format($datos['gastos']['Cuenta Bancaria'], 2) }}
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- BODEGA - TABLAS DIARIAS -->
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-center mb-4 text-green-600">BODEGA - DETALLE DIARIO</h3>
                            
                            <!-- Tabla de INGRESOS -->
                            <h4 class="text-lg font-semibold mb-3 text-green-500">INGRESOS</h4>
                            <div class="overflow-x-auto mb-6">
                                <table class="w-full border-collapse border border-gray-300">
                                    <thead>
                                        <tr class="bg-green-50">
                                            <th class="border border-gray-300 px-3 py-2 text-left font-semibold">Día</th>
                                            <th class="border border-gray-300 px-3 py-2 text-center font-semibold">Efectivo</th>
                                            <th class="border border-gray-300 px-3 py-2 text-center font-semibold">Yape/Plin</th>
                                            <th class="border border-gray-300 px-3 py-2 text-center font-semibold">Cta. Bancaria</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($datosDiariosBodega as $fecha => $datos)
                                        <tr>
                                            <td class="border border-gray-300 px-3 py-2 font-medium">
                                                {{ ucfirst($datos['fecha']->locale('es')->dayName) }} {{ $datos['fecha']->day }}
                                            </td>
                                            <td class="border border-gray-300 px-3 py-2 text-center">
                                                S/{{ number_format($datos['ingresos']['Efectivo'], 2) }}
                                            </td>
                                            <td class="border border-gray-300 px-3 py-2 text-center">
                                                S/{{ number_format($datos['ingresos']['Yape/Plin'], 2) }}
                                            </td>
                                            <td class="border border-gray-300 px-3 py-2 text-center">
                                                S/{{ number_format($datos['ingresos']['Cuenta Bancaria'], 2) }}
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Tabla de GASTOS -->
                            <h4 class="text-lg font-semibold mb-3 text-red-500">GASTOS</h4>
                            <div class="overflow-x-auto">
                                <table class="w-full border-collapse border border-gray-300">
                                    <thead>
                                        <tr class="bg-red-50">
                                            <th class="border border-gray-300 px-3 py-2 text-left font-semibold">Día</th>
                                            <th class="border border-gray-300 px-3 py-2 text-center font-semibold">Efectivo</th>
                                            <th class="border border-gray-300 px-3 py-2 text-center font-semibold">Yape/Plin</th>
                                            <th class="border border-gray-300 px-3 py-2 text-center font-semibold">Cta. Bancaria</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($datosDiariosBodega as $fecha => $datos)
                                        <tr>
                                            <td class="border border-gray-300 px-3 py-2 font-medium">
                                                {{ ucfirst($datos['fecha']->locale('es')->dayName) }} {{ $datos['fecha']->day }}
                                            </td>
                                            <td class="border border-gray-300 px-3 py-2 text-center">
                                                S/{{ number_format($datos['gastos']['Efectivo'], 2) }}
                                            </td>
                                            <td class="border border-gray-300 px-3 py-2 text-center">
                                                S/{{ number_format($datos['gastos']['Yape/Plin'], 2) }}
                                            </td>
                                            <td class="border border-gray-300 px-3 py-2 text-center">
                                                S/{{ number_format($datos['gastos']['Cuenta Bancaria'], 2) }}
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- HOTEL -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-center mb-6 text-blue-600">HOTEL</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full border-collapse border border-gray-300">
                                <thead>
                                    <tr class="bg-gray-50">
                                        <th class="border border-gray-300 px-4 py-3 text-left font-semibold">Método</th>
                                        <th class="border border-gray-300 px-4 py-3 text-center font-semibold">Ingresos</th>
                                        <th class="border border-gray-300 px-4 py-3 text-center font-semibold">Gastos</th>
                                        <th class="border border-gray-300 px-4 py-3 text-center font-semibold bg-gray-200">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="border border-gray-300 px-4 py-3 font-medium">Efectivo</td>
                                        <td class="border border-gray-300 px-4 py-3 text-center">
                                            S/{{ number_format($datosHotel['Efectivo']['ingreso'] ?? 0, 2) }}
                                        </td>
                                        <td class="border border-gray-300 px-4 py-3 text-center">
                                            S/{{ number_format($datosHotel['Efectivo']['gasto'] ?? 0, 2) }}
                                        </td>
                                        <td class="border border-gray-300 px-4 py-3 text-center bg-gray-100 font-semibold {{ ($datosHotel['Efectivo']['total'] ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                            S/{{ number_format($datosHotel['Efectivo']['total'] ?? 0, 2) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="border border-gray-300 px-4 py-3 font-medium">Yape/Plin</td>
                                        <td class="border border-gray-300 px-4 py-3 text-center">
                                            S/{{ number_format($datosHotel['Yape/Plin']['ingreso'] ?? 0, 2) }}
                                        </td>
                                        <td class="border border-gray-300 px-4 py-3 text-center">
                                            S/{{ number_format($datosHotel['Yape/Plin']['gasto'] ?? 0, 2) }}
                                        </td>
                                        <td class="border border-gray-300 px-4 py-3 text-center bg-gray-100 font-semibold {{ ($datosHotel['Yape/Plin']['total'] ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                            S/{{ number_format($datosHotel['Yape/Plin']['total'] ?? 0, 2) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="border border-gray-300 px-4 py-3 font-medium">Cuenta Bancaria</td>
                                        <td class="border border-gray-300 px-4 py-3 text-center">
                                            S/{{ number_format($datosHotel['Cuenta Bancaria']['ingreso'] ?? 0, 2) }}
                                        </td>
                                        <td class="border border-gray-300 px-4 py-3 text-center">
                                            S/{{ number_format($datosHotel['Cuenta Bancaria']['gasto'] ?? 0, 2) }}
                                        </td>
                                        <td class="border border-gray-300 px-4 py-3 text-center bg-gray-100 font-semibold {{ ($datosHotel['Cuenta Bancaria']['total'] ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                            S/{{ number_format($datosHotel['Cuenta Bancaria']['total'] ?? 0, 2) }}
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr class="bg-blue-50 font-bold">
                                        <td class="border border-gray-300 px-4 py-3">TOTAL HOTEL</td>
                                        <td class="border border-gray-300 px-4 py-3 text-center">
                                            @php
                                                $totalIngresosHotel = collect($datosHotel)->sum('ingreso');
                                            @endphp
                                            S/{{ number_format($totalIngresosHotel, 2) }}
                                        </td>
                                        <td class="border border-gray-300 px-4 py-3 text-center">
                                            @php
                                                $totalGastosHotel = collect($datosHotel)->sum('gasto');
                                            @endphp
                                            S/{{ number_format($totalGastosHotel, 2) }}
                                        </td>
                                        <td class="border border-gray-300 px-4 py-3 text-center bg-gray-200 {{ ($totalIngresosHotel - $totalGastosHotel) >= 0 ? 'text-green-700' : 'text-red-700' }}">
                                            S/{{ number_format($totalIngresosHotel - $totalGastosHotel, 2) }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- BODEGA -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-center mb-6 text-green-600">BODEGA</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full border-collapse border border-gray-300">
                                <thead>
                                    <tr class="bg-gray-50">
                                        <th class="border border-gray-300 px-4 py-3 text-left font-semibold">Método</th>
                                        <th class="border border-gray-300 px-4 py-3 text-center font-semibold">Ingresos</th>
                                        <th class="border border-gray-300 px-4 py-3 text-center font-semibold">Gastos</th>
                                        <th class="border border-gray-300 px-4 py-3 text-center font-semibold bg-gray-200">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="border border-gray-300 px-4 py-3 font-medium">Efectivo</td>
                                        <td class="border border-gray-300 px-4 py-3 text-center">
                                            S/{{ number_format($datosBodega['Efectivo']['ingreso'] ?? 0, 2) }}
                                        </td>
                                        <td class="border border-gray-300 px-4 py-3 text-center">
                                            S/{{ number_format($datosBodega['Efectivo']['gasto'] ?? 0, 2) }}
                                        </td>
                                        <td class="border border-gray-300 px-4 py-3 text-center bg-gray-100 font-semibold {{ ($datosBodega['Efectivo']['total'] ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                            S/{{ number_format($datosBodega['Efectivo']['total'] ?? 0, 2) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="border border-gray-300 px-4 py-3 font-medium">Yape/Plin</td>
                                        <td class="border border-gray-300 px-4 py-3 text-center">
                                            S/{{ number_format($datosBodega['Yape/Plin']['ingreso'] ?? 0, 2) }}
                                        </td>
                                        <td class="border border-gray-300 px-4 py-3 text-center">
                                            S/{{ number_format($datosBodega['Yape/Plin']['gasto'] ?? 0, 2) }}
                                        </td>
                                        <td class="border border-gray-300 px-4 py-3 text-center bg-gray-100 font-semibold {{ ($datosBodega['Yape/Plin']['total'] ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                            S/{{ number_format($datosBodega['Yape/Plin']['total'] ?? 0, 2) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="border border-gray-300 px-4 py-3 font-medium">Cuenta Bancaria</td>
                                        <td class="border border-gray-300 px-4 py-3 text-center">
                                            S/{{ number_format($datosBodega['Cuenta Bancaria']['ingreso'] ?? 0, 2) }}
                                        </td>
                                        <td class="border border-gray-300 px-4 py-3 text-center">
                                            S/{{ number_format($datosBodega['Cuenta Bancaria']['gasto'] ?? 0, 2) }}
                                        </td>
                                        <td class="border border-gray-300 px-4 py-3 text-center bg-gray-100 font-semibold {{ ($datosBodega['Cuenta Bancaria']['total'] ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                            S/{{ number_format($datosBodega['Cuenta Bancaria']['total'] ?? 0, 2) }}
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr class="bg-green-50 font-bold">
                                        <td class="border border-gray-300 px-4 py-3">TOTAL BODEGA</td>
                                        <td class="border border-gray-300 px-4 py-3 text-center">
                                            @php
                                                $totalIngresosBodega = collect($datosBodega)->sum('ingreso');
                                            @endphp
                                            S/{{ number_format($totalIngresosBodega, 2) }}
                                        </td>
                                        <td class="border border-gray-300 px-4 py-3 text-center">
                                            @php
                                                $totalGastosBodega = collect($datosBodega)->sum('gasto');
                                            @endphp
                                            S/{{ number_format($totalGastosBodega, 2) }}
                                        </td>
                                        <td class="border border-gray-300 px-4 py-3 text-center bg-gray-200 {{ ($totalIngresosBodega - $totalGastosBodega) >= 0 ? 'text-green-700' : 'text-red-700' }}">
                                            S/{{ number_format($totalIngresosBodega - $totalGastosBodega, 2) }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Resumen por Métodos de Pago -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mt-8">
                <div class="p-6">
                    <h3 class="text-xl font-bold text-center mb-6 text-gray-800">RESUMEN POR MÉTODOS DE PAGO</h3>
                    @php
                        // Calcular totales combinados por método de pago
                        $efectivoTotal = ($datosHotel['Efectivo']['ingreso'] ?? 0) + ($datosBodega['Efectivo']['ingreso'] ?? 0) - ($datosHotel['Efectivo']['gasto'] ?? 0) - ($datosBodega['Efectivo']['gasto'] ?? 0);
                        $yapePlinTotal = ($datosHotel['Yape/Plin']['ingreso'] ?? 0) + ($datosBodega['Yape/Plin']['ingreso'] ?? 0) - ($datosHotel['Yape/Plin']['gasto'] ?? 0) - ($datosBodega['Yape/Plin']['gasto'] ?? 0);
                        $cuentaBancariaTotal = ($datosHotel['Cuenta Bancaria']['ingreso'] ?? 0) + ($datosBodega['Cuenta Bancaria']['ingreso'] ?? 0) - ($datosHotel['Cuenta Bancaria']['gasto'] ?? 0) - ($datosBodega['Cuenta Bancaria']['gasto'] ?? 0);
                    @endphp
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="text-center p-6 bg-green-50 rounded-lg border shadow-sm">
                            <div class="text-3xl font-bold {{ $efectivoTotal >= 0 ? 'text-green-600' : 'text-red-600' }} mb-2">
                                S/{{ number_format($efectivoTotal, 2) }}
                            </div>
                            <div class="text-sm font-medium text-gray-700">Efectivo</div>
                            <div class="text-xs text-gray-500 mt-1">Hotel + Bodega</div>
                        </div>
                        <div class="text-center p-6 bg-blue-50 rounded-lg border shadow-sm">
                            <div class="text-3xl font-bold {{ $yapePlinTotal >= 0 ? 'text-green-600' : 'text-red-600' }} mb-2">
                                S/{{ number_format($yapePlinTotal, 2) }}
                            </div>
                            <div class="text-sm font-medium text-gray-700">Yape/Plin</div>
                            <div class="text-xs text-gray-500 mt-1">Hotel + Bodega</div>
                        </div>
                        <div class="text-center p-6 bg-purple-50 rounded-lg border shadow-sm">
                            <div class="text-3xl font-bold {{ $cuentaBancariaTotal >= 0 ? 'text-green-600' : 'text-red-600' }} mb-2">
                                S/{{ number_format($cuentaBancariaTotal, 2) }}
                            </div>
                            <div class="text-sm font-medium text-gray-700">Cta. Bancaria</div>
                            <div class="text-xs text-gray-500 mt-1">Hotel + Bodega</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Resumen General -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mt-8">
                <div class="p-6">
                    <h3 class="text-xl font-bold text-center mb-6 text-gray-800">RESUMEN GENERAL</h3>
                    @php
                        $totalIngresosGeneral = $totalIngresosHotel + $totalIngresosBodega;
                        $totalGastosGeneral = $totalGastosHotel + $totalGastosBodega;
                        $totalGeneral = $totalIngresosGeneral - $totalGastosGeneral;
                    @endphp
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="text-center p-4 bg-blue-50 rounded-lg border">
                            <div class="text-2xl font-bold text-blue-600">S/{{ number_format($totalIngresosGeneral, 2) }}</div>
                            <div class="text-sm text-gray-600">Total Ingresos</div>
                        </div>
                        <div class="text-center p-4 bg-red-50 rounded-lg border">
                            <div class="text-2xl font-bold text-red-600">S/{{ number_format($totalGastosGeneral, 2) }}</div>
                            <div class="text-sm text-gray-600">Total Gastos</div>
                        </div>
                        <div class="text-center p-4 {{ $totalGeneral >= 0 ? 'bg-green-50' : 'bg-red-50' }} rounded-lg border">
                            <div class="text-2xl font-bold {{ $totalGeneral >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                S/{{ number_format($totalGeneral, 2) }}
                            </div>
                            <div class="text-sm text-gray-600">Balance Total</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const personalizadoBtn = document.getElementById('personalizadoBtn');
            const fechasPersonalizadas = document.getElementById('fechasPersonalizadas');
            
            personalizadoBtn.addEventListener('click', function() {
                fechasPersonalizadas.classList.toggle('hidden');
                
                // Actualizar el estado visual del botón
                const isActive = !fechasPersonalizadas.classList.contains('hidden');
                if (isActive) {
                    personalizadoBtn.classList.remove('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
                    personalizadoBtn.classList.add('bg-blue-600', 'text-white');
                } else {
                    personalizadoBtn.classList.remove('bg-blue-600', 'text-white');
                    personalizadoBtn.classList.add('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
                }
            });
        });
    </script>

@endsection