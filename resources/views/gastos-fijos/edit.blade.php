{{-- resources/views/gastos-fijos/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Editar Servicio - Gastos Fijos')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">
                    <i class='bx bx-edit mr-2' style="color: #6B8CC7;"></i>
                    Editar Servicio
                </h1>
                <p class="text-gray-600">Modifica la información del servicio de gasto fijo</p>
            </div>
            <div class="flex items-center space-x-2">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    <i class='bx bx-receipt mr-1'></i>
                    {{ $gastoFijo->nombre_servicio }}
                </span>
            </div>
        </div>
        
        <div class="flex items-center space-x-4 text-sm text-gray-600">
            <a href="{{ route('gastos-fijos.index') }}" class="inline-flex items-center hover:text-blue-600 transition-colors">
                <i class='bx bx-arrow-back mr-1'></i>
                Volver a gastos fijos
            </a>
            <span>•</span>
            <a href="{{ route('gastos-fijos.historial', $gastoFijo->id_gasto_fijo) }}" class="inline-flex items-center hover:text-blue-600 transition-colors">
                <i class='bx bx-history mr-1'></i>
                Ver historial de pagos
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="rounded-lg border border-red-300 bg-red-50 p-4 text-red-800 mb-6 shadow-sm">
            <div class="flex items-center mb-2">
                <i class='bx bx-error-circle mr-2 text-lg'></i>
                <span class="font-medium">Se encontraron errores:</span>
            </div>
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li class="text-sm">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Formulario --}}
    <div class="bg-white rounded-lg shadow-sm border p-6">
        <form action="{{ route('gastos-fijos.update', $gastoFijo->id_gasto_fijo) }}" method="POST">
            @csrf
            @method('PUT')

            <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class='bx bx-receipt mr-2' style="color: #6B8CC7;"></i>
                Información del Servicio
            </h2>
            
            <div class="space-y-6">
                {{-- Nombre del servicio --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        <i class='bx bx-edit mr-1'></i>
                        Nombre del Servicio *
                    </label>
                    <input type="text" 
                           name="nombre_servicio" 
                           value="{{ old('nombre_servicio', $gastoFijo->nombre_servicio) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" 
                           placeholder="Ej: Internet, Agua, Luz, Cable"
                           required 
                           maxlength="100">
                    <p class="text-xs text-gray-500 mt-1">Solo letras, espacios y guiones</p>
                    @error('nombre_servicio')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Día de vencimiento --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        <i class='bx bx-calendar mr-1'></i>
                        Día de Vencimiento *
                    </label>
                    <input type="number" 
                           name="dia_vencimiento" 
                           value="{{ old('dia_vencimiento', $gastoFijo->dia_vencimiento) }}"
                           min="1" 
                           max="31"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" 
                           placeholder="Día del mes (1-31)"
                           required>
                    <p class="text-xs text-gray-500 mt-1">
                        <i class='bx bx-info-circle mr-1'></i>
                        Día del mes en que vence el servicio
                    </p>
                    @error('dia_vencimiento')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Monto fijo --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        <i class='bx bx-dollar mr-1'></i>
                        Monto Fijo *
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 font-medium">S/</span>
                        <input type="number" 
                               name="monto_fijo" 
                               value="{{ old('monto_fijo', $gastoFijo->monto_fijo) }}"
                               step="0.01"
                               min="0"
                               class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" 
                               placeholder="0.00"
                               required>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Monto típico del servicio (puede variar al momento del pago)</p>
                    @error('monto_fijo')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Botones --}}
            <div class="flex justify-between items-center mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('gastos-fijos.index') }}" 
                   class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-lg font-medium transition-all inline-flex items-center">
                    <i class='bx bx-x mr-2'></i>
                    Cancelar
                </a>
                
                <button type="submit" 
                        class="bg-blue-500 hover:bg-blue-600 text-white px-8 py-3 rounded-lg font-medium shadow-lg transition-all inline-flex items-center">
                    <i class='bx bx-save mr-2'></i>
                    Actualizar Servicio
                </button>
            </div>
        </form>
    </div>

    {{-- Información adicional --}}
    <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
        <div class="flex items-start">
            <i class='bx bx-info-circle text-blue-600 text-lg mr-2 mt-1'></i>
            <div class="text-sm text-blue-800">
                <p class="font-medium mb-1">Información importante:</p>
                <ul class="list-disc list-inside space-y-1">
                    <li>Los cambios afectarán solo a la información del servicio</li>
                    <li>Los pagos ya registrados no se verán afectados</li>
                    <li>El monto fijo es solo una referencia, puede variar en cada pago</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection