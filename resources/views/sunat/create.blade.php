@extends('layouts.app')

@section('title', 'Agregar Comprobante SUNAT - Hotel Romance')

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

        /* File input styling */
        .file-input {
            border: 2px dashed var(--tertiary-color);
            border-radius: 0.5rem;
            padding: 1rem;
            text-align: center;
            transition: all 0.3s ease;
            background: rgba(136, 166, 211, 0.05);
        }

        .file-input:hover {
            border-color: var(--primary-color);
            background: rgba(136, 166, 211, 0.1);
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
                        <i class='bx bx-receipt'></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold">Agregar Nuevo Comprobante SUNAT</h1>
                        <p class="text-blue-100 mt-1">Registra un nuevo comprobante fiscal con todos los detalles necesarios</p>
                    </div>
                </div>
            </div>

            <!-- Formulario principal -->
            <div class="form-container">
                
                <!-- Mensajes de error -->
                @if ($errors->any())
                    <div class="error-container mb-6">
                        <div class="flex items-center mb-2">
                            <i class='bx bx-error-circle mr-2 text-lg'></i>
                            <span class="font-semibold">Se encontraron errores en el formulario:</span>
                        </div>
                        <ul class="list-disc ps-5 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li class="text-sm">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('sunat.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <!-- Fila 1: Tipo de Comprobante y Código -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <!-- Tipo de Comprobante -->
                        <div>
                            <label for="tipo_comprobante" class="field-label">Tipo de Comprobante *</label>
                            <select name="tipo_comprobante" id="tipo_comprobante" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('tipo_comprobante') border-red-500 @enderror" required>
                                <option value="">Seleccionar tipo...</option>
                                <option value="NINGUNO" {{ old('tipo_comprobante') == 'NINGUNO' ? 'selected' : '' }}>NINGUNO</option>
                                <option value="BOLETA" {{ old('tipo_comprobante') == 'BOLETA' ? 'selected' : '' }}>BOLETA</option>
                                <option value="FACTURA" {{ old('tipo_comprobante') == 'FACTURA' ? 'selected' : '' }}>FACTURA</option>
                            </select>
                            @error('tipo_comprobante')
                                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Código de Comprobante -->
                        <div>
                            <label for="codigo_comprobante" class="field-label">Código de Comprobante</label>
                            <input type="text" name="codigo_comprobante" id="codigo_comprobante" 
                                   class="input-field w-full @error('codigo_comprobante') border-red-500 @enderror" 
                                   value="{{ old('codigo_comprobante') }}" 
                                   placeholder="Ingrese el código del comprobante" 
                                   maxlength="50"
                                   disabled>
                            <small class="text-sm text-gray-500 mt-1">
                                <span id="codigo_help_text">Solo se habilita cuando seleccionas BOLETA o FACTURA</span>
                            </small>
                            @error('codigo_comprobante')
                                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Fila 2: Monto y Fecha -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <!-- Monto -->
                        <div>
                            <label for="monto" class="field-label">Monto *</label>
                            <input type="number" step="0.01" min="0" max="99999999.99" 
                                   name="monto" id="monto" 
                                   class="input-field w-full @error('monto') border-red-500 @enderror" 
                                   value="{{ old('monto') }}" 
                                   placeholder="0.00" required>
                            @error('monto')
                                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Fecha -->
                        <div>
                            <label for="fecha_comprobante" class="field-label">Fecha del Comprobante *</label>
                            <input type="date" name="fecha_comprobante" id="fecha_comprobante" 
                                   class="input-field w-full @error('fecha_comprobante') border-red-500 @enderror" 
                                   value="{{ old('fecha_comprobante', date('Y-m-d')) }}" required>
                            @error('fecha_comprobante')
                                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Archivo de Comprobante -->
                    <div class="mb-6">
                        <label for="archivo_comprobante" class="field-label">Archivo de Comprobante (Opcional)</label>
                        <div class="file-input">
                            <input type="file" name="archivo_comprobante" id="archivo_comprobante" 
                                   class="w-full @error('archivo_comprobante') border-red-500 @enderror"
                                   accept=".jpg,.jpeg,.png,.pdf">
                            <small class="text-sm text-gray-500 mt-2 block">
                                <i class='bx bx-upload mr-1'></i>
                                Formatos permitidos: JPG, PNG, PDF. Tamaño máximo: 5MB
                            </small>
                        </div>
                        @error('archivo_comprobante')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Botones de acción -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-end">
                        <a href="{{ route('sunat.index') }}" class="btn-secondary text-center">
                            <i class='bx bx-x mr-2'></i>Cancelar
                        </a>
                        <button type="submit" class="btn-romance">
                            <i class='bx bx-save mr-2'></i>Guardar Comprobante
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
                                <li>• Verifica que el monto ingresado sea correcto</li>
                                <li>• El código de comprobante es obligatorio para BOLETA y FACTURA</li>
                                <li>• Puedes adjuntar una imagen o PDF del comprobante fiscal</li>
                                <li>• Los datos se pueden editar posteriormente desde la lista de comprobantes</li>
                                <li>• Asegúrate de seleccionar la fecha correcta del comprobante</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tipoComprobanteSelect = document.getElementById('tipo_comprobante');
            const codigoComprobanteInput = document.getElementById('codigo_comprobante');
            const codigoHelpText = document.getElementById('codigo_help_text');
            
            function toggleCodigoComprobante() {
                const tipoSeleccionado = tipoComprobanteSelect.value;
                
                if (tipoSeleccionado === 'BOLETA' || tipoSeleccionado === 'FACTURA') {
                    // Habilitar campo código
                    codigoComprobanteInput.disabled = false;
                    codigoComprobanteInput.required = true;
                    codigoComprobanteInput.focus();
                    codigoHelpText.textContent = 'Código obligatorio para ' + tipoSeleccionado;
                    codigoHelpText.style.color = 'var(--accent-color)';
                    codigoComprobanteInput.style.borderColor = 'var(--primary-color)';
                } else {
                    // Deshabilitar campo código
                    codigoComprobanteInput.disabled = true;
                    codigoComprobanteInput.required = false;
                    codigoComprobanteInput.value = '';
                    codigoHelpText.textContent = 'Solo se habilita cuando seleccionas BOLETA o FACTURA';
                    codigoHelpText.style.color = '#6b7280';
                    codigoComprobanteInput.style.borderColor = '#e5e7eb';
                }
            }
            
            // Ejecutar al cargar la página
            toggleCodigoComprobante();
            
            // Ejecutar cuando cambie la selección
            tipoComprobanteSelect.addEventListener('change', toggleCodigoComprobante);

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
            const inputs = document.querySelectorAll('.input-field');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.style.transform = 'scale(1.02)';
                });
                
                input.addEventListener('blur', function() {
                    this.style.transform = 'scale(1)';
                });
            });

            // Preview del archivo seleccionado
            const fileInput = document.getElementById('archivo_comprobante');
            if (fileInput) {
                fileInput.addEventListener('change', function() {
                    const fileName = this.files[0]?.name || 'Ningún archivo seleccionado';
                    const fileContainer = this.closest('.file-input');
                    let preview = fileContainer.querySelector('.file-preview');
                    
                    if (!preview) {
                        preview = document.createElement('div');
                        preview.className = 'file-preview text-sm mt-2';
                        fileContainer.appendChild(preview);
                    }
                    
                    if (this.files[0]) {
                        preview.innerHTML = `<i class='bx bx-check text-green-500 mr-1'></i>Archivo: ${fileName}`;
                        preview.style.color = 'var(--accent-color)';
                    } else {
                        preview.innerHTML = '';
                    }
                });
            }
        });
    </script>
</body>
</html>

@endsection