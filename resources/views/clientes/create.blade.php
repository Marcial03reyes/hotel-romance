@extends('layouts.app')

@section('title', 'Agregar Registro - Hotel Romance')

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



        /* Responsive adjustments */
        @media (max-width: 768px) {
            .form-container {
                padding: 1.5rem;
                margin: 1rem;
            }
        }
    </style>
</head>

<body class="text-gray-800 font-inter bg-gray-50">


    <!-- Contenido principal sin margen para sidebar -->
    <main class="min-h-screen py-6 px-4" style="background: linear-gradient(135deg, var(--sidebar-bg) 0%, #ffffff 100%);">
        <div class="max-w-2xl mx-auto mt-1">
            
            <!-- Header de la página -->
            <div class="page-header">
                <div class="flex items-center">
                    <div class="floating-icon mr-4">
                        <i class='bx bx-user-plus'></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold mb-1">Agregar Nuevo Cliente</h1>
                        <p class="opacity-90 text-sm">Registra la información del cliente en el sistema del Hotel Romance</p>
                    </div>
                </div>
            </div>

            <!-- Contenedor del formulario -->
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

                <!-- Formulario -->
                <form action="{{ route('clientes.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- Campo Documento -->
                    <div class="space-y-2">
                        <label class="field-label block text-sm font-medium">
                            <i class='bx bx-id-card mr-1'></i>
                            Documento de Identidad
                        </label>
                        <input name="doc_identidad" 
                               type="text" 
                               value="{{ old('doc_identidad') }}"
                               class="input-field w-full rounded-lg px-4 py-3 text-gray-700 placeholder-gray-400"
                               placeholder="Ingrese DNI o CE (ej. 12345678)" 
                               required 
                               maxlength="20">
                        <p class="text-xs text-gray-500 mt-1">
                            <i class='bx bx-info-circle mr-1'></i>
                            DNI (8 dígitos) o Carnet de Extranjería (más de 8 dígitos)
                        </p>
                    </div>

                    <!-- Campo Nombre y Apellido -->
                    <div class="space-y-2">
                        <label class="field-label block text-sm font-medium">
                            <i class='bx bx-user mr-1'></i>
                            Nombre y Apellido Completo
                        </label>
                        <input name="nombre_apellido" 
                               type="text" 
                               value="{{ old('nombre_apellido') }}"
                               class="input-field w-full rounded-lg px-4 py-3 text-gray-700 placeholder-gray-400"
                               placeholder="Ingrese el nombre completo (ej. Juan Carlos Pérez)" 
                               required 
                               maxlength="100">
                        <p class="text-xs text-gray-500 mt-1">
                            <i class='bx bx-info-circle mr-1'></i>
                            Ingrese el nombre y apellido tal como aparece en el documento
                        </p>
                    </div>

                    <!-- Campos adicionales opcionales -->
                    <div class="grid md:grid-cols-3 gap-4">
                        <!-- Estado Civil -->
                        <div class="space-y-2">
                            <label class="field-label block text-sm font-medium">
                                <i class='bx bx-heart mr-1'></i>
                                Estado Civil
                            </label>
                            <select name="estado_civil" 
                                    class="input-field w-full rounded-lg px-4 py-3 text-gray-700">
                                <option value="">Seleccionar</option>
                                <option value="Soltero" {{ old('estado_civil') == 'Soltero' ? 'selected' : '' }}>Soltero</option>
                                <option value="Casado" {{ old('estado_civil') == 'Casado' ? 'selected' : '' }}>Casado</option>
                                <option value="Divorciado" {{ old('estado_civil') == 'Divorciado' ? 'selected' : '' }}>Divorciado</option>
                                <option value="Viudo" {{ old('estado_civil') == 'Viudo' ? 'selected' : '' }}>Viudo</option>
                            </select>
                            <p class="text-xs text-gray-500 mt-1">
                                <i class='bx bx-info-circle mr-1'></i>
                                Campo opcional
                            </p>
                        </div>

                        <!-- Fecha de Nacimiento -->
                        <div class="space-y-2">
                            <label class="field-label block text-sm font-medium">
                                <i class='bx bx-calendar mr-1'></i>
                                Fecha de Nacimiento
                            </label>
                            <input name="fecha_nacimiento" 
                                type="date" 
                                value="{{ old('fecha_nacimiento') }}"
                                class="input-field w-full rounded-lg px-4 py-3 text-gray-700">
                            <p class="text-xs text-gray-500 mt-1">
                                <i class='bx bx-info-circle mr-1'></i>
                                Campo opcional
                            </p>
                        </div>

                        <!-- Lugar de Nacimiento -->
                        <div class="space-y-2">
                            <label class="field-label block text-sm font-medium">
                                <i class='bx bx-map mr-1'></i>
                                Lugar de Nacimiento
                            </label>
                            <input name="lugar_nacimiento" 
                                type="text" 
                                value="{{ old('lugar_nacimiento') }}"
                                class="input-field w-full rounded-lg px-4 py-3 text-gray-700 placeholder-gray-400"
                                placeholder="Ciudad, país..." 
                                maxlength="100">
                            <p class="text-xs text-gray-500 mt-1">
                                <i class='bx bx-info-circle mr-1'></i>
                                Campo opcional
                            </p>
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="flex items-center gap-4 pt-4">
                        <a href="{{ route('clientes.index') }}" 
                           class="btn-secondary px-6 py-3 rounded-lg text-sm font-medium flex items-center transition-all">
                            <i class='bx bx-arrow-back mr-2'></i>
                            Cancelar
                        </a>
                        
                        <button type="submit" 
                                class="btn-romance px-8 py-3 rounded-lg text-sm font-medium flex items-center flex-1 justify-center">
                            <i class='bx bx-save mr-2'></i>
                            Guardar Cliente
                        </button>
                    </div>
                </form>

                <!-- Información adicional -->
                <div class="mt-6 p-4 rounded-lg" style="background: rgba(136, 166, 211, 0.05); border: 1px solid rgba(136, 166, 211, 0.2);">
                    <div class="flex items-start">
                        <i class='bx bx-lightbulb text-lg mr-2' style="color: var(--accent-color);"></i>
                        <div class="text-sm text-gray-600">
                            <p class="font-medium mb-1" style="color: var(--accent-color);">Consejos para el registro:</p>
                            <ul class="space-y-1 text-xs">
                                <li>• Verifica que el documento de identidad sea válido</li>
                                <li>• Asegúrate de escribir el nombre correctamente</li>
                                <li>• Los datos se pueden editar posteriormente desde la lista de clientes</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Mejorar la experiencia del usuario con validación en tiempo real
        document.addEventListener('DOMContentLoaded', function() {
            const docInput = document.querySelector('input[name="doc_identidad"]');
            const nombreInput = document.querySelector('input[name="nombre_apellido"]');
            
            if (docInput) {
                docInput.addEventListener('input', function() {
                    // Solo permitir números
                    this.value = this.value.replace(/[^0-9]/g, '');
                    
                    // Cambiar el color del borde según la validez
                    if (this.value.length >= 8) {
                        this.style.borderColor = 'var(--primary-color)';
                    } else {
                        this.style.borderColor = '#e5e7eb';
                    }
                });
            }
            
            // Capitalizar primera letra de cada palabra en el nombre
            if (nombreInput) {
                nombreInput.addEventListener('blur', function() {
                    this.value = this.value.replace(/\b\w/g, l => l.toUpperCase());
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
        });
    </script>
</body>
</html>

@endsection