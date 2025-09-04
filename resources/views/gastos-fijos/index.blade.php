@extends('layouts.app')

@section('title', 'Gastos Fijos - Hotel Romance')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Gastos Fijos</h1>
            <p class="text-gray-600">Gestión de servicios y pagos mensuales</p>
        </div>
        
        <div class="flex gap-3">
            <button onclick="showModalAgregarServicio()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded flex items-center transition-colors">
                <i class='bx bx-plus mr-2'></i>
                Agregar Servicio
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
            <i class='bx bx-check-circle mr-2'></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
            <i class='bx bx-error-circle mr-2'></i>
            {{ session('error') }}
        </div>
    @endif

    {{-- Tabla principal --}}
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                {{-- Encabezado --}}
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Servicio
                        </th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Fecha Ven.
                        </th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Monto
                        </th>
                        @foreach($meses as $mes)
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ $mes['nombre'] }}
                        </th>
                        @endforeach
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>

                {{-- Cuerpo de la tabla --}}
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($gastosFijos as $servicio)
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                        {{-- Nombre del servicio --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $servicio->nombre_servicio }}
                            </div>
                        </td>

                        {{-- Fecha de vencimiento --}}
                        <td class="px-4 py-4 whitespace-nowrap text-center">
                            <div class="text-sm text-gray-600">
                                {{ $servicio->dia_vencimiento }}
                            </div>
                        </td>

                        {{-- Monto fijo --}}
                        <td class="px-4 py-4 whitespace-nowrap text-center">
                            <div class="text-sm font-semibold text-gray-900">
                                S/{{ number_format($servicio->monto_fijo, 2) }}
                            </div>
                        </td>

                        {{-- Columnas de meses --}}
                        @foreach($meses as $mes)
                        <td class="px-4 py-4 whitespace-nowrap text-center">
                            @php
                                $pago = $servicio->getPago($mes['numero'], $mes['anio']);
                                $estaPagado = $servicio->estaPagado($mes['numero'], $mes['anio']);
                            @endphp
                            
                            <div class="flex gap-2 justify-center">
                                @if($estaPagado)
                                    {{-- Botón PAGADO (verde) --}}
                                    <button class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 text-xs rounded font-semibold cursor-default">
                                        PAGADO
                                    </button>
                                    
                                    {{-- Botón VER --}}
                                    <button onclick="verComprobante({{ $pago->id_pago_gasto }})" 
                                            class="border border-gray-300 text-gray-700 px-3 py-1 text-xs rounded hover:bg-gray-50 transition-colors" 
                                            title="Ver comprobante">
                                        VER
                                    </button>
                                @else
                                    {{-- Botón PAGAR (rojo) --}}
                                    <button onclick="showModalPagar({{ $servicio->id_gasto_fijo }}, {{ $mes['numero'] }}, {{ $mes['anio'] }}, '{{ $servicio->nombre_servicio }}', {{ $servicio->monto_fijo }})"
                                            class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 text-xs rounded font-semibold transition-colors">
                                        PAGAR
                                    </button>
                                @endif
                            </div>
                        </td>
                        @endforeach

                        {{-- Acciones --}}
                        <td class="px-4 py-4 whitespace-nowrap text-center">
                            <button onclick="eliminarServicio({{ $servicio->id_gasto_fijo }}, '{{ $servicio->nombre_servicio }}')"
                                    class="text-red-600 hover:text-red-900 p-1 rounded hover:bg-red-50 transition-colors" 
                                    title="Eliminar servicio">
                                <i class='bx bx-trash text-lg'></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ count($meses) + 5 }}" class="px-6 py-12 text-center">
                            <div class="text-gray-500">
                                <i class='bx bx-receipt text-4xl mb-2'></i>
                                <p class="text-lg">No hay servicios registrados</p>
                                <p class="text-sm">Agrega tu primer servicio para comenzar</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal Agregar Servicio --}}
