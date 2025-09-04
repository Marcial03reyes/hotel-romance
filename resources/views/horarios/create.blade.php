@extends('layouts.app')

@section('title', 'Asignar Horario')

@section('content')
<div class="mb-6">
    <div class="flex items-center space-x-4">
        <a href="{{ route('horarios.index') }}" class="text-gray-600 hover:text-gray-900">
            <i class="bx bx-arrow-back text-xl"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Asignar Horario Individual</h1>
            <p class="mt-1 text-sm text-gray-600">Asigna un horario específico para un día de la semana</p>
        </div>
    </div>
</div>

<div class="bg-white border border-gray-100 shadow-md shadow-black/5 rounded-lg overflow-hidden">
    <div class="p-6">
        <form action="{{ route('horarios.store') }}" method="POST">
            @csrf
            
            <!-- Seleccionar Trabajador -->
            <div class="mb-6">
                <label for="DNI" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="bx bx-user mr-1"></i>
                    Trabajador
                </label>
                <select name="DNI" id="DNI" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('DNI') border-red-500 @enderror">
                    <option value="">Seleccionar trabajador...</option>
                    @foreach($trabajadores as $trabajador)
                        <option value="{{ $trabajador->DNI }}" {{ old('DNI') == $trabajador->DNI ? 'selected' : '' }}>
                            {{ $trabajador->nombre_apellido }} (DNI: {{ $trabajador->DNI }})
                        </option>
                    @endforeach
                </select>
                @error('DNI')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Seleccionar Día -->
            <div class="mb-6">
                <label for="dia_semana" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="bx bx-calendar mr-1"></i>
                    Día de la semana
                </label>
                <select name="dia_semana" id="dia_semana" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('dia_semana') border-red-500 @enderror">
                    <option value="">Seleccionar día...</option>
                    @foreach($diasSemana as $valor => $nombre)
                        <option value="{{ $valor }}" {{ old('dia_semana') == $valor ? 'selected' : '' }}>
                            {{ $nombre }}
                        </option>
                    @endforeach
                </select>
                @error('dia_semana')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Horario -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="hora_inicio" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="bx bx-time mr-1"></i>
                        Hora de inicio
                    </label>
                    <input type="time" name="hora_inicio" id="hora_inicio" value="{{ old('hora_inicio') }}" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('hora_inicio') border-red-500 @enderror">
                    @error('hora_inicio')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="hora_fin" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="bx bx-time mr-1"></i>
                        Hora de finalización
                    </label>
                    <input type="time" name="hora_fin" id="hora_fin" value="{{ old('hora_fin') }}" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('hora_fin') border-red-500 @enderror">
                    @error('hora_fin')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Vista previa del horario -->
            <div id="horario-preview" class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg hidden">
                <h4 class="font-medium text-blue-900 mb-2">Vista previa del horario:</h4>
                <div id="preview-content" class="text-blue-800"></div>
            </div>

            <!-- Botones -->
            <div class="flex justify-end space-x-3">
                <a href="{{ route('horarios.index') }}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400 transition-all">
                    Cancelar
                </a>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-all flex items-center">
                    <i class="bx bx-save mr-2"></i>
                    Guardar Horario
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const dniSelect = document.getElementById('DNI');
    const diaSelect = document.getElementById('dia_semana');
    const horaInicio = document.getElementById('hora_inicio');
    const horaFin = document.getElementById('hora_fin');
    const preview = document.getElementById('horario-preview');
    const previewContent = document.getElementById('preview-content');

    function updatePreview() {
        const trabajadorText = dniSelect.options[dniSelect.selectedIndex]?.text || '';
        const diaText = diaSelect.options[diaSelect.selectedIndex]?.text || '';
        const inicio = horaInicio.value;
        const fin = horaFin.value;

        if (trabajadorText && diaText && inicio && fin) {
            const trabajadorNombre = trabajadorText.split(' (DNI:')[0];
            previewContent.innerHTML = `
                <strong>${trabajadorNombre}</strong> trabajará el <strong>${diaText}</strong> 
                de <strong>${inicio}</strong> a <strong>${fin}</strong>
            `;
            preview.classList.remove('hidden');
        } else {
            preview.classList.add('hidden');
        }
    }

    // Agregar listeners para actualizar la vista previa
    [dniSelect, diaSelect, horaInicio, horaFin].forEach(element => {
        element.addEventListener('change', updatePreview);
        element.addEventListener('input', updatePreview);
    });

    // Validación de horas
    horaFin.addEventListener('change', function() {
        if (horaInicio.value && horaFin.value) {
            if (horaFin.value <= horaInicio.value) {
                horaFin.setCustomValidity('La hora de fin debe ser posterior a la hora de inicio');
            } else {
                horaFin.setCustomValidity('');
            }
        }
    });
});
</script>
@endpush
@endsection