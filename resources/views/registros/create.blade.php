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
                            Buscar Cliente *
                        </label>

                        <!-- Campo de búsqueda con dropdown -->
                        <div class="relative" id="cliente-search-wrapper">
                            <div class="flex gap-2 items-center px-4 py-3 border border-gray-300 rounded-lg focus-within:ring-2 focus-within:ring-blue-500 focus-within:border-blue-500 bg-white">
                                <i class='bx bx-search text-gray-400'></i>
                                <input type="text" id="cliente-search-input"
                                    class="flex-1 outline-none bg-transparent text-gray-800 placeholder-gray-400"
                                    placeholder="Escribe nombre, apellido o DNI..."
                                    autocomplete="off">
                                <button type="button" id="btn-nuevo-cliente"
                                        class="flex items-center gap-1 bg-gray-800 hover:bg-gray-700 text-white px-3 py-1.5 rounded-lg text-sm font-medium transition-all"
                                        style="display: none;">
                                    <i class='bx bx-user-plus'></i>
                                    Nuevo
                                </button>
                            </div>

                            <!-- Dropdown de resultados -->
                            <div id="cliente-dropdown"
                                class="absolute z-50 w-full bg-white border border-gray-200 rounded-lg shadow-lg mt-1 max-h-60 overflow-y-auto"
                                style="display: none;">
                                <!-- Se llena dinámicamente -->
                            </div>
                        </div>

                        <!-- Inputs ocultos que se envían al formulario -->
                        <input type="hidden" name="doc_identidad" id="doc_identidad">
                        <input type="hidden" name="nombre_apellido" id="nombre_apellido">

                        <!-- Estado del cliente seleccionado -->
                        <div id="cliente-status" class="mt-2" style="display: none;">
                            <div id="cliente-message"></div>
                        </div>
                    </div>

                    <!-- Campos adicionales opcionales -->
                    <div class="grid md:grid-cols-3 gap-4 mt-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                <i class='bx bx-id-card mr-1'></i>
                                Tipo de Documento
                            </label>
                            <select name="tipo_doc" id="tipo_doc" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="DNI">DNI</option>
                                <option value="CE">CE</option>
                                <option value="RUC">RUC</option>
                                <option value="PAS">PASAPORTE</option>
                            </select>
                        </div>

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
                                <option value="S">Soltero</option>
                                <option value="C">Casado</option>
                                <option value="D">Divorciado</option>
                                <option value="V">Viudo</option>
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

        <!-- SECCIÓN DE PENALIZACIONES (solo visible con cliente verificado) -->
        <div id="seccion-penalizaciones" class="bg-orange-50 border border-orange-200 rounded-lg p-6" style="display: none;">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-orange-800 flex items-center">
                    <i class='bx bx-error-circle mr-2'></i>
                    Registro de Daños/Penalizaciones
                </h2>
                <button type="button" id="btn-agregar-penalizacion" 
                        class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm transition-all">
                    <i class='bx bx-plus mr-1'></i>
                    Agregar Penalización
                </button>
            </div>
            
            <p class="text-sm text-orange-700 mb-4">
                <i class='bx bx-info-circle mr-1'></i>
                Registra daños ocasionados por el huésped durante su estadía
            </p>
            
            <!-- Lista de penalizaciones -->
            <div id="lista-penalizaciones" class="space-y-3">
                <!-- Se llenarán dinámicamente -->
            </div>
            
            <!-- Resumen de penalizaciones -->
            <div id="resumen-penalizaciones" class="mt-4 p-3 bg-white rounded-lg border border-orange-200" style="display: none;">
                <div class="flex justify-between items-center">
                    <span class="font-medium text-orange-800">Total Penalizaciones:</span>
                    <span class="font-bold text-red-600" id="total-penalizaciones">S/ 0.00</span>
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

