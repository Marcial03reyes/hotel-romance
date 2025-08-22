@extends('layouts.app')

@section('title', 'Editar Producto de Hotel')

@section('content')
<div class="py-6">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Editar Producto de Hotel</h1>
                    <p class="text-gray-600 mt-1">Modifica el nombre del producto: <span class="font-medium">{{ $producto->nombre }}</span></p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('productos-hotel.index') }}" 
                       class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        <i class='bx bx-arrow-back mr-2'></i>
                        Volver
                    </a>
                </div>
            </div>
        </div>

        <!-- Alertas -->
        @if($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class='bx bx-error text-red-400 text-xl'></i>
                    </div>
                    <div class="ml-3">
                        <h4 class="text-sm font-medium text-red-800 mb-2">Se encontraron errores:</h4>
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($errors->all() as $error)
                                <li class="text-sm text-red-700">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <!-- Formulario -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <form action="{{ route('productos-hotel.update-producto', $producto->id_prod_hotel) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="space-y-6">
                    <!-- Campo Nombre -->
                    <div>
                        <label for="nombre" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class='bx bx-package mr-1'></i>
                            Nombre del Producto
                        </label>
                        <input type="text" 
                               id="nombre" 
                               name="nombre" 
                               value="{{ old('nombre', $producto->nombre) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                               placeholder="Ej: Detergente para pisos, Jabón antibacterial..."
                               maxlength="50"
                               required>
                        <p class="mt-1 text-xs text-gray-500">
                            <i class='bx bx-info-circle mr-1'></i>
                            Máximo 50 caracteres. Debe ser único en el sistema.
                        </p>
                    </div>

                    <!-- Información adicional -->
                    <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class='bx bx-lightbulb text-blue-400 text-xl'></i>
                            </div>
                            <div class="ml-3">
                                <h4 class="text-sm font-medium text-blue-800 mb-2">Consejos para nombrar productos</h4>
                                <div class="text-sm text-blue-700 space-y-1">
                                    <p>• Usa nombres descriptivos y específicos</p>
                                    <p>• Incluye la marca si es relevante</p>
                                    <p>• Evita caracteres especiales o acentos</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                        <a href="{{ route('productos-hotel.index') }}" 
                           class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                            <i class='bx bx-x mr-1'></i>
                            Cancelar
                        </a>
                        <button type="submit" 
                                class="px-6 py-2 bg-blue-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            <i class='bx bx-save mr-1'></i>
                            Guardar Cambios
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Información del producto -->
        <div class="mt-6 bg-gray-50 border border-gray-200 rounded-md p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class='bx bx-info-circle text-gray-400 text-xl'></i>
                </div>
                <div class="ml-3">
                    <h4 class="text-sm font-medium text-gray-800 mb-2">Información del producto</h4>
                    <div class="text-sm text-gray-600 space-y-1">
                        <p><strong>ID:</strong> {{ $producto->id_prod_hotel }}</p>
                        <p><strong>Nombre actual:</strong> {{ $producto->nombre }}</p>
                        <p class="text-xs text-gray-500 mt-2">
                            Solo puedes cambiar el nombre del producto. Para eliminar este producto, regresa a la lista principal.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const nombreInput = document.getElementById('nombre');
    
    // Auto-focus y seleccionar texto
    if (nombreInput) {
        nombreInput.focus();
        nombreInput.select();
    }
    
    // Formatear nombre automáticamente
    nombreInput.addEventListener('blur', function() {
        let value = this.value.trim();
        if (value) {
            // Primera letra mayúscula
            value = value.charAt(0).toUpperCase() + value.slice(1).toLowerCase();
            this.value = value;
        }
    });
    
    // Validación en tiempo real
    nombreInput.addEventListener('input', function() {
        const maxLength = 50;
        const currentLength = this.value.length;
        
        if (currentLength > maxLength) {
            this.value = this.value.substring(0, maxLength);
        }
        
        // Cambiar color del borde según la validez
        if (this.value.trim().length >= 3) {
            this.classList.remove('border-red-300');
            this.classList.add('border-green-300');
        } else {
            this.classList.remove('border-green-300');
            this.classList.add('border-red-300');
        }
    });
});
</script>
@endsection