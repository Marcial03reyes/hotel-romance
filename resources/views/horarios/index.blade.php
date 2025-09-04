@extends('layouts.app')

@section('title', 'Horarios de Trabajadores')

@section('content')
<div class="mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Horarios de Trabajadores</h1>
            <p class="mt-1 text-sm text-gray-600">Gestiona los horarios de trabajo de todo el personal</p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <a href="{{ route('horarios.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-all flex items-center">
                <i class="bx bx-plus mr-2"></i>
                Asignar Horario
            </a>
        </div>
    </div>
</div>

<!-- Cronograma Semanal -->
<div class="bg-white border border-gray-100 shadow-md shadow-black/5 rounded-lg overflow-hidden mb-6">
    <div class="p-6 border-b border-gray-100">
        <h2 class="text-lg font-semibold text-gray-900 flex items-center">
            <i class="bx bx-calendar mr-2 text-blue-600"></i>
            Cronograma Semanal
        </h2>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trabajador</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Lunes</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Martes</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Miércoles</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Jueves</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Viernes</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Sábado</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Domingo</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($trabajadores as $trabajador)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold mr-3">
                                {{ strtoupper(substr($trabajador->nombre_apellido, 0, 1)) }}
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-gray-900">{{ $trabajador->nombre_apellido }}</div>
                                <div class="text-xs text-gray-500">DNI: {{ $trabajador->DNI }}</div>
                            </div>
                        </div>
                    </td>
                    
                    @php
                        $diasSemana = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'];
                        $horariosIndexados = $trabajador->horariosActivos->keyBy('dia_semana');
                    @endphp
                    
                    @foreach($diasSemana as $dia)
                        <td class="px-4 py-4 text-center">
                            @if(isset($horariosIndexados[$dia]))
                                @php $horario = $horariosIndexados[$dia] @endphp
                                <div class="bg-green-100 text-green-800 px-2 py-1 rounded-md text-xs font-medium">
                                    {{ $horario->hora_inicio->format('H:i') }} - {{ $horario->hora_fin->format('H:i') }}
                                </div>
                                <div class="mt-1 flex justify-center space-x-1">
                                    <a href="{{ route('horarios.edit', $horario) }}" class="text-blue-600 hover:text-blue-800 text-xs">
                                        <i class="bx bx-edit"></i>
                                    </a>
                                    <form action="{{ route('horarios.destroy', $horario) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar este horario?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 text-xs">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            @else
                                <span class="text-gray-400 text-xs">Libre</span>
                            @endif
                        </td>
                    @endforeach
                    
                    <td class="px-6 py-4 text-center">
                        <button onclick="openAssignModal('{{ $trabajador->DNI }}', '{{ $trabajador->nombre_apellido }}')" 
                                class="bg-blue-100 text-blue-700 px-3 py-1 rounded-md text-xs font-medium hover:bg-blue-200 transition-all">
                            <i class="bx bx-plus mr-1"></i>
                            Asignar
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-6 py-8 text-center text-gray-500">
                        <i class="bx bx-calendar-x text-4xl mb-2"></i>
                        <p>No hay trabajadores registrados</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Trabajadores sin horarios -->
@if($trabajadoresSinHorarios->count() > 0)
<div class="bg-amber-50 border border-amber-200 rounded-lg p-6">
    <h3 class="text-lg font-semibold text-amber-800 mb-3 flex items-center">
        <i class="bx bx-time mr-2"></i>
        Trabajadores sin horarios asignados
    </h3>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($trabajadoresSinHorarios as $trabajador)
        <div class="bg-white border border-amber-200 rounded-lg p-4 flex items-center justify-between">
            <div>
                <div class="font-medium text-gray-900">{{ $trabajador->nombre_apellido }}</div>
                <div class="text-sm text-gray-500">DNI: {{ $trabajador->DNI }}</div>
            </div>
            <button onclick="openAssignModal('{{ $trabajador->DNI }}', '{{ $trabajador->nombre_apellido }}')" 
                    class="bg-amber-600 text-white px-3 py-1 rounded-md text-sm hover:bg-amber-700 transition-all">
                Asignar
            </button>
        </div>
        @endforeach
    </div>
</div>
@endif

<!-- Modal para asignar horarios -->
<div id="assignModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">Asignar Horarios</h3>
                <button onclick="closeAssignModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="bx bx-x text-2xl"></i>
                </button>
            </div>
            
            <form action="{{ route('horarios.asignar-completo') }}" method="POST">
                @csrf
                <input type="hidden" id="modal_dni" name="DNI">
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Trabajador</label>
                    <p id="modal_trabajador_nombre" class="text-gray-900 font-medium"></p>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Días de trabajo</label>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="checkbox" name="dias_seleccionados[]" value="lunes" class="rounded border-gray-300 text-blue-600">
                            <span class="ml-2">Lunes</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="dias_seleccionados[]" value="martes" class="rounded border-gray-300 text-blue-600">
                            <span class="ml-2">Martes</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="dias_seleccionados[]" value="miercoles" class="rounded border-gray-300 text-blue-600">
                            <span class="ml-2">Miércoles</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="dias_seleccionados[]" value="jueves" class="rounded border-gray-300 text-blue-600">
                            <span class="ml-2">Jueves</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="dias_seleccionados[]" value="viernes" class="rounded border-gray-300 text-blue-600">
                            <span class="ml-2">Viernes</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="dias_seleccionados[]" value="sabado" class="rounded border-gray-300 text-blue-600">
                            <span class="ml-2">Sábado</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="dias_seleccionados[]" value="domingo" class="rounded border-gray-300 text-blue-600">
                            <span class="ml-2">Domingo</span>
                        </label>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Hora inicio</label>
                        <input type="time" name="hora_inicio" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Hora fin</label>
                        <input type="time" name="hora_fin" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeAssignModal()" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400 transition-all">
                        Cancelar
                    </button>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-all">
                        Asignar Horarios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openAssignModal(dni, nombre) {
    document.getElementById('modal_dni').value = dni;
    document.getElementById('modal_trabajador_nombre').textContent = nombre;
    document.getElementById('assignModal').classList.remove('hidden');
}

function closeAssignModal() {
    document.getElementById('assignModal').classList.add('hidden');
    // Limpiar checkboxes
    document.querySelectorAll('input[name="dias_seleccionados[]"]').forEach(checkbox => {
        checkbox.checked = false;
    });
    // Limpiar campos de tiempo
    document.querySelector('input[name="hora_inicio"]').value = '';
    document.querySelector('input[name="hora_fin"]').value = '';
}
</script>
@endpush
@endsection