{{-- resources/views/gastos-fijos/historial.blade.php --}}
@extends('layouts.app')

@section('title', 'Historial de Pagos - ' . $gastoFijo->nombre_servicio)

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">
                    <i class='bx bx-history mr-2' style="color: #6B8CC7;"></i>
                    Historial de Pagos
                </h1>
                <p class="text-gray-600">{{ $gastoFijo->nombre_servicio }}</p>
            </div>
            <a href="{{ route('gastos-fijos.create-pago', $gastoFijo->id_gasto_fijo) }}" 
               class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg font-medium shadow-lg transition-all inline-flex items-center">
                <i class='bx bx-plus mr-2'></i>
                Registrar Pago
            </a>
        </div>
        
        <div class="flex items-center space-x-4 text-sm text-gray-600">
            <a href="{{ route('gastos-fijos.index') }}" class="inline-flex items-center hover:text-blue-600 transition-colors">
                <i class='bx bx-arrow-back mr-1'></i>
                Volver a gastos fijos
            </a>
            <span>•</span>
            <a href="{{ route('gastos-fijos.edit', $gastoFijo->id_gasto_fijo) }}" class="inline-flex items-center hover:text-blue-600 transition-colors">
                <i class='bx bx-edit mr-1'></i>
                Editar servicio
            </a>
        </div>
    </div>

    <!-- Mensajes -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
            <i class='bx bx-check-circle mr-2'></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
            <i class='bx bx-error-circle mr-2'></i>
            {{ session('error') }}
        </div>
    @endif

    <!-- Información del servicio -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white p-4 rounded-lg border shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Día de Vencimiento</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $gastoFijo->dia_vencimiento }}</p>
                </div>
                <i class='bx bx-calendar text-3xl' style="color: #6B8CC7;"></i>
            </div>
        </div>

        <div class="bg-white p-4 rounded-lg border shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Monto Fijo</p>
                    <p class="text-2xl font-bold text-gray-800">S/ {{ number_format($gastoFijo->monto_fijo, 2) }}</p>
                </div>
                <i class='bx bx-money text-3xl' style="color: #6B8CC7;"></i>
            </div>
        </div>

        <div class="bg-white p-4 rounded-lg border shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Cantidad de Pagos</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $gastoFijo->getCantidadPagos() }}</p>
                </div>
                <i class='bx bx-receipt text-3xl' style="color: #6B8CC7;"></i>
            </div>
        </div>
    </div>

    <!-- Tabla de pagos -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha de Pago</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Monto Pagado</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Método de Pago</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Comprobante</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>

                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($pagos as $pago)
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                        <!-- Fecha de pago -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <i class='bx bx-calendar mr-2 text-gray-400'></i>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y') }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $pago->nombre_mes }} {{ $pago->anio }}
                                    </div>
                                </div>
                            </div>
                        </td>

                        <!-- Monto pagado -->
                        <td class="px-4 py-4 whitespace-nowrap text-center">
                            <span class="text-sm font-semibold text-gray-900">
                                S/ {{ number_format($pago->monto_pagado, 2) }}
                            </span>
                        </td>

                        <!-- Método de pago -->
                        <td class="px-4 py-4 whitespace-nowrap text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $pago->metodoPago->met_pago }}
                            </span>
                        </td>

                        <!-- Comprobante -->
                        <td class="px-4 py-4 whitespace-nowrap text-center">
                            @if($pago->tiene_comprobante)
                                <a href="{{ route('gastos-fijos.ver-comprobante', [$gastoFijo->id_gasto_fijo, $pago->id_pago_gasto]) }}" 
                                   target="_blank"
                                   class="inline-flex items-center text-green-600 hover:text-green-800 transition-colors">
                                    <i class='bx bx-check-circle mr-1'></i>
                                    Ver
                                </a>
                            @else
                                <span class="text-gray-400 text-xs">Sin comprobante</span>
                            @endif
                        </td>

                        <!-- Acciones -->
                        <td class="px-4 py-4 whitespace-nowrap text-center">
                            <div class="flex gap-2 justify-center">
                                <a href="{{ route('gastos-fijos.edit-pago', [$gastoFijo->id_gasto_fijo, $pago->id_pago_gasto]) }}" 
                                   class="text-blue-600 hover:text-blue-900 p-1 rounded hover:bg-blue-50 transition-colors" 
                                   title="Editar pago">
                                    <i class='bx bx-edit text-lg'></i>
                                </a>
                                <button onclick="eliminarPago({{ $gastoFijo->id_gasto_fijo }}, {{ $pago->id_pago_gasto }}, '{{ \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y') }}')"
                                        class="text-red-600 hover:text-red-900 p-1 rounded hover:bg-red-50 transition-colors" 
                                        title="Eliminar pago">
                                    <i class='bx bx-trash text-lg'></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="text-gray-500">
                                <i class='bx bx-receipt text-4xl mb-2'></i>
                                <p class="text-lg">No hay pagos registrados</p>
                                <p class="text-sm mb-4">Comienza registrando el primer pago de este servicio</p>
                                <a href="{{ route('gastos-fijos.create-pago', $gastoFijo->id_gasto_fijo) }}" 
                                   class="inline-flex items-center bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                                    <i class='bx bx-plus mr-2'></i>
                                    Registrar Primer Pago
                                </a>
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
function eliminarPago(idGastoFijo, idPago, fecha) {
    if (confirm(`¿Eliminar el pago del ${fecha}?\n\nEsta acción no se puede deshacer.`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `{{ route('gastos-fijos.destroy-pago', [':idGasto', ':idPago']) }}`
            .replace(':idGasto', idGastoFijo)
            .replace(':idPago', idPago);
        
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        form.appendChild(csrfInput);
        
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection