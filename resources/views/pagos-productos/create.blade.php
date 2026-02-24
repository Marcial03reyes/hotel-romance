@extends('layouts.app')

@section('title', 'Registrar Venta de Bodega - Hotel Romance')

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

    .pago-item {
        position: relative;
    }
</style>

<div class="container mx-auto py-6 px-4">
    
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">
                    <i class='bx bx-plus-circle mr-2' style="color: #6B8CC7;"></i>
                    Nueva Venta de Bodega
                </h1>
                <p class="text-gray-600">Registra una venta de productos por día y turno</p>
            </div>
        </div>
        
        <div class="flex items-center space-x-4 text-sm text-gray-600">
            <a href="{{ route('pagos-productos.index') }}" class="inline-flex items-center hover:text-blue-600 transition-colors">
                <i class='bx bx-arrow-back mr-1'></i>
                Volver a registro de bodega
            </a>
            <span>ⓘ</span>
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
    <form action="{{ route('pagos-productos.store') }}" method="POST" id="form-venta" class="space-y-6">
        @csrf

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
                        <input name="fecha_venta" type="date" value="{{ old('fecha_venta', now()->format('Y-m-d')) }}"
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
                                <input type="radio" name="turno" value="0" class="sr-only turno-radio" required>
                                <div class="turno-button turno-dia border-2 border-gray-200 rounded-lg p-4 text-center transition-all hover:border-yellow-400 hover:bg-yellow-50">
                                    <i class='bx bx-sun text-3xl mb-2 text-yellow-600'></i>
                                    <div class="font-semibold text-gray-800">DÍA</div>
                                </div>
                            </label>
                            
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" name="turno" value="1" class="sr-only turno-radio" required>
                                <div class="turno-button turno-noche border-2 border-gray-200 rounded-lg p-4 text-center transition-all hover:border-blue-400 hover:bg-blue-50">
                                    <i class='bx bx-moon text-3xl mb-2 text-blue-600'></i>
                                    <div class="font-semibold text-gray-800">NOCHE</div>
                                </div>
                            </label>
                        </div>
                        <p class="text-xs text-gray-500 text-center mt-3">* Selecciona el turno correspondiente</p>
                    </div>

                    <!-- Tipo de Registro (Consumo Interno) -->
                    <div class="field-group">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <i class='bx bx-info-circle mr-2' style="color: #6B8CC7;"></i>
                            Tipo de Registro
                        </h2>
                        
                        <div class="bg-yellow-50 border border-yellow-300 rounded-lg p-4">
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" 
                                    id="es_consumo_interno" 
                                    name="es_consumo_interno" 
                                    value="1"
                                    class="mr-3 w-5 h-5 text-blue-600 rounded focus:ring-blue-500">
                                <div>
                                    <span class="font-medium text-gray-800">
                                        <i class='bx bx-user-check mr-1'></i>
                                        Consumo Interno
                                    </span>
                                    <p class="text-xs text-gray-600 mt-1">
                                        Marcar si el producto es consumido por el personal. 
                                        Se registrará con precio S/ 0.00 pero afectará el inventario.
                                    </p>
                                </div>
                            </label>
                        </div>
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
                                <option value="{{ $producto->id_prod_bod }}" data-precio="{{ $producto->precio_actual }}">
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
                            <input name="cantidad" id="cantidad" type="number" min="1" max="9999" value="{{ old('cantidad', 1) }}"
                                   class="input-field w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                   required>
                            <input type="hidden" name="monto_total" id="monto_total_hidden" value="0">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                <i class='bx bx-dollar mr-1'></i>
                                Precio Unitario *
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 font-medium">S/</span>
                                <input name="precio_unitario" id="precio_unitario" type="number" step="0.01" min="0" readonly
                                       class="input-field w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg bg-gray-50" 
                                       placeholder="0.00">
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Se completa automáticamente</p>
                        </div>
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700">
                                <i class='bx bx-calculator mr-1'></i>
                                Total:
                            </span>
                            <span class="text-2xl font-bold text-blue-600" id="total-venta">S/ 0.00</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información de Pago -->
            <div class="field-group lg:col-span-2">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                        <i class='bx bx-credit-card mr-2' style="color: #6B8CC7;"></i>
                        Información de Pago
                    </h2>
                    <button type="button" id="btn-add-pago" 
                            class="inline-flex items-center bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm transition-all">
                        <i class='bx bx-plus mr-2'></i>
                        Agregar Método de Pago
                    </button>
                </div>

                <!-- Total de la Venta (referencia) -->
                <div class="mb-4 bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700">
                            <i class='bx bx-shopping-bag mr-1'></i>
                            Total de la Venta:
                        </span>
                        <span class="text-xl font-bold text-gray-800" id="total-venta-ref">S/ 0.00</span>
                    </div>
                </div>

                <!-- Métodos de Pago -->
                <div id="pagos-wrapper" class="space-y-4 mb-6">
                    <!-- Primer método de pago (siempre visible) -->
                    <div class="pago-item bg-gray-50 border border-gray-200 rounded-lg p-4" data-pago-index="0">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="font-medium text-gray-700">Método de Pago #1</h4>
                        </div>
                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    <i class='bx bx-wallet mr-1'></i>
                                    Método de Pago *
                                </label>
                                <select name="id_met_pago" class="input-field w-full px-4 py-3 border border-gray-300 rounded-lg metodo-pago" required>
                                    <option value="">Selecciona un método</option>
                                    @foreach($metodos as $metodo)
                                        <option value="{{ $metodo->id_met_pago }}">{{ $metodo->met_pago }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    <i class='bx bx-dollar mr-1'></i>
                                    Monto *
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 font-medium">S/</span>
                                    <input name="monto_individual" type="number" step="0.01" min="0"
                                        class="input-field w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg monto-pago" 
                                        placeholder="0.00" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Resumen de Pagos -->
                <div id="resumen-pagos" class="bg-blue-50 border-2 border-blue-200 rounded-lg p-4 mb-6" style="display: none;">
                    <div class="flex items-center justify-between">
                        <span class="font-medium text-blue-900">
                            <i class='bx bx-calculator mr-2'></i>
                            Total Pagado:
                        </span>
                        <span class="text-lg font-bold text-blue-700" id="total-pagado">S/ 0.00</span>
                    </div>
                    <div class="flex items-center justify-between mt-2">
                        <span class="text-sm text-blue-800">Pendiente:</span>
                        <span class="text-sm font-medium text-blue-700" id="total-pendiente">S/ 0.00</span>
                    </div>
                </div>

                <!-- Alerta de diferencia -->
                <div id="alerta-diferencia" class="bg-yellow-50 border border-yellow-300 rounded-lg p-4 mb-6" style="display: none;">
                    <div class="flex items-center">
                        <i class='bx bx-error text-yellow-600 mr-2 text-lg'></i>
                        <span class="font-medium text-yellow-800" id="mensaje-diferencia"></span>
                    </div>
                </div>

                <!-- Comprobante -->
                <div id="comprobante-section">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        <i class='bx bx-receipt mr-1'></i>
                        ¿Requiere Comprobante?
                    </label>
                    <div class="flex items-center space-x-6 pt-3">
                        <label class="flex items-center">
                            <input type="radio" name="comprobante" value="SI" class="radio-azul focus:ring-blue-500 mr-2">
                            <span class="text-sm font-medium text-gray-700">SÍ</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="comprobante" value="NO" checked class="radio-azul focus:ring-blue-500 mr-2">
                            <span class="text-sm font-medium text-gray-700">No</span>
                        </label>
                    </div>
                </div>
            </div>
 
        </div>

        {{-- Aviso de turno cerrado (controlado por JS) --}}
        <div id="aviso-turno-cerrado" 
            class="hidden rounded-lg border-2 border-red-500 bg-red-50 p-4 text-red-800 shadow-sm">
            <div class="flex items-center">
                <i class='bx bx-lock-alt mr-3 text-2xl text-red-600'></i>
                <div>
                    <p class="font-bold text-red-700 text-base">🔒 TURNO CERRADO</p>
                    <p class="text-sm mt-1" id="aviso-turno-cerrado-texto"></p>
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
                Registrar Venta
            </button>
        </div>
    </form>
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
    
    const horaActual = new Date().getHours();
    const turnoAutomatico = (horaActual >= 6 && horaActual < 18) ? '0' : '1';
    const radioSugerido = document.querySelector(`input[name="turno"][value="${turnoAutomatico}"]`);
    if (radioSugerido) {
        radioSugerido.click();
    }

    // === VARIABLES ===
    const productoSelect = document.getElementById('id_prod_bod');
    const cantidadInput = document.getElementById('cantidad');
    const precioInput = document.getElementById('precio_unitario');
    const totalSpan = document.getElementById('total-venta');
    const totalVentaRef = document.getElementById('total-venta-ref');
    
    const pagosWrapper = document.getElementById('pagos-wrapper');
    const btnAddPago = document.getElementById('btn-add-pago');
    const resumenPagos = document.getElementById('resumen-pagos');
    const totalPagadoSpan = document.getElementById('total-pagado');
    const totalPendienteSpan = document.getElementById('total-pendiente');
    const alertaDiferencia = document.getElementById('alerta-diferencia');
    const mensajeDiferencia = document.getElementById('mensaje-diferencia');
    
    let pagoIndex = 1;

    // === CALCULAR TOTAL VENTA ===
    function calcularTotalVenta() {
        const precio = parseFloat(precioInput.value) || 0;
        const cantidad = parseInt(cantidadInput.value) || 0;
        const total = precio * cantidad;
        totalSpan.textContent = `S/ ${total.toFixed(2)}`;
        totalVentaRef.textContent = `S/ ${total.toFixed(2)}`;
        
        const montoTotalHidden = document.getElementById('monto_total_hidden');
        if (montoTotalHidden) {
            montoTotalHidden.value = total.toFixed(2);
        }
        
        const primerMontoPago = document.querySelector('[name="monto_pago"]');
        if (primerMontoPago && !consumoInternoCheck.checked) {
            primerMontoPago.value = total.toFixed(2);
        }
    }

    productoSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const precio = selectedOption.getAttribute('data-precio') || '0';
        
        // Solo actualizar precio si no es consumo interno
        if (!consumoInternoCheck.checked) {
            precioInput.value = parseFloat(precio).toFixed(2);
        }
        
        calcularTotalVenta();
    });

    cantidadInput.addEventListener('input', calcularTotalVenta);

    // === FUNCIONES DE PAGO ===
    function getNextPagoNumber() {
        const existingNumbers = Array.from(document.querySelectorAll('.pago-item')).map(item => {
            const text = item.querySelector('h4').textContent;
            const match = text.match(/Método de Pago #(\d+)/);
            return match ? parseInt(match[1]) : 0;
        });
        if (existingNumbers.length === 0) return 2;
        return Math.max(...existingNumbers) + 1;
    }

    function addPagoRow() {
        const nextNumber = getNextPagoNumber();
        const div = document.createElement('div');
        div.className = 'pago-item bg-gray-50 border border-gray-200 rounded-lg p-4';
        div.setAttribute('data-pago-index', pagoIndex);
        div.innerHTML = `
            <div class="flex items-center justify-between mb-3">
                <h4 class="font-medium text-gray-700">Método de Pago #${nextNumber}</h4>
                <button type="button" onclick="removePagoRow(this)" 
                        class="text-red-500 hover:text-red-700 transition-colors">
                    <i class='bx bx-trash'></i>
                </button>
            </div>
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        <i class='bx bx-wallet mr-1'></i>
                        Método de Pago *
                    </label>
                    <select name="pagos[${pagoIndex}][id_met_pago]" class="input-field w-full px-4 py-3 border border-gray-300 rounded-lg metodo-pago" required>
                        <option value="">Selecciona un método</option>
                        @foreach($metodos as $metodo)
                            <option value="{{ $metodo->id_met_pago }}">{{ $metodo->met_pago }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        <i class='bx bx-dollar mr-1'></i>
                        Monto *
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 font-medium">S/</span>
                        <input name="pagos[${pagoIndex}][monto]" type="number" step="0.01" min="0"
                            class="input-field w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg monto-pago" 
                            required placeholder="0.00">
                    </div>
                </div>
            </div>
        `;
        pagosWrapper.appendChild(div);
        pagoIndex++;
        updatePagoEvents();
    }

    window.removePagoRow = function(button) {
        button.closest('.pago-item').remove();
        updatePagoEvents();
        calcularTotalesPago();
    };

    function updatePagoEvents() {
        document.querySelectorAll('.monto-pago').forEach(input => {
            input.removeEventListener('input', calcularTotalesPago);
            input.addEventListener('input', calcularTotalesPago);
        });
    }

    function calcularTotalesPago() {
        const totalVenta = parseFloat(precioInput.value || 0) * parseInt(cantidadInput.value || 0);
        let totalPagado = 0;
        
        document.querySelectorAll('.monto-pago').forEach(input => {
            const monto = parseFloat(input.value) || 0;
            totalPagado += monto;
        });
        
        const pendiente = totalVenta - totalPagado;
        
        if (totalVenta > 0 && document.querySelectorAll('.pago-item').length > 0) {
            resumenPagos.style.display = 'block';
            totalPagadoSpan.textContent = `S/ ${totalPagado.toFixed(2)}`;
            totalPendienteSpan.textContent = `S/ ${pendiente.toFixed(2)}`;
            
            if (Math.abs(pendiente) > 0.01) {
                alertaDiferencia.style.display = 'block';
                if (pendiente > 0) {
                    mensajeDiferencia.textContent = `Falta S/ ${pendiente.toFixed(2)} por completar`;
                } else {
                    mensajeDiferencia.textContent = `Exceso de S/ ${Math.abs(pendiente).toFixed(2)}`;
                }
            } else {
                alertaDiferencia.style.display = 'none';
            }
        } else {
            resumenPagos.style.display = 'none';
            alertaDiferencia.style.display = 'none';
        }
    }

    btnAddPago.addEventListener('click', addPagoRow);
    updatePagoEvents();

    // NUEVO: Manejo de consumo interno
    const consumoInternoCheck = document.getElementById('es_consumo_interno');
    const comprobanteSection = document.getElementById('comprobante-section');
    const precioField = document.getElementById('precio_unitario');
    const originalPrecio = {};
    
    if (consumoInternoCheck) {
        consumoInternoCheck.addEventListener('change', function() {
            if (this.checked) {
                // Es consumo interno
                const selectedOption = productoSelect.options[productoSelect.selectedIndex];
                originalPrecio[productoSelect.value] = selectedOption.getAttribute('data-precio');
                
                precioField.value = '0.00';
                precioField.style.backgroundColor = '#fef3c7';
                
                // Ocultar sección de comprobante
                if (comprobanteSection) {
                    comprobanteSection.style.display = 'none';
                }
                
                // Actualizar el total
                calcularTotalVenta();
                
                // Mostrar alerta visual
                precioField.parentElement.insertAdjacentHTML('afterend', 
                    '<p class="text-xs text-yellow-600 mt-1" id="consumo-alert">' +
                    '<i class="bx bx-info-circle"></i> Consumo interno - Sin costo</p>'
                );

                // Ocultar también la sección de pago o establecer un valor por defecto
                const metodoPagoSelect = document.querySelector('[name="id_met_pago"]');
                if (metodoPagoSelect) {
                    // Seleccionar automáticamente "Efectivo" o el primer método
                    metodoPagoSelect.value = metodoPagoSelect.options[1]?.value || '';
                    metodoPagoSelect.closest('.field-group').style.display = 'none';

                    // AGREGAR: Quitar required de campos de pago
                    document.querySelectorAll('.monto-pago').forEach(input => {
                        input.removeAttribute('required');
                    });
                    const selectMetodo = document.querySelector('select[name*="metodo"]');
                    if (selectMetodo) selectMetodo.removeAttribute('required');
                }

                const seccionPagoMultiple = document.querySelector('.field-group:has(#pagos-wrapper)');
                if (seccionPagoMultiple) {
                    seccionPagoMultiple.style.display = 'none';
                }

            } else {
                // Es venta normal
                if (productoSelect.value && originalPrecio[productoSelect.value]) {
                    precioField.value = parseFloat(originalPrecio[productoSelect.value]).toFixed(2);
                }
                precioField.style.backgroundColor = '#f9fafb';
                
                // Mostrar sección de comprobante
                if (comprobanteSection) {
                    comprobanteSection.style.display = '';
                }
                
                // Quitar alerta
                const alert = document.getElementById('consumo-alert');
                if (alert) alert.remove();
                calcularTotalVenta();
            
                // Restaurar sección de pago
                const metodoPagoSelect = document.querySelector('[name="id_met_pago"]');
                if (metodoPagoSelect) {
                    metodoPagoSelect.closest('.field-group').style.display = 'block';

                    document.querySelectorAll('.monto-pago').forEach(input => {
                        input.setAttribute('required', 'required');
                    });
                    const selectMetodo = document.querySelector('select[name*="metodo"]');
                    if (selectMetodo) selectMetodo.setAttribute('required', 'required');
                }

                const seccionPagoMultiple = document.querySelector('.field-group:has(#pagos-wrapper)');
                if (seccionPagoMultiple) {
                    seccionPagoMultiple.style.display = 'block';
                }
            }
        });
    }

    // === VALIDACIÓN ===
    document.getElementById('form-venta').addEventListener('submit', function(e) {
        console.log('=== INICIANDO VALIDACIÓN ===');

        const turnoSeleccionado = document.querySelector('input[name="turno"]:checked');
        if (!turnoSeleccionado) {

            console.log('ERROR: No hay turno seleccionado');

            e.preventDefault();
            alert('⚠️ Debes seleccionar un turno');
            return false;
        }

        if (!productoSelect.value) {
            console.log('ERROR: No hay producto seleccionado');
            e.preventDefault();
            alert('⚠️ Debes seleccionar un producto');
            productoSelect.focus();
            return false;
        }

        const cantidad = parseInt(cantidadInput.value);
        if (!cantidad || cantidad <= 0) {
            console.log('ERROR: Cantidad inválida:', cantidad);
            e.preventDefault();
            alert('⚠️ La cantidad debe ser mayor a 0');
            cantidadInput.focus();
            return false;
        }

        console.log('Pagos válidos:', pagosValidos);
        console.log('Es consumo interno:', esConsumoInterno);
        console.log('Total venta:', totalVenta);
        console.log('Total pagado:', totalPagado);

        // Validar pagos
        const totalVenta = parseFloat(precioInput.value || 0) * parseInt(cantidadInput.value || 0);
        let totalPagado = 0;
        let pagosValidos = 0;
        
        document.querySelectorAll('.pago-item').forEach((item) => {
            const metodo = item.querySelector('.metodo-pago').value;
            const monto = parseFloat(item.querySelector('.monto-pago').value) || 0;
            if (metodo && monto > 0) {
                totalPagado += monto;
                pagosValidos++;
            }
        });
        
        const esConsumoInterno = document.querySelector('input[name="tipo_registro"]:checked')?.value === 'consumo_interno';
        if (pagosValidos === 0 && !esConsumoInterno) {
            e.preventDefault();
            alert('⚠️ Debe especificar al menos un método de pago');
            return false;
        }
        
        if (Math.abs(totalVenta - totalPagado) > 0.01 && !esConsumoInterno) {
            e.preventDefault();
            const diferencia = totalVenta - totalPagado;
            if (diferencia > 0) {
                alert(`⚠️ Falta S/ ${diferencia.toFixed(2)} por completar`);
            } else {
                alert(`⚠️ Exceso de S/ ${Math.abs(diferencia).toFixed(2)}`);
            }
            return false;
        }

        const producto = productoSelect.options[productoSelect.selectedIndex].text;
        const turno = turnoSeleccionado.value == '0' ? 'DÍA' : 'NOCHE';
        const fecha = document.querySelector('[name="fecha_venta"]').value;
        const total = totalSpan.textContent;
        
        const mensaje = `¿Confirmar registro de venta?\n\nProducto: ${producto}\nCantidad: ${cantidad}\nFecha: ${fecha}\nTurno: ${turno}\nTotal: ${total}`;
        
        if (!confirm(mensaje)) {
            e.preventDefault();
            return false;
        }

        console.log('✅ TODAS LAS VALIDACIONES PASARON - ENVIANDO FORMULARIO');
        
        return true;
    });

    // === VERIFICACIÓN DE TURNO CERRADO ===
    const btnSubmit = document.querySelector('button[type="submit"]');
    const avisoTurnoCerrado = document.getElementById('aviso-turno-cerrado');
    const avisoTexto = document.getElementById('aviso-turno-cerrado-texto');

    async function verificarTurnoCerrado() {
        const fechaInput = document.querySelector('input[name="fecha_venta"]');
        const turnoInput = document.querySelector('input[name="turno"]:checked');

        if (!fechaInput?.value || !turnoInput) return;

        const fecha = fechaInput.value;
        const turno = turnoInput.value;
        const turnoNombre = turno == '0' ? 'DÍA' : 'NOCHE';

        try {
            const res = await fetch(`/api/turnos/verificar?fecha=${fecha}&turno=${turno}`);
            const data = await res.json();

            if (data.cerrado) {
                const fechaFormateada = new Date(fecha + 'T00:00:00').toLocaleDateString('es-PE', {day:'2-digit', month:'2-digit', year:'numeric'});
                avisoTexto.textContent = `El turno ${turnoNombre} del ${fechaFormateada} ya fue cerrado y verificado. No se pueden registrar ni modificar datos. Contacta al administrador si necesitas hacer cambios.`;
                avisoTurnoCerrado.classList.remove('hidden');
                btnSubmit.disabled = true;
                btnSubmit.classList.add('opacity-50', 'cursor-not-allowed');
            } else {
                avisoTurnoCerrado.classList.add('hidden');
                btnSubmit.disabled = false;
                btnSubmit.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        } catch (err) {
            console.error('Error verificando turno:', err);
        }
    }

    document.querySelector('input[name="fecha_venta"]')?.addEventListener('change', verificarTurnoCerrado);
    document.querySelectorAll('input[name="turno"]').forEach(r => r.addEventListener('change', verificarTurnoCerrado));

    verificarTurnoCerrado();
});
</script>

<!-- Boxicons -->
<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

@endsection