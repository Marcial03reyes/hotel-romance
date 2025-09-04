@extends('layouts.app')

@section('title', 'Agregar Inversión - Hotel Romance')

@section('content')

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Hotel Romance - Sistema Administrativo')</title>

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap');
        
        /* Paleta de colores azul Hotel Romance */
        :root {
            --primary-color: #88A6D3; /* Azul principal */
            --secondary-color: #6B8CC7; /* Azul secundario más oscuro */
            --tertiary-color: #A5BFDB; /* Azul terciario más claro */
            --accent-color: #4A73B8; /* Azul de acento oscuro */
            --light-blue: #C8D7ED; /* Azul muy claro */
            --sidebar-bg: #f4f8fc; /* Fondo sidebar azul muy suave */
            --hover-bg: #88A6D3; /* Color hover */
            --gradient-start: #88A6D3; /* Inicio gradiente */
            --gradient-end: #6B8CC7; /* Fin gradiente */
        }

        /* Contenedor principal del formulario*/
        .form-container {
            background: linear-gradient(135deg, var(--sidebar-bg) 0%, var(--light-blue) 100%);
            border-radius: 1rem;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(136, 166, 211, 0.1);
            border: 1px solid rgba(136, 166, 211, 0.2);
        }

        /* Estilos para inputs */
        .input-field {
            transition: all 0.3s ease;
            border: 2px solid #e5e7eb;
            border-radius: 0.5rem;
            padding: 0.75rem;
            background-color: white;
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

        /* Botón principal */
        .btn-romance {
            background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
            transition: all 0.3s ease;
            color: white;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(136, 166, 211, 0.3);
            border-radius: 0.5rem;
            padding: 0.75rem 1.5rem;
        }

        .btn-romance:hover {
            background: linear-gradient(135deg, var(--secondary-color), var(--accent-color));
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(136, 166, 211, 0.4);
        }

        /* Botón secundario (Cancelar) */
        .btn-secondary {
            background: white;
            color: var(--accent-color);
            border: 2px solid var(--tertiary-color);
            transition: all 0.3s ease;
            font-weight: 500;
            border-radius: 0.5rem;
            padding: 0.75rem 1.5rem;
        }

        .btn-secondary:hover {
            background: var(--sidebar-bg);
            border-color: var(--primary-color);
            color: var(--secondary-color);
            transform: translateY(-1px);
        }

        /* Etiquetas de campos */
        .field-label {
            color: var(--accent-color);
            font-weight: 600;
            margin-bottom: 0.5rem;
            display: block;
        }

        /* Header personalizado */
        .page-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 1.5rem;
            border-radius: 1rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 16px rgba(136, 166, 211, 0.3);
        }

        /* Mensajes de error */
        .error-container {
            background: linear-gradient(135deg, #fee2e2, #fecaca);
            border: 2px solid #f87171;
            border-radius: 0.75rem;
            padding: 1rem;
            color: #dc2626;
            box-shadow: 0 2px 8px rgba(248, 113, 113, 0.1);
        }

        /* Efectos visuales adicionales */
        .floating-icon {
            background: linear-gradient(135deg, var(--tertiary-color), var(--primary-color));
            color: white;
            border-radius: 50%;
            width: 3rem;
            height: 3rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            box-shadow: 0 4px 12px rgba(136, 166, 211, 0.3);
        }

        /* Animación sutil para el contenedor */
        .form-container {
            animation: fadeInUp 0.6s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Estilos para los tips informativos */
        .info-tip {
            background: rgba(136, 166, 211, 0.05);
            border: 1px solid rgba(136, 166, 211, 0.2);
            border-radius: 0.75rem;
            padding: 1rem;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .form-container {
                padding: 1.5rem;
                margin: 1rem;
            }
        }

        /* Textarea personalizado */
        .textarea-field {
            transition: all 0.3s ease;
            border: 2px solid #e5e7eb;
            border-radius: 0.5rem;
            padding: 0.75rem;
            background-color: white;
            resize: vertical;
            min-height: 100px;
        }

        .textarea-field:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(136, 166, 211, 0.1);
            outline: none;
            background-color: white;
        }

        .textarea-field:hover {
            border-color: var(--tertiary-color);
        }
    </style>
</head>

<body class="text-gray-800 font-inter bg-gray-50">

    <!-- Contenido principal sin margen para sidebar -->
    <main class="min-h-screen py-6 px-4" style="background: linear-gradient(135deg, var(--sidebar-bg) 0%, #ffffff 100%);">
        <div class="max-w-4xl mx-auto mt-1">
            
            <!-- Header de la página -->
            <div class="page-header">
                <div class="flex items-center">
                    <div class="floating-icon mr-4">
                        <i class='bx bx-trending-up'></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold">Agregar Nueva Inversión</h1>
                        <p class="text-blue-100 mt-1">Registra una nueva inversión de la empresa con todos los detalles necesarios</p>
                    </div>
                </div>
            </div>

            <!-- Formulario principal -->
            <div class="form-container">
                <form action="{{ route('inversiones.store') }}" method="POST">
                    @csrf
                    
                    <!-- Fila 1: Detalle de la Inversión -->
                    <div class="mb-6">
                        <label for="detalle" class="field-label">Detalle de la Inversión *</label>
                        <textarea name="detalle" id="detalle" rows="3"
                                  class="textarea-field w-full @error('detalle') border-red-500 @enderror" 
                                  placeholder="Describe la inversión a realizar (ej: Compra de equipos de cómputo para oficina administrativa, Adquisición de mobiliario para recepción, etc.)"
                                  required>{{ old('detalle') }}</textarea>
                        @error('detalle')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Fila 2: Monto y Método de Pago -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <!-- Monto -->
                        <div>
                            <label for="monto" class="field-label">Monto de la Inversión (S/) *</label>
                            <input type="number" step="0.01" min="0" max="999999.99" 
                                   name="monto" id="monto" 
                                   class="input-field w-full @error('monto') border-red-500 @enderror" 
                                   value="{{ old('monto') }}" 
                                   placeholder="0.00" required>
                            @error('monto')
                                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Método de Pago -->
                        <div>
                            <label for="id_met_pago" class="field-label">Método de Pago *</label>
                            <select name="id_met_pago" id="id_met_pago" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('id_met_pago') border-red-500 @enderror" required>
                                <option value="">Seleccionar método...</option>
                                @foreach($metodos as $metodo)
                                    <option value="{{ $metodo->id_met_pago }}" {{ old('id_met_pago') == $metodo->id_met_pago ? 'selected' : '' }}>
                                        {{ $metodo->met_pago }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_met_pago')
                                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Fila 3: Fecha -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <!-- Fecha -->
                        <div>
                            <label for="fecha_inversion" class="field-label">Fecha de la Inversión *</label>
                            <input type="date" name="fecha_inversion" id="fecha_inversion" 
                                   class="input-field w-full @error('fecha_inversion') border-red-500 @enderror" 
                                   value="{{ old('fecha_inversion', date('Y-m-d')) }}" required>
                            @error('fecha_inversion')
                                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Campo vacío para mantener el grid -->
                        <div class="hidden md:block"></div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-end">
                        <a href="{{ route('inversiones.index') }}" class="btn-secondary text-center">
                            <i class='bx bx-x mr-2'></i>Cancelar
                        </a>
                        <button type="submit" class="btn-romance">
                            <i class='bx bx-save mr-2'></i>Guardar Inversión
                        </button>
                    </div>
                </form>

                <!-- Información adicional -->
                <div class="info-tip mt-6">
                    <div class="flex items-start">
                        <i class='bx bx-lightbulb text-lg mr-2' style="color: var(--accent-color);"></i>
                        <div class="text-sm text-gray-600">
                            <p class="font-medium mb-1" style="color: var(--accent-color);">Consejos para el registro:</p>
                            <ul class="space-y-1 text-xs">
                                <li>• Describe la inversión de manera clara y detallada</li>
                                <li>• Verifica que el monto ingresado sea correcto</li>
                                <li>• Todos los campos marcados con * son obligatorios</li>
                                <li>• Los datos se pueden editar posteriormente desde la lista de inversiones</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // Mejorar la experiencia del usuario con validación en tiempo real
            const montoInput = document.querySelector('input[name="monto"]');
            
            if (montoInput) {
                montoInput.addEventListener('input', function() {
                    // Cambiar el color del borde según la validez
                    if (this.value && parseFloat(this.value) > 0) {
                        this.style.borderColor = 'var(--primary-color)';
                    } else {
                        this.style.borderColor = '#e5e7eb';
                    }
                });
            }
            
            // Animación de focus para los inputs
            const inputs = document.querySelectorAll('.input-field, .textarea-field');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.style.transform = 'scale(1.02)';
                });
                
                input.addEventListener('blur', function() {
                    this.style.transform = 'scale(1)';
                });
            });

            // Contador de caracteres para el textarea
            const detalleTextarea = document.querySelector('#detalle');
            if (detalleTextarea) {
                // Crear contador visual
                const counter = document.createElement('div');
                counter.className = 'text-xs text-gray-500 mt-1 text-right';
                counter.textContent = '0/255 caracteres';
                detalleTextarea.parentNode.appendChild(counter);

                detalleTextarea.addEventListener('input', function() {
                    const length = this.value.length;
                    counter.textContent = `${length}/255 caracteres`;
                    
                    if (length > 200) {
                        this.style.borderColor = '#f59e0b';
                        counter.style.color = '#f59e0b';
                    } else if (length > 0) {
                        this.style.borderColor = 'var(--primary-color)';
                        counter.style.color = '#6b7280';
                    } else {
                        this.style.borderColor = '#e5e7eb';
                        counter.style.color = '#6b7280';
                    }
                });
            }
        });
    </script>
</body>
</html>

@endsection