{{-- resources/views/gastos-fijos/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Agregar Servicio - Gastos Fijos')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-md">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Agregar Nuevo Servicio</h1>
        <p class="text-gray-600 dark:text-gray-400">Registra un nuevo servicio de gasto fijo</p>
    </div>

    {{-- Formulario --}}
    <div class="bg-white dark:bg-zinc-900 rounded-lg border border-gray-200 dark:border-zinc-700 shadow-sm p-6">
        <form action="{{ route('gastos-fijos.store') }}" method="POST">
            @csrf
            
            <div class="space-y-6">
                {{-- Nombre del servicio --}}
                <div>
                    <x-flux::field>
                        <x-flux::label>Nombre del Servicio *</x-flux::label>
                        <x-flux::input 
                            name="nombre_servicio" 
                            value="{{ old('nombre_servicio') }}"
                            placeholder="Ej: Internet, Agua, Luz, Cable"
                            required />
                        <x-flux::error name="nombre_servicio" />
                    </x-flux::field>
                </div>

                {{-- Día de vencimiento --}}
                <div>
                    <x-flux::field>
                        <x-flux::label>Día de Vencimiento *</x-flux::label>
                        <x-flux::input 
                            type="number" 
                            name="dia_vencimiento" 
                            value="{{ old('dia_vencimiento') }}"
                            min="1" 
                            max="31"
                            placeholder="Día del mes (1-31)"
                            required />
                        <x-flux::description>
                            Día del mes en que vence el servicio
                        </x-flux::description>
                        <x-flux::error name="dia_vencimiento" />
                    </x-flux::field>
                </div>

                {{-- Monto fijo --}}
                <div>
                    <x-flux::field>
                        <x-flux::label>Monto Fijo *</x-flux::label>
                        <x-flux::input 
                            type="number" 
                            name="monto_fijo" 
                            value="{{ old('monto_fijo') }}"
                            step="0.01"
                            min="0"
                            placeholder="0.00"
                            required />
                        <x-flux::description>
                            Monto típico del servicio (puede variar al momento del pago)
                        </x-flux::description>
                        <x-flux::error name="monto_fijo" />
                    </x-flux::field>
                </div>
            </div>

            {{-- Botones --}}
            <div class="flex justify-between items-center mt-8">
                <x-flux::button 
                    type="button" 
                    variant="ghost" 
                    icon="arrow-left"
                    onclick="window.history.back()">
                    Volver
                </x-flux::button>
                
                <x-flux::button 
                    type="submit" 
                    variant="primary" 
                    icon="plus">
                    Guardar Servicio
                </x-flux::button>
            </div>
        </form>
    </div>
</div>
@endsection