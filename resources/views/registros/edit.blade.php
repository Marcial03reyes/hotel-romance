@extends('layouts.app')

@section('title', 'Editar Registro #' . $estadia->id_estadia . ' - Hotel Romance')

@section('content')

<style>
    /* Paleta de colores azul Hotel Romance */
    :root {
        --primary-color: #88A6D3;      /* Azul principal */
        --secondary-color: #6B8CC7;    /* Azul secundario más oscuro */
        --tertiary-color: #A5BFDB;     /* Azul terciario más claro */
        --accent-color: #4A73B8;       /* Azul de acento oscuro */
        --light-blue: #C8D7ED;         /* Azul muy claro */
        --sidebar-bg: #f4f8fc;         /* Fondo sidebar azul muy suave */
        --hover-bg: #88A6D3;           /* Color hover */
        --gradient-start: #88A6D3;     /* Inicio gradiente */
        --gradient-end: #6B8CC7;       /* Fin gradiente */
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

    /* Iconos con color azul */
    .icon-azul {
        color: var(--secondary-color);
    }

    /* Enlaces de navegación */
    .nav-link:hover {
        color: var(--secondary-color);
    }

    .nav-link-blue:hover {
        color: var(--accent-color);
    }

    /* Radio buttons personalizados */
    .radio-azul {
        accent-color: var(--primary-color);
    }

    /* Información adicional con tema azul */
    .info-box {
        background-color: #f0f7ff;
        border-color: var(--light-blue);
    }

    .info-text {
        color: var(--accent-color);
    }

    .info-icon {
        color: var(--secondary-color);
    }

    /* Campos deshabilitados con estilo personalizado */
    .disabled-field {
        background-color: #f8fafc;
        border-color: var(--light-blue);
        color: #6b7280;
    }

    .lock-icon {
        color: var(--tertiary-color);
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

<div class="container mx-auto py-6 px-4">
    
    <!-- Header con información del registro -->
    <div class="mb-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">
                    <i class='bx bx-edit mr-2 icon-azul'></i>
                    Editar Registro #{{ $estadia->id_estadia }}
                </h1>
                <p class="text-gray-600">Modifica la información del registro de habitación</p>
            </div>
            <div class="flex items-center space-x-2">
                <span class="badge-info">
                    <i class='bx bx-calendar mr-1'></i>
                    {{ \Carbon\Carbon::parse($estadia->fecha_ingreso)->format('d/m/Y') }}
                </span>
                <span class="badge-info">
                    <i class='bx bx-bed mr-1'></i>
                    Habitación {{ $estadia->habitacion }}
                </span>
            </div>
        </div>
        
        <div class="flex items-center space-x-4 text-sm text-gray-600">
            <a href="{{ route('registros.index') }}" 
               class="inline-flex items-center nav-link transition-colors">
                <i class='bx bx-arrow-back mr-1'></i>
                Volver a registros
            </a>
            <span>•</span>
            <a href="{{ route('registros.consumo', $estadia->id_estadia) }}" 
               class="inline-flex items-center nav-link-blue transition-colors">
                <i class='bx bx-receipt mr-1'></i>
                Ver consumos de esta estadía
            </a>
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

    <!-- Formulario de edición -->
    <form action="{{ route('registros.update', $estadia->id_estadia) }}" method="POST" class="space-y-6">
        @csrf 
        @method('PUT')

        <div class="space-y-6">
            
            <!-- Información del Cliente -->
            <div class="field-group">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class='bx bx-user mr-2 icon-azul'></i>
                    Información del Cliente
                </h2>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class='bx bx-id-card mr-1'></i>
                            Documento de Identidad
                        </label>
                        <input name="doc_identidad" type="text" 
                               value="{{ old('doc_identidad', $estadia->doc_identidad) }}"
                               class="input-field w-full px-4 py-3 border border-gray-300 rounded-lg transition-all"
                               maxlength="20" required placeholder="DNI o Carnet de Extranjería"
                               id="doc_identidad">
                        <p class="text-xs text-gray-500 mt-1">DNI (8 dígitos) o Carnet de extranjería (más de 8 dígitos)</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre y Apellido</label>
                        <div class="relative">
                            <input type="text" value="{{ $estadia->cliente->nombre_apellido ?? 'No especificado' }}" disabled
                                   class="disabled-field w-full px-4 py-3 border rounded-lg cursor-not-allowed"
                                   id="nombre_apellido_display">
                            <div class="absolute right-3 top-1/2 transform -translate-y-1/2">
                                <i class='bx bx-lock lock-icon'></i>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Para cambiar el nombre, edita en la sección de Clientes</p>
                    </div>
                </div>
            </div>

            <!-- Información de la Estadía -->
             <!-- Selector de Turno -->
            <div class="field-group">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class='bx bx-sun mr-2 icon-azul'></i>
                    Turno de Trabajo
                </h2>
                
                <div class="space-y-4">
                    <p class="text-sm text-gray-600 mb-4">
                        <i class='bx bx-info-circle mr-1'></i>
                        Selecciona el turno correspondiente al momento del registro
                    </p>
                    
                    <div class="flex space-x-4">
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" name="turno" value="0" class="sr-only turno-radio" 
                                   {{ old('turno', $estadia->turno ?? 0) == 0 ? 'checked' : '' }} required>
                            <div class="turno-button turno-dia border-2 border-gray-200 rounded-lg p-4 text-center transition-all hover:border-yellow-400 hover:bg-yellow-50">
                                <i class='bx bx-sun text-3xl mb-2 text-yellow-600'></i>
                                <div class="font-semibold text-gray-800">DÍA</div>
                            </div>
                        </label>
                        
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" name="turno" value="1" class="sr-only turno-radio" 
                                   {{ old('turno', $estadia->turno ?? 0) == 1 ? 'checked' : '' }} required>
                            <div class="turno-button turno-noche border-2 border-gray-200 rounded-lg p-4 text-center transition-all hover:border-blue-400 hover:bg-blue-50">
                                <i class='bx bx-moon text-3xl mb-2 text-blue-600'></i>
                                <div class="font-semibold text-gray-800">NOCHE</div>
                            </div>
                        </label>
                    </div>
                    
                    <div class="text-xs text-gray-500 text-center mt-3">
                        * Campo obligatorio - El turno actual es: <strong>{{ isset($estadia->turno) ? ($estadia->turno == 0 ? 'DÍA' : 'NOCHE') : 'No definido' }}</strong>
                    </div>
                </div>
            </div>

            <div class="field-group">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class='bx bx-bed mr-2 icon-azul'></i>
                    Detalles de la Estadía
                </h2>
                
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class='bx bx-time mr-1'></i>
                            Hora de Ingreso
                        </label>
                        <input name="hora_ingreso" type="time" 
                               value="{{ old('hora_ingreso', $estadia->hora_ingreso) }}"
                               class="input-field w-full px-4 py-3 border border-gray-300 rounded-lg transition-all" 
                               required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class='bx bx-time-five mr-1'></i>
                            Hora de Salida (Opcional)
                        </label>
                        <input name="hora_salida" type="time" 
                            value="{{ old('hora_salida', $estadia->hora_salida) }}"
                            class="input-field w-full px-4 py-3 border border-gray-300 rounded-lg transition-all">
                        <p class="text-xs text-gray-500 mt-1">
                            <i class='bx bx-info-circle mr-1'></i>
                            Campo opcional
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class='bx bx-calendar mr-1'></i>
                            Fecha de Ingreso
                        </label>
                        <input name="fecha_ingreso" type="date"
                               value="{{ old('fecha_ingreso', \Carbon\Carbon::parse($estadia->fecha_ingreso)->toDateString()) }}"
                               class="input-field w-full px-4 py-3 border border-gray-300 rounded-lg transition-all" 
                               required>
                    </div>
                </div>
                
                <!-- Campos auxiliares solo para turno NOCHE -->
                <div id="campos-auxiliares-noche" class="mt-4" style="display: none;">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                        <div class="flex items-center mb-3">
                            <i class='bx bx-moon text-blue-600 mr-2'></i>
                            <h3 class="text-sm font-semibold text-blue-800">Horario Real (Solo Turno Noche)</h3>
                        </div>
                        <p class="text-xs text-blue-700 mb-3">
                            Para turno noche, registra la fecha y hora real de ingreso del cliente
                        </p>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    <i class='bx bx-calendar mr-1'></i>
                                    Fecha Real de Ingreso *
                                </label>
                                <input name="fecha_ingreso_real" id="fecha_ingreso_real" type="date" 
                                    value="{{ old('fecha_ingreso_real', $estadia->fecha_ingreso_real ? $estadia->fecha_ingreso_real->format('Y-m-d') : '') }}"
                                    class="input-field w-full px-4 py-3 border border-gray-300 rounded-lg transition-all">
                                <p class="text-xs text-gray-500 mt-1">Fecha real cuando llegó el cliente</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    <i class='bx bx-time mr-1'></i>
                                    Hora Real de Ingreso *
                                </label>
                                <input name="hora_ingreso_real" id="hora_ingreso_real" type="time" 
                                    value="{{ old('hora_ingreso_real', $estadia->hora_ingreso_real) }}"
                                    class="input-field w-full px-4 py-3 border border-gray-300 rounded-lg transition-all">
                                <p class="text-xs text-gray-500 mt-1">Hora real cuando llegó el cliente</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        <i class='bx bx-home mr-1'></i>
                        Habitación
                    </label>
                    <select name="habitacion" class="input-field w-full px-4 py-3 border border-gray-300 rounded-lg transition-all" required>
                        @foreach ($habitaciones as $h)
                            <option value="{{ $h }}" {{ old('habitacion', $estadia->habitacion) == $h ? 'selected' : '' }}>
                                Habitación {{ $h }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        <i class='bx bx-note mr-1'></i>
                        Observaciones
                    </label>
                    <textarea name="obs" rows="3" maxlength="1000"
                            class="input-field w-full px-4 py-3 border border-gray-300 rounded-lg transition-all"
                            placeholder="Número de placa, notas especiales, etc...">{{ old('obs', $estadia->obs) }}</textarea>
                    <p class="text-xs text-gray-500 mt-1">
                        <i class='bx bx-info-circle mr-1'></i>
                        Campo opcional para observaciones adicionales
                    </p>
                </div>
            </div>
        </div>

        <!-- Información de Pago -->
        <div class="field-group">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class='bx bx-credit-card mr-2 icon-azul'></i>
                Información de Pago
            </h2>
            
            <div class="grid md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        <i class='bx bx-dollar mr-1'></i>
                        Tarifa (Monto)
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 font-medium">S/</span>
                        <input name="monto" type="number" step="0.01" min="0"
                               value="{{ old('monto', $pago->monto ?? 0) }}"
                               class="input-field w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg transition-all" 
                               required placeholder="0.00">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        <i class='bx bx-wallet mr-1'></i>
                        Método de Pago
                    </label>
                    <select name="id_met_pago" class="input-field w-full px-4 py-3 border border-gray-300 rounded-lg transition-all" required>
                        @foreach ($metodos as $m)
                            <option value="{{ $m->id_met_pago }}"
                                {{ old('id_met_pago', $pago->id_met_pago ?? null) == $m->id_met_pago ? 'selected' : '' }}>
                                {{ $m->met_pago }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        <i class='bx bx-receipt mr-1'></i>
                        Boleta
                    </label>
                    <div class="flex items-center space-x-6 pt-3">
                        <label class="flex items-center">
                            <input type="radio" name="boleta" value="SI" 
                                   {{ old('boleta', $pago->boleta ?? 'NO') == 'SI' ? 'checked' : '' }}
                                   class="radio-azul focus:ring-blue-500 mr-2">
                            <span class="text-sm font-medium text-gray-700">Sí</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="boleta" value="NO" 
                                   {{ old('boleta', $pago->boleta ?? 'NO') == 'NO' ? 'checked' : '' }}
                                   class="radio-azul focus:ring-blue-500 mr-2">
                            <span class="text-sm font-medium text-gray-700">No</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botones de acción -->
        <div class="flex items-center justify-between pt-6 border-t border-gray-200">
            <div class="flex items-center space-x-4">
                <a href="{{ route('registros.index') }}" 
                   class="btn-secondary px-6 py-3 rounded-lg font-medium transition-all">
                    <i class='bx bx-x mr-2'></i>
                    Cancelar
                </a>
                
                <a href="{{ route('registros.consumo', $estadia->id_estadia) }}" 
                   class="inline-flex items-center nav-link-blue text-sm font-medium">
                    <i class='bx bx-receipt mr-1'></i>
                    Ver consumos de esta estadía
                </a>
            </div>
            
            <button type="submit" 
                    class="btn-romance text-white px-8 py-3 rounded-lg font-medium shadow-lg">
                <i class='bx bx-save mr-2'></i>
                Actualizar Registro
            </button>
        </div>
    </form>

    <!-- Información adicional -->
    <div class="mt-8 p-4 info-box border rounded-lg">
        <div class="flex items-start">
            <i class='bx bx-info-circle info-icon text-lg mr-2 mt-1'></i>
            <div class="text-sm info-text">
                <p class="font-medium mb-1">Información importante:</p>
                <ul class="list-disc list-inside space-y-1">
                    <li>Para cambios en el nombre del cliente, ve a la sección "Clientes"</li>
                    <li>El documento de identidad sí se puede modificar para corregir errores</li>
                    <li>Los consumos adicionales se gestionan por separado</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // === FUNCIONALIDAD SELECTOR DE TURNO ===
    const turnoRadios = document.querySelectorAll('.turno-radio');
    
    console.log('Turno radios encontrados:', turnoRadios.length); // DEBUG
    
    function toggleCamposAuxiliares(valor) {
        const camposAuxiliares = document.getElementById('campos-auxiliares-noche');
        const fechaRealInput = document.getElementById('fecha_ingreso_real');
        const horaRealInput = document.getElementById('hora_ingreso_real');
        
        console.log('Toggle campos auxiliares, valor:', valor); // DEBUG
        console.log('Campos auxiliares encontrado:', !!camposAuxiliares); // DEBUG
        
        if (valor === '1') { // Turno NOCHE
            console.log('Mostrando campos auxiliares'); // DEBUG
            camposAuxiliares.style.display = 'block';
            fechaRealInput.required = true;
            horaRealInput.required = true;
        } else { // Turno DÍA
            console.log('Ocultando campos auxiliares'); // DEBUG
            camposAuxiliares.style.display = 'none';
            fechaRealInput.required = false;
            horaRealInput.required = false;
            fechaRealInput.value = '';
            horaRealInput.value = '';
        }
    }
    
    turnoRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            console.log('Radio cambiado, valor:', this.value); // DEBUG
            
            // Remover selección previa
            document.querySelectorAll('.turno-button').forEach(btn => {
                btn.classList.remove('selected');
            });
            
            if (this.checked) {
                this.nextElementSibling.classList.add('selected');
                toggleCamposAuxiliares(this.value);
            }
        });
    });
    
    // Inicializar estado al cargar
    const turnoActivo = document.querySelector('input[name="turno"]:checked');
    if (turnoActivo) {
        console.log('Turno activo al cargar:', turnoActivo.value); // DEBUG
        turnoActivo.nextElementSibling.classList.add('selected');
        toggleCamposAuxiliares(turnoActivo.value);
    }

    // Auto-focus en el primer campo editable
    const firstInput = document.querySelector('input[name="doc_identidad"]');
    if (firstInput) {
        firstInput.focus();
    }
    
    // Autocompletar nombre por documento (opcional)
    const docInput = document.getElementById('doc_identidad');
    const nombreDisplay = document.getElementById('nombre_apellido_display');
    
    docInput.addEventListener('blur', async function() {
        const doc = this.value.trim();
        if (!doc) return;
        
        try {
            const response = await fetch(`{{ route('clientes.lookup') }}?doc=${encodeURIComponent(doc)}`);
            const data = await response.json();
            
            if (data.ok && data.nombre_apellido) {
                nombreDisplay.value = data.nombre_apellido;
                nombreDisplay.style.color = '#059669'; // Verde si encuentra el cliente
            } else {
                nombreDisplay.value = 'Cliente no encontrado en el sistema';
                nombreDisplay.style.color = '#dc2626'; // Rojo si no encuentra
            }
        } catch (error) {
            console.log('Error al buscar cliente:', error);
        }
    });
    
    // Validación del formulario
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const monto = parseFloat(document.querySelector('input[name="monto"]').value);
        const docIdentidad = document.querySelector('input[name="doc_identidad"]').value.trim();
        
        if (monto <= 0) {
            e.preventDefault();
            alert('El monto debe ser mayor a 0');
            return false;
        }
        
        if (!docIdentidad) {
            e.preventDefault();
            alert('El documento de identidad es obligatorio');
            return false;
        }
        
        if (docIdentidad.length < 8) {
            e.preventDefault();
            alert('El documento debe tener al menos 8 caracteres');
            return false;
        }
        
        // Confirmación antes de guardar
        if (!confirm('¿Estás seguro de que quieres actualizar este registro?')) {
            e.preventDefault();
            return false;
        }

        // Validar turno seleccionado
        const turnoSeleccionado = document.querySelector('input[name="turno"]:checked');
        if (!turnoSeleccionado) {
            e.preventDefault();
            alert('Debes seleccionar un turno (DÍA o NOCHE)');
            document.querySelector('input[name="turno"]').focus();
            return false;
        }

        // Validar campos auxiliares para turno NOCHE
        if (turnoSeleccionado.value === '1') {
            const fechaReal = document.getElementById('fecha_ingreso_real').value;
            const horaReal = document.getElementById('hora_ingreso_real').value;
            
            if (!fechaReal || !horaReal) {
                e.preventDefault();
                alert('Para turno NOCHE debes completar la fecha y hora real');
                return false;
            }
        }
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

@endsection