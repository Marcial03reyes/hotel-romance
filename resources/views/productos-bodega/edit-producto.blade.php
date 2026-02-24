@extends('layouts.app')

@section('title', 'Editar Producto - ' . $producto->nombre)

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
                <li>
                    <div class="flex items-center">
                        <i class='bx bx-chevron-right text-gray-400'></i>
                        <a href="{{ route('productos-bodega.historial', $producto->id_prod_bod) }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2">
                            {{ $producto->nombre }}
                        </a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class='bx bx-chevron-right text-gray-400'></i>
                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Editar</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Editar Producto</h1>
                    <p class="text-gray-600 mt-1">
                        Modifica la información de: <span class="font-semibold text-blue-600">{{ $producto->nombre }}</span>
                    </p>
                    <p class="text-gray-500 text-sm mt-1">ID: {{ $producto->id_prod_bod }}</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('productos-bodega.historial', $producto->id_prod_bod) }}" 
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

            <form action="{{ route('productos-bodega.update-producto', $producto->id_prod_bod) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

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
                                   value="{{ old('nombre', $producto->nombre) }}"
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

                    <!-- Precio de venta -->
                    <div class="mt-6">
                        <label for="precio_actual" class="block text-sm font-medium text-gray-700 mb-2">
                            Precio de Venta Actual <span class="text-red-500">*</span>
                        </label>
                        <div class="relative max-w-xs">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-500 font-medium">
                                S/
                            </span>
                            <input type="number" 
                                id="precio_actual" 
                                name="precio_actual" 
                                value="{{ old('precio_actual', $producto->precio_actual) }}"
                                step="0.01"
                                min="0.01"
                                max="9999.99"
                                class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm @error('precio_actual') border-red-300 @enderror"
                                placeholder="0.00"
                                required>
                        </div>
                        @error('precio_actual')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">
                            Precio sugerido de venta. Se usa como referencia para nuevas ventas.
                        </p>
                    </div>
                </div>

                <!-- Información del producto -->
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Información del Producto</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        <div>
                            <span class="text-gray-500">Fecha de creación:</span>
                            <p class="font-medium">{{ $producto->created_at ? $producto->created_at->format('d/m/Y H:i') : 'No disponible' }}</p>
                        </div>
                        <div>
                            <span class="text-gray-500">Última actualización:</span>
                            <p class="font-medium">{{ $producto->updated_at ? $producto->updated_at->format('d/m/Y H:i') : 'No disponible' }}</p>
                        </div>
                        <div>
                            <span class="text-gray-500">Estado:</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Activo
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Advertencia sobre el cambio de nombre -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class='bx bx-warning text-yellow-400 text-xl'></i>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-sm font-medium text-yellow-800 mb-2">Importante</h4>
                            <div class="text-sm text-yellow-700 space-y-1">
                                <p>• Al cambiar el nombre del producto, se actualizará en todo el sistema</p>
                                <p>• El historial de compras y ventas se mantendrá intacto</p>
                                <p>• Los reportes existentes reflejarán el nuevo nombre</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botones -->
                <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('productos-bodega.historial', $producto->id_prod_bod) }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        Cancelar
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-6 py-2 bg-blue-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        <i class='bx bx-save mr-2'></i>
                        Actualizar Producto
                    </button>
                </div>
            </form>
        </div>

        <!-- Estadísticas rápidas -->
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-md p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class='bx bx-info-circle text-blue-400 text-xl'></i>
                </div>
                <div class="ml-3">
                    <h4 class="text-sm font-medium text-blue-800 mb-2">Estadísticas del Producto</h4>
                    <div class="text-sm text-blue-700 grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <span class="block text-blue-600">Stock Actual:</span>
                            <span class="font-semibold">{{ $producto->stock ?? 0 }} unidades</span>
                        </div>
                        <div>
                            <span class="block text-blue-600">Total Compras:</span>
                            <span class="font-semibold">{{ $producto->comprasBodega->count() ?? 0 }}</span>
                        </div>
                        <div>
                            <span class="block text-blue-600">Inversión Total:</span>
                            <span class="font-semibold">S/ {{ number_format($producto->inversion_total ?? 0, 2) }}</span>
                        </div>
                        <div>
                            <span class="block text-blue-600">Unidades Vendidas:</span>
                            <span class="font-semibold">{{ $producto->unidades_vendidas ?? 0 }}</span>
                        </div>
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
    const precioInput = document.getElementById('precio_actual');
    const nombreOriginal = "{{ $producto->nombre }}";
    const precioOriginal = "{{ $producto->precio_actual }}";
    const formulario = document.querySelector('form');
    
    // Auto-focus en el campo nombre
    nombreInput.focus();
    
    // Convertir primera letra a mayúscula
    nombreInput.addEventListener('input', function() {
        let value = this.value;
        if (value.length > 0) {
            this.value = value.charAt(0).toUpperCase() + value.slice(1);
        }
    });
    
    // Desactivar advertencia al enviar formulario
    formulario.addEventListener('submit', function() {
        window.onbeforeunload = null;
    });
    
    // Advertir solo si hay cambios Y NO se está enviando
    function verificarCambios() {
        const hayNombreCambiado = nombreInput.value !== nombreOriginal;
        const hayPrecioCambiado = precioInput.value !== precioOriginal;
        
        if (hayNombreCambiado || hayPrecioCambiado) {
            window.onbeforeunload = function() {
                return "¿Seguro que quieres salir sin guardar?";
            };
        } else {
            window.onbeforeunload = null;
        }
    }
    
    nombreInput.addEventListener('input', verificarCambios);
    precioInput.addEventListener('input', verificarCambios);
});


@endpush
@endsection