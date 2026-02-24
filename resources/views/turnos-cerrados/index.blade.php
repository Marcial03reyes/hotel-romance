@extends('layouts.app')

@section('title', 'Gestión de Turnos - Hotel Romance')

@section('content')
<div class="container mx-auto py-6 px-4">

    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">
                    <i class='bx bx-lock-alt mr-2' style="color: #6B8CC7;"></i>
                    Gestión de Turnos
                </h1>
                <p class="text-gray-600">Cierra y reabre turnos una vez verificado el cuadre de caja</p>
            </div>
        </div>
    </div>

    {{-- Alertas --}}
    @if(session('success'))
        <div class="rounded-lg border border-green-300 bg-green-50 p-4 text-green-800 mb-6 shadow-sm">
            <div class="flex items-center">
                <i class='bx bx-check-circle mr-2 text-lg'></i>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-lg border border-red-300 bg-red-50 p-4 text-red-800 mb-6 shadow-sm">
            <div class="flex items-center mb-2">
                <i class='bx bx-error-circle mr-2 text-lg'></i>
                <span class="font-medium">Error:</span>
            </div>
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li class="text-sm">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid lg:grid-cols-2 gap-6">

        {{-- FORMULARIO PARA CERRAR TURNO --}}
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class='bx bx-lock mr-2' style="color: #6B8CC7;"></i>
                Cerrar un Turno
            </h2>
            <p class="text-sm text-gray-500 mb-4">
                Una vez cerrado, no se podrán crear, editar ni eliminar registros de esa fecha y turno.
            </p>

            <form action="{{ route('turnos-cerrados.store') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        <i class='bx bx-calendar mr-1'></i>
                        Fecha *
                    </label>
                    <input name="fecha" type="date" value="{{ old('fecha', now()->format('Y-m-d')) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class='bx bx-sun mr-1'></i>
                        Turno *
                    </label>
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

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        <i class='bx bx-note mr-1'></i>
                        Observación (opcional)
                    </label>
                    <textarea name="observacion" rows="2" maxlength="500"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Ej: Cuadre conforme, S/ 3,420.00">{{ old('observacion') }}</textarea>
                </div>

                {{-- Preview del estado antes de cerrar --}}
                <div id="preview-estado" class="hidden rounded-lg p-3 border text-sm"></div>

                <button type="submit"
                        id="btn-cerrar-turno"
                        class="w-full bg-red-500 hover:bg-red-600 text-white px-6 py-3 rounded-lg font-medium transition-all flex items-center justify-center"
                        disabled>
                    <i class='bx bx-lock mr-2'></i>
                    Cerrar Turno
                </button>
            </form>
        </div>

        {{-- TURNOS CERRADOS --}}
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class='bx bx-list-check mr-2' style="color: #6B8CC7;"></i>
                    Turnos Cerrados
                </h2>
                <span class="text-xs bg-gray-100 text-gray-600 px-3 py-1 rounded-full font-medium">
                    {{ $turnosCerrados->count() }} registros
                </span>
            </div>

            {{-- Filtro por fecha --}}
            <form method="GET" action="{{ route('turnos-cerrados.index') }}" class="mb-4">
                <div class="flex gap-2">
                    <input name="fecha" type="date" value="{{ request('fecha') }}"
                           class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                    <button type="submit"
                            class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm transition-all">
                        <i class='bx bx-filter-alt'></i>
                    </button>
                    @if(request('fecha'))
                        <a href="{{ route('turnos-cerrados.index') }}"
                           class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm transition-all">
                            <i class='bx bx-x'></i>
                        </a>
                    @endif
                </div>
            </form>

            @if($turnosCerrados->isEmpty())
                <div class="text-center py-12 text-gray-400">
                    <i class='bx bx-unlock text-5xl mb-3'></i>
                    <p class="text-sm">No hay turnos cerrados</p>
                </div>
            @else
                <div class="space-y-3 max-h-96 overflow-y-auto pr-1">
                    @foreach($turnosCerrados as $turno)
                        <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                            <div class="flex items-start justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center
                                        {{ $turno->turno == 0 ? 'bg-yellow-100' : 'bg-blue-100' }}">
                                        <i class='bx {{ $turno->turno == 0 ? "bx-sun text-yellow-600" : "bx-moon text-blue-600" }} text-xl'></i>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-800">
                                            {{ \Carbon\Carbon::parse($turno->fecha)->format('d/m/Y') }}
                                            — {{ $turno->turno == 0 ? 'DÍA' : 'NOCHE' }}
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            Cerrado por <strong>{{ $turno->usuario->name ?? 'N/A' }}</strong>
                                            el {{ \Carbon\Carbon::parse($turno->cerrado_en)->format('d/m/Y H:i') }}
                                        </p>
                                        @if($turno->observacion)
                                            <p class="text-xs text-gray-600 mt-1 italic">
                                                "{{ $turno->observacion }}"
                                            </p>
                                        @endif
                                    </div>
                                </div>

                                {{-- Botón reabrir --}}
                                <form action="{{ route('turnos-cerrados.destroy') }}" method="POST"
                                    onsubmit="return confirmarReapertura({{ $turno->turno }}, '{{ \Carbon\Carbon::parse($turno->fecha)->format('d/m/Y') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="fecha" value="{{ $turno->fecha->format('Y-m-d') }}">
                                    <input type="hidden" name="turno" value="{{ $turno->turno }}">
                                    <button type="submit"
                                            class="text-xs bg-green-100 hover:bg-green-200 text-green-700 px-3 py-1.5 rounded-lg transition-all flex items-center">
                                        <i class='bx bx-lock-open mr-1'></i>
                                        Reabrir
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .turno-button { transition: all 0.3s ease; position: relative; }
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
        top: 8px; right: 8px;
        background: #10b981;
        color: white;
        width: 20px; height: 20px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: bold;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const turnoRadios = document.querySelectorAll('.turno-radio');
    const fechaInput  = document.querySelector('input[name="fecha"]');
    const btnCerrar   = document.getElementById('btn-cerrar-turno');
    const preview     = document.getElementById('preview-estado');

    async function verificarEstado() {
        const fecha = fechaInput.value;
        const turnoInput = document.querySelector('input[name="turno"]:checked');

        if (!fecha || !turnoInput) {
            preview.classList.add('hidden');
            btnCerrar.disabled = true;
            return;
        }

        const turno = turnoInput.value;
        const turnoNombre = turno == '0' ? 'DÍA' : 'NOCHE';
        const fechaFormateada = new Date(fecha + 'T00:00:00').toLocaleDateString('es-PE', {
            day: '2-digit', month: '2-digit', year: 'numeric'
        });

        try {
            const res  = await fetch(`/api/turnos/verificar?fecha=${fecha}&turno=${turno}`);
            const data = await res.json();

            preview.classList.remove('hidden');

            if (data.cerrado) {
                preview.className = 'rounded-lg p-3 border text-sm bg-red-50 border-red-300 text-red-700';
                preview.innerHTML = `<i class='bx bx-lock mr-1'></i> El turno <strong>${turnoNombre}</strong> del <strong>${fechaFormateada}</strong> ya está <strong>CERRADO</strong>. Para reabrirlo usa el panel de la derecha.`;
                btnCerrar.disabled = true;
                btnCerrar.classList.add('opacity-50', 'cursor-not-allowed');
            } else {
                preview.className = 'rounded-lg p-3 border text-sm bg-green-50 border-green-300 text-green-700';
                preview.innerHTML = `<i class='bx bx-lock-open mr-1'></i> El turno <strong>${turnoNombre}</strong> del <strong>${fechaFormateada}</strong> está <strong>ABIERTO</strong>. Puedes cerrarlo.`;
                btnCerrar.disabled = false;
                btnCerrar.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        } catch (err) {
            console.error('Error verificando turno:', err);
        }
    }

    function confirmarReapertura(turno, fecha) {
        const turnoNombre = turno == 0 ? 'DÍA' : 'NOCHE';
        return confirm(`¿Reabrir el turno ${turnoNombre} del ${fecha}?\nEsto permitirá editar registros de ese período.`);
    }

    turnoRadios.forEach(r => r.addEventListener('change', verificarEstado));
    fechaInput.addEventListener('change', verificarEstado);
});
</script>

@endsection