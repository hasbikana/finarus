<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Finarus - Aplikasi Manajemen Keuangan Pribadi')</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [data-theme="dark"] {
            color-scheme: dark;
        }
        [data-theme="light"] {
            color-scheme: light;
        }
    </style>
    <script>
        if (localStorage.getItem('finarus-theme') === 'dark' || (!localStorage.getItem('finarus-theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>
</head>
<body class="bg-background text-foreground antialiased">
    @auth
    <div x-data="{ sidebarOpen: false }" class="flex min-h-screen bg-background">
        @include('components.sidebar')

        <main class="flex-1 p-3 md:p-4 lg:p-5 lg:ml-64">
            @include('components.header')

            <div class="mt-4 md:mt-5 space-y-3 md:space-y-4">
                @yield('content')
            </div>
        </main>
    </div>
    @endauth

    @include('components.toast')

    @guest
    <div class="min-h-screen">
        @yield('content')
    </div>
    @endguest

    @stack('scripts')
</body>
</html>