<div id="modalAgregarServicio" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Agregar Nuevo Servicio</h3>
                <button onclick="closeModal('modalAgregarServicio')" class="text-gray-400 hover:text-gray-600">
                    <i class='bx bx-x text-2xl'></i>
                </button>
            </div>
            
            <form id="formAgregarServicio" action="{{ route('gastos-fijos.store') }}" method="POST">
                @csrf
                
                <div class="space-y-4">
                    <div>
                        <label for="nombre_servicio" class="block text-sm font-medium text-gray-700 mb-1">
                            Nombre del Servicio
                        </label>
                        <input type="text" 
                               id="nombre_servicio"
                               name="nombre_servicio" 
                               placeholder="Ej: Internet, Agua, Luz"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               required>
                    </div>

                    <div>
                        <label for="dia_vencimiento" class="block text-sm font-medium text-gray-700 mb-1">
                            Día de Vencimiento
                        </label>
                        <input type="number" 
                               id="dia_vencimiento"
                               name="dia_vencimiento" 
                               min="1" 
                               max="31"
                               placeholder="Día del mes (1-31)"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               required>
                    </div>

                    <div>
                        <label for="monto_fijo" class="block text-sm font-medium text-gray-700 mb-1">
                            Monto Fijo
                        </label>
                        <input type="number" 
                               id="monto_fijo"
                               name="monto_fijo" 
                               step="0.01"
                               min="0"
                               placeholder="0.00"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               required>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" 
                            onclick="closeModal('modalAgregarServicio')"
                            class="px-4 py-2 text-gray-700 border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                        Guardar Servicio
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Registrar Pago --}}
<div id="modalRegistrarPago" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Registrar Pago</h3>
                <button onclick="closeModal('modalRegistrarPago')" class="text-gray-400 hover:text-gray-600">
                    <i class='bx bx-x text-2xl'></i>
                </button>
            </div>
            
            <form id="formRegistrarPago" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="pago_id_gasto_fijo" name="id_gasto_fijo">
                <input type="hidden" id="pago_mes" name="mes">
                <input type="hidden" id="pago_anio" name="anio">
                
                <div class="space-y-4">
                    <div id="infoPago" class="bg-gray-50 p-3 rounded-lg">
                        <p class="text-sm text-gray-700">
                            <strong>Servicio:</strong> <span id="info_servicio"></span><br>
                            <strong>Mes:</strong> <span id="info_mes"></span><br>
                            <strong>Monto sugerido:</strong> S/<span id="info_monto"></span>
                        </p>
                    </div>

                    <div>
                        <label for="monto_pagado" class="block text-sm font-medium text-gray-700 mb-1">
                            Monto Pagado
                        </label>
                        <input type="number" 
                               id="monto_pagado"
                               name="monto_pagado" 
                               step="0.01"
                               min="0"
                               placeholder="0.00"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               required>
                    </div>

                    <div>
                        <label for="id_met_pago" class="block text-sm font-medium text-gray-700 mb-1">
                            Método de Pago
                        </label>
                        <select id="id_met_pago"
                                name="id_met_pago" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                required>
                            <option value="">Seleccionar método</option>
                            @foreach($metodosPago as $metodo)
                                <option value="{{ $metodo->id_met_pago }}">
                                    {{ $metodo->met_pago }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="fecha_pago_real" class="block text-sm font-medium text-gray-700 mb-1">
                            Fecha Real del Pago
                        </label>
                        <input type="date" 
                            id="fecha_pago_real"
                            name="fecha_pago_real" 
                            value="{{ date('Y-m-d') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            required>
                        <p class="text-xs text-gray-500 mt-1">
                            Fecha en la que realmente se realizó el pago
                        </p>
                    </div>

                    <div>
                        <label for="comprobante" class="block text-sm font-medium text-gray-700 mb-1">
                            Comprobante (PDF o PNG/JPG)
                        </label>
                        <input type="file" 
                               id="comprobante"
                               name="comprobante" 
                               accept=".pdf,.png,.jpg,.jpeg"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               >
                        <p class="text-xs text-gray-500 mt-1">
                            Formatos permitidos: PDF, PNG, JPG (máx. 10MB) - Opcional
                        </p>
                        <div id="file-preview" class="mt-2"></div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" 
                            onclick="closeModal('modalRegistrarPago')"
                            class="px-4 py-2 text-gray-700 border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">
                        Cancelar
                    </button>
                    <button type="button" 
                            onclick="submitPago()" 
                            id="btnGuardarPago"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors flex items-center">
                        <i class="bx bx-save mr-2"></i>
                        Registrar Pago
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Scripts JavaScript --}}
<script>
// Variables globales
let currentModal = null;
let isSubmitting = false;

// Mostrar modal para agregar servicio
function showModalAgregarServicio() {
    document.getElementById('modalAgregarServicio').classList.remove('hidden');
    document.getElementById('modalAgregarServicio').classList.add('flex');
    currentModal = 'modalAgregarServicio';
    
    setTimeout(() => {
        document.getElementById('nombre_servicio')?.focus();
    }, 100);
}

// Mostrar modal para registrar pago
function showModalPagar(idGastoFijo, mes, anio, nombreServicio, montoFijo) {
    document.getElementById('pago_id_gasto_fijo').value = idGastoFijo;
    document.getElementById('pago_mes').value = mes;
    document.getElementById('pago_anio').value = anio;
    document.getElementById('monto_pagado').value = parseFloat(montoFijo).toFixed(2);
    
    document.getElementById('info_servicio').textContent = nombreServicio;
    document.getElementById('info_mes').textContent = getNombreMes(mes) + ' ' + anio;
    document.getElementById('info_monto').textContent = parseFloat(montoFijo).toFixed(2);
    
    document.getElementById('modalRegistrarPago').classList.remove('hidden');
    document.getElementById('modalRegistrarPago').classList.add('flex');
    currentModal = 'modalRegistrarPago';
    
    setTimeout(() => {
        document.getElementById('monto_pagado')?.focus();
    }, 100);
}

// Cerrar modal
function closeModal(modalName) {
    document.getElementById(modalName).classList.add('hidden');
    document.getElementById(modalName).classList.remove('flex');
    currentModal = null;
    isSubmitting = false;
    
    if (modalName === 'modalAgregarServicio') {
        document.getElementById('formAgregarServicio').reset();
    } else if (modalName === 'modalRegistrarPago') {
        document.getElementById('formRegistrarPago').reset();
        document.getElementById('file-preview').innerHTML = '';
        clearErrors();
    }
}

// Limpiar errores
function clearErrors() {
    document.querySelectorAll('.error-message').forEach(el => el.remove());
    document.querySelectorAll('.border-red-500').forEach(el => {
        el.classList.remove('border-red-500');
    });
}

// Validar archivo
function validateFile(fileInput) {
    const file = fileInput.files[0];
    if (!file) return true;
    
    const maxSize = 10 * 1024 * 1024; // 10MB
    if (file.size > maxSize) {
        alert('El archivo es demasiado grande. Máximo 10MB permitido.');
        fileInput.value = '';
        return false;
    }
    
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
    if (!allowedTypes.includes(file.type)) {
        alert('Tipo de archivo no permitido. Solo PDF, JPG o PNG.');
        fileInput.value = '';
        return false;
    }
    
    // Mostrar preview
    previewFile(fileInput);
    return true;
}

// Preview archivo
function previewFile(input) {
    const file = input.files[0];
    if (!file) return;
    
    const previewContainer = document.getElementById('file-preview');
    previewContainer.innerHTML = `
        <div class="flex items-center p-2 bg-gray-50 rounded border mt-2">
            <i class="bx ${file.type.includes('pdf') ? 'bx-file-pdf text-red-500' : 'bx-image text-blue-500'} text-lg mr-2"></i>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900 truncate">${file.name}</p>
                <p class="text-xs text-gray-500">${(file.size / 1024 / 1024).toFixed(2)} MB</p>
            </div>
        </div>
    `;
}

