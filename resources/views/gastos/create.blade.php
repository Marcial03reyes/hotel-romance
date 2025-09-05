@extends('layouts.app')

@section('title', 'Nuevo Gasto Variable')

@section('content')
<style>
    :root {
        --primary-color: #88A6D3;
        --secondary-color: #6B8CC7;
        --accent-color: #4A73B8;
        --tertiary-color: #A5BFDB;
        --sidebar-bg: #F8FAFC;
        --gradient-start: #88A6D3;
        --gradient-end: #6B8CC7;
    }

    .form-container {
        background: white;
        border-radius: 1.5rem;
        padding: 2rem;
        box-shadow: 0 10px 30px rgba(136, 166, 211, 0.1);
        border: 1px solid rgba(136, 166, 211, 0.2);
    }

    .input-field {
        transition: all 0.3s ease;
        border: 2px solid #e5e7eb;
    }

    .input-field:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(136, 166, 211, 0.1);
        outline: none;
        background-color: white;
    }

    .input-field:hover {
        border-color: var(--tertiary-color);
    }

    .btn-romance {
        background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
        transition: all 0.3s ease;
        color: white;
        font-weight: 600;
        box-shadow: 0 2px 8px rgba(136, 166, 211, 0.3);
    }

    .btn-romance:hover {
        background: linear-gradient(135deg, var(--secondary-color), var(--accent-color));
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(136, 166, 211, 0.4);
    }

    .btn-secondary {
        background: white;
        color: var(--accent-color);
        border: 2px solid var(--tertiary-color);
        transition: all 0.3s ease;
        font-weight: 500;
    }

    .btn-secondary:hover {
        background: var(--sidebar-bg);
        border-color: var(--primary-color);
        color: var(--secondary-color);
        transform: translateY(-1px);
    }

    .field-label {
        color: var(--accent-color);
        font-weight: 600;
    }

    .page-header {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white;
        padding: 1.5rem;
        border-radius: 1rem;
        margin-bottom: 2rem;
        box-shadow: 0 8px 20px rgba(136, 166, 211, 0.3);
    }

    .file-upload-area {
        border: 2px dashed var(--tertiary-color);
        border-radius: 0.75rem;
        padding: 1.5rem;
        background: rgba(136, 166, 211, 0.05);
        transition: all 0.3s ease;
    }

    .file-upload-area:hover {
        border-color: var(--primary-color);
        background: rgba(136, 166, 211, 0.1);
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

<!-- Header -->
<div class="page-header">
    <div class="flex items-center">
        <a href="{{ route('gastos.index') }}" class="text-white hover:text-gray-200 mr-4 transition-colors">
            <i class='bx bx-arrow-back text-2xl'></i>
        </a>
        <div>
            <h1 class="text-3xl font-bold flex items-center">
                <i class='bx bx-wallet mr-3 text-4xl'></i>
                Nuevo Gasto Variable
            </h1>
            <p class="opacity-90 mt-1">Registra un nuevo gasto variable del hotel</p>
        </div>
    </div>
</div>

<!-- Formulario -->
<div class="form-container max-w-2xl mx-auto">
    <form action="{{ route('gastos.store') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Tipo de Gasto -->
            <div class="space-y-2">
                <label class="field-label block text-sm font-medium">
                    <i class='bx bx-category mr-1'></i>
                    Tipo de Gasto
                </label>
                <select name="id_tipo_gasto" 
                        class="input-field w-full rounded-lg px-4 py-3 text-gray-700"
                        required>
                    <option value="">Seleccionar tipo de gasto</option>
                    @foreach($tipos as $tipo)
                        <option value="{{ $tipo->id_tipo_gasto }}" {{ old('id_tipo_gasto') == $tipo->id_tipo_gasto ? 'selected' : '' }}>
                            {{ $tipo->nombre }}
                        </option>
                    @endforeach
                </select>
                @error('id_tipo_gasto')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Monto -->
            <div class="space-y-2">
                <label class="field-label block text-sm font-medium">
                    <i class='bx bx-money mr-1'></i>
                    Monto (S/)
                </label>
                <input name="monto" 
                       type="number" 
                       step="0.01"
                       min="0"
                       max="999999.99"
                       value="{{ old('monto') }}"
                       class="input-field w-full rounded-lg px-4 py-3 text-gray-700 placeholder-gray-400"
                       placeholder="0.00" 
                       required>
                @error('monto')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Método de Pago -->
            <div class="space-y-2">
                <label class="field-label block text-sm font-medium">
                    <i class='bx bx-credit-card mr-1'></i>
                    Método de Pago
                </label>
                <select name="id_met_pago" 
                        class="input-field w-full rounded-lg px-4 py-3 text-gray-700"
                        required>
                    <option value="">Seleccionar método</option>
                    @foreach($metodos as $metodo)
                        <option value="{{ $metodo->id_met_pago }}" {{ old('id_met_pago') == $metodo->id_met_pago ? 'selected' : '' }}>
                            {{ $metodo->met_pago }}
                        </option>
                    @endforeach
                </select>
                @error('id_met_pago')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Fecha -->
            <div class="space-y-2">
                <label class="field-label block text-sm font-medium">
                    <i class='bx bx-calendar mr-1'></i>
                    Fecha del Gasto
                </label>
                <input name="fecha_gasto" 
                       type="date" 
                       value="{{ old('fecha_gasto', date('Y-m-d')) }}"
                       class="input-field w-full rounded-lg px-4 py-3 text-gray-700"
                       required>
                @error('fecha_gasto')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Turno -->
            <div class="space-y-2 md:col-span-2">
                <label class="field-label block text-sm font-medium">
                    <i class='bx bx-sun mr-1'></i>
                    Turno de Trabajo
                </label>
                <p class="text-xs text-gray-500 mb-3">
                    <i class='bx bx-info-circle mr-1'></i>
                    Selecciona el turno en el que se realizó el gasto
                </p>
                
                <div class="flex space-x-4">
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="turno" value="0" class="sr-only turno-radio" required>
                        <div class="turno-button turno-dia border-2 border-gray-200 rounded-lg p-4 text-center transition-all hover:border-yellow-400 hover:bg-yellow-50">
                            <i class='bx bx-sun text-3xl mb-2 text-yellow-600'></i>
                            <div class="font-semibold text-gray-800">DÍA</div>
                            <div class="text-xs text-gray-500 mt-1"></div>
                        </div>
                    </label>
                    
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="turno" value="1" class="sr-only turno-radio" required>
                        <div class="turno-button turno-noche border-2 border-gray-200 rounded-lg p-4 text-center transition-all hover:border-blue-400 hover:bg-blue-50">
                            <i class='bx bx-moon text-3xl mb-2 text-blue-600'></i>
                            <div class="font-semibold text-gray-800">NOCHE</div>
                            <div class="text-xs text-gray-500 mt-1"></div>
                        </div>
                    </label>
                </div>
                
                <div class="text-xs text-gray-500 text-center mt-3">
                    * Campo obligatorio - Selecciona una opción para continuar
                </div>
                @error('turno')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Campo Comprobante (ancho completo) -->
        <div class="mt-6 space-y-2">
            <label class="field-label block text-sm font-medium">
                <i class='bx bx-receipt mr-1'></i>
                Código de Comprobante
            </label>
            <input name="comprobante" 
                   type="text" 
                   value="{{ old('comprobante') }}"
                   class="input-field w-full rounded-lg px-4 py-3 text-gray-700 placeholder-gray-400"
                   placeholder="Ingrese el código de la boleta o factura (opcional)" 
                   maxlength="100">
            <p class="text-xs text-gray-500 mt-1">
                <i class='bx bx-info-circle mr-1'></i>
                Código de boleta o factura para identificar el comprobante (opcional)
            </p>
            @error('comprobante')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Botones -->
        <div class="flex items-center gap-4 pt-6 mt-8 border-t border-gray-200">
            <a href="{{ route('gastos.index') }}" 
               class="btn-secondary px-6 py-3 rounded-lg text-sm font-medium flex items-center transition-all">
                <i class='bx bx-arrow-back mr-2'></i>
                Cancelar
            </a>
            
            <button type="submit" 
                    class="btn-romance px-8 py-3 rounded-lg text-sm font-medium flex items-center flex-1 justify-center">
                <i class='bx bx-save mr-2'></i>
                Guardar Gasto Variable
            </button>
        </div>
    </form>

    <!-- Información adicional -->
    <div class="mt-6 p-4 rounded-lg" style="background: rgba(136, 166, 211, 0.1); border-left: 4px solid var(--primary-color);">
        <h3 class="font-semibold text-sm mb-2" style="color: var(--accent-color);">
            <i class='bx bx-info-circle mr-1'></i>
            Información importante
        </h3>
        <ul class="text-xs text-gray-600 space-y-1">
            <li>• Los gastos variables son aquellos que pueden fluctuar mes a mes</li>
            <li>• El código de comprobante es opcional pero recomendado para facilitar búsquedas</li>
            <li>• Todos los campos marcados con * son obligatorios</li>
        </ul>
    </div>
</div>

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
            
            // Agregar selección actual
            if (this.checked) {
                this.nextElementSibling.classList.add('selected');
            }
        });
    });
});
</script>

@endsection