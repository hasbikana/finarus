<header class="space-y-3 md:space-y-4 animate-slide-in-up">
    <div class="flex items-center justify-between gap-3">
        <div class="flex items-center gap-2 flex-1">
            <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-md hover:bg-secondary transition-all duration-300 hover:scale-110">
                <svg class="w-5 h-5 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </button>
            <div class="relative flex-1 max-w-md hidden sm:block">
                <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                <input type="text" placeholder="Cari transaksi..." class="pl-9 pr-3 md:pr-16 h-9 text-sm bg-card border border-border rounded-md transition-all duration-300 focus:shadow-lg focus:shadow-primary/10 w-full">
            </div>
        </div>

        <div class="flex items-center gap-1.5 md:gap-2">
            <button onclick="toggleTheme()" class="relative hover:bg-secondary transition-all duration-300 hover:scale-110 h-8 w-8 p-2 rounded-md">
                <svg id="sun-icon" class="w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1m-16 0H1m15.364 1.636l.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                <svg id="moon-icon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
            </button>
            <button class="relative hover:bg-secondary transition-all duration-300 hover:scale-110 h-8 w-8 p-2 rounded-md">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                <span class="absolute top-1.5 right-1.5 w-1.5 h-1.5 bg-destructive rounded-full animate-pulse"></span>
            </button>

            @auth
            <div class="flex items-center gap-2 pl-2 md:pl-3 border-l border-border">
                <div class="w-7 h-7 md:w-8 md:h-8 rounded-full bg-primary/10 flex items-center justify-center ring-2 ring-primary/20 transition-all duration-300">
                    <span class="text-xs font-semibold text-primary">{{ strtoupper(auth()->user()->name[0] ?? 'U') }}</span>
                </div>
                <div class="text-xs hidden sm:block">
                    <p class="font-semibold text-foreground">{{ auth()->user()->name }}</p>
                    <p class="text-muted-foreground text-[10px]">{{ auth()->user()->email }}</p>
                </div>
            </div>
            @endauth
        </div>
    </div>

    <div>
        <h1 class="text-xl md:text-2xl lg:text-3xl font-bold text-foreground mb-1">@yield('page-title', 'Halaman')</h1>
        <p class="text-xs md:text-sm text-muted-foreground">@yield('page-description')</p>
    </div>

    @if(View::hasSection('page-actions'))
        <div class="flex flex-col sm:flex-row gap-2">
            @yield('page-actions')
        </div>
    @endif
</header>

<script>
function toggleTheme() {
    const html = document.documentElement;
    const isDark = html.classList.toggle('dark');
    localStorage.setItem('finarus-theme', isDark ? 'dark' : 'light');
    updateThemeIcon(isDark);
}

function updateThemeIcon(isDark) {
    document.getElementById('sun-icon').classList.toggle('hidden');
    document.getElementById('moon-icon').classList.toggle('hidden');
}

const savedTheme = localStorage.getItem('finarus-theme') || 'light';
if (savedTheme === 'dark') {
    document.documentElement.classList.add('dark');
    updateThemeIcon(true);
}
</script>