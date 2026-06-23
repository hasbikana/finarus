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
        [x-cloak] { display: none !important; }
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

    @auth
    <div x-data="alertBanner()" x-init="init()" x-show="show" x-cloak
         class="fixed bottom-4 right-4 max-w-sm z-50 bg-red-600 text-white rounded-lg shadow-2xl p-4 animate-slide-in-up">
        <div class="flex gap-3 items-start">
            <span class="text-lg shrink-0">⚠️</span>
            <div class="flex-1 text-sm font-medium" x-text="message"></div>
            <button @click="dismiss()" class="text-white/70 hover:text-white shrink-0 p-0.5">&times;</button>
        </div>
    </div>
    <script>
    function alertBanner() {
        return {
            show: false,
            message: '',
            init() {
                fetch('/api/alerts/daily', {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(r => r.json())
                .then(d => {
                    if (d.alert) { this.message = d.alert; this.show = true; }
                })
                .catch(() => {});
            },
            dismiss() {
                this.show = false;
            }
        }
    }
    </script>
    @endauth

    @guest
    <div class="min-h-screen">
        @yield('content')
    </div>
    @endguest

    @stack('scripts')
</body>
</html>