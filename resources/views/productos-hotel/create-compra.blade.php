 @extends('layouts.app')

@section('title', 'Registrar Compra - ' . $producto->nombre)

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
                <li>
                    <div class="flex items-center">
                        <i class='bx bx-chevron-right text-gray-400'></i>
                        <a href="{{ route('productos-hotel.historial', $producto->id_prod_hotel) }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2">
                            {{ $producto->nombre }}
                        </a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class='bx bx-chevron-right text-gray-400'></i>
                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Registrar Compra</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Registrar Nueva Compra</h1>
                    <p class="text-gray-600 mt-1">Producto: <span class="font-semibold text-blue-600">{{ $producto->nombre }}</span></p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('productos-hotel.historial', $producto->id_prod_hotel) }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        <i class='bx bx-arrow-back mr-2'></i>
                        Volver al Historial
                    </a>
                </div>
            </div>
        </div>

        <!-- Formulario -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            @if ($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class='bx bx-error text-red-400 text-xl'></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">
                                Se encontraron errores en el formulario:
                            </h3>
                            <div class="mt-2 text-sm text-red-700">
                                <ul class="list-disc list-inside space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <form action="{{ route('productos-hotel.store-compra', $producto->id_prod_hotel) }}" method="POST" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Cantidad -->
                    <div>
                        <label for="cantidad" class="block text-sm font-medium text-gray-700 mb-2">
                            Cantidad <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="number" 
                                   id="cantidad" 
                                   name="cantidad" 
                                   value="{{ old('cantidad') }}"
                                   min="1" 
                                   max="9999"
                                   class="block w-full pl-3 pr-12 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm @error('cantidad') border-red-300 @enderror"
                                   placeholder="Ej: 12"
                                   required>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">unidades</span>
                            </div>
                        </div>
                        @error('cantidad')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Precio Unitario -->
                    <div>
                        <label for="precio_unitario" class="block text-sm font-medium text-gray-700 mb-2">
                            Precio Unitario <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">S/</span>
                            </div>
                            <input type="number" 
                                   id="precio_unitario" 
                                   name="precio_unitario" 
                                   value="{{ old('precio_unitario') }}"
                                   step="0.01" 
                                   min="0.01" 
                                   max="99999.99"
                                   class="block w-full pl-8 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm @error('precio_unitario') border-red-300 @enderror"
                                   placeholder="Ej: 15.50"
                                   required>
                        </div>
                        @error('precio_unitario')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Fecha de Compra -->
                    <div>
                        <label for="fecha_compra" class="block text-sm font-medium text-gray-700 mb-2">
                            Fecha de Compra <span class="text-red-500">*</span>
                        </label>
                        <input type="date" 
                               id="fecha_compra" 
                               name="fecha_compra" 
                               value="{{ old('fecha_compra', $fechaActual) }}"
                               max="{{ date('Y-m-d') }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm @error('fecha_compra') border-red-300 @enderror"
                               required>
                        @error('fecha_compra')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Proveedor -->
                    <div>
                        <label for="proveedor" class="block text-sm font-medium text-gray-700 mb-2">
                            Proveedor <span class="text-gray-400">(Opcional)</span>
                        </label>
                        <input type="text" 
                               id="proveedor" 
                               name="proveedor" 
                               value="{{ old('proveedor') }}"
                               maxlength="255"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm @error('proveedor') border-red-300 @enderror"
                               placeholder="Ej: Comercial Limpieza S.A.C.">
                        @error('proveedor')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Total Calculado -->
                <div class="bg-gray-50 rounded-lg p-4 border">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700">Total de la compra:</span>
                        <span id="total-compra" class="text-lg font-bold text-blue-600">S/ 0.00</span>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">El total se calcula automáticamente: cantidad × precio unitario</p>
                </div>

                <!-- Información del producto -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class='bx bx-info-circle text-blue-400 text-xl'></i>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-sm font-medium text-blue-800 mb-2">Registro de compra para productos del hotel</h4>
                            <div class="text-sm text-blue-700 space-y-1">
                                <p>• Esta compra se registrará en el historial del producto <strong>{{ $producto->nombre }}</strong></p>
                                <p>• El sistema calculará automáticamente la frecuencia de compra basándose en el historial</p>
                                <p>• Esto te ayudará a planificar futuras compras de productos de limpieza y mantenimiento</p>
                                <p>• Asegúrate de registrar todas las compras para obtener cálculos precisos</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botones -->
                <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('productos-hotel.historial', $producto->id_prod_hotel) }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        Cancelar
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-6 py-2 bg-blue-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        <i class='bx bx-save mr-2'></i>
                        Registrar Compra
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const cantidadInput = document.getElementById('cantidad');
    const precioInput = document.getElementById('precio_unitario');
    const totalElement = document.getElementById('total-compra');

    function calcularTotal() {
        const cantidad = parseFloat(cantidadInput.value) || 0;
        const precio = parseFloat(precioInput.value) || 0;
        const total = cantidad * precio;
        
        totalElement.textContent = 'S/ ' + total.toFixed(2);
    }

    cantidadInput.addEventListener('input', calcularTotal);
    precioInput.addEventListener('input', calcularTotal);
    
    // Calcular al cargar la página si hay valores
    calcularTotal();

    // Auto-focus en el campo cantidad
    cantidadInput.focus();
});
</script>
@endpush
@endsection