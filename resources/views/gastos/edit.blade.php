@extends('layouts.app')

@section('title', 'Editar gasto #'.$gasto->id_gasto)

@section('content')
<div class="max-w-xl space-y-4">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold">Editar Gasto #{{ $gasto->id_gasto }}</h1>
        <span class="text-sm text-gray-500">
            <i class='bx bx-calendar mr-1'></i>
            Creado: {{ \Carbon\Carbon::parse($gasto->fecha_gasto)->format('d/m/Y') }}
        </span>
    </div>

    @if ($errors->any())
        <div class="rounded-lg border border-red-300 bg-red-50 p-3 text-red-800">
            <ul class="list-disc ps-5">
                @foreach ($errors->all() as $error)
                    <li class="text-sm">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- IMPORTANTE: Agregar enctype para subida de archivos -->
    <form action="{{ route('gastos.update', $gasto->id_gasto) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf 
        @method('PUT')

        <!-- Tipo de gasto -->
        <div>
            <label class="block text-sm font-medium mb-1">
                <i class='bx bx-tag mr-1 text-blue-600'></i>
                Nombre (tipo de gasto)
            </label>
            <select name="id_tipo_gasto" class="w-full rounded-lg border px-3 py-2 focus:ring-2 focus:ring-blue-500" required>
                @foreach ($tipos as $t)
                    <option value="{{ $t->id_tipo_gasto }}" 
                            {{ old('id_tipo_gasto', $gasto->id_tipo_gasto) == $t->id_tipo_gasto ? 'selected' : '' }}>
                        {{ $t->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Monto y Método -->
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">
                    <i class='bx bx-money mr-1 text-green-600'></i>
                    Monto (S/)
                </label>
                <input name="monto" 
                       type="number" 
                       step="0.01" 
                       min="0.01"
                       value="{{ old('monto', $gasto->monto) }}"
                       class="w-full rounded-lg border px-3 py-2 focus:ring-2 focus:ring-blue-500" 
                       required>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">
                    <i class='bx bx-credit-card mr-1 text-purple-600'></i>
                    Método de Pago
                </label>
                <select name="id_met_pago" class="w-full rounded-lg border px-3 py-2 focus:ring-2 focus:ring-blue-500" required>
                    @foreach ($metodos as $m)
                        <option value="{{ $m->id_met_pago }}" 
                                {{ old('id_met_pago', $gasto->id_met_pago) == $m->id_met_pago ? 'selected' : '' }}>
                            {{ $m->met_pago }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Fecha -->
        <div>
            <label class="block text-sm font-medium mb-1">
                <i class='bx bx-calendar mr-1 text-indigo-600'></i>
                Fecha del Gasto
            </label>
            <input name="fecha_gasto" 
                   type="date" 
                   value="{{ old('fecha_gasto', \Carbon\Carbon::parse($gasto->fecha_gasto)->toDateString()) }}"
                   max="{{ date('Y-m-d') }}"
                   class="w-full rounded-lg border px-3 py-2 focus:ring-2 focus:ring-blue-500" 
                   required>
        </div>

        <!-- NUEVO: Campo para comprobante -->
        <div>
            <label for="comprobante" class="block text-sm font-medium mb-2">
                <i class='bx bx-file mr-1 text-blue-600'></i>
                Comprobante
            </label>
            
            <!-- Mostrar comprobante actual si existe -->
            @if($gasto->comprobante)
                <div class="mb-3 p-3 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class='bx bx-check-circle mr-2 text-green-600'></i>
                            <span class="text-sm text-green-700 font-medium">
                                Comprobante actual: {{ basename($gasto->comprobante) }}
                            </span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('gastos.comprobante', $gasto->id_gasto) }}" 
                               target="_blank"
                               class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                <i class='bx bx-external-link mr-1'></i>
                                Ver
                            </a>
                            @if(Str::endsWith($gasto->comprobante, '.pdf'))
                                <a href="{{ route('gastos.comprobante.download', $gasto->id_gasto) }}" 
                                   class="text-purple-600 hover:text-purple-800 text-sm font-medium">
                                    <i class='bx bx-download mr-1'></i>
                                    Descargar
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @else
                <div class="mb-3 p-3 bg-gray-50 border border-gray-200 rounded-lg">
                    <div class="flex items-center">
                        <i class='bx bx-info-circle mr-2 text-gray-600'></i>
                        <span class="text-sm text-gray-600">No hay comprobante registrado</span>
                    </div>
                </div>
            @endif
            
            <!-- Input para nuevo comprobante -->
            <div class="relative">
                <input type="file" 
                       id="comprobante" 
                       name="comprobante"
                       accept="image/jpeg,image/jpg,image/png,application/pdf"
                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent
                              file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold
                              file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition-all">
                
                <!-- Información de ayuda -->
                <div class="mt-2 flex items-center text-sm text-gray-500">
                    <i class='bx bx-info-circle mr-1'></i>
                    <span>
                        @if($gasto->comprobante)
                            Subir un nuevo archivo reemplazará el actual
                        @else
                            Formatos: JPG, PNG, PDF (máximo 5MB)
                        @endif
                    </span>
                </div>
                
                <!-- Preview del nuevo archivo seleccionado -->
                <div id="file-preview" class="hidden mt-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class='bx bx-file mr-2 text-blue-600'></i>
                            <span class="text-sm text-blue-700 font-medium">Nuevo archivo:</span>
                            <span id="file-name" class="text-sm text-blue-900 ml-1"></span>
                            <span id="file-size" class="text-xs text-blue-600 ml-2"></span>
                        </div>
                        <button type="button" id="remove-file" class="text-red-600 hover:text-red-800">
                            <i class='bx bx-x'></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botones de acción -->
        <div class="flex items-center gap-2 pt-4">
            <a href="{{ route('gastos.index') }}" 
               class="rounded-lg border border-gray-300 px-4 py-2 text-sm hover:bg-gray-50 transition-colors">
                <i class='bx bx-x mr-1'></i>
                Cancelar
            </a>
            <button type="submit" 
                    class="rounded-lg bg-blue-600 px-4 py-2 text-sm text-white hover:bg-blue-700 transition-colors">
                <i class='bx bx-save mr-1'></i>
                Actualizar Gasto
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('comprobante');
    const filePreview = document.getElementById('file-preview');
    const fileName = document.getElementById('file-name');
    const fileSize = document.getElementById('file-size');
    const removeFileBtn = document.getElementById('remove-file');

    // Manejar selección de archivo
    fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        
        if (file) {
            // Validar tamaño (5MB máximo)
            if (file.size > 5 * 1024 * 1024) {
                alert('❌ El archivo es muy grande. Máximo 5MB permitido.');
                fileInput.value = '';
                filePreview.classList.add('hidden');
                return;
            }
            
            // Mostrar preview
            fileName.textContent = file.name;
            fileSize.textContent = `(${(file.size / 1024 / 1024).toFixed(2)} MB)`;
            filePreview.classList.remove('hidden');
        } else {
            filePreview.classList.add('hidden');
        }
    });

    // Remover archivo
    removeFileBtn.addEventListener('click', function() {
        fileInput.value = '';
        filePreview.classList.add('hidden');
    });

    // Validación antes de enviar
    document.querySelector('form').addEventListener('submit', function(e) {
        const monto = parseFloat(document.querySelector('[name="monto"]').value);
        
        if (!monto || monto <= 0) {
            e.preventDefault();
            alert('❌ El monto debe ser mayor a 0');
            document.querySelector('[name="monto"]').focus();
            return false;
        }
        
        // Confirmación si va a reemplazar el comprobante
        const nuevoArchivo = fileInput.files[0];
        const tieneComprobanteActual = {{ $gasto->comprobante ? 'true' : 'false' }};
        
        if (nuevoArchivo && tieneComprobanteActual) {
            if (!confirm('⚠️ ¿Estás seguro de reemplazar el comprobante actual con el nuevo archivo?')) {
                e.preventDefault();
                return false;
            }
        }
        
        return true;
    });
});
</script>

@endsection