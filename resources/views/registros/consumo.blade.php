@extends('layouts.app')

@section('title', 'Consumo de estadía #'.$estadia->id_estadia)

@section('content')

    <div class="flex flex-col gap-4">
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-semibold">Consumo — Estadía #{{ $estadia->id_estadia }}</h1>
            <a href="{{ route('registros.index') }}" class="rounded-lg border px-3 py-2 text-sm">Volver</a>
        </div>

        <div class="overflow-x-auto rounded-xl border border-neutral-200 dark:border-neutral-700">
            <table class="min-w-full text-sm">
                <thead class="bg-zinc-50 dark:bg-zinc-900/50">
                    <tr>
                        <th class="px-4 py-3 text-left">Producto</th>
                        <th class="px-4 py-3 text-left">Cantidad</th>
                        <th class="px-4 py-3 text-left">Precio unitario</th>
                        <th class="px-4 py-3 text-left">Método de pago</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                    @forelse($consumos as $c)
                        <tr>
                            <td class="px-4 py-3">{{ $c->producto }}</td>
                            <td class="px-4 py-3">{{ $c->cantidad }}</td>
                            <td class="px-4 py-3">S/ {{ number_format($c->precio_unitario, 2) }}</td>
                            <td class="px-4 py-3">{{ $c->metodo }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-6 text-center text-zinc-500">Sin consumos.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.app>

@endsection
