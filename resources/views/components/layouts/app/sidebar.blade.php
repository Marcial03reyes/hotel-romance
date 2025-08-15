@php
    $navActive = 'border-s-[3px] border-blue-500 bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-300 dark:border-blue-400';
    $navIdle = 'border-s-[3px] border-transparent text-gray-500 hover:border-gray-100 hover:bg-gray-50 hover:text-gray-700 dark:text-gray-400 dark:hover:border-gray-700 dark:hover:bg-gray-800 dark:hover:text-gray-200';
@endphp

{{-- Sidebar Component para usar como layout --}}
<x-flux::sidebar class="w-64 h-screen bg-white dark:bg-zinc-900 border-r border-gray-200 dark:border-zinc-700">
    {{-- Header --}}
    <x-flux::sidebar.header class="flex items-center justify-center h-16 px-4 border-b border-gray-200 dark:border-zinc-700">
        <x-flux::heading size="lg" class="text-gray-800 dark:text-zinc-100">Hotel Admin</x-flux::heading>
    </x-flux::sidebar.header>

    {{-- Navigation Groups --}}
    <x-flux::sidebar.content class="flex-1 overflow-y-auto p-2">
        {{-- Panel --}}
        <x-flux::navlist variant="outline">
            <x-flux::navlist.group heading="Panel">
                <x-flux::navlist.item 
                    icon="credit-card"
                    :href="route('metodos-pago.index')"
                    :current="request()->routeIs('metodos-pago.*')"
                    class="{{ request()->routeIs('metodos-pago.*') ? $navActive : $navIdle }}"
                    wire:navigate>
                    Métodos de pago
                </x-flux::navlist.item>
            </x-flux::navlist.group>

            {{-- RR.HH. --}}
            <x-flux::navlist.group heading="RR.HH.">
                <x-flux::navlist.item 
                    icon="id-card"
                    :href="route('trabajadores.index')"
                    :current="request()->routeIs('trabajadores.*')"
                    class="{{ request()->routeIs('trabajadores.*') ? $navActive : $navIdle }}"
                    wire:navigate>
                    Trabajadores
                </x-flux::navlist.item>
            </x-flux::navlist.group>
        </x-flux::navlist>
    </x-flux::sidebar.content>

    {{-- Footer --}}
    <x-flux::sidebar.footer class="p-4 border-t border-gray-200 dark:border-zinc-700">
        <p class="text-xs text-gray-500 dark:text-gray-400 text-center">
            © 2025 Hotel Admin
        </p>
    </x-flux::sidebar.footer>
</x-flux::sidebar>::navlist.item 
                    icon="home"
                    :href="route('dashboard')"
                    :current="request()->routeIs('dashboard')"
                    class="{{ request()->routeIs('dashboard') ? $navActive : $navIdle }}"
                    wire:navigate>
                    Dashboard
                </x-flux::navlist.item>
            </x-flux::navlist.group>

            {{-- Recepción --}}
            <x-flux::navlist.group heading="Recepción">
                <x-flux::navlist.item 
                    icon="calendar-days"
                    :href="route('registros.index')"
                    :current="request()->routeIs('registros.*')"
                    class="{{ request()->routeIs('registros.*') ? $navActive : $navIdle }}"
                    wire:navigate>
                    Registros
                </x-flux::navlist.item>

                <x-flux::navlist.item 
                    icon="users"
                    :href="route('clientes.index')"
                    :current="request()->routeIs('clientes.*')"
                    class="{{ request()->routeIs('clientes.*') ? $navActive : $navIdle }}"
                    wire:navigate>
                    Clientes
                </x-flux::navlist.item>
            </x-flux::navlist.group>

            {{-- Productos --}}
            <x-flux::navlist.group heading="Productos">
                <x-flux::navlist.item 
                    icon="shopping-bag"
                    :href="route('productos-bodega.index')"
                    :current="request()->routeIs('productos-bodega.*')"
                    class="{{ request()->routeIs('productos-bodega.*') ? $navActive : $navIdle }}"
                    wire:navigate>
                    Bodega
                </x-flux::navlist.item>

                <x-flux::navlist.item 
                    icon="warehouse"
                    :href="route('productos-hotel.index')"
                    :current="request()->routeIs('productos-hotel.*')"
                    class="{{ request()->routeIs('productos-hotel.*') ? $navActive : $navIdle }}"
                    wire:navigate>
                    Hotel
                </x-flux::navlist.item>

                <x-flux::navlist.item 
                    icon="scan-line"
                    :href="route('compras-internas.index')"
                    :current="request()->routeIs('compras-internas.*')"
                    class="{{ request()->routeIs('compras-internas.*') ? $navActive : $navIdle }}"
                    wire:navigate>
                    Compras internas
                </x-flux::navlist.item>
            </x-flux::navlist.group>

            {{-- Pagos --}}
            <x-flux::navlist.group heading="Pagos">
                <x-flux::navlist.item 
                    icon="bed-double"
                    :href="route('pagos-habitacion.index')"
                    :current="request()->routeIs('pagos-habitacion.*')"
                    class="{{ request()->routeIs('pagos-habitacion.*') ? $navActive : $navIdle }}"
                    wire:navigate>
                    Hab.
                </x-flux::navlist.item>

                <x-flux::navlist.item 
                    icon="cup-soda"
                    :href="route('pagos-productos.index')"
                    :current="request()->routeIs('pagos-productos.*')"
                    class="{{ request()->routeIs('pagos-productos.*') ? $navActive : $navIdle }}"
                    wire:navigate>
                    Consumos
                </x-flux::navlist.item>
            </x-flux::navlist.group>

            {{-- Finanzas --}}
            <x-flux::navlist.group heading="Finanzas">
                <x-flux::navlist.item 
                    icon="wallet"
                    :href="route('gastos.index')"
                    :current="request()->routeIs('gastos.*')"
                    class="{{ request()->routeIs('gastos.*') ? $navActive : $navIdle }}"
                    wire:navigate>
                    Gastos
                </x-flux::navlist.item>

                <x-flux::navlist.item 
                    icon="receipt"
                    :href="route('tipos-gasto.index')"
                    :current="request()->routeIs('tipos-gasto.*')"
                    class="{{ request()->routeIs('tipos-gasto.*') ? $navActive : $navIdle }}"
                    wire:navigate>
                    Tipos de gasto
                </x-flux::navlist.item>

                <x-flux