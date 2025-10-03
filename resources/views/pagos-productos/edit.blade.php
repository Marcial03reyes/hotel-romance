@extends('layouts.app')

@section('title', 'Editar Venta #' . $venta->id_compra . ' - Hotel Romance')

@section('content')

<style>
    /* Paleta de colores azul Hotel Romance */
    :root {
        --primary-color: #88A6D3;
        --secondary-color: #6B8CC7;
        --tertiary-color: #A5BFDB;
        --accent-color: #4A73B8;
        --light-blue: #C8D7ED;
        --sidebar-bg: #f4f8fc;
        --hover-bg: #88A6D3;
        --gradient-start: #88A6D3;
        --gradient-end: #6B8CC7;
    }

    .form-container {
        background: linear-gradient(135deg, #f4f8fc 0%, #e8f2ff 100%);
    }
    
    .input-field:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(136, 166, 211, 0.1);
        outline: none;
    }
    
    .btn-romance {
        background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
        transition: all 0.3s ease;
    }
    
    .btn-romance:hover {
        background: linear-gradient(135deg, var(--secondary-color), var(--accent-color));
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(136, 166, 211, 0.3);
    }
    
    .btn-secondary {
        background: #f3f4f6;
        color: #374151;
        border: 1px solid #d1d5db;
        transition: all 0.3s ease;
    }
    
    .btn-secondary:hover {
        background: #e5e7eb;
        border-color: #9ca3af;
    }
    
    .field-group {
        background: white;
        border-radius: 0.75rem;
        padding: 1.5rem;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        border: 1px solid #e5e7eb;
    }

    .badge-info {
        background: linear-gradient(135deg, var(--tertiary-color), var(--light-blue));
        color: var(--accent-color);
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .turno-button {
        transition: all 0.3s ease;
        position: relative;
    }

    .turno-radio:checked + .turno-button.turno-dia {
        border-color: #f59e0b;
        background: linear-gradient(135deg, #fef3c7, #fde68a);
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
        transform: translateY(-2px);
    }

    .turno-radio:checked + .turno-button.turno-noche {
        border-color: #3b82f6;
        background: linear-gradient(135deg, #dbeafe, #93c5fd);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        transform: translateY(-2px);
    }

    .turno-radio:checked + .turno-button::after {
        content: '✓';
        position: absolute;
        top: 8px;
        right: 8px;
        background: #10b981;
        color: white;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: bold;
    }

    .radio-azul {
        accent-color: var(--primary-color);
    }

    .info-box {
        background-color: #f0f7ff;
        border-color: var(--light-blue);
    }

    .info-text {
        color: var(--accent-color);
    }
</style>

<div class="container mx-auto py-6 px-4">
    
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">
                    <i class='bx bx-edit mr-2' style="color: #6B8CC7;"></i>
                    Editar Venta #{{ $venta->id_compra }}
                </h1>
                <p class="text-gray-600">Modifica la información de la venta registrada</p>
            </div>
            <div class="flex items-center space-x-2">
                <span class="badge-info">
                    <i class='bx bx-calendar mr-1'></i>
                    {{ $venta->fecha_formateada }}
                </span>
                <span class="badge-info">
                    <i class='bx {{ $venta->turno == 0 ? "bx-sun" : "bx-moon" }} mr-1'></i>
                    {{ $venta->turno_nombre }}
                </span>
            </div>
        </div>
        
        <div class="flex items-center space-x-4 text-sm text-gray-600">
            <a href="{{ route('pagos-productos.index') }}" class="inline-flex items-center hover:text-blue-600 transition-colors">
                <i class='bx bx-arrow-back mr-1'></i>
                Volver a registro de bodega
            </a>
            <span>•</span>
            <span class="text-gray-500">Modifica los campos necesarios</span>
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
    <form action="{{ route('pagos-productos.update', $venta->id_compra) }}" method="POST" id="form-venta" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid lg:grid-cols-2 gap-6">
            
            <!-- Fecha y Turno -->
            <div class="field-group">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class='bx bx-calendar mr-2' style="color: #6B8CC7;"></i>
                    Fecha y Turno
                </h2>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class='bx bx-calendar-event mr-1'></i>
                            Fecha de Venta *
                        </label>
                        <input name="fecha_venta" type="date" value="{{ old('fecha_venta', $venta->fecha_venta) }}"
                               class="input-field w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               required>
                        <p class="text-xs text-gray-500 mt-1">Fecha en que se realizó la venta</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class='bx bx-sun mr-1'></i>
                            Turno *
                        </label>
                        <div class="flex space-x-4">
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" name="turno" value="0" class="sr-only turno-radio" 
                                       {{ old('turno', $venta->turno) == 0 ? 'checked' : '' }} required>
                                <div class="turno-button turno-dia border-2 border-gray-200 rounded-lg p-4 text-center transition-all hover:border-yellow-400 hover:bg-yellow-50">
                                    <i class='bx bx-sun text-3xl mb-2 text-yellow-600'></i>
                                    <div class="font-semibold text-gray-800">DÍA</div>
                                </div>
                            </label>
                            
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" name="turno" value="1" class="sr-only turno-radio" 
                                       {{ old('turno', $venta->turno) == 1 ? 'checked' : '' }} required>
                                <div class="turno-button turno-noche border-2 border-gray-200 rounded-lg p-4 text-center transition-all hover:border-blue-400 hover:bg-blue-50">
                                    <i class='bx bx-moon text-3xl mb-2 text-blue-600'></i>
                                    <div class="font-semibold text-gray-800">NOCHE</div>
                                </div>
                            </label>
                        </div>
                        <p class="text-xs text-gray-500 text-center mt-3">
                            * Turno actual: <strong>{{ $venta->turno_nombre }}</strong>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Información del Producto -->
            <div class="field-group">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class='bx bx-package mr-2' style="color: #6B8CC7;"></i>
                    Información del Producto
                </h2>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class='bx bx-shopping-bag mr-1'></i>
                            Producto *
                        </label>
                        <select name="id_prod_bod" id="id_prod_bod" 
                                class="input-field w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                required>
                            <option value="">Selecciona un producto</option>
                            @foreach($productos as $producto)
                                <option value="{{ $producto->id_prod_bod }}" 
                                        data-precio="{{ $producto->precio_actual }}"
                                        {{ old('id_prod_bod', $venta->id_prod_bod) == $producto->id_prod_bod ? 'selected' : '' }}>
                                    {{ $producto->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                <i class='bx bx-hash mr-1'></i>
                                Cantidad *
                            </label>
                            <input name="cantidad" id="cantidad" type="number" min="1" max="9999" 
                                   value="{{ old('cantidad', $venta->cantidad) }}"
                                   class="input-field w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                   required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                <i class='bx bx-dollar mr-1'></i>
                                Precio Unitario *
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 font-medium">S/</span>
                                <input name="precio_unitario" id="precio_unitario" type="number" step="0.01" min="0" readonly
                                       value="{{ old('precio_unitario', $venta->precio_unitario) }}"
                                       class="input-field w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg bg-gray-50" 
                                       placeholder="0.00">
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Precio al momento de la venta</p>
                        </div>
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700">
                                <i class='bx bx-calculator mr-1'></i>
                                Total:
                            </span>
                            <span class="text-2xl font-bold text-blue-600" id="total-venta">
                                S/ {{ number_format($venta->total, 2) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información de Pago -->
            <div class="field-group lg:col-span-2">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class='bx bx-credit-card mr-2' style="color: #6B8CC7;"></i>
                    Información de Pago
                </h2>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class='bx bx-wallet mr-1'></i>
                            Método de Pago *
                        </label>
                        <select name="id_met_pago" 
                                class="input-field w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                required>
                            <option value="">Selecciona un método</option>
                            @foreach($metodos as $metodo)
                                <option value="{{ $metodo->id_met_pago }}"
                                        {{ old('id_met_pago', $venta->id_met_pago) == $metodo->id_met_pago ? 'selected' : '' }}>
                                    {{ $metodo->met_pago }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class='bx bx-receipt mr-1'></i>
                            ¿Requiere Comprobante?
                        </label>
                        <div class="flex items-center space-x-6 pt-3">
                            <label class="flex items-center">
                                <input type="radio" name="comprobante" value="SI" 
                                       {{ old('comprobante', $venta->comprobante) == 'SI' ? 'checked' : '' }}
                                       class="radio-azul focus:ring-blue-500 mr-2">
                                <span class="text-sm font-medium text-gray-700">SÍ</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="comprobante" value="NO" 
                                       {{ old('comprobante', $venta->comprobante) == 'NO' ? 'checked' : '' }}
                                       class="radio-azul focus:ring-blue-500 mr-2">
                                <span class="text-sm font-medium text-gray-700">No</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botones de acción -->
        <div class="flex items-center justify-between pt-6 border-t border-gray-200">
            <a href="{{ route('pagos-productos.index') }}" 
               class="btn-secondary px-6 py-3 rounded-lg font-medium transition-all">
                <i class='bx bx-x mr-2'></i>
                Cancelar
            </a>
            
            <button type="submit" 
                    class="btn-romance text-white px-8 py-3 rounded-lg font-medium shadow-lg">
                <i class='bx bx-save mr-2'></i>
                Actualizar Venta
            </button>
        </div>
    </form>

    <!-- Información adicional -->
    <div class="mt-8 p-4 info-box border rounded-lg">
        <div class="flex items-start">
            <i class='bx bx-info-circle text-lg mr-2 mt-1' style="color: #6B8CC7;"></i>
            <div class="text-sm info-text">
                <p class="font-medium mb-1">Información importante:</p>
                <ul class="list-disc list-inside space-y-1">
                    <li>El precio se mantendrá como el registrado originalmente</li>
                    <li>Si cambias el producto, el precio se actualizará al precio actual del catálogo</li>
                    <li>Puedes modificar la cantidad y se recalculará el total automáticamente</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // === FUNCIONALIDAD SELECTOR DE TURNO ===
    const turnoRadios = document.querySelectorAll('.turno-radio');
    
    turnoRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            document.querySelectorAll('.turno-button').forEach(btn => {
                btn.classList.remove('selected');
            });
            if (this.checked) {
                this.nextElementSibling.classList.add('selected');
            }
        });
    });

    // Activar el turno preseleccionado visualmente
    const turnoPreseleccionado = document.querySelector('input[name="turno"]:checked');
    if (turnoPreseleccionado) {
        turnoPreseleccionado.nextElementSibling.classList.add('selected');
    }

    // === FUNCIONALIDAD DE PRECIO Y TOTAL ===
    const productoSelect = document.getElementById('id_prod_bod');
    const cantidadInput = document.getElementById('cantidad');
    const precioInput = document.getElementById('precio_unitario');
    const totalSpan = document.getElementById('total-venta');
    const productoOriginal = productoSelect.value;

    function calcularTotal() {
        const precio = parseFloat(precioInput.value) || 0;
        const cantidad = parseInt(cantidadInput.value) || 0;
        const total = precio * cantidad;
        totalSpan.textContent = `S/ ${total.toFixed(2)}`;
    }

    // Si cambia el producto, actualizar precio
    productoSelect.addEventListener('change', function() {
        // Solo actualizar precio si cambió el producto
        if (this.value !== productoOriginal) {
            const selectedOption = this.options[this.selectedIndex];
            const precio = selectedOption.getAttribute('data-precio') || '0';
            precioInput.value = parseFloat(precio).toFixed(2);
            calcularTotal();
        }
    });

    // Recalcular al cambiar cantidad
    cantidadInput.addEventListener('input', calcularTotal);

    // === VALIDACIÓN ===
    document.getElementById('form-venta').addEventListener('submit', function(e) {
        const turnoSeleccionado = document.querySelector('input[name="turno"]:checked');
        if (!turnoSeleccionado) {
            e.preventDefault();
            alert('Debes seleccionar un turno (DÍA o NOCHE)');
            return false;
        }

        if (!productoSelect.value) {
            e.preventDefault();
            alert('Debes seleccionar un producto');
            productoSelect.focus();
            return false;
        }

        const cantidad = parseInt(cantidadInput.value);
        if (!cantidad || cantidad <= 0) {
            e.preventDefault();
            alert('La cantidad debe ser mayor a 0');
            cantidadInput.focus();
            return false;
        }

        if (!confirm('¿Estás seguro de actualizar esta venta?')) {
            e.preventDefault();
            return false;
        }
        
        return true;
    });
});
</script>

<!-- Boxicons -->
<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

@endsection