<!-- MODAL NUEVO CLIENTE -->
<div id="modal-nuevo-cliente" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-white rounded-lg p-6 w-full max-w-lg">
            
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class='bx bx-user-plus mr-2' style="color: #6B8CC7;"></i>
                    Nuevo Cliente
                </h3>
                <button type="button" id="btn-cerrar-modal-cliente"
                        class="text-gray-400 hover:text-gray-600">
                    <i class='bx bx-x text-xl'></i>
                </button>
            </div>

            <div class="space-y-4">

                <!-- Fila 1: Tipo Doc + Número -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Tipo Doc. Identidad *
                        </label>
                        <select id="modal-tipo-doc"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="DNI">DNI</option>
                            <option value="CE">CE</option>
                            <option value="RUC">RUC</option>
                            <option value="PAS">PASAPORTE</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Número *
                        </label>
                        <input type="text" id="modal-doc-identidad" maxlength="20"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                               placeholder="Nro de documento">
                    </div>
                </div>

                <!-- Fila 2: Nombre y Apellido -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Nombres y Apellidos *
                    </label>
                    <input type="text" id="modal-nombre-apellido" maxlength="100"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="Nombre completo">
                </div>

                <!-- Fila 3: Sexo + Nacionalidad + Estado Civil -->
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sexo</label>
                        <select id="modal-sexo"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">Seleccionar</option>
                            <option value="F">F</option>
                            <option value="M">M</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nacionalidad</label>
                        <input type="text" id="modal-nacionalidad" maxlength="50"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                               placeholder="Ej: Peruana">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Estado Civil</label>
                        <select id="modal-estado-civil"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">Seleccionar</option>
                            <option value="S">Soltero</option>
                            <option value="C">Casado</option>
                            <option value="D">Divorciado</option>
                            <option value="V">Viudo</option>
                        </select>
                    </div>
                </div>

                <!-- Fila 4: Fecha Nacimiento + Lugar Nacimiento -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Nacimiento</label>
                        <input type="date" id="modal-fecha-nacimiento"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Lugar de Nacimiento</label>
                        <input type="text" id="modal-lugar-nacimiento" maxlength="100"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                               placeholder="Ciudad, país...">
                    </div>
                </div>

            </div>

            <!-- Botones -->
            <div class="flex justify-end space-x-3 mt-6">
                <button type="button" id="btn-cancelar-modal-cliente"
                        class="px-4 py-2 text-gray-600 bg-gray-200 hover:bg-gray-300 rounded-lg transition-colors">
                    Cancelar
                </button>
                <button type="button" id="btn-guardar-modal-cliente"
                        class="px-6 py-2 text-white rounded-lg transition-colors font-medium" style="background-color: #6B8CC7;">
                    <i class='bx bx-save mr-1'></i>
                    Guardar
                </button>
            </div>

        </div>
    </div>
</div>

