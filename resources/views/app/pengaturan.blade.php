@extends('layouts.app')

@section('title', 'Pengaturan - Finarus')
@section('page-title', 'Pengaturan')
@section('page-description', 'Kelola preferensi dan keamanan akun Anda')

@section('content')
<div class="max-w-2xl bg-card rounded-lg shadow-lg p-5">
    <div class="space-y-6">
        <div>
            <h3 class="font-semibold mb-3">Notifikasi</h3>
            <div class="space-y-2">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" {{ $settings->email_notifications ? 'checked' : '' }} class="rounded" onchange="updateSetting('email_notifications', this.checked)">
                    <span class="text-sm">Notifikasi Transaksi</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" {{ $settings->budget_alerts ? 'checked' : '' }} class="rounded" onchange="updateSetting('budget_alerts', this.checked)">
                    <span class="text-sm">Notifikasi Anggaran</span>
                </label>
            </div>
        </div>

        <hr class="border-border">

        <div>
            <h3 class="font-semibold mb-3">Tampilan</h3>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" {{ $settings->theme === 'dark' ? 'checked' : '' }} class="rounded" onchange="toggleThemeFromSetting(this.checked)">
                <span class="text-sm">Mode Gelap</span>
            </label>
        </div>

        <hr class="border-border">

        <div>
            <h3 class="font-semibold mb-3">Keamanan</h3>
            <a href="{{ route('profile.edit') }}" class="inline-flex items-center h-9 px-4 rounded-md bg-primary text-primary-foreground hover:bg-primary/90 transition-colors text-sm font-medium">
                Ubah Password
            </a>
        </div>

        <hr class="border-border">

        <div>
            <div class="flex items-center justify-between mb-3">
                <div>
                    <h3 class="font-semibold">Integrasi Gmail</h3>
                    <p class="text-xs text-muted-foreground mt-0.5">Ambil transaksi bank & e-wallet otomatis dari email</p>
                </div>
                @if($oauthConnected)
                <form method="POST" action="{{ route('oauth.google.disconnect') }}" onsubmit="return confirm('Putuskan koneksi Google dan hentikan email fetching?')">
                    @csrf
                    <button type="submit" class="h-8 px-3 rounded-md border border-red-200 text-red-600 hover:bg-red-50 transition-colors text-xs font-medium dark:border-red-900 dark:hover:bg-red-900/20">
                        Putuskan
                    </button>
                </form>
                @else
                <a href="{{ route('oauth.google.connect') }}" class="inline-flex items-center gap-2 h-9 px-4 rounded-md bg-primary text-primary-foreground hover:bg-primary/90 transition-colors text-sm font-medium">
                    <svg class="w-4 h-4" viewBox="0 0 24 24"><path fill="currentColor" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 01-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z"/><path fill="currentColor" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="currentColor" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="currentColor" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                    Hubungkan Google
                </a>
                @endif
            </div>

            @if($oauthConnected)
            <div class="text-sm">
                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-green-100 text-green-700 text-xs font-medium dark:bg-green-900/20 dark:text-green-400">
                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                    Tersambung
                </span>
                @if($oauthEmail)
                <span class="ml-2 text-muted-foreground">{{ $oauthEmail }}</span>
                @endif
                <label class="flex items-center gap-2 mt-3 cursor-pointer">
                    <input type="checkbox" {{ $oauthFetchEnabled ? 'checked' : '' }} class="rounded" onchange="updateSetting('email_fetch_enabled', this.checked)">
                    <span>Ambil transaksi bank/e-wallet otomatis</span>
                </label>
            </div>
            @else
            <p class="text-sm text-muted-foreground">Hubungkan akun Google untuk otomatis mencatat transaksi dari email bank & e-wallet Anda.</p>
            @endif
        </div>

        <hr class="border-border">

        <div class="flex justify-end gap-3">
            <button class="h-9 px-4 rounded-md border border-border hover:bg-muted transition-colors text-sm font-medium">
                Batal
            </button>
            <button class="h-9 px-4 rounded-md bg-primary text-primary-foreground hover:bg-primary/90 transition-colors text-sm font-medium">
                Simpan
            </button>
        </div>
    </div>
</div>

<script>
function updateSetting(key, value) {
    fetch('/api/settings', {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ [key]: value })
    }).then(r => r.json()).then(data => {
        console.log('Setting updated:', data);
    }).catch(err => console.error(err));
}

function toggleThemeFromSetting(isDark) {
    if (isDark) {
        document.documentElement.classList.add('dark');
        localStorage.setItem('finarus-theme', 'dark');
        if (typeof updateThemeIcon === 'function') updateThemeIcon(true);
    } else {
        document.documentElement.classList.remove('dark');
        localStorage.setItem('finarus-theme', 'light');
        if (typeof updateThemeIcon === 'function') updateThemeIcon(false);
    }
    updateSetting('theme', isDark ? 'dark' : 'light');
}
</script>
@endsection
