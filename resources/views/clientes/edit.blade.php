@extends('layouts.app')

@section('title', 'Editar Cliente - Hotel Romance')

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

    /* Campos deshabilitados con estilo personalizado */
    .disabled-field {
        background-color: #f8fafc;
        border-color: var(--light-blue);
        color: #6b7280;
    }

    .lock-icon {
        color: var(--tertiary-color);
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
</style>

<div class="container mx-auto py-6 px-4">
    
    <!-- Header con información del cliente -->
    <div class="mb-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">
                    <i class='bx bx-user-circle mr-2 icon-azul'></i>
                    Editar Cliente
                </h1>
                <p class="text-gray-600">Modifica la información del cliente registrado</p>
            </div>
            <div class="flex items-center space-x-2">
                <span class="badge-info">
                    <i class='bx bx-id-card mr-1'></i>
                    Doc: {{ $cliente->doc_identidad }}
                </span>
                <span class="badge-info">
                    <i class='bx bx-calendar mr-1'></i>
                    Cliente registrado
                </span>
            </div>
        </div>
        
        <div class="flex items-center space-x-4 text-sm text-gray-600">
            <a href="{{ route('clientes.index') }}" 
               class="inline-flex items-center nav-link transition-colors">
                <i class='bx bx-arrow-back mr-1'></i>
                Volver a clientes
            </a>
            <span>•</span>
            <span class="text-gray-500">
                <i class='bx bx-info-circle mr-1'></i>
                Editando información del cliente
            </span>
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
    <form action="{{ route('clientes.update', $cliente->doc_identidad) }}" method="POST" class="space-y-6">
        @csrf 
        @method('PUT')

        <div class="grid lg:grid-cols-1 gap-6 max-w-2xl">
            
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
                        <div class="relative">
                            <input type="text" value="{{ $cliente->doc_identidad }}" disabled
                                   class="disabled-field w-full px-4 py-3 border rounded-lg cursor-not-allowed"
                                   id="doc_identidad_display">
                            <div class="absolute right-3 top-1/2 transform -translate-y-1/2">
                                <i class='bx bx-lock lock-icon'></i>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">El documento de identidad no se puede modificar</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class='bx bx-user mr-1'></i>
                            Nombre y Apellido
                        </label>
                        <input name="nombre_apellido" type="text" 
                               value="{{ old('nombre_apellido', $cliente->nombre_apellido) }}"
                               class="input-field w-full px-4 py-3 border border-gray-300 rounded-lg transition-all"
                               maxlength="100" required placeholder="Ingrese nombre completo"
                               id="nombre_apellido">
                        <p class="text-xs text-gray-500 mt-1">Máximo 100 caracteres</p>
                    </div>


                    <div class="grid md:grid-cols-3 gap-4 mt-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                <i class='bx bx-heart mr-1'></i>
                                Estado Civil
                            </label>
                            <select name="estado_civil" class="input-field w-full px-4 py-3 border border-gray-300 rounded-lg transition-all">
                                <option value="">Seleccionar</option>
                                <option value="S" {{ old('estado_civil', $cliente->estado_civil) == 'S' ? 'selected' : '' }}>Soltero</option>
                                <option value="C" {{ old('estado_civil', $cliente->estado_civil) == 'C' ? 'selected' : '' }}>Casado</option>
                                <option value="D" {{ old('estado_civil', $cliente->estado_civil) == 'D' ? 'selected' : '' }}>Divorciado</option>
                                <option value="V" {{ old('estado_civil', $cliente->estado_civil) == 'V' ? 'selected' : '' }}>Viudo</option>
                            </select>
                        </div>

                        <!-- Sexo -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                <i class='bx bx-venus-mars mr-1'></i>
                                Sexo
                            </label>
                            <select name="sexo" class="input-field w-full px-4 py-3 border border-gray-300 rounded-lg transition-all">
                                <option value="">Seleccionar</option>
                                <option value="F" {{ old('sexo', $cliente->sexo) == 'F' ? 'selected' : '' }}>F</option>
                                <option value="M" {{ old('sexo', $cliente->sexo) == 'M' ? 'selected' : '' }}>M</option>
                            </select>
                        </div>

                        <!-- Nacionalidad -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                <i class='bx bx-flag mr-1'></i>
                                Nacionalidad
                            </label>
                            <input name="nacionalidad" type="text" maxlength="50"
                                value="{{ old('nacionalidad', $cliente->nacionalidad) }}"
                                class="input-field w-full px-4 py-3 border border-gray-300 rounded-lg transition-all"
                                placeholder="Ej: Peruana">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                <i class='bx bx-calendar mr-1'></i>
                                Fecha de Nacimiento
                            </label>
                            <input name="fecha_nacimiento" type="date" 
                                value="{{ old('fecha_nacimiento', $cliente->fecha_nacimiento ? $cliente->fecha_nacimiento->toDateString() : '') }}"
                                class="input-field w-full px-4 py-3 border border-gray-300 rounded-lg transition-all">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                <i class='bx bx-map mr-1'></i>
                                Lugar de Nacimiento
                            </label>
                            <input name="lugar_nacimiento" type="text" maxlength="100"
                                value="{{ old('lugar_nacimiento', $cliente->lugar_nacimiento) }}"
                                class="input-field w-full px-4 py-3 border border-gray-300 rounded-lg transition-all"
                                placeholder="Ciudad, país...">
                        </div>

                        <!-- Profesión/Ocupación -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                <i class='bx bx-briefcase mr-1'></i>
                                Profesión/Ocupación
                            </label>
                            <input name="profesion_ocupacion" type="text" maxlength="100"
                                value="{{ old('profesion_ocupacion', $cliente->profesion_ocupacion) }}"
                                class="input-field w-full px-4 py-3 border border-gray-300 rounded-lg transition-all"
                                placeholder="Ej: Contador, Estudiante...">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botones de acción -->
        <div class="flex items-center justify-between pt-6 border-t border-gray-200 max-w-2xl">
            <div class="flex items-center space-x-4">
                <a href="{{ route('clientes.index') }}" 
                   class="btn-secondary px-6 py-3 rounded-lg font-medium transition-all">
                    <i class='bx bx-x mr-2'></i>
                    Cancelar
                </a>
                
                <span class="text-sm text-gray-500">
                    <i class='bx bx-info-circle mr-1'></i>
                    Los cambios se aplicarán inmediatamente
                </span>
            </div>
            
            <button type="submit" 
                    class="btn-romance text-white px-8 py-3 rounded-lg font-medium shadow-lg">
                <i class='bx bx-save mr-2'></i>
                Actualizar Cliente
            </button>
        </div>
    </form>

    <!-- Información adicional -->
    <div class="mt-8 p-4 info-box border rounded-lg max-w-2xl">
        <div class="flex items-start">
            <i class='bx bx-info-circle info-icon text-lg mr-2 mt-1'></i>
            <div class="text-sm info-text">
                <p class="font-medium mb-1">Información importante:</p>
                <ul class="list-disc list-inside space-y-1">
                    <li>El documento de identidad es único e inmutable en el sistema</li>
                    <li>El nombre y apellido se actualiza en todos los registros relacionados</li>
                    <li>Los campos adicionales (estado civil, fecha y lugar de nacimiento) son opcionales</li>
                    <li>Los cambios se reflejan automáticamente en las estadías del cliente</li>
                    <li>Asegúrate de escribir correctamente toda la información</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-focus en el campo editable
    const nombreInput = document.getElementById('nombre_apellido');
    if (nombreInput) {
        nombreInput.focus();
        // Seleccionar todo el texto para facilitar la edición
        nombreInput.select();
    }
    
    // Validación del formulario
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const nombreApellido = document.querySelector('input[name="nombre_apellido"]').value.trim();
        
        if (!nombreApellido) {
            e.preventDefault();
            alert('El nombre y apellido es obligatorio');
            return false;
        }
        
        if (nombreApellido.length < 3) {
            e.preventDefault();
            alert('El nombre debe tener al menos 3 caracteres');
            return false;
        }
        
        if (nombreApellido.length > 100) {
            e.preventDefault();
            alert('El nombre no puede exceder los 100 caracteres');
            return false;
        }
        
        // Validar que no contenga solo números
        if (/^\d+$/.test(nombreApellido)) {
            e.preventDefault();
            alert('El nombre no puede contener solo números');
            return false;
        }
        
        // Confirmación antes de guardar
        if (!confirm('¿Estás seguro de que quieres actualizar la información de este cliente?')) {
            e.preventDefault();
            return false;
        }
    });
    
    // Formatear automáticamente el nombre (primera letra mayúscula)
    nombreInput.addEventListener('blur', function() {
        let value = this.value.trim();
        if (value) {
            // CONVERTIR TODO A MAYUSCULA
            this.value = value.toUpperCase();
        }
    });
    
    // Prevenir caracteres especiales problemáticos
    nombreInput.addEventListener('input', function() {
        // Remover caracteres no deseados (mantener letras, espacios, acentos y algunos caracteres especiales)
        this.value = this.value.replace(/[^a-záéíóúñü\s'-]/gi, '');
    });
});
</script>

@endsection