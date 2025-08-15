<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    @include('partials.head')
</head>
<body class="min-h-screen bg-white text-zinc-900 dark:bg-zinc-900 dark:text-zinc-100">
    <div class="grid min-h-screen" style="grid-template-columns: 260px 1fr;">
        {{-- SIDEBAR --}}
        @include('partials.sidebar')

        {{-- CONTENIDO --}}
        <main class="min-h-screen overflow-y-auto">
            {{ $slot }}
        </main>
    </div>
    @fluxScripts
</body>
</html>