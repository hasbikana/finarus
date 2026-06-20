<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'FinFlow - Aplikasi Manajemen Keuangan Pribadi')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [data-theme="dark"] {
            color-scheme: dark;
        }
        [data-theme="light"] {
            color-scheme: light;
        }
    </style>
</head>
<body class="bg-background text-foreground antialiased">
    <div class="flex min-h-screen bg-background">
        <!-- Sidebar -->
        <div class="hidden lg:block">
            @include('components.sidebar')
        </div>

        <!-- Main Content -->
        <main class="flex-1 p-3 md:p-4 lg:p-5 lg:ml-64">
            @include('components.header')
            
            <div class="mt-4 md:mt-5 space-y-3 md:space-y-4">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Mobile Menu Toggle Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const themeToggle = document.querySelector('[data-theme-toggle]');
            const html = document.documentElement;
            
            if (themeToggle) {
                const savedTheme = localStorage.getItem('finflow-theme') || 'light';
                html.className = savedTheme === 'dark' ? 'dark' : '';
                
                themeToggle.addEventListener('click', function() {
                    const isDark = html.classList.toggle('dark');
                    localStorage.setItem('finflow-theme', isDark ? 'dark' : 'light');
                });
            }
        });
    </script>
</body>
</html>
