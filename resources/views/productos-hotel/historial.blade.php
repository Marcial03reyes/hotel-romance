@extends('layouts.app')

@section('title', 'Historial - ' . $producto->nombre)

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumb -->
        <nav class="flex mb-6" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                <li class="inline-flex items-center">
                    <a href="{{ route('productos-hotel.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                        <i class='bx bx-cleaning mr-2'></i>
                        Productos Hotel
                    </a>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class='bx bx-chevron-right text-gray-400'></i>
                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">{{ $producto->nombre }}</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Header con estadísticas -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $producto->nombre }}</h1>
                    <p class="text-gray-600 mt-1">Historial de compras y análisis de frecuencia</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('productos-hotel.create-compra', $producto->id_prod_hotel) }}" 
                       class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-green-700 transition-colors">
                        <i class='bx bx-plus mr-2'></i>
                        Registrar Compra
                    </a>
                    <a href="{{ route('productos-hotel.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        <i class='bx bx-arrow-back mr-2'></i>
                        Volver
                    </a>
                </div>
            </div>

            <!-- Estadísticas en tarjetas -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-blue-600">Total Comprado</p>
                            <p class="text-2xl font-bold text-blue-900">{{ $totalComprado }}</p>
                        </div>
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class='bx bx-package text-blue-600 text-xl'></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-green-600">Inversión Total</p>
                            <p class="text-2xl font-bold text-green-900">S/ {{ number_format($inversionTotal, 2) }}</p>
                        </div>
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class='bx bx-dollar text-green-600 text-xl'></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-purple-600">Precio Promedio</p>
                            <p class="text-2xl font-bold text-purple-900">S/ {{ number_format($precioPromedio, 2) }}</p>
                        </div>
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                            <i class='bx bx-calculator text-purple-600 text-xl'></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-orange-600">Frecuencia de Compra</p>
                            <p class="text-2xl font-bold text-orange-900">
                                @if($frecuenciaCompra)
                                    {{ $frecuenciaCompra }} días
                                @else
                                    N/A
                                @endif
                            </p>
                        </div>
                        <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                            <i class='bx bx-time text-orange-600 text-xl'></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información de frecuencia -->
            @if($frecuenciaCompra && $diasDesdeUltima !== null)
                <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class='bx bx-info-circle text-gray-500 mr-2'></i>
                            <span class="text-sm text-gray-700">
                                Última compra hace <strong>{{ $diasDesdeUltima }}</strong> días.
                                Frecuencia promedio: <strong>{{ $frecuenciaCompra }}</strong> días.
                            </span>
                        </div>
                        <div class="text-sm">
                            @php
                                $porcentaje = $frecuenciaCompra > 0 ? ($diasDesdeUltima / $frecuenciaCompra) * 100 : 0;
                            @endphp
                            @if($porcentaje < 50)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class='bx bx-check-circle mr-1'></i>
                                    Reciente
                                </span>
                            @elseif($porcentaje < 80)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <i class='bx bx-time mr-1'></i>
                                    Normal
                                </span>
                            @elseif($porcentaje < 100)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <i class='bx bx-error mr-1'></i>
                                    Próximo
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <i class='bx bx-error-circle mr-1'></i>
                                    Recomprar
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Alertas -->
        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class='bx bx-check-circle text-green-400 text-xl'></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class='bx bx-error text-red-400 text-xl'></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800">{{ $errors->first() }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Tabla de historial de compras -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Historial de Compras</h3>
                <p class="text-sm text-gray-500">Registro cronológico de todas las compras realizadas</p>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Fecha
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Cantidad
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Precio Unitario
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Total
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Proveedor
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Días desde anterior
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($historialCompras as $index => $compra)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $compra->fecha_compra ? $compra->fecha_compra->format('d/m/Y') : 'Sin fecha' }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $compra->created_at ? $compra->created_at->format('H:i') : '' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $compra->cantidad }} unidades
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">S/ {{ number_format($compra->precio_unitario, 2) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        S/ {{ number_format($compra->cantidad * $compra->precio_unitario, 2) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $compra->proveedor ?: 'No especificado' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        @if($index < count($historialCompras) - 1)
                                            @php
                                                $compraAnterior = $historialCompras[$index + 1];
                                                $diasDiferencia = $compra->fecha_compra && $compraAnterior->fecha_compra 
                                                    ? $compraAnterior->fecha_compra->diffInDays($compra->fecha_compra) 
                                                    : null;
                                            @endphp
                                            @if($diasDiferencia !== null)
                                                <span class="text-gray-600">{{ $diasDiferencia }} días</span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        @else
                                            <span class="text-gray-400 text-xs">Primera compra</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        <a href="{{ route('productos-hotel.edit-compra', [$producto->id_prod_hotel, $compra->id_compra_interna]) }}" 
                                           class="text-yellow-600 hover:text-yellow-900 transition-colors"
                                           title="Editar compra">
                                            <i class='bx bx-edit text-lg'></i>
                                        </a>
                                        <form method="POST" action="{{ route('productos-hotel.destroy-compra', [$producto->id_prod_hotel, $compra->id_compra_interna]) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="text-red-600 hover:text-red-900 transition-colors"
                                                    title="Eliminar compra"
                                                    onclick="return confirm('¿Estás seguro de eliminar esta compra? Esta acción no se puede deshacer.')">
                                                <i class='bx bx-trash text-lg'></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="text-gray-500">
                                        <i class='bx bx-receipt text-4xl mb-4'></i>
                                        <p class="text-lg font-medium">No hay compras registradas</p>
                                        <p class="text-sm">Comienza registrando la primera compra de este producto</p>
                                        <a href="{{ route('productos-hotel.create-compra', $producto->id_prod_hotel) }}" 
                                           class="inline-flex items-center mt-4 px-4 py-2 bg-green-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-green-700 transition-colors">
                                            <i class='bx bx-plus mr-2'></i>
                                            Registrar Primera Compra
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Información sobre frecuencias -->
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-md p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class='bx bx-info-circle text-blue-400 text-xl'></i>
                </div>
                <div class="ml-3">
                    <h4 class="text-sm font-medium text-blue-800 mb-2">Cómo interpretar la frecuencia de compra</h4>
                    <div class="text-sm text-blue-700 space-y-1">
                        <p>• <strong>Frecuencia de compra:</strong> Promedio de días entre compras basado en el historial</p>
                        <p>• <strong>Días desde anterior:</strong> Tiempo transcurrido entre cada compra consecutiva</p>
                        <p>• <strong>Estado:</strong> Indicador visual basado en la frecuencia promedio y días desde última compra</p>
                        <p>• Se requieren al menos 2 compras para calcular frecuencias precisas</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection