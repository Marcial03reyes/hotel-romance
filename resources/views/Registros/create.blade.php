@extends('layouts.app')

@section('title', 'Agregar Registro - Hotel Romance')

@section('content')
<div class="container mx-auto py-6 px-4">
    
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">
                    <i class='bx bx-plus-circle mr-2' style="color: #6B8CC7;"></i>
                    Nuevo Registro de Huésped
                </h1>
                <p class="text-gray-600">Registra el ingreso de un nuevo huésped al Hotel Romance</p>
            </div>
        </div>
        
        <div class="flex items-center space-x-4 text-sm text-gray-600">
            <a href="{{ route('registros.index') }}" class="inline-flex items-center hover:text-blue-600 transition-colors">
                <i class='bx bx-arrow-back mr-1'></i>
                Volver a registros
            </a>
            <span>•</span>
            <span class="text-gray-500">Completa todos los campos requeridos</span>
        </div>
    </div>

    <!-- Indicador de pasos -->
    <div class="flex justify-center mb-8">
        <div class="flex items-center space-x-8">
            <div class="step active" id="step1">
                <div class="w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center font-bold mr-2">1</div>
                <span class="text-sm font-medium">Cliente</span>
            </div>
            <div class="step inactive" id="step2">
                <div class="w-8 h-8 bg-gray-300 text-gray-600 rounded-full flex items-center justify-center font-bold mr-2">2</div>
                <span class="text-sm font-medium">Estadía</span>
            </div>
            <div class="step inactive" id="step3">
                <div class="w-8 h-8 bg-gray-300 text-gray-600 rounded-full flex items-center justify-center font-bold mr-2">3</div>
                <span class="text-sm font-medium">Pago</span>
            </div>
        </div>
    </div>

    <!-- Formulario -->
    <form action="{{ route('registros.store') }}" method="POST" id="form-registro" class="space-y-6">
        @csrf

        <div class="grid lg:grid-cols-2 gap-6">
            
            <!-- Información del Cliente -->
            <div class="bg-white p-6 rounded-lg shadow-sm border">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class='bx bx-user mr-2' style="color: #6B8CC7;"></i>
                    Información del Cliente
                </h2>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class='bx bx-id-card mr-1'></i>
                            Documento de Identidad *
                        </label>
                        <div class="flex gap-2">
                            <input name="doc_identidad" id="doc_identidad" type="text" 
                                   class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                   placeholder="DNI o Carnet de Extranjería" 
                                   required maxlength="20">
                            <button type="button" id="btn-verificar" 
                                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-3 rounded-lg font-medium transition-all">
                                <i class='bx bx-search mr-1'></i>
                                Verificar
                            </button>
                        </div>
                        
                        <div class="mt-2" id="cliente-status" style="display: none;">
                            <div id="cliente-message"></div>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class='bx bx-user mr-1'></i>
                            Nombre y Apellido *
                        </label>
                        <div class="flex gap-2">
                            <input name="nombre_apellido" id="nombre_apellido" type="text" 
                                   class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                   placeholder="Nombre completo del huésped" 
                                   maxlength="100">
                            <button type="button" id="btn-guardar-cliente" style="display: none;"
                                    class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-3 rounded-lg font-medium transition-all">
                                <i class='bx bx-plus mr-1'></i>
                                Guardar Cliente
                            </button>
                        </div>
                        <p class="text-xs text-gray-500 mt-1" id="nombre-help">
                            Presiona "Verificar" para buscar el cliente en la base de datos
                        </p>
                    </div>
                </div>
            </div>

            <!-- Información de la Estadía -->
            <div class="bg-white p-6 rounded-lg shadow-sm border">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class='bx bx-bed mr-2' style="color: #6B8CC7;"></i>
                    Detalles de la Estadía
                </h2>
                
                <div class="grid grid-cols-3 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class='bx bx-time mr-1'></i>
                            Hora de Ingreso *
                        </label>
                        <input name="hora_ingreso" type="time" value="{{ now()->format('H:i') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class='bx bx-time-five mr-1'></i>
                            Hora de Salida (Opcional)
                        </label>
                        <input name="hora_salida" type="time" value="{{ old('hora_salida') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <small class="text-xs text-gray-500 mt-1">
                            <i class='bx bx-info-circle mr-1'></i>
                            Se puede registrar después
                        </small>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class='bx bx-calendar mr-1'></i>
                            Fecha de Ingreso *
                        </label>
                        <input name="fecha_ingreso" type="date" value="{{ now()->format('Y-m-d') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               required>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        <i class='bx bx-home mr-1'></i>
                        Habitación *
                    </label>
                    <select name="habitacion" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="">Selecciona una habitación</option>
                        @foreach([201, 202, 203, 301, 302, 303] as $habitacion)
                            <option value="{{ $habitacion }}">Habitación {{ $habitacion }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Información de Pago -->
        <div class="field-group">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class='bx bx-credit-card mr-2 icon-azul'></i>
                    Información de Pago de la Habitación
                </h2>
                <button type="button" id="btn-add-pago" 
                        class="inline-flex items-center bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm transition-all">
                    <i class='bx bx-plus mr-2'></i>
                    Agregar Método de Pago
                </button>
            </div>

            <!-- Tarifa Total -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    <i class='bx bx-dollar mr-1'></i>
                    Tarifa Total de la Habitación *
                </label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 font-medium">S/</span>
                    <input name="monto" id="tarifa_total" type="number" step="0.01" min="0"
                        class="input-field w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg transition-all" 
                        required placeholder="0.00">
                </div>
                <p class="text-xs text-gray-500 mt-1">Monto total que debe pagar el huésped por la habitación</p>
            </div>
        
            <!-- Métodos de Pago -->
            <div id="pagos-wrapper" class="space-y-4 mb-6">
                <!-- Primer método de pago (siempre visible) -->
                <div class="pago-item" data-pago-index="0">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="font-medium text-gray-700">Método de Pago #1</h4>
                    </div>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                <i class='bx bx-wallet mr-1'></i>
                                Método de Pago *
                            </label>
                            <select name="id_met_pago" class="input-field w-full px-4 py-3 border border-gray-300 rounded-lg transition-all metodo-pago" required>
                                <option value="">Selecciona un método</option>
                                <option value="1">Efectivo</option>
                                <option value="2">Tarjeta de Crédito</option>
                                <option value="3">Tarjeta de Débito</option>
                                <option value="4">Yape</option>
                                <option value="5">Plin</option>
                                <option value="6">Transferencia</option>
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
                                    class="input-field w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg transition-all monto-pago" 
                                    placeholder="0.00" required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Resumen de Pagos -->
            <div id="resumen-pagos" class="total-display p-4 rounded-lg mb-6" style="display: none;">
                <div class="flex items-center justify-between">
                    <span class="font-medium">
                        <i class='bx bx-calculator mr-2'></i>
                        Total Pagado:
                    </span>
                    <span class="text-lg font-bold" id="total-pagado">S/ 0.00</span>
                </div>
                <div class="flex items-center justify-between mt-2">
                    <span class="text-sm">Pendiente:</span>
                    <span class="text-sm font-medium" id="total-pendiente">S/ 0.00</span>
                </div>
            </div>

            <!-- Alerta de diferencia -->
            <div id="alerta-diferencia" class="alert-warning p-4 rounded-lg mb-6" style="display: none;">
                <div class="flex items-center">
                    <i class='bx bx-error mr-2 text-lg'></i>
                    <span class="font-medium" id="mensaje-diferencia"></span>
                </div>
            </div>

            <!-- Información de Boleta -->
            <div id="seccion-boleta">
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class='bx bx-receipt mr-1'></i>
                            ¿Requiere Boleta?
                        </label>
                        <div class="flex items-center space-x-6 pt-3">
                            <label class="flex items-center">
                                <input type="radio" name="boleta" value="SI" id="boleta_si"
                                        class="radio-azul focus:ring-blue-500 mr-2">
                                <span class="text-sm font-medium text-gray-700">Sí</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="boleta" value="NO" id="boleta_no" checked
                                        class="radio-azul focus:ring-blue-500 mr-2">
                                <span class="text-sm font-medium text-gray-700">No</span>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Monto a Boletear (solo visible con múltiples pagos y boleta = SÍ) -->
                    <div id="monto-boleta-container" style="display: none;">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class='bx bx-receipt mr-1'></i>
                            Monto a Boletear *
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 font-medium">S/</span>
                            <input name="monto_boleta" id="monto_boleta" type="number" step="0.01" min="0"
                                    class="input-field w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg transition-all" 
                                    placeholder="0.00">
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Especifica cuánto del total se incluirá en la boleta</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Consumos Adicionales -->
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class='bx bx-receipt mr-2' style="color: #6B8CC7;"></i>
                    Consumos Adicionales (Opcional)
                </h2>
                <button type="button" id="btn-add-consumo" 
                        class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm transition-all">
                    <i class='bx bx-plus mr-2'></i>
                    Agregar Consumo
                </button>
            </div>
            
            <div id="consumos-wrapper" class="space-y-4">
                <!-- Los consumos se agregarán dinámicamente aquí -->
            </div>
            
            <div class="text-sm text-gray-500 mt-2">
                <i class='bx bx-info-circle mr-1'></i>
                Puedes agregar productos que el huésped haya consumido durante su estadía
            </div>
        </div>

        <!-- Botones de acción -->
        <div class="flex items-center justify-between pt-6 border-t border-gray-200">
            <a href="{{ route('registros.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-lg font-medium transition-all">
                <i class='bx bx-x mr-2'></i>
                Cancelar
            </a>
            
            <button type="submit" 
                    class="bg-blue-500 hover:bg-blue-600 text-white px-8 py-3 rounded-lg font-medium shadow-lg transition-all">
                <i class='bx bx-save mr-2'></i>
                Guardar Registro
            </button>
        </div>
    </form>
</div>

<!-- CSS Personalizado -->
<style>
.step {
    display: flex;
    align-items: center;
    transition: all 0.3s ease;
}

.step.active .w-8 {
    background-color: #3b82f6;
    color: white;
}

.step.completed .w-8 {
    background-color: #10b981;
    color: white;
}

.step.inactive .w-8 {
    background-color: #d1d5db;
    color: #6b7280;
}

.cliente-status {
    padding: 0.75rem;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    font-weight: 500;
}

.cliente-found {
    background-color: #dcfce7;
    color: #166534;
    border: 1px solid #bbf7d0;
}

.cliente-new {
    background-color: #fef3c7;
    color: #92400e;
    border: 1px solid #fde68a;
}

.consumo-item {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 0.5rem;
    padding: 1rem;
    margin-bottom: 1rem;
}

.pago-item {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 0.5rem;
    padding: 1rem;
    margin-bottom: 1rem;
    position: relative;
}

.total-display {
    background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
    border: 2px solid #0ea5e9;
    color: #0c4a6e;
}

.alert-warning {
    background: #fef3c7;
    border: 1px solid #f59e0b;
    color: #92400e;
}

.radio-azul {
    accent-color: #6B8CC7;
}

</style>

<!-- JavaScript Principal -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Variables principales
    const docInput = document.getElementById('doc_identidad');
    const nomInput = document.getElementById('nombre_apellido');
    const clienteStatus = document.getElementById('cliente-status');
    const clienteMessage = document.getElementById('cliente-message');
    const btnVerificar = document.getElementById('btn-verificar');
    const btnGuardarCliente = document.getElementById('btn-guardar-cliente');
    const nombreHelp = document.getElementById('nombre-help');
    
    let clienteExistente = false;
    let clienteVerificado = false;
    let clienteNuevoGuardado = false;
    let documentoOriginal = '';

    // Variables de pago
    const tarifaTotalInput = document.getElementById('tarifa_total');
    const pagosWrapper = document.getElementById('pagos-wrapper');
    const btnAddPago = document.getElementById('btn-add-pago');
    const resumenPagos = document.getElementById('resumen-pagos');
    const totalPagadoSpan = document.getElementById('total-pagado');
    const totalPendienteSpan = document.getElementById('total-pendiente');
    const alertaDiferencia = document.getElementById('alerta-diferencia');
    const mensajeDiferencia = document.getElementById('mensaje-diferencia');
    
    // Variables de boleta
    const boletaSi = document.getElementById('boleta_si');
    const boletaNo = document.getElementById('boleta_no');
    const montoBletaContainer = document.getElementById('monto-boleta-container');
    const montoBletaInput = document.getElementById('monto_boleta');
    
    let pagoIndex = 1;

    // === FUNCIÓN PARA RESETEAR ESTADO ===
    function resetearEstadoCliente() {
        clienteExistente = false;
        clienteVerificado = false;
        clienteNuevoGuardado = false;
        
        nomInput.value = '';
        nomInput.readOnly = false;
        nomInput.style.backgroundColor = '';
        nomInput.style.borderColor = '';
        
        clienteStatus.style.display = 'none';
        btnGuardarCliente.style.display = 'none';
        
        btnVerificar.disabled = false;
        btnVerificar.className = 'bg-blue-500 hover:bg-blue-600 text-white px-4 py-3 rounded-lg font-medium transition-all';
        btnVerificar.innerHTML = '<i class="bx bx-search mr-1"></i> Verificar';
        
        nombreHelp.textContent = 'Presiona "Verificar" para buscar el cliente en la base de datos';
        updateStepStatus(1, 'active');
    }

    // === FUNCIONES DE PAGO ===
    function getNextPagoNumber() {
        // Obtener el número más alto actual y sumar 1
        const existingNumbers = Array.from(document.querySelectorAll('.pago-item')).map(item => {
            const text = item.querySelector('h4').textContent;
            const match = text.match(/Método de Pago #(\d+)/);
            return match ? parseInt(match[1]) : 0;
        });
        
        if (existingNumbers.length === 0) return 2; // Si no hay elementos, el siguiente es #2
        return Math.max(...existingNumbers) + 1;
    }

    function addPagoRow() {
        const nextNumber = getNextPagoNumber();
        
        const div = document.createElement('div');
        div.className = 'pago-item';
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
                    <select name="pagos[${pagoIndex}][id_met_pago]" class="input-field w-full px-4 py-3 border border-gray-300 rounded-lg transition-all metodo-pago" required>
                        <option value="">Selecciona un método</option>
                        <option value="1">Efectivo</option>
                        <option value="2">Tarjeta de Crédito</option>
                        <option value="3">Tarjeta de Débito</option>
                        <option value="4">Yape</option>
                        <option value="5">Plin</option>
                        <option value="6">Transferencia</option>
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
                            class="input-field w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg transition-all monto-pago" 
                            required placeholder="0.00">
                    </div>
                </div>
            </div>
        `;
        pagosWrapper.appendChild(div);
        pagoIndex++;
        
        // Actualizar eventos
        updatePagoEvents();
        updateBoletaVisibility();
    }

    function removePagoRow(button) {
        button.closest('.pago-item').remove();
        updatePagoEvents();
        updateBoletaVisibility();
        calcularTotales();
    }

    function updatePagoEvents() {
        // Remover eventos anteriores y agregar nuevos
        document.querySelectorAll('.monto-pago').forEach(input => {
            input.removeEventListener('input', calcularTotales);
            input.addEventListener('input', calcularTotales);
        });
    }

    function calcularTotales() {
        const tarifaTotal = parseFloat(tarifaTotalInput.value) || 0;
        let totalPagado = 0;
        
        // Sumar todos los montos de pago
        document.querySelectorAll('.monto-pago').forEach(input => {
            const monto = parseFloat(input.value) || 0;
            totalPagado += monto;
        });
        
        const pendiente = tarifaTotal - totalPagado;
        
        // Mostrar resumen solo si hay tarifa total
        if (tarifaTotal > 0) {
            resumenPagos.style.display = 'block';
            totalPagadoSpan.textContent = `S/ ${totalPagado.toFixed(2)}`;
            totalPendienteSpan.textContent = `S/ ${pendiente.toFixed(2)}`;
            
            // Mostrar alerta si hay diferencia
            if (Math.abs(pendiente) > 0.01) {
                alertaDiferencia.style.display = 'block';
                if (pendiente > 0) {
                    mensajeDiferencia.textContent = `Falta S/ ${pendiente.toFixed(2)} por completar el pago total`;
                    alertaDiferencia.style.backgroundColor = '#fef3c7';
                    alertaDiferencia.style.borderColor = '#f59e0b';
                    alertaDiferencia.style.color = '#92400e';
                } else {
                    mensajeDiferencia.textContent = `Hay un exceso de S/ ${Math.abs(pendiente).toFixed(2)} en los pagos`;
                    alertaDiferencia.style.backgroundColor = '#fef2f2';
                    alertaDiferencia.style.borderColor = '#ef4444';
                    alertaDiferencia.style.color = '#dc2626';
                }
            } else {
                alertaDiferencia.style.display = 'none';
            }
        } else {
            resumenPagos.style.display = 'none';
            alertaDiferencia.style.display = 'none';
        }
        
        // Actualizar límite del monto de boleta
        if (montoBletaInput) {
            montoBletaInput.max = totalPagado;
            if (parseFloat(montoBletaInput.value) > totalPagado) {
                montoBletaInput.value = totalPagado.toFixed(2);
            }
        }
    }

    function updateBoletaVisibility() {
        const totalPagos = document.querySelectorAll('.pago-item').length;
        const requiereBoleta = boletaSi.checked;
        
        // Mostrar campo de monto boleta solo si:
        // 1. Hay más de un método de pago Y
        // 2. Se requiere boleta
        if (totalPagos > 1 && requiereBoleta) {
            montoBletaContainer.style.display = 'block';
            montoBletaInput.required = true;
        } else {
            montoBletaContainer.style.display = 'none';
            montoBletaInput.required = false;
            montoBletaInput.value = '';
        }
    }

    // === DETECTAR CAMBIOS EN DNI ===
    docInput.addEventListener('input', function() {
        const valorActual = this.value.trim();
        if (valorActual !== documentoOriginal && clienteVerificado) {
            resetearEstadoCliente();
        }
    });

    // === VERIFICAR CLIENTE ===
    btnVerificar.addEventListener('click', async function() {
        const doc = docInput.value.trim();
        
        if (!doc) {
            alert('❌ Ingresa un documento de identidad primero');
            docInput.focus();
            return;
        }
        
        documentoOriginal = doc;
        
        btnVerificar.disabled = true;
        btnVerificar.innerHTML = '<i class="bx bx-loader-alt mr-1"></i> Verificando...';
        
        try {
            const response = await fetch(`{{ route('registros.lookup-cliente') }}?doc=${doc}`);
            const data = await response.json();
            
            setTimeout(() => {
                if (data.ok && data.nombre_apellido) {
                    // Cliente encontrado
                    clienteExistente = true;
                    clienteVerificado = true;
                    
                    nomInput.value = data.nombre_apellido;
                    nomInput.readOnly = true;
                    nomInput.style.backgroundColor = '#f0fdf4';
                    nomInput.style.borderColor = '#10b981';
                    
                    clienteStatus.className = 'cliente-status cliente-found';
                    clienteStatus.style.display = 'block';
                    clienteMessage.innerHTML = `
                        <div class="flex items-center">
                            <i class='bx bx-check-circle text-green-600 text-lg mr-2'></i>
                            <div>
                                <strong class="text-green-800">Cliente encontrado:</strong> ${data.nombre_apellido}
                                <br><small class="text-green-600">✅ Información completada automáticamente</small>
                            </div>
                        </div>
                    `;
                    nombreHelp.textContent = '✅ Cliente existente - Campo completado automáticamente';
                    updateStepStatus(1, 'completed');
                    
                    btnVerificar.innerHTML = '<i class="bx bx-check mr-1"></i> Verificado';
                    btnVerificar.className = 'bg-green-500 text-white px-4 py-3 rounded-lg font-medium cursor-default';
                    
                } else {
                    // Cliente no encontrado
                    clienteExistente = false;
                    clienteVerificado = true;
                    
                    nomInput.value = '';
                    nomInput.readOnly = false;
                    nomInput.style.backgroundColor = '#fffbeb';
                    nomInput.style.borderColor = '#f59e0b';
                    nomInput.focus();
                    
                    clienteStatus.className = 'cliente-status cliente-new';
                    clienteStatus.style.display = 'block';
                    clienteMessage.innerHTML = `
                        <div class="flex items-center">
                            <i class='bx bx-user-plus text-orange-600 text-lg mr-2'></i>
                            <div>
                                <strong class="text-orange-800">Cliente nuevo</strong>
                                <br><small class="text-orange-600">⚠️ Completa el nombre y guarda el cliente</small>
                            </div>
                        </div>
                    `;
                    nombreHelp.textContent = '⚠️ Cliente nuevo - Completa el nombre y presiona "Guardar Cliente"';
                    btnGuardarCliente.style.display = 'flex';
                    
                    btnVerificar.innerHTML = '<i class="bx bx-user-plus mr-1"></i> Cliente Nuevo';
                    btnVerificar.className = 'bg-orange-500 text-white px-4 py-3 rounded-lg font-medium cursor-default';
                }
            }, 1000);
            
        } catch (error) {
            console.error('Error al verificar cliente:', error);
            alert('❌ Error al verificar el cliente. Intenta nuevamente.');
            resetearEstadoCliente();
        }
    });

    // === GUARDAR CLIENTE NUEVO ===
    btnGuardarCliente.addEventListener('click', async function() {
        const doc = docInput.value.trim();
        const nombre = nomInput.value.trim();
        
        if (!doc || !nombre) {
            alert('❌ Completa el documento y nombre antes de guardar');
            if (!nombre) nomInput.focus();
            return;
        }
        
        if (nombre.length < 3) {
            alert('❌ El nombre debe tener al menos 3 caracteres');
            nomInput.focus();
            return;
        }
        
        btnGuardarCliente.disabled = true;
        btnGuardarCliente.innerHTML = '<i class="bx bx-loader-alt mr-1"></i> Guardando...';
        
        try {
            const response = await fetch('{{ route("clientes.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    doc_identidad: doc,
                    nombre_apellido: nombre
                })
            });
            
            const data = await response.json();
            
            setTimeout(() => {
                if (response.ok && data.ok) {
                    // Cliente guardado
                    clienteNuevoGuardado = true;
                    clienteExistente = true;
                    
                    nomInput.readOnly = true;
                    nomInput.style.backgroundColor = '#f0fdf4';
                    nomInput.style.borderColor = '#10b981';
                    
                    clienteStatus.className = 'cliente-status cliente-found';
                    clienteMessage.innerHTML = `
                        <div class="flex items-center">
                            <i class='bx bx-check-circle text-green-600 text-lg mr-2'></i>
                            <div>
                                <strong class="text-green-800">Cliente guardado:</strong> ${nombre}
                                <br><small class="text-green-600">✅ Cliente registrado en la base de datos</small>
                            </div>
                        </div>
                    `;
                    nombreHelp.textContent = '✅ Cliente registrado exitosamente';
                    btnGuardarCliente.style.display = 'none';
                    updateStepStatus(1, 'completed');
                    
                } else {
                    alert('❌ Error al guardar el cliente: ' + (data.message || 'Error desconocido'));
                    btnGuardarCliente.disabled = false;
                    btnGuardarCliente.innerHTML = '<i class="bx bx-plus mr-1"></i> Guardar Cliente';
                }
            }, 1000);
            
        } catch (error) {
            console.error('Error al guardar cliente:', error);
            alert('❌ Error de conexión al guardar el cliente');
            btnGuardarCliente.disabled = false;
            btnGuardarCliente.innerHTML = '<i class="bx bx-plus mr-1"></i> Guardar Cliente';
        }
    });

    // === EVENTOS DE PAGO ===
    btnAddPago.addEventListener('click', addPagoRow);
    
    tarifaTotalInput.addEventListener('input', calcularTotales);

    // Eventos iniciales para el primer pago
    updatePagoEvents();
    
    // Eventos de boleta
    boletaSi.addEventListener('change', updateBoletaVisibility);
    boletaNo.addEventListener('change', updateBoletaVisibility);
    
    // Hacer función removePagoRow global
    window.removePagoRow = removePagoRow;

    // === CONSUMOS ===
    const consumosWrapper = document.getElementById('consumos-wrapper');
    const btnAddConsumo = document.getElementById('btn-add-consumo');
    let consumoIndex = 0;

    function addConsumoRow() {
        const div = document.createElement('div');
        div.className = 'consumo-item';
        div.innerHTML = `
            <div class="flex items-center justify-between mb-3">
                <h4 class="font-medium text-gray-700">Consumo #${consumoIndex + 1}</h4>
                <button type="button" onclick="this.parentElement.parentElement.remove()" 
                        class="text-red-500 hover:text-red-700 transition-colors">
                    <i class='bx bx-trash'></i>
                </button>
            </div>
            <div class="grid md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Producto</label>
                    <select name="consumo[${consumoIndex}][id_prod_bod]" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        <option value="">Seleccionar</option>
                        @foreach($productos as $producto)
                            <option value="{{ $producto->id_prod_bod }}">{{ $producto->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cantidad</label>
                    <input name="consumo[${consumoIndex}][cantidad]" type="number" min="1" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="1">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Precio Unit.</label>
                    <input name="consumo[${consumoIndex}][precio_unitario]" type="number" step="0.01" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="0.00">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Método Pago</label>
                    <select name="consumo[${consumoIndex}][id_met_pago]" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        <option value="">Seleccionar</option>
                        @foreach($metodos as $metodo)
                            <option value="{{ $metodo->id_met_pago }}">{{ $metodo->met_pago }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        `;
        consumosWrapper.appendChild(div);
        consumoIndex++;
    }

    btnAddConsumo.addEventListener('click', addConsumoRow);

    // === SISTEMA DE PASOS ===
    function updateStepStatus(stepNumber, status) {
        const step = document.getElementById(`step${stepNumber}`);
        if (step) {
            step.className = `step ${status}`;
        }
    }

    // === VALIDACIÓN FINAL ===
    document.getElementById('form-registro').addEventListener('submit', function(e) {
        if (!clienteVerificado) {
            e.preventDefault();
            alert('❌ Debes verificar el cliente primero');
            btnVerificar.focus();
            return false;
        }
        
        if (!clienteExistente && !clienteNuevoGuardado) {
            e.preventDefault();
            alert('❌ Para un cliente nuevo, debes guardarlo primero');
            btnGuardarCliente.focus();
            return false;
        }
        
        const camposBasicos = ['doc_identidad', 'nombre_apellido', 'hora_ingreso', 'fecha_ingreso', 'habitacion'];
        for (let campo of camposBasicos) {
            const input = document.querySelector(`[name="${campo}"]`);
            if (!input.value.trim()) {
                e.preventDefault();
                alert(`❌ El campo ${campo.replace('_', ' ')} es obligatorio`);
                input.focus();
                return false;
            }
        }
        
        // Validar tarifa
        const tarifaTotal = parseFloat(tarifaTotalInput.value) || 0;
        if (!tarifaTotal || tarifaTotal <= 0) {
            e.preventDefault();
            alert('❌ La tarifa total debe ser mayor a 0');
            tarifaTotalInput.focus();
            return false;
        }
        
        // Validar pagos
        let totalPagado = 0;
        let pagosValidos = 0;
        
        document.querySelectorAll('.pago-item').forEach((item, index) => {
            const metodo = item.querySelector('.metodo-pago').value;
            const monto = parseFloat(item.querySelector('.monto-pago').value) || 0;
            
            if (metodo && monto > 0) {
                totalPagado += monto;
                pagosValidos++;
            }
        });
        
        if (pagosValidos === 0) {
            e.preventDefault();
            alert('❌ Debe especificar al menos un método de pago válido');
            return false;
        }
        
        if (Math.abs(tarifaTotal - totalPagado) > 0.01) {
            e.preventDefault();
            const diferencia = tarifaTotal - totalPagado;
            if (diferencia > 0) {
                alert(`❌ Falta S/ ${diferencia.toFixed(2)} por completar el pago total`);
            } else {
                alert(`❌ Hay un exceso de S/ ${Math.abs(diferencia).toFixed(2)} en los pagos`);
            }
            return false;
        }
        
        const nombre = nomInput.value.trim();
        const habitacion = document.querySelector('[name="habitacion"]').value;
        const monto = document.querySelector('[name="monto"]').value;
        
        const mensaje = `✅ ¿Confirmar registro?\n\nCliente: ${nombre}\nHabitación: ${habitacion}\nMonto: S/ ${monto}`;
        
        if (!confirm(mensaje)) {
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