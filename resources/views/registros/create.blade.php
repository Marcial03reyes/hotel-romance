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
            <span>ⓘ</span>
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
                <span class="text-sm font-medium">Estadi­a</span>
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

        <div class="space-y-6">
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

                    <!-- Campos adicionales opcionales -->
                    <div class="grid md:grid-cols-3 gap-4 mt-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                <i class='bx bx-venus-mars mr-1'></i>
                                Sexo
                            </label>
                            <select name="sexo" id="sexo" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Seleccionar</option>
                                <option value="F">F</option>
                                <option value="M">M</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                <i class='bx bx-flag mr-1'></i>
                                Nacionalidad
                            </label>
                            <input name="nacionalidad" id="nacionalidad" type="text" maxlength="50"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Ej: Peruana">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                <i class='bx bx-heart mr-1'></i>
                                Estado Civil
                            </label>
                            <select name="estado_civil" id="estado_civil" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Seleccionar</option>
                                <option value="Soltero">S</option>
                                <option value="Casado">C</option>
                                <option value="Divorciado">D</option>
                                <option value="Viudo">V</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                <i class='bx bx-calendar mr-1'></i>
                                Fecha de Nacimiento
                            </label>
                            <input name="fecha_nacimiento" id="fecha_nacimiento" type="date"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">  
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                <i class='bx bx-map mr-1'></i>
                                Lugar de Nacimiento
                            </label>
                            <input name="lugar_nacimiento" id="lugar_nacimiento" type="text" maxlength="100"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Ciudad, país...">
                        </div>
                    </div>

                </div>
            </div>

            <!-- Selector de Turno -->
            <div class="bg-white p-6 rounded-lg shadow-sm border">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class='bx bx-sun mr-2' style="color: #6B8CC7;"></i>
                    Turno de Trabajo
                </h2>
                
                <div class="flex space-x-3">
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="turno" value="0" class="sr-only turno-radio" required>
                        <div class="turno-button turno-dia border-2 border-gray-200 rounded-lg p-3 text-center transition-all hover:border-yellow-400 hover:bg-yellow-50">
                            <i class='bx bx-sun text-2xl mb-1 text-yellow-600'></i>
                            <div class="font-semibold text-gray-800 text-sm">DÍA</div>
                        </div>
                    </label>
                    
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="turno" value="1" class="sr-only turno-radio" required>
                        <div class="turno-button turno-noche border-2 border-gray-200 rounded-lg p-3 text-center transition-all hover:border-blue-400 hover:bg-blue-50">
                            <i class='bx bx-moon text-2xl mb-1 text-blue-600'></i>
                            <div class="font-semibold text-gray-800 text-sm">NOCHE</div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Campos auxiliares para TURNO NOCHE -->
            <div id="campos-auxiliares-noche" class="bg-yellow-50 border border-yellow-200 rounded-lg p-6" style="display: none;">
                <h3 class="text-lg font-semibold text-yellow-800 mb-4 flex items-center">
                    <i class='bx bx-moon mr-2'></i>
                    Campos Auxiliares - Turno Noche
                </h3>
                <p class="text-sm text-yellow-700 mb-4">
                    <i class='bx bx-info-circle mr-1'></i>
                    Para turno NOCHE, registra la fecha y hora real de ingreso del huésped
                </p>
                
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-yellow-800 mb-1">
                            <i class='bx bx-calendar mr-1'></i>
                            Fecha Real de Ingreso *
                        </label>
                        <input name="fecha_ingreso_real" id="fecha_ingreso_real" type="date" 
                            class="w-full px-4 py-3 border border-yellow-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 bg-white">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-yellow-800 mb-1">
                            <i class='bx bx-time mr-1'></i>
                            Hora Real de Ingreso *
                        </label>
                        <input name="hora_ingreso_real" id="hora_ingreso_real" type="time" 
                            class="w-full px-4 py-3 border border-yellow-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 bg-white">
                    </div>
                </div>
            </div>

            <!-- Detalles de la Estadía -->
            <div class="bg-white p-6 rounded-lg shadow-sm border col-span-2">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class='bx bx-bed mr-2' style="color: #6B8CC7;"></i>
                    Detalles de la Estadía
                </h2>
                
                <div class="grid md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class='bx bx-door-open mr-1'></i>
                            Habitación *
                        </label>
                        <select name="habitacion" id="habitacion" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                            <option value="">Seleccionar habitación</option>
                            @foreach($habitaciones as $hab)
                                <option value="{{ $hab }}">{{ $hab }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class='bx bx-car mr-1'></i>
                            Placa del Vehículo
                        </label>
                        <input name="placa_vehiculo" id="placa_vehiculo" type="text" maxlength="20"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Ej: ABC-123">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class='bx bx-calendar mr-1'></i>
                            Fecha de Ingreso *
                        </label>
                        <input name="fecha_ingreso" id="fecha_ingreso" type="date" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class='bx bx-time mr-1'></i>
                            Hora de Ingreso *
                        </label>
                        <input name="hora_ingreso" id="hora_ingreso" type="time" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class='bx bx-calendar-check mr-1'></i>
                            Fecha de Salida
                        </label>
                        <input name="fecha_salida" id="fecha_salida" type="date"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class='bx bx-time mr-1'></i>
                            Hora de Salida
                        </label>
                        <input name="hora_salida" id="hora_salida" type="time" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class='bx bx-note mr-1'></i>
                            Observaciones
                        </label>
                        <textarea name="obs" id="obs" rows="3" maxlength="1000"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Comentarios adicionales..."></textarea>
                    </div>
                </div>
            </div>

        </div>  

        <!-- Información de Pago -->
        <div class="bg-white p-6 rounded-lg shadow-sm border">
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
                <!-- Primer mÃ©todo de pago (siempre visible) -->
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
                                <span class="text-sm font-medium text-gray-700">SÍ</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="boleta" value="NO" id="boleta_no" checked
                                        class="radio-azul focus:ring-blue-500 mr-2">
                                <span class="text-sm font-medium text-gray-700">No</span>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Monto a Boletear (solo visible con mÃºltiples pagos y boleta = SÃ) -->
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
                        <p class="text-xs text-gray-500 mt-1">Especifica cuánto del total se incluirÃ¡ en la boleta</p>
                    </div>
                </div>
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

</style>

<!-- JavaScript Principal -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // === FUNCIONALIDAD SELECTOR DE TURNO ===
    const turnoRadios = document.querySelectorAll('.turno-radio');
    turnoRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            // Remover selección previa
            document.querySelectorAll('.turno-button').forEach(btn => {
                btn.classList.remove('selected');
            });
            
            if (this.checked) {
                this.nextElementSibling.classList.add('selected');
                
                // Lógica de campos auxiliares
                const camposAuxiliares = document.getElementById('campos-auxiliares-noche');
                const fechaRealInput = document.getElementById('fecha_ingreso_real');
                const horaRealInput = document.getElementById('hora_ingreso_real');
                
                if (this.value === '1') { // Turno NOCHE
                    camposAuxiliares.style.display = 'block';
                    fechaRealInput.required = true;
                    horaRealInput.required = true;
                } else { // Turno DÍA
                    camposAuxiliares.style.display = 'none';
                    fechaRealInput.required = false;
                    horaRealInput.required = false;
                    fechaRealInput.value = '';
                    horaRealInput.value = '';
                }
            }
        });
    });
    
    // Auto-seleccionar turno basado en hora actual (opcional)
    const horaActual = new Date().getHours();
    const turnoAutomatico = (horaActual >= 6 && horaActual < 18) ? '0' : '1';
    
    // Sugerir turno basado en la hora, pero no forzar selecciÃ³n
    const radioSugerido = document.querySelector(`input[name="turno"][value="${turnoAutomatico}"]`);
    if (radioSugerido) {
        radioSugerido.click(); // Auto-seleccionar el turno sugerido
    }

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
    
    let pagoIndex = 0;

    // Referencias a campos adicionales
    const estadoCivilSelect = document.getElementById('estado_civil');
    const fechaNacimientoInput = document.getElementById('fecha_nacimiento');
    const lugarNacimientoInput = document.getElementById('lugar_nacimiento');
    const sexoSelect = document.getElementById('sexo');
    const nacionalidadInput = document.getElementById('nacionalidad');

    // === FUNCIóN PARA BLOQUEAR/DESBLOQUEAR CAMPOS ADICIONALES ===
    function bloquearCamposAdicionales(bloquear = true) {
        console.log('Bloqueando campos:', bloquear); // â† AGREGAR PARA DEBUG
        const campos = [estadoCivilSelect, fechaNacimientoInput, lugarNacimientoInput, sexoSelect, nacionalidadInput];
        console.log('Campos encontrados:', campos.map(c => c ? c.id : 'null')); // â† AGREGAR PARA DEBUG
        
        campos.forEach(campo => {
            if (campo) {
                if (bloquear) {
                    // Bloquear campos
                    campo.disabled = true;
                    campo.style.backgroundColor = '#f8fafc';
                    campo.style.borderColor = '#C8D7ED';
                    campo.style.color = '#6b7280';
                    campo.style.cursor = 'not-allowed';
                    console.log('Campo bloqueado:', campo.id);
                } else {
                    // Desbloquear campos
                    campo.disabled = false;
                    campo.style.backgroundColor = '';
                    campo.style.borderColor = '';
                    campo.style.color = '';
                    campo.style.cursor = '';
                    console.log('Campo desbloqueado:', campo.id); // â† AGREGAR PARA DEBUG
                }
            } else {
                console.log('Campo no encontrado'); // â† AGREGAR PARA DEBUG
            }
        });
    }

    // === FUNCIÃ“N PARA RESETEAR ESTADO ===
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

        bloquearCamposAdicionales(false); // Desbloquear campos adicionales
    }

    // === FUNCIONES DE PAGO ===
    function getNextPagoNumber() {
        // Obtener el nÃºmero mÃ¡s alto actual y sumar 1
        const existingNumbers = Array.from(document.querySelectorAll('.pago-item')).map(item => {
            const text = item.querySelector('h4').textContent;
            const match = text.match(/MÃ©todo de Pago #(\d+)/);
            return match ? parseInt(match[1]) : 0;
        });
        
        if (existingNumbers.length === 0) return 2; // Si no hay elementos, el siguiente es #2
        return Math.max(...existingNumbers) + 1;
    }

    // DEBUG: Verificar qué se genera al agregar un pago
    function addPagoRow() {
        const nextNumber = getNextPagoNumber();
        const selectOriginal = document.querySelector('select[name="id_met_pago"]');
        const opcionesHTML = selectOriginal.innerHTML;
        
        // NO incrementar aquí todavía
        
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
                        ${opcionesHTML}
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
        
        // INCREMENTAR DESPUÉS de usar
        pagoIndex++;
        
        console.log('Pago agregado. Index usado:', pagoIndex - 1);
        
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
        
        // Actualizar lÃ­mite del monto de boleta
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
        // 1. Hay mÃ¡s de un mÃ©todo de pago Y
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
            alert('âŒ Ingresa un documento de identidad primero');
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

                    bloquearCamposAdicionales(true);
                    
                    clienteStatus.className = 'cliente-status cliente-found';
                    clienteStatus.style.display = 'block';
                    clienteMessage.innerHTML = `
                        <div class="flex items-center">
                            <i class='bx bx-check-circle text-green-600 text-lg mr-2'></i>
                            <div>
                                <strong class="text-green-800">Cliente encontrado:</strong> ${data.nombre_apellido}
                                <br><small class="text-green-600">… Información completada automÃ¡ticamente</small>
                            </div>
                        </div>
                    `;
                    nombreHelp.textContent = '… Cliente existente - Campo completado automÃ¡ticamente';
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

                    bloquearCamposAdicionales(false);
                    
                    clienteStatus.className = 'cliente-status cliente-new';
                    clienteStatus.style.display = 'block';
                    clienteMessage.innerHTML = `
                        <div class="flex items-center">
                            <i class='bx bx-user-plus text-orange-600 text-lg mr-2'></i>
                            <div>
                                <strong class="text-orange-800">Cliente nuevo</strong>
                                <br><small class="text-orange-600">âš ï¸ Completa el nombre y guarda el cliente</small>
                            </div>
                        </div>
                    `;
                    nombreHelp.textContent = 'âš ï¸ Cliente nuevo - Completa el nombre y presiona "Guardar Cliente"';
                    btnGuardarCliente.style.display = 'flex';
                    
                    btnVerificar.innerHTML = '<i class="bx bx-user-plus mr-1"></i> Cliente Nuevo';
                    btnVerificar.className = 'bg-orange-500 text-white px-4 py-3 rounded-lg font-medium cursor-default';
                }
            }, 1000);
            
        } catch (error) {
            console.error('Error al verificar cliente:', error);
            alert('âŒ Error al verificar el cliente. Intenta nuevamente.');
            resetearEstadoCliente();
        }
    });

    // === GUARDAR CLIENTE NUEVO ===
    btnGuardarCliente.addEventListener('click', async function() {
        const doc = docInput.value.trim();
        const nombre = nomInput.value.trim();
        
        if (!doc || !nombre) {
            alert('âŒ Completa el documento y nombre antes de guardar');
            if (!nombre) nomInput.focus();
            return;
        }
        
        if (nombre.length < 3) {
            alert('âŒ El nombre debe tener al menos 3 caracteres');
            nomInput.focus();
            return;
        }

        // Obtener valores de campos adicionales
        const estadoCivil = estadoCivilSelect.value;
        const fechaNacimiento = fechaNacimientoInput.value;
        const lugarNacimiento = lugarNacimientoInput.value;
        
        btnGuardarCliente.disabled = true;
        btnGuardarCliente.innerHTML = '<i class="bx bx-loader-alt mr-1"></i> Guardando...';
        
        try {
            const response = await fetch('{{ route("clientes.store-ajax") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    doc_identidad: doc,
                    nombre_apellido: nombre,
                    estado_civil: estadoCivilSelect.value,
                    fecha_nacimiento: fechaNacimientoInput.value, 
                    lugar_nacimiento: lugarNacimientoInput.value
                })
            });
            
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers.get('content-type'));
            
            let data;
            const contentType = response.headers.get('content-type');
            
            if (contentType && contentType.includes('application/json')) {
                data = await response.json();
                console.log('Response data:', data);
            } else {
                // Si no es JSON, leer como texto para debug
                const textResponse = await response.text();
                console.log('Non-JSON response:', textResponse);
                throw new Error('El servidor no devolviÃ³ una respuesta JSON vÃ¡lida');
            }
            
            setTimeout(() => {
                if (response.ok && data && data.ok) {
                    // Cliente guardado exitosamente
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
                                <br><small class="text-green-600">âœ… Cliente registrado en la base de datos</small>
                            </div>
                        </div>
                    `;
                    nombreHelp.textContent = 'âœ… Cliente registrado exitosamente';
                    btnGuardarCliente.style.display = 'none';
                    updateStepStatus(1, 'completed');
                    
                } else {
                    alert('âŒ Error al guardar el cliente: ' + (data?.message || 'Error desconocido'));
                    btnGuardarCliente.disabled = false;
                    btnGuardarCliente.innerHTML = '<i class="bx bx-plus mr-1"></i> Guardar Cliente';
                }
            }, 1000);
            
        } catch (error) {
            console.error('Error completo:', error);
            alert('Error de conexión al guardar el cliente. Revisa la consola para más detalles.');
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
    
    // Hacer funciÃ³n removePagoRow global
    window.removePagoRow = removePagoRow;

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
            alert('Debes verificar el cliente primero');
            btnVerificar.focus();
            return false;
        }
        
        if (!clienteExistente && !clienteNuevoGuardado) {
            e.preventDefault();
            alert('Para un cliente nuevo, debes guardarlo primero');
            btnGuardarCliente.focus();
            return false;
        }
        
        const camposBasicos = ['doc_identidad', 'nombre_apellido', 'hora_ingreso', 'fecha_ingreso', 'habitacion'];
        for (let campo of camposBasicos) {
            const input = document.querySelector(`[name="${campo}"]`);
            if (!input.value.trim()) {
                e.preventDefault();
                alert(`El campo ${campo.replace('_', ' ')} es obligatorio`);
                input.focus();
                return false;
            }
        }

        const turnoSeleccionado = document.querySelector('input[name="turno"]:checked');
        if (!turnoSeleccionado) {
            e.preventDefault();
            alert('Debes seleccionar un turno (DÍA o NOCHE)');
            return false;
        }

        if (turnoSeleccionado.value === '1') {
            const fechaReal = document.getElementById('fecha_ingreso_real').value;
            const horaReal = document.getElementById('hora_ingreso_real').value;
            
            if (!fechaReal || !horaReal) {
                e.preventDefault();
                alert('⚠ Para turno NOCHE debes completar la fecha y hora real');
                return false;
            }
        }
        
        const tarifaTotal = parseFloat(tarifaTotalInput.value) || 0;
        if (!tarifaTotal || tarifaTotal <= 0) {
            e.preventDefault();
            alert('La tarifa total debe ser mayor a 0');
            tarifaTotalInput.focus();
            return false;
        }
        
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
        
        if (pagosValidos === 0) {
            e.preventDefault();
            alert('Debe especificar al menos un método de pago válido');
            return false;
        }
        
        if (Math.abs(tarifaTotal - totalPagado) > 0.01) {
            e.preventDefault();
            const diferencia = tarifaTotal - totalPagado;
            if (diferencia > 0) {
                alert(`Falta S/ ${diferencia.toFixed(2)} por completar el pago total`);
            } else {
                alert(`Hay un exceso de S/ ${Math.abs(diferencia).toFixed(2)} en los pagos`);
            }
            return false;
        }
        
        const nombre = nomInput.value.trim();
        const habitacion = document.querySelector('[name="habitacion"]').value;
        const monto = document.querySelector('[name="monto"]').value;
        
        if (!confirm(`¿Confirmar registro?\n\nCliente: ${nombre}\nHabitación: ${habitacion}\nMonto: S/ ${monto}`)) {
            e.preventDefault();
            return false;
        }
        
        // Si todo está OK, permitir envío
        return true;
    });

    // Inicializar estado de campos auxiliares
    const turnoPreseleccionado = document.querySelector('input[name="turno"]:checked');
    if (turnoPreseleccionado && turnoPreseleccionado.value === '1') {
        document.getElementById('campos-auxiliares-noche').style.display = 'block';
        document.getElementById('fecha_ingreso_real').required = true;
        document.getElementById('hora_ingreso_real').required = true;
    }
});
</script>

<!-- Boxicons -->
<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

@endsection