// Enviar pago
async function submitPago() {
    if (isSubmitting) return;
    
    const form = document.getElementById('formRegistrarPago');
    const btnGuardar = document.getElementById('btnGuardarPago');
    const fileInput = document.getElementById('comprobante');
    
    if (fileInput.files.length > 0 && !validateFile(fileInput)) return;

    // Validación del método de pago
    const metodoPago = document.getElementById('id_met_pago').value;
    if (!metodoPago) {
        alert('Selecciona un método de pago.');
        return;
    }
    
    // Validación de la fecha de pago
    const fechaPago = document.getElementById('fecha_pago_real').value;
    if (!fechaPago) {
        alert('Selecciona la fecha del pago.');
        return;
    }
    
    // Validación del monto
    const monto = document.getElementById('monto_pagado').value;
    if (!monto || parseFloat(monto) <= 0) {
        alert('Ingresa un monto válido mayor a 0.');
        return;
    }
    
    isSubmitting = true;
    btnGuardar.disabled = true;
    btnGuardar.innerHTML = '<i class="bx bx-loader-alt animate-spin mr-2"></i>Guardando...';
    
    try {
        const formData = new FormData(form);
        
        const response = await fetch('{{ route("gastos-fijos.registrar-pago") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        const result = await response.json();
        
        if (result.success) {
            showSuccessMessage('Pago registrado correctamente');
            closeModal('modalRegistrarPago');
            setTimeout(() => location.reload(), 500);
        } else {
            alert('Error: ' + result.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error de conexión. Inténtalo de nuevo.');
    } finally {
        isSubmitting = false;
        btnGuardar.disabled = false;
        btnGuardar.innerHTML = '<i class="bx bx-save mr-2"></i>Registrar Pago';
    }
}

// Mostrar mensaje de éxito
function showSuccessMessage(message) {
    const notification = document.createElement('div');
    notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
    notification.innerHTML = `
        <div class="flex items-center">
            <i class="bx bx-check-circle mr-2"></i>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Ver comprobante
function verComprobante(idPago) {
    const url = `{{ route('gastos-fijos.ver-comprobante', ['pago' => ':id']) }}`.replace(':id', idPago);
    window.open(url, '_blank', 'width=800,height=600,scrollbars=yes,resizable=yes');
}

// Eliminar servicio
function eliminarServicio(idServicio, nombreServicio) {
    if (confirm(`¿Eliminar el servicio "${nombreServicio}"?\n\nEsta acción eliminará todos los pagos asociados.`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `{{ route('gastos-fijos.destroy', ['gastoFijo' => ':id']) }}`.replace(':id', idServicio);
        
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        form.appendChild(csrfInput);
        
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}

// Obtener nombre del mes
function getNombreMes(numeroMes) {
    const meses = {
        1: 'Enero', 2: 'Febrero', 3: 'Marzo', 4: 'Abril',
        5: 'Mayo', 6: 'Junio', 7: 'Julio', 8: 'Agosto',
        9: 'Septiembre', 10: 'Octubre', 11: 'Noviembre', 12: 'Diciembre'
    };
    return meses[numeroMes] || 'Desconocido';
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('comprobante');
    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            if (e.target.files.length > 0) {
                validateFile(e.target);
            }
        });
    }
    
    // Cerrar modal al hacer clic fuera
    document.addEventListener('click', function(e) {
        if (e.target.id === 'modalAgregarServicio' || e.target.id === 'modalRegistrarPago') {
            closeModal(e.target.id);
        }
    });
});

// Cerrar modal con Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && currentModal) {
        closeModal(currentModal);
    }
});
</script>

@endsection