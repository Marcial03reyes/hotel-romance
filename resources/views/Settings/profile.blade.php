@extends('layouts.app')

@section('title', 'Configuración de Perfil - Hotel Romance')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Configuración de Perfil</h1>
                <p class="text-gray-600 mt-2">Gestiona tu información personal y configuración de seguridad</p>
            </div>
            <div class="flex items-center space-x-2">
                <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-full flex items-center justify-center text-white font-bold text-lg">
                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                </div>
                <div>
                    <p class="font-semibold text-gray-900">{{ Auth::user()->name }}</p>
                    <p class="text-sm text-gray-500">Administrador</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Mensajes de éxito -->
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6">
            <div class="flex items-center">
                <i class='bx bx-check-circle text-green-600 mr-2'></i>
                {{ session('success') }}
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Actualizar Nombre de Usuario -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                        <i class='bx bx-user text-blue-600 text-xl'></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900">Información Personal</h2>
                        <p class="text-gray-600 text-sm">Actualiza tu nombre de usuario</p>
                    </div>
                </div>
            </div>

            <form action="{{ route('profile.update.name') }}" method="POST" class="p-6">
                @csrf
                @method('PATCH')

                <div class="space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nombre de Usuario
                        </label>
                        <div class="relative">
                            <input 
                                type="text" 
                                id="name" 
                                name="name" 
                                value="{{ old('name', Auth::user()->name) }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('name') border-red-500 @enderror"
                                placeholder="Ingrese su nombre de usuario"
                                required
                            >
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                <i class='bx bx-user text-gray-400'></i>
                            </div>
                        </div>
                        @error('name')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end">
                        <button 
                            type="submit" 
                            class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500 to-cyan-500 text-white rounded-lg hover:from-blue-600 hover:to-cyan-600 transition-all duration-200 shadow-md hover:shadow-lg"
                        >
                            <i class='bx bx-save mr-2'></i>
                            Actualizar Nombre
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Cambiar Contraseña -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center mr-3">
                        <i class='bx bx-lock-alt text-amber-600 text-xl'></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900">Seguridad</h2>
                        <p class="text-gray-600 text-sm">Cambia tu contraseña de acceso</p>
                    </div>
                </div>
            </div>

            <form action="{{ route('profile.update.password') }}" method="POST" class="p-6">
                @csrf
                @method('PATCH')

                <div class="space-y-4">
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">
                            Contraseña Actual
                        </label>
                        <div class="relative">
                            <input 
                                type="password" 
                                id="current_password" 
                                name="current_password" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('current_password') border-red-500 @enderror"
                                placeholder="Ingrese su contraseña actual"
                                required
                            >
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                <i class='bx bx-lock text-gray-400'></i>
                            </div>
                        </div>
                        @error('current_password')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            Nueva Contraseña
                        </label>
                        <div class="relative">
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('password') border-red-500 @enderror"
                                placeholder="Ingrese su nueva contraseña"
                                required
                            >
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                <i class='bx bx-key text-gray-400'></i>
                            </div>
                        </div>
                        @error('password')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                            Confirmar Nueva Contraseña
                        </label>
                        <div class="relative">
                            <input 
                                type="password" 
                                id="password_confirmation" 
                                name="password_confirmation" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                placeholder="Confirme su nueva contraseña"
                                required
                            >
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                <i class='bx bx-check text-gray-400'></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <i class='bx bx-info-circle text-amber-600 mr-2 mt-0.5'></i>
                            <div class="text-sm text-amber-800">
                                <p class="font-medium mb-1">Requisitos de contraseña:</p>
                                <ul class="list-disc list-inside space-y-1">
                                    <li>Mínimo 6 caracteres</li>
                                    <li>Se recomienda usar letras, números y símbolos</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button 
                            type="submit" 
                            class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-amber-500 to-orange-500 text-white rounded-lg hover:from-amber-600 hover:to-orange-600 transition-all duration-200 shadow-md hover:shadow-lg"
                        >
                            <i class='bx bx-shield-check mr-2'></i>
                            Cambiar Contraseña
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Información adicional -->
    <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
        <div class="flex items-start">
            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                <i class='bx bx-info-circle text-blue-600 text-xl'></i>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-blue-900 mb-2">Información de Seguridad</h3>
                <div class="text-blue-800 space-y-2">
                    <p>• Tu información está protegida y encriptada</p>
                    <p>• Los cambios de contraseña cerrarán todas las sesiones activas</p>
                    <p>• Si tienes problemas, contacta al administrador del sistema</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-hide success messages after 5 seconds
    const successMessage = document.querySelector('.bg-green-50');
    if (successMessage) {
        setTimeout(() => {
            successMessage.style.opacity = '0';
            successMessage.style.transform = 'translateY(-10px)';
            setTimeout(() => {
                successMessage.remove();
            }, 300);
        }, 5000);
    }

    // Password strength indicator (optional enhancement)
    const passwordInput = document.getElementById('password');
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            // You can add password strength validation here
        });
    }
});
</script>
@endpush