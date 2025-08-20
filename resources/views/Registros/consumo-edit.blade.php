@extends('layouts.app')

@section('title', 'Gestionar Consumos - Hotel Romance')

@section('content')

<style>
    /* Reset y base */
    * {
        box-sizing: border-box;
    }

    /* Paleta de colores azul Hotel Romance */
    :root {
        --primary-color: #88A6D3;
        --secondary-color: #6B8CC7;
        --tertiary-color: #A5BFDB;
        --accent-color: #4A73B8;
        --light-blue: #C8D7ED;
        --sidebar-bg: #f4f8fc;
        --hover-bg: #88A6D3;
        --gradient-start: #88A6D3;
        --gradient-end: #6B8CC7;
    }

    /* Layout principal */
    .container-custom {
        background: linear-gradient(135deg, #f4f8fc 0%, #e8f2ff 100%);
        min-height: 100vh;
        padding: 24px 16px;
    }

    .container-main {
        max-width: 1200px;
        margin: 0 auto;
    }
    
    .card-custom {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(136, 166, 211, 0.15);
        border: 1px solid rgba(136, 166, 211, 0.2);
        margin-bottom: 24px;
    }

    /* Header */
    .header-section {
        padding: 24px;
    }

    .header-flex {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 16px;
    }

    .header-title {
        font-size: 2rem;
        font-weight: bold;
        color: #1f2937;
        margin-bottom: 8px;
    }

    .header-subtitle {
        color: #6b7280;
    }

    /* Botones */
    .btn-romance {
        background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
        color: white;
        padding: 12px 24px;
        border-radius: 8px;
        font-weight: 500;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
    }
    
    .btn-romance:hover {
        background: linear-gradient(135deg, var(--secondary-color), var(--accent-color));
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(136, 166, 211, 0.3);
        color: white;
        text-decoration: none;
    }

    .btn-gray {
        background-color: #6b7280;
        color: white;
        padding: 12px 24px;
        border-radius: 8px;
        font-weight: 500;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        transition: all 0.3s ease;
    }

    .btn-gray:hover {
        background-color: #4b5563;
        color: white;
        text-decoration: none;
    }

    .btn-sm {
        padding: 6px 12px;
        font-size: 0.75rem;
        border-radius: 4px;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-blue { background-color: #3b82f6; color: white; }
    .btn-blue:hover { background-color: #2563eb; }
    .btn-red { background-color: #ef4444; color: white; }
    .btn-red:hover { background-color: #dc2626; }
    .btn-gray-sm { background-color: #6b7280; color: white; }
    .btn-gray-sm:hover { background-color: #4b5563; }

    /* Grid de información */
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        background-color: #f9fafb;
        padding: 16px;
        border-radius: 8px;
    }

    .info-item {
        color: #374151;
    }

    .info-label {
        font-size: 0.875rem;
        color: #6b7280;
        margin-bottom: 4px;
    }

    .info-value {
        font-weight: 600;
        color: #1f2937;
    }

    .info-value.blue {
        color: #2563eb;
    }

    .info-value.mono {
        font-family: monospace;
    }

    /* Mensajes */
    .alert {
        padding: 16px;
        border-radius: 8px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
    }

    .alert-success {
        background-color: #f0fdf4;
        border: 1px solid #bbf7d0;
        color: #166534;
    }

    .alert-error {
        background-color: #fef2f2;
        border: 1px solid #fecaca;
        color: #991b1b;
    }

    /* Formularios */
    .form-section {
        padding: 24px;
    }

    .form-title {
        font-size: 1.25rem;
        font-weight: bold;
        color: #1f2937;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-bottom: 16px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-label {
        font-size: 0.875rem;
        font-weight: 500;
        color: #374151;
        margin-bottom: 8px;
    }

    .form-input {
        width: 100%;
        padding: 12px;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        outline: none;
        transition: all 0.2s ease;
    }

    .form-input:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(136, 166, 211, 0.1);
    }

    .form-input.sm {
        padding: 4px 8px;
        font-size: 0.875rem;
    }

    .form-input.xs {
        width: 80px;
        text-align: center;
    }

    .form-input.w-auto {
        width: auto;
    }

    /* Tabla */
    .table-container {
        overflow: hidden;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(136, 166, 211, 0.15);
    }

    .table-section {
        padding: 24px;
        border-bottom: 1px solid #e5e7eb;
    }

    .table-title {
        font-size: 1.25rem;
        font-weight: bold;
        color: #1f2937;
        display: flex;
        align-items: center;
    }

    .table-wrapper {
        overflow-x: auto;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
        background: white;
    }

    .table-header {
        background: linear-gradient(135deg, var(--secondary-color), var(--accent-color));
        color: white;
    }

    .table-header th {
        padding: 16px;
        text-align: left;
        font-size: 0.75rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .table tbody tr {
        border-bottom: 1px solid #e5e7eb;
        transition: all 0.2s ease;
    }

    .table tbody tr:hover {
        background-color: #f4f8fc;
    }

    .table td {
        padding: 16px;
        vertical-align: middle;
    }

    .table tfoot {
        background-color: #f9fafb;
    }

    .table tfoot td {
        padding: 16px;
        font-weight: bold;
    }

    /* Badges */
    .badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 12px;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 500;
    }
    
    .badge-si {
        background-color: #dcfce7;
        color: #166534;
    }
    
    .badge-no {
        background-color: #f3f4f6;
        color: #374151;
    }

    /* Estado vacío */
    .empty-state {
        padding: 48px;
        text-align: center;
        color: #6b7280;
    }

    .empty-icon {
        font-size: 4rem;
        color: #d1d5db;
        margin-bottom: 16px;
    }

    .empty-title {
        font-size: 1.125rem;
        margin-bottom: 8px;
    }

    .empty-subtitle {
        font-size: 0.875rem;
    }

    /* Acciones */
    .actions-flex {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .hidden {
        display: none !important;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .container-custom {
            padding: 16px 8px;
        }

        .header-flex {
            flex-direction: column;
            align-items: stretch;
            gap: 16px;
        }

        .form-grid {
            grid-template-columns: 1fr;
        }

        .info-grid {
            grid-template-columns: 1fr;
        }

        .table-wrapper {
            font-size: 0.875rem;
        }

        .table th,
        .table td {
            padding: 8px;
        }
    }
</style>

<div class="container-custom">
    <div class="container-main">
        
        <!-- Header con información de la estadía -->
        <div class="card-custom">
            <div class="header-section">
                <div class="header-flex">
                    <div>
                        <h1 class="header-title">Gestionar Consumos</h1>
                        <p class="header-subtitle">Edita los consumos de la estadía</p>
                    </div>
                    <a href="{{ route('registros.index') }}" class="btn-gray">
                        <i class='bx bx-arrow-back' style="margin-right: 8px;"></i>
                        Volver a registros
                    </a>
                </div>

                <!-- Información de la estadía -->
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Cliente:</div>
                        <div class="info-value">{{ $cliente->nombre_apellido ?? 'Sin nombre' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Documento:</div>
                        <div class="info-value mono">{{ $estadia->doc_identidad }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Habitación:</div>
                        <div class="info-value blue">{{ $estadia->habitacion }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Fecha:</div>
                        <div class="info-value">{{ \Carbon\Carbon::parse($estadia->fecha_ingreso)->format('d/m/Y') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mensajes de éxito/error -->
        @if(session('success'))
            <div class="alert alert-success">
                <i class='bx bx-check-circle' style="margin-right: 8px;"></i>
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-error">
                <div>
                    <div style="display: flex; align-items: center; margin-bottom: 8px;">
                        <i class='bx bx-error-circle' style="margin-right: 8px;"></i>
                        Se encontraron errores:
                    </div>
                    <ul style="list-style: disc; padding-left: 20px;">
                        @foreach ($errors->all() as $error)
                            <li style="font-size: 0.875rem;">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <!-- Formulario para agregar nuevo consumo -->
        <div class="card-custom">
            <div class="form-section">
                <h2 class="form-title">
                    <i class='bx bx-plus-circle' style="margin-right: 8px; color: #2563eb;"></i>
                    Agregar Nuevo Consumo
                </h2>
                
                <form action="{{ route('registros.consumo.store', $estadia->id_estadia) }}" method="POST">
                    @csrf
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Producto</label>
                            <select name="id_prod_bod" class="form-input" required>
                                <option value="">Seleccionar producto...</option>
                                @foreach($productos as $producto)
                                    <option value="{{ $producto->id_prod_bod }}" {{ old('id_prod_bod') == $producto->id_prod_bod ? 'selected' : '' }}>
                                        {{ $producto->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Cantidad</label>
                            <input type="number" name="cantidad" min="1" max="999" 
                                   class="form-input" value="{{ old('cantidad', 1) }}" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Precio Unitario</label>
                            <input type="number" name="precio_unitario" step="0.01" min="0.01" max="99999.99"
                                   class="form-input" value="{{ old('precio_unitario') }}" placeholder="0.00" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Método de Pago</label>
                            <select name="id_met_pago" class="form-input" required>
                                <option value="">Seleccionar método...</option>
                                @foreach($metodos as $metodo)
                                    <option value="{{ $metodo->id_met_pago }}" {{ old('id_met_pago') == $metodo->id_met_pago ? 'selected' : '' }}>
                                        {{ $metodo->met_pago }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Comprobante</label>
                            <select name="comprobante" class="form-input">
                                <option value="NO" {{ old('comprobante', 'NO') == 'NO' ? 'selected' : '' }}>NO</option>
                                <option value="SI" {{ old('comprobante') == 'SI' ? 'selected' : '' }}>SI</option>
                            </select>
                        </div>
                    </div>
                    
                    <div style="margin-top: 16px;">
                        <button type="submit" class="btn-romance">
                            <i class='bx bx-plus' style="margin-right: 8px;"></i>
                            Agregar Consumo
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Lista de consumos existentes -->
        <div class="card-custom table-container">
            <div class="table-section">
                <h2 class="table-title">
                    <i class='bx bx-list-ul' style="margin-right: 8px; color: #2563eb;"></i>
                    Consumos Registrados ({{ count($consumos) }})
                </h2>
            </div>
            
            @if(count($consumos) > 0)
                <div class="table-wrapper">
                    <table class="table">
                        <thead class="table-header">
                            <tr>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Precio Unit.</th>
                                <th>Total</th>
                                <th>Método</th>
                                <th>Comprobante</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($consumos as $consumo)
                                <tr id="row-{{ $consumo->id_compra }}">
                                    <td>
                                        <div style="font-weight: 500;">{{ $consumo->producto }}</div>
                                    </td>
                                    <td>
                                        <input type="number" min="1" max="999" 
                                               class="edit-cantidad form-input sm xs"
                                               value="{{ $consumo->cantidad }}" data-original="{{ $consumo->cantidad }}">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" min="0.01" max="99999.99"
                                               class="edit-precio form-input sm"
                                               style="width: 100px;"
                                               value="{{ $consumo->precio_unitario }}" data-original="{{ $consumo->precio_unitario }}">
                                    </td>
                                    <td>
                                        <span class="total-display" style="font-weight: 600;">S/ {{ number_format($consumo->total, 2) }}</span>
                                    </td>
                                    <td>
                                        <select class="edit-metodo form-input sm" data-original="{{ $consumo->id_met_pago }}">
                                            @foreach($metodos as $metodo)
                                                <option value="{{ $metodo->id_met_pago }}" {{ $consumo->id_met_pago == $metodo->id_met_pago ? 'selected' : '' }}>
                                                    {{ $metodo->met_pago }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select class="edit-comprobante form-input sm" data-original="{{ $consumo->comprobante }}">
                                            <option value="NO" {{ $consumo->comprobante == 'NO' ? 'selected' : '' }}>NO</option>
                                            <option value="SI" {{ $consumo->comprobante == 'SI' ? 'selected' : '' }}>SI</option>
                                        </select>
                                    </td>
                                    <td>
                                        <div class="actions-flex">
                                            <button onclick="updateConsumo({{ $consumo->id_compra }})" 
                                                    class="update-btn btn-sm btn-blue hidden">
                                                <i class='bx bx-save' style="margin-right: 4px;"></i>Guardar
                                            </button>
                                            <button onclick="resetConsumo({{ $consumo->id_compra }})"
                                                    class="cancel-btn btn-sm btn-gray-sm hidden">
                                                <i class='bx bx-x' style="margin-right: 4px;"></i>Cancelar
                                            </button>
                                            <button onclick="deleteConsumo({{ $consumo->id_compra }}, '{{ $consumo->producto }}')"
                                                    class="btn-sm btn-red">
                                                <i class='bx bx-trash' style="margin-right: 4px;"></i>Eliminar
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" style="text-align: right; font-weight: 600;">Total General:</td>
                                <td style="font-weight: bold; font-size: 1.125rem; color: #2563eb;">
                                    S/ {{ number_format($consumos->sum('total'), 2) }}
                                </td>
                                <td colspan="3"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <div class="empty-state">
                    <i class='bx bx-package empty-icon'></i>
                    <p class="empty-title">No hay consumos registrados para esta estadía.</p>
                    <p class="empty-subtitle">Usa el formulario de arriba para agregar el primer consumo.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Detectar cambios en los campos editables
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('edit-cantidad') || 
            e.target.classList.contains('edit-precio') || 
            e.target.classList.contains('edit-metodo') || 
            e.target.classList.contains('edit-comprobante')) {
            
            const row = e.target.closest('tr');
            const consumoId = row.id.replace('row-', '');
            
            // Mostrar botones de guardar/cancelar
            showEditButtons(consumoId);
            
            // Actualizar total si cambió cantidad o precio
            if (e.target.classList.contains('edit-cantidad') || e.target.classList.contains('edit-precio')) {
                updateRowTotal(row);
            }
        }
    });
});

function showEditButtons(consumoId) {
    const row = document.getElementById(`row-${consumoId}`);
    const updateBtn = row.querySelector('.update-btn');
    const cancelBtn = row.querySelector('.cancel-btn');
    
    updateBtn.classList.remove('hidden');
    cancelBtn.classList.remove('hidden');
}

function updateRowTotal(row) {
    const cantidad = parseFloat(row.querySelector('.edit-cantidad').value) || 0;
    const precio = parseFloat(row.querySelector('.edit-precio').value) || 0;
    const total = cantidad * precio;
    
    row.querySelector('.total-display').textContent = `S/ ${total.toFixed(2)}`;
}

function updateConsumo(consumoId) {
    const row = document.getElementById(`row-${consumoId}`);
    const cantidad = row.querySelector('.edit-cantidad').value;
    const precio = row.querySelector('.edit-precio').value;
    const metodo = row.querySelector('.edit-metodo').value;
    const comprobante = row.querySelector('.edit-comprobante').value;
    
    // Crear formulario dinámico para enviar PUT
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/registros/{{ $estadia->id_estadia }}/consumo/${consumoId}`;
    
    // Token CSRF
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (csrfToken) {
        const tokenInput = document.createElement('input');
        tokenInput.type = 'hidden';
        tokenInput.name = '_token';
        tokenInput.value = csrfToken.getAttribute('content');
        form.appendChild(tokenInput);
    }
    
    // Método PUT
    const methodInput = document.createElement('input');
    methodInput.type = 'hidden';
    methodInput.name = '_method';
    methodInput.value = 'PUT';
    form.appendChild(methodInput);
    
    // Datos
    const inputs = [
        {name: 'cantidad', value: cantidad},
        {name: 'precio_unitario', value: precio},
        {name: 'id_met_pago', value: metodo},
        {name: 'comprobante', value: comprobante}
    ];
    
    inputs.forEach(input => {
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = input.name;
        hiddenInput.value = input.value;
        form.appendChild(hiddenInput);
    });
    
    document.body.appendChild(form);
    form.submit();
}

function resetConsumo(consumoId) {
    const row = document.getElementById(`row-${consumoId}`);
    
    // Restaurar valores originales
    row.querySelector('.edit-cantidad').value = row.querySelector('.edit-cantidad').dataset.original;
    row.querySelector('.edit-precio').value = row.querySelector('.edit-precio').dataset.original;
    row.querySelector('.edit-metodo').value = row.querySelector('.edit-metodo').dataset.original;
    row.querySelector('.edit-comprobante').value = row.querySelector('.edit-comprobante').dataset.original;
    
    // Recalcular total
    updateRowTotal(row);
    
    // Ocultar botones
    row.querySelector('.update-btn').classList.add('hidden');
    row.querySelector('.cancel-btn').classList.add('hidden');
}

function deleteConsumo(consumoId, productoNombre) {
    if (confirm(`¿Eliminar el consumo de "${productoNombre}"?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/registros/{{ $estadia->id_estadia }}/consumo/${consumoId}`;
        
        // Token CSRF
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            const tokenInput = document.createElement('input');
            tokenInput.type = 'hidden';
            tokenInput.name = '_token';
            tokenInput.value = csrfToken.getAttribute('content');
            form.appendChild(tokenInput);
        }
        
        // Método DELETE
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

@endsection