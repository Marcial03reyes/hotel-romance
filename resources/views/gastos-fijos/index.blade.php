@extends('layouts.app')

@section('title', 'Gastos Fijos - Hotel Romance')

@section('content')

@section('content')
<style>
    :root {
        --primary-color: #88A6D3;
        --secondary-color: #6B8CC7;
        --accent-color: #4A73B8;
        --tertiary-color: #A5BFDB;
        --sidebar-bg: #F8FAFC;
        --gradient-start: #88A6D3;
        --gradient-end: #6B8CC7;
    }

    .table-container {
        background: white;
        border-radius: 1rem;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(136, 166, 211, 0.1);
        border: 1px solid rgba(136, 166, 211, 0.2);
    }

    .btn-romance {
        background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
        transition: all 0.3s ease;
        color: white;
        font-weight: 600;
        box-shadow: 0 2px 8px rgba(136, 166, 211, 0.3);
    }

    .btn-romance:hover {
        background: linear-gradient(135deg, var(--secondary-color), var(--accent-color));
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(136, 166, 211, 0.4);
    }

    .table-row {
        transition: all 0.3s ease;
        border-left: 4px solid transparent;
    }

    .table-row:hover {
        background-color: rgba(136, 166, 211, 0.05);
        border-left-color: var(--primary-color);
    }
</style>

<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Gastos Fijos</h1>
            <p class="text-gray-600">Gestión de servicios y pagos mensuales</p>
        </div>

        <a href="{{ route('gastos-fijos.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded flex items-center transition-colors">
            <i class='bx bx-plus mr-2'></i>
            Agregar Servicio
        </a>
    </div>

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

    {{-- Tabla principal --}}
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                    <thead class="sticky top-0 z-10" style="background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-medium text-white uppercase tracking-wider">Servicio</th>
                            <th class="px-4 py-4 text-center text-xs font-medium text-white uppercase tracking-wider">Día Vencimiento</th>
                            <th class="px-4 py-4 text-center text-xs font-medium text-white uppercase tracking-wider">Monto Fijo</th>
                            <th class="px-4 py-4 text-center text-xs font-medium text-white uppercase tracking-wider">Gestión</th>
                            <th class="px-4 py-4 text-center text-xs font-medium text-white uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>

                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($gastosFijos as $servicio)
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                        {{-- Nombre del servicio --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $servicio->nombre_servicio }}
                            </div>
                        </td>

                        {{-- Día de vencimiento --}}
                        <td class="px-4 py-4 whitespace-nowrap text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                Día {{ $servicio->dia_vencimiento }}
                            </span>
                        </td>

                        {{-- Monto fijo --}}
                        <td class="px-4 py-4 whitespace-nowrap text-center">
                            <div class="text-sm font-semibold text-gray-900">
                                S/ {{ number_format($servicio->monto_fijo, 2) }}
                            </div>
                        </td>

                        {{-- Gestión --}}
                        <td class="px-4 py-4 whitespace-nowrap text-center">
                            <div class="flex gap-2 justify-center">
                                <a href="{{ route('gastos-fijos.historial', $servicio->id_gasto_fijo) }}" 
                                class="inline-flex items-center bg-blue-100 text-blue-700 px-3 py-1 text-xs rounded-full hover:bg-blue-200 transition-colors">
                                    <i class='bx bx-history mr-1'></i>
                                    Historial
                                </a>
                                <a href="{{ route('gastos-fijos.create-pago', $servicio->id_gasto_fijo) }}" 
                                class="inline-flex items-center bg-green-100 text-green-700 px-3 py-1 text-xs rounded-full hover:bg-green-200 transition-colors">
                                    <i class='bx bx-plus mr-1'></i>
                                    Pagar
                                </a>
                            </div>
                        </td>

                        {{-- Acciones --}}
                        <td class="px-4 py-4 whitespace-nowrap text-center">
                            <div class="flex gap-2 justify-center">
                                <a href="{{ route('gastos-fijos.edit', $servicio->id_gasto_fijo) }}" 
                                class="text-blue-600 hover:text-blue-900 p-1 rounded hover:bg-blue-50 transition-colors" 
                                title="Editar servicio">
                                    <i class='bx bx-edit text-lg'></i>
                                </a>
                                <button onclick="eliminarServicio({{ $servicio->id_gasto_fijo }}, '{{ $servicio->nombre_servicio }}')"
                                        class="text-red-600 hover:text-red-900 p-1 rounded hover:bg-red-50 transition-colors" 
                                        title="Eliminar servicio">
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
                                <p class="text-lg">No hay servicios registrados</p>
                                <p class="text-sm">Agrega tu primer servicio para comenzar</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- Scripts JavaScript --}}
<script>

// Eliminar servicio
function eliminarServicio(idServicio, nombreServicio) {
    if (confirm(`¿Eliminar el servicio "${nombreServicio}"?\n\nEsta acción eliminará todos los pagos asociados.`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `{{ route('gastos-fijos.destroy', ':id') }}`.replace(':id', idServicio);
        
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

@endsection