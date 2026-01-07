<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    <title>@yield('title', 'Hotel Romance - Sistema Administrativo')</title>

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom Styles -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap');
        
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
        
        /* Sidebar personalizado con estructura mejorada */
        .sidebar-menu {
            background: linear-gradient(180deg, #f4f8fc 0%, #eaf3ff 100%);
            border-right: 2px solid #e1ecf7;
            height: 100vh;
            max-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Header del sidebar fijo */
        .sidebar-header {
            flex-shrink: 0;
            padding: 1rem;
            border-bottom: 1px solid #e1ecf7;
        }

        /* Contenido scrolleable */
        .sidebar-content {
            flex: 1;
            overflow-y: auto;
            padding: 1rem;
            padding-top: 0.5rem;
        }

        /* Scrollbar personalizada */
        .sidebar-content::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar-content::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar-content::-webkit-scrollbar-thumb {
            background: #88A6D3;
            border-radius: 2px;
        }

        .sidebar-content::-webkit-scrollbar-thumb:hover {
            background: #6B8CC7;
        }
        
        /* Hover effects personalizados */
        .sidebar-item {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar-item:hover {
            background: linear-gradient(135deg, #88A6D3, #6B8CC7);
            color: white;
            transform: translateX(4px);
            box-shadow: 0 4px 12px rgba(136, 166, 211, 0.3);
        }

        .sidebar-item:hover span,
        .sidebar-item:hover i {
            color: white;
        }
        
        /* Item seleccionado */
        .selected-item {
            background: linear-gradient(135deg, #88A6D3, #6B8CC7);
            color: white;
            border-left: 4px solid #4A73B8;
            box-shadow: 0 2px 8px rgba(136, 166, 211, 0.2);
            transform: translateX(2px);
        }

        .selected-item span,
        .selected-item i {
            color: white !important;
        }
        
        .notification-tab > .active {
            border-bottom-color: #88A6D3;
            color: #88A6D3;
        }
        
        /* Efectos adicionales */
        .hotel-logo {
            background: linear-gradient(135deg, #88A6D3, #4A73B8);
            box-shadow: 0 4px 15px rgba(136, 166, 211, 0.4);
        }
        
        .hotel-title {
            background: linear-gradient(135deg, #88A6D3, #4A73B8);
        }
        
        /* Navbar personalizado */
        .navbar {
            background: linear-gradient(90deg, #ffffff 0%, #f8fbff 100%);
            border-bottom: 1px solid #e1ecf7;
        }
        
        /* Dropdown personalizado */
        .dropdown-item:hover {
            background: linear-gradient(90deg, #f0f6ff, #e6f0ff);
            color: #4A73B8;
        }

        /* Espaciado optimizado para las secciones */
        .section-title {
            margin-top: 0.75rem;
            margin-bottom: 0.25rem;
        }

        .section-title:first-child {
            margin-top: 0;
        }

        /* Items del menú con espaciado reducido */
        .menu-item {
            margin-bottom: 0.25rem;
        }

        /* Badge para trabajador */
        .role-badge {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
            font-size: 0.7rem;
            padding: 0.2rem 0.5rem;
            border-radius: 0.375rem;
            font-weight: 600;
            margin-left: 0.5rem;
        }

        .role-badge.admin {
            background: linear-gradient(135deg, #10b981, #059669);
        }
    </style>
    
    @stack('styles')
</head>
<body class="text-gray-800 font-inter">
    <!-- Sidebar - SIEMPRE VISIBLE -->
    <div class="fixed left-0 top-0 w-64 h-full sidebar-menu z-50">
        <!-- Header fijo del sidebar -->
        <div class="sidebar-header">
            <a href="{{ route('dashboard') }}" class="flex items-center">
                <div class="w-10 h-10 rounded-full hotel-logo flex items-center justify-center text-white font-bold text-xl mr-3">
                    ⭐
                </div>
                <h2 class="font-bold text-2xl">HOTEL <span class="hotel-title text-white px-2 rounded-md">ROMANCE</span></h2>
            </a>
        </div>
        
        <!-- Contenido scrolleable -->
        <div class="sidebar-content">
            <ul>
                <!-- SECCIÓN PRINCIPAL - TODOS pueden ver -->
                <span class="text-gray-500 font-bold text-xs section-title block">PRINCIPAL</span>
                
                <li class="menu-item group">
                    <a href="{{ route('dashboard') }}" class="flex font-semibold items-center py-2 px-4 text-gray-700 rounded-md sidebar-item {{ request()->routeIs('dashboard') ? 'selected-item' : '' }}">
                        <i class="ri-dashboard-line mr-3 text-lg"></i>
                        <span class="text-sm">Dashboard</span>
                    </a>
                </li>
                
                <!-- SECCIÓN HOTEL - TODOS pueden ver -->
                <span class="text-gray-500 font-bold text-xs section-title block">GESTIÓN HOTEL</span>
                
                <li class="menu-item group">
                    <a href="{{ route('registros.index') }}" class="flex font-semibold items-center py-2 px-4 text-gray-700 rounded-md sidebar-item {{ request()->routeIs('registros.*') ? 'selected-item' : '' }}">
                        <i class='bx bx-bed mr-3 text-lg'></i>
                        <span class="text-sm">Registro Habitaciones</span>
                    </a>
                </li>
                
                <li class="menu-item group">
                    <a href="{{ route('clientes.index') }}" class="flex font-semibold items-center py-2 px-4 text-gray-700 rounded-md sidebar-item {{ request()->routeIs('clientes.*') ? 'selected-item' : '' }}">
                        <i class='bx bx-user mr-3 text-lg'></i>
                        <span class="text-sm">Clientes</span>
                    </a>
                </li>

                <li class="menu-item group">
                    <a href="{{ route('pagos-productos.index') }}" class="flex font-semibold items-center py-2 px-4 text-gray-700 rounded-md sidebar-item {{ request()->routeIs('pagos-productos.*') ? 'selected-item' : '' }}">
                        <i class='bx bx-shopping-bag mr-3 text-lg'></i>
                        <span class="text-sm">Registro Bodega</span>
                    </a>
                </li>

                <!-- SECCIÓN FINANZAS -->
                <span class="text-gray-500 font-bold text-xs section-title block">FINANZAS</span>
                
                <li class="menu-item group">
                    <a href="{{ route('gastos.index') }}" class="flex font-semibold items-center py-2 px-4 text-gray-700 rounded-md sidebar-item {{ request()->routeIs('gastos.*') ? 'selected-item' : '' }}">
                        <i class='bx bx-wallet mr-3 text-lg'></i>
                        <span class="text-sm">Gastos Variables</span>
                    </a>
                </li>

                <li class="menu-item group">
                    <a href="{{ route('gastos-fijos.index') }}" class="flex font-semibold items-center py-2 px-4 text-gray-700 rounded-md sidebar-item {{ request()->routeIs('gastos-fijos.*') ? 'selected-item' : '' }}">
                        <i class='bx bx-calendar mr-3 text-lg'></i>
                        <span class="text-sm">Gastos Fijos</span>
                    </a>
                </li>

                <li class="menu-item group">
                    <a href="{{ route('sunat.index') }}" class="flex font-semibold items-center py-2 px-4 text-gray-700 rounded-md sidebar-item {{ request()->routeIs('sunat.*') ? 'selected-item' : '' }}">
                        <i class='bx bx-receipt mr-3 text-lg'></i>
                        <span class="text-sm">SUNAT</span>
                    </a>
                </li>

                

                <li class="menu-item group">
                    <a href="{{ route('cuadre-caja.index') }}" class="flex font-semibold items-center py-2 px-4 text-gray-700 rounded-md sidebar-item {{ request()->routeIs('cuadre-caja.*') ? 'selected-item' : '' }}">
                        <i class='bx bx-calculator mr-3 text-lg'></i>
                        <span class="text-sm">Cuadre de caja</span>
                    </a>
                </li>
                
                <!-- SECCIÓN INVENTARIO -->
                <span class="text-gray-500 font-bold text-xs section-title block">INVENTARIO</span>

                <li class="menu-item group">
                    <a href="{{ route('productos-bodega.index') }}" class="flex font-semibold items-center py-2 px-4 text-gray-700 rounded-md sidebar-item {{ request()->routeIs('productos-bodega.*') ? 'selected-item' : '' }}">
                        <i class='bx bx-package mr-3 text-lg'></i>
                        <span class="text-sm">Productos Bodega</span>
                    </a>
                </li>

                <li class="menu-item group">
                    <a href="{{ route('productos-hotel.index') }}" class="flex font-semibold items-center py-2 px-4 text-gray-700 rounded-md sidebar-item {{ request()->routeIs('productos-hotel.*') ? 'selected-item' : '' }}">
                        <i class='bx bx-building mr-3 text-lg'></i>
                        <span class="text-sm">Productos Hotel</span>
                    </a>
                </li>

                <!-- SECCIONES SOLO PARA ADMINISTRADORES -->
                @if(Auth::user()->isAdmin())

                <!-- SECCIÓN FINANZAS -->
                <li class="menu-item group">
                    <a href="{{ route('inversiones.index') }}" class="flex font-semibold items-center py-2 px-4 text-gray-700 rounded-md sidebar-item {{ request()->routeIs('inversiones.*') ? 'selected-item' : '' }}">
                        <i class='bx bx-trending-up mr-3 text-lg'></i>
                        <span class="text-sm">Inversiones</span>
                    </a>
                </li>
                <!-- SECCIÓN ADMINISTRACIÓN -->
                <span class="text-gray-500 font-bold text-xs section-title block">ADMINISTRACIÓN</span>
                
                <li class="menu-item group">
                    <a href="{{ route('trabajadores.index') }}" class="flex font-semibold items-center py-2 px-4 text-gray-700 rounded-md sidebar-item {{ request()->routeIs('trabajadores.*') ? 'selected-item' : '' }}">
                        <i class='bx bx-id-card mr-3 text-lg'></i>
                        <span class="text-sm">Trabajadores</span>
                    </a>
                </li>

                <li class="menu-item group">
                    <a href="{{ route('horarios.index') }}" class="flex font-semibold items-center py-2 px-4 text-gray-700 rounded-md sidebar-item {{ request()->routeIs('horarios.*') ? 'selected-item' : '' }}">
                        <i class='bx bx-time mr-3 text-lg'></i>
                        <span class="text-sm">Horarios</span>
                    </a>
                </li>
                
                <!-- SECCIÓN REPORTES -->
                <span class="text-gray-500 font-bold text-xs section-title block">REPORTES</span>
                
                <li class="menu-item group">
                    <a href="{{ route('graficas.index') }}" class="flex font-semibold items-center py-2 px-4 text-gray-700 rounded-md sidebar-item {{ request()->routeIs('graficas.*') ? 'selected-item' : '' }}">
                        <i class='bx bx-line-chart mr-3 text-lg'></i>
                        <span class="text-sm">Gráficas</span>
                    </a>
                </li>
                
                @endif
                <!-- FIN SECCIONES SOLO ADMINISTRADORES -->
            </ul>
        </div>
    </div>

    <!-- Main Content - SIEMPRE CON MARGEN PARA EL SIDEBAR -->
    <main class="ml-64 bg-gray-50 min-h-screen">
        <!-- Navbar - SOLO ICONOS Y PERFIL -->
        <div class="py-2 px-6 navbar flex items-center shadow-md shadow-black/5 sticky top-0 left-0 z-30">
            <!-- Espacio vacío a la izquierda -->
            <div class="flex-1"></div>

            <ul class="ml-auto flex items-center">
                <!-- Pantalla completa -->
                <button id="fullscreen-button" class="text-gray-400 mr-4 w-8 h-8 rounded flex items-center justify-center hover:text-blue-600">
                    <i class="ri-fullscreen-line"></i>
                </button>

                <!-- Perfil de usuario -->
                <li class="dropdown ml-3">
                    <button type="button" class="dropdown-toggle flex items-center">
                        <div class="flex-shrink-0 w-10 h-10 relative">
                            <div class="p-1 bg-white rounded-full focus:outline-none focus:ring">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-r from-blue-400 to-blue-600 flex items-center justify-center text-white font-bold">
                                    {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                                </div>
                                <div class="top-0 left-7 absolute w-3 h-3 bg-green-400 border-2 border-white rounded-full animate-ping"></div>
                                <div class="top-0 left-7 absolute w-3 h-3 bg-green-500 border-2 border-white rounded-full"></div>
                            </div>
                        </div>
                        <div class="p-2 md:block text-left">
                            <h2 class="text-sm font-semibold text-gray-800">{{ Auth::user()->name ?? 'Usuario' }}</h2>
                            <p class="text-xs text-gray-500">{{ Auth::user()->role_name }}</p>
                        </div>
                    </button>
                    <ul class="dropdown-menu shadow-md shadow-black/5 z-30 hidden py-1.5 rounded-md bg-white border border-blue-100 w-full max-w-[200px]">
                        <!-- Información del usuario -->
                        <div class="px-4 py-2 border-b border-gray-100">
                            <p class="text-sm text-gray-900 font-medium">{{ Auth::user()->name ?? 'Usuario' }}</p>
                            <p class="text-xs text-gray-500">{{ Auth::user()->role_name }}</p>
                        </div>

                        <!-- Enlaces del menú -->
                        <div class="py-1">
                            <li>
                                <a href="{{ route('profile.show') }}" class="dropdown-item flex items-center text-[13px] py-1.5 px-4 text-gray-600 hover:text-blue-700">
                                    <i class="ri-user-line mr-2"></i>
                                    Mi Perfil
                                </a>
                            </li>
                            
                            <li>
                                <a href="{{ route('profile.show') }}" class="dropdown-item flex items-center text-[13px] py-1.5 px-4 text-gray-600 hover:text-blue-700">
                                    <i class="ri-settings-3-line mr-2"></i>
                                    Configuración
                                </a>
                            </li>
                        </div>

                        <!-- Cerrar sesión -->
                        <div class="border-t border-blue-100 py-1">
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center text-[13px] py-1.5 px-4 text-red-600 hover:bg-red-50 cursor-pointer">
                                        <i class="ri-logout-box-r-line mr-2"></i>
                                        Cerrar Sesión
                                    </button>
                                </form>
                            </li>
                        </div>
                    </ul>
                </li>
            </ul>
        </div>
        <!-- End Navbar -->

        <!-- Page Content -->
        <div class="p-6">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Scripts -->
    <script src="https://unpkg.com/@popperjs/core@2"></script>
    @stack('scripts')
    
    <script>
        // Dropdown menus
        const popperInstance = {}
        document.querySelectorAll('.dropdown').forEach(function (item, index) {
            const popperId = 'popper-' + index
            const toggle = item.querySelector('.dropdown-toggle')
            const menu = item.querySelector('.dropdown-menu')
            if (menu) {
                menu.dataset.popperId = popperId
                popperInstance[popperId] = Popper.createPopper(toggle, menu, {
                    modifiers: [
                        {
                            name: 'offset',
                            options: {
                                offset: [0, 8],
                            },
                        },
                        {
                            name: 'preventOverflow',
                            options: {
                                padding: 24,
                            },
                        },
                    ],
                    placement: 'bottom-end'
                });
            }
        })
        
        document.addEventListener('click', function (e) {
            const toggle = e.target.closest('.dropdown-toggle')
            const menu = e.target.closest('.dropdown-menu')
            if (toggle) {
                const menuEl = toggle.closest('.dropdown').querySelector('.dropdown-menu')
                if (menuEl) {
                    const popperId = menuEl.dataset.popperId
                    if (menuEl.classList.contains('hidden')) {
                        hideDropdown()
                        menuEl.classList.remove('hidden')
                        showPopper(popperId)
                    } else {
                        menuEl.classList.add('hidden')
                        hidePopper(popperId)
                    }
                }
            } else if (!menu) {
                hideDropdown()
            }
        })

        function hideDropdown() {
            document.querySelectorAll('.dropdown-menu').forEach(function (item) {
                item.classList.add('hidden')
            })
        }
        
        function showPopper(popperId) {
            if (popperInstance[popperId]) {
                popperInstance[popperId].setOptions(function (options) {
                    return {
                        ...options,
                        modifiers: [
                            ...options.modifiers,
                            { name: 'eventListeners', enabled: true },
                        ],
                    }
                });
                popperInstance[popperId].update();
            }
        }
        
        function hidePopper(popperId) {
            if (popperInstance[popperId]) {
                popperInstance[popperId].setOptions(function (options) {
                    return {
                        ...options,
                        modifiers: [
                            ...options.modifiers,
                            { name: 'eventListeners', enabled: false },
                        ],
                    }
                });
            }
        }

        // Fullscreen
        const fullscreenButton = document.getElementById('fullscreen-button')
        if (fullscreenButton) {
            fullscreenButton.addEventListener('click', toggleFullscreen)
        }
        
        function toggleFullscreen() {
            if (document.fullscreenElement) {
                document.exitFullscreen()
            } else {
                document.documentElement.requestFullscreen()
            }
        }
    </script>
</body>
</html>