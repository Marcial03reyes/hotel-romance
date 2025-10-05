{{-- resources/views/gastos-fijos/create-pago.blade.php --}}
@extends('layouts.app')

@section('title', 'Registrar Pago - ' . $gastoFijo->nombre_servicio)

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">
                    <i class='bx bx-plus-circle mr-2' style="color: #6B8CC7;"></i>
                    Registrar Pago
                </h1>
                <p class="text-gray-600">{{ $gastoFijo->nombre_servicio }}</p>
            </div>
            <div class="flex items-center space-x-2">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    <i class='bx bx-calendar mr-1'></i>
                    Vence día {{ $gastoFijo->dia_vencimiento }}
                </span>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    <i class='bx bx-dollar mr-1'></i>
                    S/ {{ number_format($gastoFijo->monto_fijo, 2) }}
                </span>
            </div>
        </div>
        
        <div class="flex items-center space-x-4 text-sm text-gray-600">
            <a href="{{ route('gastos-fijos.historial', $gastoFijo->id_gasto_fijo) }}" class="inline-flex items-center hover:text-blue-600 transition-colors">
                <i class='bx bx-arrow-back mr-1'></i>
                Volver al historial
            </a>
            <span>•</span>
            <span class="text-gray-500">Completa todos los campos requeridos</span>
        </div>
    </div>

    <!-- Mensajes de error -->
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

    <!-- Formulario -->
    <form action="{{ route('gastos-fijos.store-pago', $gastoFijo->id_gasto_fijo) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class='bx bx-receipt mr-2' style="color: #6B8CC7;"></i>
                Información del Pago
            </h2>
            
            <div class="space-y-4">
                <!-- Monto pagado -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        <i class='bx bx-dollar mr-1'></i>
                        Monto Pagado *
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 font-medium">S/</span>
                        <input type="number" 
                               name="monto_pagado" 
                               value="{{ old('monto_pagado', $gastoFijo->monto_fijo) }}"
                               step="0.01"
                               min="0.01"
                               class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" 
                               placeholder="0.00"
                               required>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Monto que se pagó por este servicio</p>
                    @error('monto_pagado')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Método de pago -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        <i class='bx bx-wallet mr-1'></i>
                        Método de Pago *
                    </label>
                    <select name="id_met_pago" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" 
                            required>
                        <option value="">Seleccionar método</option>
                        @foreach($metodos as $metodo)
                            <option value="{{ $metodo->id_met_pago }}" {{ old('id_met_pago') == $metodo->id_met_pago ? 'selected' : '' }}>
                                {{ $metodo->met_pago }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_met_pago')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Fecha de pago -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        <i class='bx bx-calendar mr-1'></i>
                        Fecha de Pago *
                    </label>
                    <input type="date" 
                           name="fecha_pago" 
                           value="{{ old('fecha_pago', date('Y-m-d')) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" 
                           required>
                    <p class="text-xs text-gray-500 mt-1">
                        <i class='bx bx-info-circle mr-1'></i>
                        Fecha en la que se realizó el pago
                    </p>
                    @error('fecha_pago')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Comprobante -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        <i class='bx bx-file mr-1'></i>
                        Comprobante (Opcional)
                    </label>
                    <input type="file" 
                           name="comprobante" 
                           accept=".pdf,.png,.jpg,.jpeg"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                           id="comprobante">
                    <p class="text-xs text-gray-500 mt-1">
                        Formatos: PDF, PNG, JPG (máx. 10MB)
                    </p>
                    @error('comprobante')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                    <div id="file-preview" class="mt-2"></div>
                </div>
            </div>
        </div>

        <!-- Botones -->
        <div class="flex justify-between items-center pt-6 border-t border-gray-200">
            <a href="{{ route('gastos-fijos.historial', $gastoFijo->id_gasto_fijo) }}" 
               class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-lg font-medium transition-all inline-flex items-center">
                <i class='bx bx-x mr-2'></i>
                Cancelar
            </a>
            
            <button type="submit" 
                    class="bg-green-500 hover:bg-green-600 text-white px-8 py-3 rounded-lg font-medium shadow-lg transition-all inline-flex items-center">
                <i class='bx bx-save mr-2'></i>
                Registrar Pago
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('comprobante');
    
    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            const previewContainer = document.getElementById('file-preview');
            
            if (file) {
                // Validar tamaño
                const maxSize = 10 * 1024 * 1024; // 10MB
                if (file.size > maxSize) {
                    alert('El archivo es demasiado grande. Máximo 10MB.');
                    fileInput.value = '';
                    previewContainer.innerHTML = '';
                    return;
                }
                
                // Validar tipo
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Tipo de archivo no permitido. Solo PDF, JPG o PNG.');
                    fileInput.value = '';
                    previewContainer.innerHTML = '';
                    return;
                }
                
                // Mostrar preview
                const isPdf = file.type === 'application/pdf';
                previewContainer.innerHTML = `
                    <div class="flex items-center p-3 bg-gray-50 rounded border">
                        <i class="bx ${isPdf ? 'bx-file-pdf text-red-500' : 'bx-image text-blue-500'} text-2xl mr-3"></i>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">${file.name}</p>
                            <p class="text-xs text-gray-500">${(file.size / 1024 / 1024).toFixed(2)} MB</p>
                        </div>
                        <button type="button" onclick="document.getElementById('comprobante').value=''; document.getElementById('file-preview').innerHTML='';" 
                                class="ml-2 text-red-600 hover:text-red-800">
                            <i class="bx bx-x text-xl"></i>
                        </button>
                    </div>
                `;
            }
        });
    }
});
</script>
@endsection