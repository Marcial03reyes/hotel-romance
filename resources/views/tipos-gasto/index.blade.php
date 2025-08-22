@extends('layouts.app')

@section('title', 'Tipos de Gasto')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Tipos de Gasto</h1>
                    <p class="text-gray-600 mt-1">Gestiona las categorías de gastos del hotel</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('tipos-gasto.create') }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700 transition-colors">
                        <i class='bx bx-plus mr-2'></i>
                        Nuevo Tipo
                    </a>
                </div>
            </div>
        </div>

        <!-- Alertas -->
        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class='bx bx-check-circle text-green-400 text-xl'></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class='bx bx-error text-red-400 text-xl'></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800">{{ $errors->first() }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Tabla de Tipos -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gradient-to-r from-blue-600 to-blue-800 text-white sticky top-0 z-10" style="background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));">

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
</style>
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider">ID</th>
                            <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider">Nombre</th>
                            <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider">Gastos Registrados</th>
                            <th class="px-6 py-4 text-left text-xs font-medium uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($tipos as $tipo)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900"># {{ $tipo->id_tipo_gasto }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="text-sm font-medium text-gray-900">{{ $tipo->nombre }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        <span class="font-medium">{{ $tipo->gastos->count() }}</span> gastos
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        <!-- Editar -->
                                        <a href="{{ route('tipos-gasto.edit', $tipo->id_tipo_gasto) }}" 
                                           class="inline-flex items-center px-2 py-1 bg-yellow-100 text-yellow-700 text-xs font-medium rounded-md hover:bg-yellow-200 transition-colors"
                                           title="Editar tipo de gasto">
                                            <i class='bx bx-edit mr-1'></i>
                                            Editar
                                        </a>
                                        
                                        <!-- Eliminar -->
                                        <form action="{{ route('tipos-gasto.destroy', $tipo->id_tipo_gasto) }}" 
                                              method="POST" style="display: inline;" 
                                              onsubmit="return confirm('¿Estás seguro de eliminar este tipo de gasto?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="inline-flex items-center px-2 py-1 bg-red-100 text-red-700 text-xs font-medium rounded-md hover:bg-red-200 transition-colors"
                                                    title="Eliminar tipo de gasto">
                                                <i class='bx bx-trash mr-1'></i>
                                                Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center">
                                    <div class="text-gray-500">
                                        <i class='bx bx-receipt text-4xl mb-4'></i>
                                        <p class="text-lg font-medium">No hay tipos de gasto registrados</p>
                                        <p class="text-sm">Comienza agregando tu primer tipo de gasto</p>
                                        <a href="{{ route('tipos-gasto.create') }}" 
                                           class="inline-flex items-center mt-4 px-4 py-2 bg-blue-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700 transition-colors">
                                            <i class='bx bx-plus mr-2'></i>
                                            Crear Primer Tipo
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Información adicional -->
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-md p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class='bx bx-info-circle text-blue-400 text-xl'></i>
                </div>
                <div class="ml-3">
                    <h4 class="text-sm font-medium text-blue-800 mb-2">Sobre los tipos de gasto</h4>
                    <div class="text-sm text-blue-700 space-y-1">
                        <p>• Los tipos de gasto te ayudan a categorizar y organizar los gastos del hotel</p>
                        <p>• Solo puedes eliminar tipos que no tengan gastos registrados</p>
                        <p>• Ejemplos: Servicios básicos, Limpieza, Mantenimiento, Marketing, etc.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection