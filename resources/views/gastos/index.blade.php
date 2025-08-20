@extends('layouts.app')

@section('title', 'Gastos - Hotel Romance')

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
    
    .btn-romance {
        background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
        transition: all 0.3s ease;
        color: white;
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

    .btn-edit {
        background: var(--tertiary-color);
        color: var(--accent-color);
        border: 1px solid var(--light-blue);
        transition: all 0.3s ease;
    }

    .btn-edit:hover:not(.pointer-events-none) {
        background: var(--primary-color);
        color: white;
        transform: translateY(-1px);
    }

    .btn-delete {
        background: #fee2e2;
        color: #dc2626;
        border: 1px solid #fecaca;
        transition: all 0.3s ease;
    }

    .btn-delete:hover:not(.pointer-events-none) {
        background: #dc2626;
        color: white;
        transform: translateY(-1px);
    }
    
    .table-container {
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

    /* ← NUEVOS ESTILOS PARA COMPROBANTES */
    .badge-boleta {
        background: linear-gradient(135deg, #dbeafe, #bfdbfe);
        color: #1e40af;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .badge-factura {
        background: linear-gradient(135deg, #dcfce7, #bbf7d0);
        color: #166534;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .badge-ninguno {
        background: linear-gradient(135deg, #f3f4f6, #e5e7eb);
        color: #6b7280;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .codigo-comprobante {
        background: #f8fafc;
        color: var(--accent-color);
        padding: 0.25rem 0.5rem;
        border-radius: 0.375rem;
        font-family: 'Courier New', monospace;
        font-size: 0.8rem;
        border: 1px solid var(--light-blue);
    }

    /* Iconos con color azul */
    .icon-azul {
        color: var(--secondary-color);
    }

    /* Enlaces de navegación */
    .nav-link:hover {
        color: var(--secondary-color);
    }

    /* Checkbox personalizado */
    .checkbox-azul {
        accent-color: var(--primary-color);
        transform: scale(1.2);
    }

    /* Header de tabla con tema azul */
    .table-header {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border-bottom: 2px solid var(--light-blue);
    }

    .table-header th {
        color: var(--accent-color);
        font-weight: 600;
        font-size: 0.875rem;
    }

    /* Filas de tabla */
    .table-row:hover {
        background-color: #f8fafc;
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

    /* Mensaje de éxito personalizado */
    .success-message {
        background: linear-gradient(135deg, #ecfdf5 0%, #f0fdf4 100%);
        border-color: #86efac;
        color: #166534;
    }

    /* Estadísticas */
    .stats-card {
        background: linear-gradient(135deg, var(--light-blue) 0%, #e0f2fe 100%);
        border: 1px solid var(--tertiary-color);
    }

    .stats-icon {
        background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
    }
</style>

<div class="container mx-auto py-6 px-4">
    
    <!-- Header con información de gastos -->
    <div class="mb-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">
                    <i class='bx bx-wallet mr-2 icon-azul'></i>
                    Gestión de Gastos
                </h1>
                <p class="text-gray-600">Administra y controla todos los gastos del hotel</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('gastos.create') }}"
                   class="btn-romance px-6 py-3 rounded-lg font-medium shadow-lg">
                    <i class='bx bx-plus mr-2'></i>
                    Agregar Gasto
                </a>
            </div>
        </div>
        
        <div class="flex items-center space-x-4 text-sm text-gray-600">
            <a href="{{ route('dashboard') }}" 
               class="inline-flex items-center nav-link transition-colors">
                <i class='bx bx-arrow-back mr-1'></i>
                Volver al dashboard
            </a>
            <span>•</span>
            <span class="text-gray-500">
                <i class='bx bx-info-circle mr-1'></i>
                Selecciona las filas para habilitar las acciones
            </span>
        </div>
    </div>

    <!-- Estadísticas rápidas -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="stats-card p-4 rounded-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total de Gastos</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $gastos->count() }}</p>
                </div>
                <div class="stats-icon w-12 h-12 rounded-full flex items-center justify-center text-white">
                    <i class='bx bx-receipt text-xl'></i>
                </div>
            </div>
        </div>
        
        <div class="stats-card p-4 rounded-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Monto Total</p>
                    <p class="text-2xl font-bold text-gray-800">S/ {{ number_format($gastos->sum('monto'), 2) }}</p>
                </div>
                <div class="stats-icon w-12 h-12 rounded-full flex items-center justify-center text-white">
                    <i class='bx bx-money text-xl'></i>
                </div>
            </div>
        </div>
        
        <div class="stats-card p-4 rounded-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Monto Este Mes</p>
                    <p class="text-2xl font-bold text-gray-800">S/ {{ number_format($gastos->where('fecha_gasto', '>=', now()->startOfMonth())->sum('monto'), 2) }}</p>
                </div>
                <div class="stats-icon w-12 h-12 rounded-full flex items-center justify-center text-white">
                    <i class='bx bx-calendar text-xl'></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Mensaje de éxito -->
    @if(session('success'))
        <div class="success-message rounded-lg border p-4 mb-6 shadow-sm">
            <div class="flex items-center">
                <i class='bx bx-check-circle mr-2 text-lg'></i>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <!-- Botones de acción -->
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center space-x-3">
            <button id="editSelected" 
                    class="btn-edit inline-flex items-center rounded-lg px-4 py-2 text-sm font-medium opacity-50 pointer-events-none transition-all">
                <i class='bx bx-edit mr-2'></i>
                Editar Seleccionado
            </button>
            
            <button id="deleteSelected" 
                    class="btn-delete inline-flex items-center rounded-lg px-4 py-2 text-sm font-medium opacity-50 pointer-events-none transition-all">
                <i class='bx bx-trash mr-2'></i>
                Eliminar Seleccionado
            </button>
        </div>
        
        <span id="selectionCounter" class="text-sm text-gray-500">
            <i class='bx bx-info-circle mr-1'></i>
            0 gasto(s) seleccionado(s)
        </span>
    </div>

    <!-- Tabla de gastos -->
    <div class="table-container">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                <i class='bx bx-list-ul mr-2 icon-azul'></i>
                Lista de Gastos
            </h2>
            <span class="badge-info">
                <i class='bx bx-info-circle mr-1'></i>
                {{ $gastos->count() }} registro(s) total(es)
            </span>
        </div>
        
        <div class="overflow-x-auto rounded-xl border border-gray-200">
            <table class="min-w-full text-sm">
                <thead class="table-header">
                <tr>
                    <th class="px-6 py-4 text-left">
                        <i class='bx bx-check-square mr-1'></i>
                        
                    </th>
                    <th class="px-6 py-4 text-left">
                        <i class='bx bx-tag mr-1'></i>
                        Detalle
                    </th>
                    <th class="px-6 py-4 text-left">
                        <i class='bx bx-money mr-1'></i>
                        Monto
                    </th>
                    <th class="px-6 py-4 text-left">
                        <i class='bx bx-credit-card mr-1'></i>
                        Método de Pago
                    </th>
                    <th class="px-6 py-4 text-left">
                        <i class='bx bx-calendar mr-1'></i>
                        Fecha
                    </th>
                    <!-- ← NUEVAS COLUMNAS -->
                    <th class="px-6 py-4 text-left">
                        <i class='bx bx-receipt mr-1'></i>
                        Tipo Comprobante
                    </th>
                    <th class="px-6 py-4 text-left">
                        <i class='bx bx-barcode mr-1'></i>
                        Código
                    </th>
                    <th class="px-6 py-4 text-right">
                        <i class='bx bx-file-blank mr-1'></i>
                        Ver Comprobante
                    </th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                @forelse($gastos as $g)
                    <tr class="table-row transition-colors">
                        <td class="px-6 py-4">
                            <input type="checkbox" class="checkbox-azul row-check" data-row="{{ $g->id_gasto }}">
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <i class='bx bx-receipt mr-2 text-gray-400'></i>
                                <span class="font-medium text-gray-900">{{ $g->tipo }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-semibold text-green-600">S/ {{ number_format($g->monto, 2) }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $g->met_pago }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center text-gray-600">
                                <i class='bx bx-time mr-1'></i>
                                {{ \Illuminate\Support\Carbon::parse($g->fecha_gasto)->format('d/m/Y') }}
                            </div>
                        </td>
                        
                        <!-- ← NUEVA COLUMNA: TIPO DE COMPROBANTE -->
                        <td class="px-6 py-4">
                            @if($g->tipo_comprobante == 'BOLETA')
                                <span class="badge-boleta">
                                    <i class='bx bx-receipt mr-1'></i>
                                    BOLETA
                                </span>
                            @elseif($g->tipo_comprobante == 'FACTURA')
                                <span class="badge-factura">
                                    <i class='bx bx-file-blank mr-1'></i>
                                    FACTURA
                                </span>
                            @else
                                <span class="badge-ninguno">
                                    <i class='bx bx-x mr-1'></i>
                                    NINGUNO
                                </span>
                            @endif
                        </td>
                        
                        <!-- ← NUEVA COLUMNA: CÓDIGO DE COMPROBANTE -->
                        <td class="px-6 py-4">
                            @if($g->codigo_comprobante)
                                <span class="codigo-comprobante">
                                    <i class='bx bx-barcode mr-1'></i>
                                    {{ $g->codigo_comprobante }}
                                </span>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        
                        <td class="px-6 py-4 text-right space-x-2">
                            @if($g->comprobante)
                                <a href="{{ route('gastos.comprobante', $g->id_gasto) }}" 
                                   class="inline-flex items-center rounded-lg px-3 py-1.5 text-xs font-medium bg-green-100 text-green-800 hover:bg-green-200 transition-all">
                                    <i class='bx bx-file-blank mr-1'></i>
                                    Ver comprobante
                                </a>
                            @else
                                <span class="inline-flex items-center rounded-lg px-3 py-1.5 text-xs font-medium bg-gray-100 text-gray-500">
                                    <i class='bx bx-x mr-1'></i>
                                    Sin comprobante
                                </span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <i class='bx bx-receipt text-4xl text-gray-300 mb-2'></i>
                                <span class="text-gray-500 font-medium">No hay gastos registrados</span>
                                <span class="text-gray-400 text-sm">Comienza agregando tu primer gasto</span>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Información adicional -->
    <div class="mt-8 p-4 info-box border rounded-lg">
        <div class="flex items-start">
            <i class='bx bx-info-circle info-icon text-lg mr-2 mt-1'></i>
            <div class="text-sm info-text">
                <p class="font-medium mb-1">Información importante:</p>
                <ul class="list-disc list-inside space-y-1">
                    <li>Selecciona el checkbox de una fila para habilitar las acciones de editar y eliminar</li>
                    <li>Los gastos se ordenan por fecha de registro de más reciente a más antiguo</li>
                    <li>Los comprobantes en verde indican que tienen archivo adjunto</li>
                    <li><strong>Nuevo:</strong> Ahora puedes especificar el tipo de comprobante (BOLETA, FACTURA o NINGUNO) y su código</li>
                    <li>Todos los montos se muestran en soles peruanos (S/)</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const editButton = document.getElementById('editSelected');
    const deleteButton = document.getElementById('deleteSelected');
    const selectionCounter = document.getElementById('selectionCounter');
    let selectedItems = [];

    // Habilitar acciones según selección
    function updateActionButtons() {
        const selectedCount = selectedItems.length;
        
        // Actualizar contador
        selectionCounter.innerHTML = `<i class='bx bx-info-circle mr-1'></i>${selectedCount} gasto(s) seleccionado(s)`;
        
        if (selectedCount === 0) {
            // Sin selección - deshabilitar botones
            editButton.classList.add('opacity-50', 'pointer-events-none');
            deleteButton.classList.add('opacity-50', 'pointer-events-none');
        } else if (selectedCount === 1) {
            // Un elemento - habilitar ambos botones
            editButton.classList.remove('opacity-50', 'pointer-events-none');
            deleteButton.classList.remove('opacity-50', 'pointer-events-none');
        } else {
            // Múltiple selección - solo eliminar
            editButton.classList.add('opacity-50', 'pointer-events-none');
            deleteButton.classList.remove('opacity-50', 'pointer-events-none');
        }
    }

    // Manejar checkboxes individuales
    document.querySelectorAll('.row-check').forEach(chk => {
        chk.addEventListener('change', () => {
            const rowId = chk.dataset.row;
            const row = chk.closest('tr');

            if (chk.checked) {
                selectedItems.push(rowId);
                row.classList.add('bg-blue-50', 'border-blue-200');
            } else {
                selectedItems = selectedItems.filter(id => id !== rowId);
                row.classList.remove('bg-blue-50', 'border-blue-200');
            }
            
            updateActionButtons();
        });
    });

    // Botón editar
    editButton.addEventListener('click', () => {
        if (selectedItems.length === 1) {
            window.location.href = `{{ url('gastos') }}/${selectedItems[0]}/edit`;
        }
    });

    // Botón eliminar
    deleteButton.addEventListener('click', () => {
        if (selectedItems.length > 0) {
            const confirmMessage = selectedItems.length === 1 
                ? '¿Estás seguro de eliminar este gasto?' 
                : `¿Estás seguro de eliminar ${selectedItems.length} gastos?`;
                
            if (confirm(confirmMessage)) {
                // Crear formulario para eliminación múltiple
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("gastos.destroy.multiple") }}';
                
                // Token CSRF
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);
                
                // Método DELETE
                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';
                form.appendChild(methodField);
                
                // IDs seleccionados
                selectedItems.forEach(id => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'ids[]';
                    input.value = id;
                    form.appendChild(input);
                });
                
                document.body.appendChild(form);
                form.submit();
            }
        }
    });

    // Auto-focus en el botón de agregar gasto cuando no hay gastos
    const noDataMessage = document.querySelector('td[colspan="8"]');
    if (noDataMessage) {
        const addButton = document.querySelector('.btn-romance');
        if (addButton) {
            addButton.classList.add('animate-pulse');
        }
    }
});
</script>

@endsection