<!-- MODAL PARA PENALIZACIONES -->
<div id="modal-penalizacion" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Registrar Penalización</h3>
                <button type="button" id="btn-cerrar-modal-penalizacion" 
                        class="text-gray-400 hover:text-gray-600">
                    <i class='bx bx-x text-xl'></i>
                </button>
            </div>
            
            <form id="form-penalizacion">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class='bx bx-dollar mr-1'></i>
                            Monto de Penalización *
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 font-medium">S/</span>
                            <input type="number" id="monto-penalizacion" step="0.01" min="0" required
                                   class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                   placeholder="0.00">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class='bx bx-credit-card mr-1'></i>
                            Método de Pago *
                        </label>
                        <select id="metodo-pago-penalizacion" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                            <option value="">Seleccionar método...</option>
                            <!-- Se llenará dinámicamente -->
                        </select>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" id="btn-cancelar-penalizacion" 
                            class="px-4 py-2 text-gray-600 bg-gray-200 hover:bg-gray-300 rounded-lg transition-colors">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors">
                        <i class='bx bx-check mr-1'></i>
                        Agregar
                    </button>
                </div>
            </form>
        </div>
    </div>
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

    // VARIABLES PRINCIPALES
    const searchInput       = document.getElementById('cliente-search-input');
    const dropdown          = document.getElementById('cliente-dropdown');
    const btnNuevoCliente   = document.getElementById('btn-nuevo-cliente');
    const clienteStatus     = document.getElementById('cliente-status');
    const clienteMessage    = document.getElementById('cliente-message');

    // Inputs ocultos
    const docHidden         = document.getElementById('doc_identidad');
    const nomHidden         = document.getElementById('nombre_apellido');

    // Campos adicionales del formulario principal
    const tipoDocSelect     = document.getElementById('tipo_doc');
    const sexoSelect        = document.getElementById('sexo');
    const nacionalidadInput = document.getElementById('nacionalidad');
    const estadoCivilSelect = document.getElementById('estado_civil');
    const fechaNacInput     = document.getElementById('fecha_nacimiento');
    const lugarNacInput     = document.getElementById('lugar_nacimiento');

    // Modal nuevo cliente
    const modalNuevoCliente         = document.getElementById('modal-nuevo-cliente');
    const btnCerrarModalCliente     = document.getElementById('btn-cerrar-modal-cliente');
    const btnCancelarModalCliente   = document.getElementById('btn-cancelar-modal-cliente');
    const btnGuardarModalCliente    = document.getElementById('btn-guardar-modal-cliente');

    // Estado
    let clienteSeleccionado = false;
    let debounceTimer       = null;
    let penalizaciones      = [];
    let penalizacionIndex   = 0;

    // BLOQUEAR / DESBLOQUEAR CAMPOS ADICIONALES
    function bloquearCampo(campo) {
        if (!campo) return;
        campo.disabled = true;
        campo.style.backgroundColor = '#f8fafc';
        campo.style.borderColor     = '#C8D7ED';
        campo.style.color           = '#6b7280';
        campo.style.cursor          = 'not-allowed';
    }

    function desbloquearCampo(campo) {
        if (!campo) return;
        campo.disabled              = false;
        campo.style.backgroundColor = '';
        campo.style.borderColor     = '';
        campo.style.color           = '';
        campo.style.cursor          = '';
    }

    function aplicarEstadoCampos(cliente) {
        const campos = {
            tipo_doc:         { el: tipoDocSelect,      val: cliente.tipo_doc },
            sexo:             { el: sexoSelect,          val: cliente.sexo },
            nacionalidad:     { el: nacionalidadInput,   val: cliente.nacionalidad },
            estado_civil:     { el: estadoCivilSelect,   val: cliente.estado_civil },
            fecha_nacimiento: { el: fechaNacInput,       val: cliente.fecha_nacimiento },
            lugar_nacimiento: { el: lugarNacInput,       val: cliente.lugar_nacimiento },
        };

        Object.values(campos).forEach(({ el, val }) => {
            if (!el) return;
            if (val) {
                el.value = val;
                bloquearCampo(el);
            } else {
                el.value = '';
                desbloquearCampo(el);
            }
        });
    }

    function desbloquearTodosCampos() {
        [tipoDocSelect, sexoSelect, nacionalidadInput,
         estadoCivilSelect, fechaNacInput, lugarNacInput].forEach(el => {
            if (el) {
                el.value = '';
                desbloquearCampo(el);
            }
        });
    }

    // RESETEAR ESTADO CLIENTE
    function resetearCliente() {
        clienteSeleccionado     = false;
        docHidden.value         = '';
        nomHidden.value         = '';
        clienteStatus.style.display = 'none';
        btnNuevoCliente.style.display = 'none';
        dropdown.style.display  = 'none';
        desbloquearTodosCampos();
        seccionPenalizaciones.style.display = 'none';
        penalizaciones = [];
        renderizarPenalizaciones();
        actualizarTotalPenalizaciones();
    }

    // MOSTRAR CLIENTE SELECCIONADO
    function mostrarClienteSeleccionado(nombre) {
        clienteStatus.style.display = 'block';
        clienteMessage.innerHTML = `
            <div class="flex items-center px-3 py-2 bg-green-50 border border-green-200 rounded-lg">
                <i class='bx bx-check-circle text-green-600 text-lg mr-2'></i>
                <span class="text-green-800 font-medium">${nombre}</span>
            </div>`;
    }

    // BÚSQUEDA CON DEBOUNCE
    searchInput.addEventListener('input', function () {
        const q = this.value.trim();

        clearTimeout(debounceTimer);
        dropdown.style.display  = 'none';
        btnNuevoCliente.style.display = 'none';

        if (clienteSeleccionado) resetearCliente();

        if (q.length < 2) return;

        debounceTimer = setTimeout(() => buscarCliente(q), 300);
    });

    async function buscarCliente(q) {
        try {
            const res  = await fetch(`{{ route('registros.lookup-cliente') }}?q=${encodeURIComponent(q)}`);
            const data = await res.json();

            dropdown.innerHTML = '';

            if (!Array.isArray(data) || data.length === 0) {
                // Sin resultados → mostrar botón Nuevo
                btnNuevoCliente.style.display = 'flex';
                return;
            }

            btnNuevoCliente.style.display = 'none';

            data.forEach(cliente => {
                const item = document.createElement('div');
                item.className = 'px-4 py-3 hover:bg-blue-50 cursor-pointer border-b border-gray-100 last:border-0 text-sm';
                item.innerHTML = `
                    <span class="font-mono text-gray-500 mr-2">${cliente.doc_identidad}</span>
                    <span class="font-medium text-gray-800">${cliente.nombre_apellido}</span>`;

                item.addEventListener('click', () => seleccionarCliente(cliente));
                dropdown.appendChild(item);
            });

            dropdown.style.display = 'block';

        } catch (err) {
            console.error('Error búsqueda cliente:', err);
        }
    }

    // SELECCIONAR CLIENTE DEL DROPDOWN
    function seleccionarCliente(cliente) {
        clienteSeleccionado     = true;
        docHidden.value         = cliente.doc_identidad;
        nomHidden.value         = cliente.nombre_apellido;
        searchInput.value       = `${cliente.doc_identidad} - ${cliente.nombre_apellido}`;

        dropdown.style.display        = 'none';
        btnNuevoCliente.style.display = 'none';

        aplicarEstadoCampos(cliente);
        mostrarClienteSeleccionado(cliente.nombre_apellido);
        updateStepStatus(1, 'completed');
        toggleSeccionPenalizaciones();
    }

    // CERRAR DROPDOWN AL CLICK FUERA
    document.addEventListener('click', function (e) {
        if (!document.getElementById('cliente-search-wrapper').contains(e.target)) {
            dropdown.style.display = 'none';
        }
    });

    // MODAL NUEVO CLIENTE — ABRIR / CERRAR
    btnNuevoCliente.addEventListener('click', () => {
        // Pre-llenar con lo que escribió el usuario
        const q = searchInput.value.trim();
        const soloNumeros = /^\d+$/.test(q);

        document.getElementById('modal-doc-identidad').value       = soloNumeros ? q : '';
        document.getElementById('modal-nombre-apellido').value     = soloNumeros ? '' : q.toUpperCase();
        document.getElementById('modal-tipo-doc').value            = 'DNI';
        document.getElementById('modal-sexo').value                = '';
        document.getElementById('modal-nacionalidad').value        = '';
        document.getElementById('modal-estado-civil').value        = '';
        document.getElementById('modal-fecha-nacimiento').value    = '';
        document.getElementById('modal-lugar-nacimiento').value    = '';

        modalNuevoCliente.classList.remove('hidden');
    });

    function cerrarModalCliente() {
        modalNuevoCliente.classList.add('hidden');
    }

    btnCerrarModalCliente.addEventListener('click', cerrarModalCliente);
    btnCancelarModalCliente.addEventListener('click', cerrarModalCliente);

    // GUARDAR NUEVO CLIENTE DESDE MODAL
    btnGuardarModalCliente.addEventListener('click', async function () {
        const doc    = document.getElementById('modal-doc-identidad').value.trim();
        const nombre = document.getElementById('modal-nombre-apellido').value.trim();

        if (!doc || !nombre) {
            alert('El número de documento y el nombre son obligatorios');
            return;
        }
        if (nombre.length < 3) {
            alert('El nombre debe tener al menos 3 caracteres');
            return;
        }

        btnGuardarModalCliente.disabled = true;
        btnGuardarModalCliente.innerHTML = '<i class="bx bx-loader-alt mr-1"></i> Guardando...';

        try {
            const response = await fetch('{{ route("clientes.store-ajax") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    doc_identidad:    doc,
                    nombre_apellido:  nombre,
                    tipo_doc:         document.getElementById('modal-tipo-doc').value,
                    sexo:             document.getElementById('modal-sexo').value,
                    nacionalidad:     document.getElementById('modal-nacionalidad').value,
                    estado_civil:     document.getElementById('modal-estado-civil').value,
                    fecha_nacimiento: document.getElementById('modal-fecha-nacimiento').value,
                    lugar_nacimiento: document.getElementById('modal-lugar-nacimiento').value,
                })
            });

            const data = await response.json();

            if (response.ok && data.ok) {
                // Seleccionar automáticamente el cliente recién creado
                seleccionarCliente({
                    doc_identidad:    doc,
                    nombre_apellido:  nombre,
                    tipo_doc:         document.getElementById('modal-tipo-doc').value,
                    sexo:             document.getElementById('modal-sexo').value,
                    nacionalidad:     document.getElementById('modal-nacionalidad').value,
                    estado_civil:     document.getElementById('modal-estado-civil').value,
                    fecha_nacimiento: document.getElementById('modal-fecha-nacimiento').value,
                    lugar_nacimiento: document.getElementById('modal-lugar-nacimiento').value,
                });
                cerrarModalCliente();
            } else {
                alert('Error: ' + (data.message || 'No se pudo guardar el cliente'));
            }

        } catch (err) {
            console.error('Error guardando cliente:', err);
            alert('Error de conexión al guardar el cliente');
        } finally {
            btnGuardarModalCliente.disabled = false;
            btnGuardarModalCliente.innerHTML = '<i class="bx bx-save mr-1"></i> Guardar';
        }
    });

    // VARIABLES DE PAGO
    const tarifaTotalInput      = document.getElementById('tarifa_total');
    const pagosWrapper          = document.getElementById('pagos-wrapper');
    const btnAddPago            = document.getElementById('btn-add-pago');
    const resumenPagos          = document.getElementById('resumen-pagos');
    const totalPagadoSpan       = document.getElementById('total-pagado');
    const totalPendienteSpan    = document.getElementById('total-pendiente');
    const alertaDiferencia      = document.getElementById('alerta-diferencia');
    const mensajeDiferencia     = document.getElementById('mensaje-diferencia');
    const boletaSi              = document.getElementById('boleta_si');
    const boletaNo              = document.getElementById('boleta_no');
    const montoBletaContainer   = document.getElementById('monto-boleta-container');
    const montoBletaInput       = document.getElementById('monto_boleta');

    let pagoIndex = 0;

    function getNextPagoNumber() {
        const existingNumbers = Array.from(document.querySelectorAll('.pago-item')).map(item => {
            const match = item.querySelector('h4').textContent.match(/Método de Pago #(\d+)/);
            return match ? parseInt(match[1]) : 0;
        });
        return existingNumbers.length === 0 ? 2 : Math.max(...existingNumbers) + 1;
    }

    function addPagoRow() {
        const nextNumber     = getNextPagoNumber();
        const selectOriginal = document.querySelector('select[name="id_met_pago"]');
        const opcionesHTML   = selectOriginal.innerHTML;

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
                    <label class="block text-sm font-medium text-gray-700 mb-1">Método de Pago *</label>
                    <select name="pagos[${pagoIndex}][id_met_pago]"
                            class="input-field w-full px-4 py-3 border border-gray-300 rounded-lg transition-all metodo-pago" required>
                        ${opcionesHTML}
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Monto *</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 font-medium">S/</span>
                        <input name="pagos[${pagoIndex}][monto]" type="number" step="0.01" min="0"
                            class="input-field w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg transition-all monto-pago"
                            required placeholder="0.00">
                    </div>
                </div>
            </div>`;

        pagosWrapper.appendChild(div);
        pagoIndex++;
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
        document.querySelectorAll('.monto-pago').forEach(input => {
            input.removeEventListener('input', calcularTotales);
            input.addEventListener('input', calcularTotales);
        });
    }

    function calcularTotales() {
        const tarifaTotal = parseFloat(tarifaTotalInput.value) || 0;
        let totalPagado = 0;

        document.querySelectorAll('.monto-pago').forEach(input => {
            totalPagado += parseFloat(input.value) || 0;
        });

        const pendiente = tarifaTotal - totalPagado;

        if (tarifaTotal > 0) {
            resumenPagos.style.display = 'block';
            totalPagadoSpan.textContent    = `S/ ${totalPagado.toFixed(2)}`;
            totalPendienteSpan.textContent = `S/ ${pendiente.toFixed(2)}`;

            if (Math.abs(pendiente) > 0.01) {
                alertaDiferencia.style.display = 'block';
                if (pendiente > 0) {
                    mensajeDiferencia.textContent          = `Falta S/ ${pendiente.toFixed(2)} por completar el pago total`;
                    alertaDiferencia.style.backgroundColor = '#fef3c7';
                    alertaDiferencia.style.borderColor     = '#f59e0b';
                    alertaDiferencia.style.color           = '#92400e';
                } else {
                    mensajeDiferencia.textContent          = `Hay un exceso de S/ ${Math.abs(pendiente).toFixed(2)} en los pagos`;
                    alertaDiferencia.style.backgroundColor = '#fef2f2';
                    alertaDiferencia.style.borderColor     = '#ef4444';
                    alertaDiferencia.style.color           = '#dc2626';
                }
            } else {
                alertaDiferencia.style.display = 'none';
            }
        } else {
            resumenPagos.style.display     = 'none';
            alertaDiferencia.style.display = 'none';
        }

        if (montoBletaInput) {
            montoBletaInput.max = totalPagado;
            if (parseFloat(montoBletaInput.value) > totalPagado) {
                montoBletaInput.value = totalPagado.toFixed(2);
            }
        }
    }

    function updateBoletaVisibility() {
        const totalPagos      = document.querySelectorAll('.pago-item').length;
        const requiereBoleta  = boletaSi.checked;

        if (totalPagos > 1 && requiereBoleta) {
            montoBletaContainer.style.display = 'block';
            montoBletaInput.required          = true;
        } else {
            montoBletaContainer.style.display = 'none';
            montoBletaInput.required          = false;
            montoBletaInput.value             = '';
        }
    }

    btnAddPago.addEventListener('click', addPagoRow);
    tarifaTotalInput.addEventListener('input', calcularTotales);
    updatePagoEvents();
    boletaSi.addEventListener('change', updateBoletaVisibility);
    boletaNo.addEventListener('change', updateBoletaVisibility);
    window.removePagoRow = removePagoRow;

    // SISTEMA DE PASOS
    function updateStepStatus(stepNumber, status) {
        const step = document.getElementById(`step${stepNumber}`);
        if (step) step.className = `step ${status}`;
    }

    // VALIDACIÓN FINAL
    document.getElementById('form-registro').addEventListener('submit', function (e) {
        if (!clienteSeleccionado) {
            e.preventDefault();
            alert('Debes seleccionar un cliente primero');
            searchInput.focus();
            return false;
        }

        const camposBasicos = ['hora_ingreso', 'fecha_ingreso', 'habitacion'];
        for (let campo of camposBasicos) {
            const input = document.querySelector(`[name="${campo}"]`);
            if (input && !input.value.trim()) {
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
            const horaReal  = document.getElementById('hora_ingreso_real').value;
            if (!fechaReal || !horaReal) {
                e.preventDefault();
                alert('Para turno NOCHE debes completar la fecha y hora real');
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
        document.querySelectorAll('.pago-item').forEach(item => {
            const metodo = item.querySelector('.metodo-pago').value;
            const monto  = parseFloat(item.querySelector('.monto-pago').value) || 0;
            if (metodo && monto > 0) { totalPagado += monto; pagosValidos++; }
        });

        if (pagosValidos === 0) {
            e.preventDefault();
            alert('Debe especificar al menos un método de pago válido');
            return false;
        }

        if (Math.abs(tarifaTotal - totalPagado) > 0.01) {
            e.preventDefault();
            const diferencia = tarifaTotal - totalPagado;
            alert(diferencia > 0
                ? `Falta S/ ${diferencia.toFixed(2)} por completar el pago total`
                : `Hay un exceso de S/ ${Math.abs(diferencia).toFixed(2)} en los pagos`);
            return false;
        }

        const nombre    = nomHidden.value;
        const habitacion = document.querySelector('[name="habitacion"]').value;
        const monto     = document.querySelector('[name="monto"]').value;

        if (!confirm(`¿Confirmar registro?\n\nCliente: ${nombre}\nHabitación: ${habitacion}\nMonto: S/ ${monto}`)) {
            e.preventDefault();
            return false;
        }

        return true;
    });

    // PENALIZACIONES

    const seccionPenalizaciones  = document.getElementById('seccion-penalizaciones');
    const btnAgregarPenalizacion = document.getElementById('btn-agregar-penalizacion');
    const modalPenalizacion      = document.getElementById('modal-penalizacion');
    const formPenalizacion       = document.getElementById('form-penalizacion');
    const listaPenalizaciones    = document.getElementById('lista-penalizaciones');
    const resumenPenalizaciones  = document.getElementById('resumen-penalizaciones');
    const totalPenalizacionesSpan = document.getElementById('total-penalizaciones');

    function toggleSeccionPenalizaciones() {
        if (clienteSeleccionado) {
            seccionPenalizaciones.style.display = 'block';
            cargarMetodosPagoPenalizaciones();
        } else {
            seccionPenalizaciones.style.display = 'none';
        }
    }

    async function cargarMetodosPagoPenalizaciones() {
        try {
            const response = await fetch('{{ route("penalizaciones.metodos-pago") }}');
            const metodos  = await response.json();
            const select   = document.getElementById('metodo-pago-penalizacion');
            select.innerHTML = '<option value="">Seleccionar método...</option>';
            metodos.forEach(m => {
                const opt  = document.createElement('option');
                opt.value  = m.id_met_pago;
                opt.textContent = m.met_pago;
                select.appendChild(opt);
            });
        } catch (err) {
            console.error('Error cargando métodos pago:', err);
        }
    }

    function agregarPenalizacion(monto, metodoId, metodoNombre) {
        penalizaciones.push({ id: penalizacionIndex++, monto: parseFloat(monto), metodo_id: metodoId, metodo_nombre: metodoNombre });
        renderizarPenalizaciones();
        actualizarTotalPenalizaciones();
    }

    function renderizarPenalizaciones() {
        listaPenalizaciones.innerHTML = '';
        penalizaciones.forEach((pen, index) => {
            const div = document.createElement('div');
            div.className = 'flex items-center justify-between p-3 bg-white rounded-lg border border-red-200';
            div.innerHTML = `
                <div class="flex items-center space-x-3">
                    <i class='bx bx-error-circle text-red-500'></i>
                    <div>
                        <span class="font-medium">Daño #${index + 1}</span>
                        <div class="text-sm text-gray-600">${pen.metodo_nombre}</div>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <span class="font-bold text-red-600">S/ ${pen.monto.toFixed(2)}</span>
                    <button type="button" onclick="eliminarPenalizacion(${pen.id})"
                            class="text-red-500 hover:text-red-700">
                        <i class='bx bx-trash'></i>
                    </button>
                </div>
                <input type="hidden" name="penalizaciones[${pen.id}][monto]" value="${pen.monto}">
                <input type="hidden" name="penalizaciones[${pen.id}][id_met_pago]" value="${pen.metodo_id}">`;
            listaPenalizaciones.appendChild(div);
        });
    }

    function actualizarTotalPenalizaciones() {
        const total = penalizaciones.reduce((sum, p) => sum + p.monto, 0);
        totalPenalizacionesSpan.textContent = `S/ ${total.toFixed(2)}`;
        resumenPenalizaciones.style.display = total > 0 ? 'block' : 'none';
    }

    function eliminarPenalizacion(id) {
        penalizaciones = penalizaciones.filter(p => p.id !== id);
        renderizarPenalizaciones();
        actualizarTotalPenalizaciones();
    }

    btnAgregarPenalizacion.addEventListener('click', () => modalPenalizacion.classList.remove('hidden'));
    document.getElementById('btn-cerrar-modal-penalizacion').addEventListener('click', () => modalPenalizacion.classList.add('hidden'));
    document.getElementById('btn-cancelar-penalizacion').addEventListener('click', () => modalPenalizacion.classList.add('hidden'));

    formPenalizacion.addEventListener('submit', e => {
        e.preventDefault();
        const monto        = document.getElementById('monto-penalizacion').value;
        const metodoSelect = document.getElementById('metodo-pago-penalizacion');
        const metodoId     = metodoSelect.value;
        const metodoNombre = metodoSelect.options[metodoSelect.selectedIndex].text;

        if (monto && metodoId) {
            agregarPenalizacion(monto, metodoId, metodoNombre);
            document.getElementById('monto-penalizacion').value       = '';
            document.getElementById('metodo-pago-penalizacion').value = '';
            modalPenalizacion.classList.add('hidden');
        }
    });

    window.eliminarPenalizacion = eliminarPenalizacion;
}); 
</script>

<!-- Boxicons -->
<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

@endsection