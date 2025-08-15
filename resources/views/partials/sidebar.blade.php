@php
    $navActive = 'border-s-[3px] border-blue-500 bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-300 dark:border-blue-400';
    $navIdle = 'border-s-[3px] border-transparent text-gray-500 hover:border-gray-100 hover:bg-gray-50 hover:text-gray-700 dark:text-gray-400 dark:hover:border-gray-700 dark:hover:bg-gray-800 dark:hover:text-gray-200';
@endphp

<aside class="flex flex-col w-full h-screen bg-white dark:bg-zinc-900 border-r border-gray-200 dark:border-zinc-700">
    {{-- Logo/Header --}}
    <div class="flex items-center justify-center h-16 px-4 border-b border-gray-200 dark:border-zinc-700">
        <h2 class="text-xl font-bold text-gray-800 dark:text-zinc-100">Hotel Admin</h2>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto">
        <ul class="p-2 space-y-1" role="list">
            {{-- Panel --}}
            <li class="px-3 pt-3 text-xs font-semibold text-gray-400 uppercase dark:text-gray-500">Panel</li>
            <li>
                <a href="{{ route('dashboard') }}"
                   class="flex w-full items-center gap-2 px-4 py-3 {{ request()->routeIs('dashboard') ? $navActive : $navIdle }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5 opacity-75 shadow-sm" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2">
                        <path strokeLinecap="round" strokeLinejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                    </svg>
                    <span class="text-sm font-medium">Dashboard</span>
                </a>
            </li>

            {{-- Recepción --}}
            <li class="px-3 pt-3 text-xs font-semibold text-gray-400 uppercase dark:text-gray-500">Recepción</li>
            <li>
                <a href="{{ route('registros.index') }}"
                   class="flex w-full items-center gap-2 px-4 py-3 {{ request()->routeIs('registros.*') ? $navActive : $navIdle }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5 opacity-75 shadow-sm" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2">
                        <path strokeLinecap="round" strokeLinejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                    </svg>
                    <span class="text-sm font-medium">Registro</span>
                </a>
            </li>
            <li>
                <a href="{{ route('clientes.index') }}"
                   class="flex w-full items-center gap-2 px-4 py-3 {{ request()->routeIs('clientes.*') ? $navActive : $navIdle }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5 opacity-75 shadow-sm" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2">
                        <path strokeLinecap="round" strokeLinejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                    </svg>
                    <span class="text-sm font-medium">Clientes</span>
                </a>
            </li>

            {{-- Productos --}}
            <li class="px-3 pt-3 text-xs font-semibold text-gray-400 uppercase dark:text-gray-500">Productos</li>
            <li>
                <a href="{{ route('productos-bodega.index') }}"
                   class="flex w-full items-center gap-2 px-4 py-3 {{ request()->routeIs('productos.bodega.*') ? $navActive : $navIdle }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5 opacity-75 shadow-sm" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2">
                        <path strokeLinecap="round" strokeLinejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                    </svg>
                    <span class="text-sm font-medium">Bodega</span>
                </a>
            </li>
            <li>
                <a href="{{ route('productos-hotel.index') }}"
                   class="flex w-full items-center gap-2 px-4 py-3 {{ request()->routeIs('productos.hotel.*') ? $navActive : $navIdle }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5 opacity-75 shadow-sm" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2">
                        <path strokeLinecap="round" strokeLinejoin="round" d="M8.25 21v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21m0 0h4.5V3.545M12.75 21h7.5V10.75M2.25 21h1.5m18 0h-18M2.25 9l4.5-1.636M18.75 3l-1.5.545m0 6.205 3 1m1.5.5-1.5-.5M6.75 7.364V3h-3v18m3-13.636 10.5-3.819" />
                    </svg>
                    <span class="text-sm font-medium">Hotel</span>
                </a>
            </li>
            <li>
                <a href="{{ route('compras-internas.index') }}"
                   class="flex w-full items-center gap-2 px-4 py-3 {{ request()->routeIs('compras.internas.*') ? $navActive : $navIdle }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5 opacity-75 shadow-sm" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2">
                        <path strokeLinecap="round" strokeLinejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5ZM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5ZM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 13.5 9.375v-4.5Z" />
                        <path strokeLinecap="round" strokeLinejoin="round" d="M6.75 6.75h.75v.75h-.75v-.75ZM6.75 16.5h.75v.75h-.75v-.75ZM16.5 6.75h.75v.75h-.75v-.75Z" />
                    </svg>
                    <span class="text-sm font-medium">Compras internas</span>
                </a>
            </li>

            {{-- Pagos --}}
            <li class="px-3 pt-3 text-xs font-semibold text-gray-400 uppercase dark:text-gray-500">Pagos</li>
            <li>
                <a href="{{ route('pagos-habitacion.index') }}"
                   class="flex w-full items-center gap-2 px-4 py-3 {{ request()->routeIs('pagos.hab.*') ? $navActive : $navIdle }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5 opacity-75 shadow-sm" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2">
                        <path strokeLinecap="round" strokeLinejoin="round" d="M12 3v17.25m0 0c-1.472 0-2.882.265-4.185.75M12 20.25c1.472 0 2.882.265 4.185.75M18.75 4.97A48.254 48.254 0 0 0 12 4.5c-2.291 0-4.545.16-6.75.47m13.5 0c1.01.143 2.01.317 3 .52m-3-.52 2.62 10.726c.122.499-.106 1.028-.589 1.202a5.988 5.988 0 0 1-2.031.352 5.988 5.988 0 0 1-2.031-.352c-.483-.174-.711-.703-.589-1.202L18.75 4.971Zm-16.5.52c.99-.203 1.99-.377 3-.52m0 0 2.62 10.726c.122.499-.106 1.028-.589 1.202a5.989 5.989 0 0 1-2.031.352 5.989 5.989 0 0 1-2.031-.352c-.483-.174-.711-.703-.589-1.202L5.25 4.971Z" />
                    </svg>
                    <span class="text-sm font-medium">Hab.</span>
                </a>
            </li>
            <li>
                <a href="{{ route('pagos-productos.index') }}"
                   class="flex w-full items-center gap-2 px-4 py-3 {{ request()->routeIs('pagos.prod.*') ? $navActive : $navIdle }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5 opacity-75 shadow-sm" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2">
                        <path strokeLinecap="round" strokeLinejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m6-6.75h6m2.25-3h-15A2.625 2.625 0 0 0 0 7.875v6.75a2.625 2.625 0 0 0 2.625 2.625h15A2.625 2.625 0 0 0 21 14.625v-6.75A2.625 2.625 0 0 0 18.375 4.5Z" />
                    </svg>
                    <span class="text-sm font-medium">Consumos</span>
                </a>
            </li>

            {{-- Finanzas --}}
            <li class="px-3 pt-3 text-xs font-semibold text-gray-400 uppercase dark:text-gray-500">Finanzas</li>
            <li>
                <a href="{{ route('gastos.index') }}"
                   class="flex w-full items-center gap-2 px-4 py-3 {{ request()->routeIs('gastos.*') ? $navActive : $navIdle }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5 opacity-75 shadow-sm" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2">
                        <path strokeLinecap="round" strokeLinejoin="round" d="M21 12a2.25 2.25 0 0 0-2.25-2.25H15a3 3 0 1 1-6 0H5.25A2.25 2.25 0 0 0 3 12m18 0v6a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 18v-6m18 0V9M3 12V9m18 0a2.25 2.25 0 0 0-2.25-2.25H5.25A2.25 2.25 0 0 0 3 9m18 0V6a2.25 2.25 0 0 0-2.25-2.25H5.25A2.25 2.25 0 0 0 3 6v3" />
                    </svg>
                    <span class="text-sm font-medium">Gastos</span>
                </a>
            </li>

            {{-- RR.HH. --}}
            <li class="px-3 pt-3 text-xs font-semibold text-gray-400 uppercase dark:text-gray-500">RR.HH.</li>
            <li>
                <a href="{{ route('trabajadores.index') }}"
                   class="flex w-full items-center gap-2 px-4 py-3 {{ request()->routeIs('trabajadores.*') ? $navActive : $navIdle }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5 opacity-75 shadow-sm" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2">
                        <path strokeLinecap="round" strokeLinejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Zm6-10.125a1.875 1.875 0 1 1-3.75 0 1.875 1.875 0 0 1 3.75 0Zm-1.875 4.5a4.125 4.125 0 1 1-8.25 0 4.125 4.125 0 0 1 8.25 0Z" />
                    </svg>
                    <span class="text-sm font-medium">Trabajadores</span>
                </a>
            </li>
        </ul>
    </nav>

    {{-- Footer opcional --}}
    <div class="p-4 border-t border-gray-200 dark:border-zinc-700">
        <p class="text-xs text-gray-500 dark:text-gray-400 text-center">
            © 2025 Hotel Admin
        </p>
    </div>
</aside>