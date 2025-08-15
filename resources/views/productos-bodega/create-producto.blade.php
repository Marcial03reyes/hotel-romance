@extends('layouts.app')

@section('title', 'Crear Nuevo Producto')

@section('content')
<div class="py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumb -->
        <nav class="flex mb-6" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                <li class="inline-flex items-center">
                    <a href="{{ route('productos-bodega.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                        <i class='bx bx-package mr-2'></i>
                        Productos Bodega
                    </a>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class='bx bx-chevron-right text-gray-400'></i>
                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Nuevo Producto</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Crear Nuevo Producto</h1>
                    <p class="text-gray-600 mt-1">Agrega un nuevo producto a la bodega para comenzar a gestionar su inventario</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('productos-bodega.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        <i class='bx bx-arrow-back mr-2'></i>
                        Volver a Productos
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

            <form action="{{ route('productos-bodega.store-producto') }}" method="POST" class="space-y-6">
                @csrf

                <div class="max-w-md">
                    <!-- Nombre del Producto -->
                    <div>
                        <label for="nombre" class="block text-sm font-medium text-gray-700 mb-2">
                            Nombre del Producto <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="text" 
                                   id="nombre" 
                                   name="nombre" 
                                   value="{{ old('nombre') }}"
                                   maxlength="50"
                                   class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm @error('nombre') border-red-300 @enderror"
                                   placeholder="Ej: Pilsen Lata, Agua San Luis, etc."
                                   required
                                   autocomplete="off">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class='bx bx-package text-gray-400'></i>
                            </div>
                        </div>
                        @error('nombre')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">
                            Máximo 50 caracteres. El nombre debe ser único.
                        </p>
                    </div>
                </div>

                <!-- Información adicional -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class='bx bx-info-circle text-blue-400 text-xl'></i>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-sm font-medium text-blue-800 mb-2">Sobre los productos de bodega</h4>
                            <div class="text-sm text-blue-700 space-y-1">
                                <p>• Los productos de bodega son aquellos que se venden directamente a los clientes del hotel</p>
                                <p>• Una vez creado el producto, podrás registrar compras para mantener el inventario</p>
                                <p>• El sistema calculará automáticamente el stock basado en compras y ventas</p>
                                <p>• Puedes editar el nombre del producto después de crearlo</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botones -->
                <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('productos-bodega.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        Cancelar
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-6 py-2 bg-blue-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        <i class='bx bx-plus mr-2'></i>
                        Crear Producto
                    </button>
                </div>
            </form>
        </div>

        <!-- Productos similares (opcional) -->
        <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-md p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class='bx bx-lightbulb text-yellow-400 text-xl'></i>
                </div>
                <div class="ml-3">
                    <h4 class="text-sm font-medium text-yellow-800 mb-2">Ejemplos de productos de bodega</h4>
                    <div class="text-sm text-yellow-700 grid grid-cols-2 md:grid-cols-3 gap-2">
                        <span class="bg-yellow-100 px-2 py-1 rounded text-xs">Pilsen Lata</span>
                        <span class="bg-yellow-100 px-2 py-1 rounded text-xs">Cristal Botella</span>
                        <span class="bg-yellow-100 px-2 py-1 rounded text-xs">Agua San Luis</span>
                        <span class="bg-yellow-100 px-2 py-1 rounded text-xs">Coca Cola</span>
                        <span class="bg-yellow-100 px-2 py-1 rounded text-xs">Papas Lays</span>
                        <span class="bg-yellow-100 px-2 py-1 rounded text-xs">Chocolates</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const nombreInput = document.getElementById('nombre');
    
    // Auto-focus en el campo nombre
    nombreInput.focus();
    
    // Convertir primera letra a mayúscula
    nombreInput.addEventListener('input', function() {
        let value = this.value;
        if (value.length > 0) {
            this.value = value.charAt(0).toUpperCase() + value.slice(1);
        }
    });
});
</script>
@endpush
@endsection