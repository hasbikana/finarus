# FinFlow Laravel Breeze - Complete Implementation Guide

Panduan lengkap untuk mengkonversi FinFlow dari Next.js ke Laravel Breeze dengan semua code yang diperlukan.

## Quick Start

1. Create Laravel project dengan Breeze:
```bash
composer create-project laravel/laravel finflow
cd finflow
composer require laravel/breeze --dev
php artisan breeze:install blade
npm install
```

2. Setup database di `.env`

3. Copy semua Blade files dari dokumentasi di bawah

4. Update `routes/web.php` sesuai routing guide

5. Copy logos ke `public/logos/`

6. Run:
```bash
php artisan migrate
npm run dev    # di terminal 1
php artisan serve  # di terminal 2
```

---

## File Structure yang Diperlukan

```
resources/views/
├── layouts/
│   └── app.blade.php          (main layout)
├── components/
│   ├── sidebar.blade.php      (sidebar navigation)
│   └── header.blade.php       (page header)
├── app/
│   ├── dashboard.blade.php    (halaman dasbor)
│   ├── transaksi/
│   │   └── index.blade.php    (halaman transaksi)
│   ├── kategori/
│   │   └── index.blade.php    (halaman kategori)
│   ├── anggaran/
│   │   └── index.blade.php    (halaman anggaran)
│   ├── tabungan/
│   │   └── index.blade.php    (halaman tabungan)
│   ├── laporan/
│   │   └── index.blade.php    (halaman laporan)
│   ├── dompet-digital/
│   │   └── index.blade.php    (e-wallet & bank)
│   ├── pengaturan.blade.php
│   └── bantuan.blade.php
```

---

## Routes (routes/web.php)

```php
<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('app.dashboard');
    })->name('dashboard');

    Route::get('/transaksi', function () {
        return view('app.transaksi.index');
    })->name('transaksi');

    Route::get('/kategori', function () {
        return view('app.kategori.index');
    })->name('kategori');

    Route::get('/anggaran', function () {
        return view('app.anggaran.index');
    })->name('anggaran');

    Route::get('/tabungan', function () {
        return view('app.tabungan.index');
    })->name('tabungan');

    Route::get('/laporan', function () {
        return view('app.laporan.index');
    })->name('laporan');

    Route::get('/dompet-digital', function () {
        return view('app.dompet-digital.index');
    })->name('dompet-digital');

    Route::get('/pengaturan', function () {
        return view('app.pengaturan');
    })->name('pengaturan');

    Route::get('/bantuan', function () {
        return view('app.bantuan');
    })->name('bantuan');

    Route::post('/logout', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

require __DIR__.'/auth.php';
```

---

## Main Layout (resources/views/layouts/app.blade.php)

```blade
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - FinFlow</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white text-gray-900 dark:bg-gray-950 dark:text-gray-100 antialiased">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        @include('components.sidebar')
        
        <!-- Main Content -->
        <main class="flex-1 p-3 md:p-4 lg:p-5 lg:ml-64">
            <!-- Header -->
            @include('components.header')
            
            <!-- Page Content -->
            @yield('content')
        </main>
    </div>
</body>
</html>
```

---

## Header Component (resources/views/components/header.blade.php)

```blade
<header class="space-y-3 md:space-y-4 mb-4">
  <div class="flex items-center justify-between gap-3">
    <div class="flex items-center gap-2 flex-1">
      <!-- Search -->
      <div class="relative flex-1 max-w-md">
        <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
        </svg>
        <input 
          type="text" 
          placeholder="Cari transaksi..." 
          class="pl-9 pr-3 h-9 w-full text-sm rounded-md border border-gray-300 bg-white text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
        >
      </div>
    </div>

    <!-- User Section -->
    <div class="flex items-center gap-2 pl-3 border-l border-gray-300 dark:border-gray-700">
      <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold">
        {{ auth()->user()->name[0] ?? 'U' }}
      </div>
      <div class="text-xs hidden sm:block">
        <p class="font-semibold text-gray-900 dark:text-white">{{ auth()->user()->name }}</p>
        <p class="text-gray-500 dark:text-gray-400 text-[10px]">{{ auth()->user()->email }}</p>
      </div>
    </div>
  </div>

  <!-- Page Title -->
  <div>
    <h1 class="text-xl md:text-2xl lg:text-3xl font-bold text-gray-900 mb-1 dark:text-white">{{ $headerTitle ?? 'Dashboard' }}</h1>
    <p class="text-xs md:text-sm text-gray-600 dark:text-gray-400">{{ $headerDescription ?? 'Selamat datang di FinFlow' }}</p>
  </div>
</header>
```

---

## Dashboard Page (resources/views/app/dashboard.blade.php)

```blade
@extends('layouts.app')

@section('title', 'Dasbor')

@section('content')
<div class="mt-4 md:mt-5 space-y-3 md:space-y-4">
  <!-- Stats Cards -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
    <!-- Saldo Total -->
    <div class="bg-blue-600 text-white p-5 rounded-lg shadow-lg hover:shadow-xl transition-all duration-300">
      <div class="flex items-start justify-between mb-4">
        <h3 class="text-xs font-semibold opacity-90 tracking-wider">Saldo Total</h3>
        <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
        </div>
      </div>
      <p class="text-2xl sm:text-3xl font-bold mb-3">Rp 12.459.500</p>
      <div class="flex items-center gap-1.5 text-xs opacity-80">
        <svg class="w-3 h-3 text-green-400" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h.01a1 1 0 110 2H12zm-3.976 7a1 1 0 11-1.414-1.414l2.83-2.83a1 1 0 111.414 1.414l-2.83 2.83zm5.657-5.657a1 1 0 001.414-1.414L12.343 6.343a1 1 0 00-1.414 1.414l2.83 2.83z"></path>
        </svg>
        <span>+5.2% dari bulan lalu</span>
      </div>
    </div>

    <!-- Pemasukan Bulan Ini -->
    <div class="bg-white border border-gray-200 p-5 rounded-lg shadow-lg hover:shadow-xl transition-all duration-300 dark:bg-gray-800 dark:border-gray-700">
      <div class="flex items-start justify-between mb-4">
        <h3 class="text-xs font-semibold text-gray-600 dark:text-gray-400 tracking-wider">Pemasukan Bulan Ini</h3>
        <div class="w-8 h-8 rounded-lg bg-green-500/10 flex items-center justify-center">
          <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8L5.343 18.657a2 2 0 01-2.828 0l-1.414-1.414a2 2 0 010-2.828L16.172 3"></path>
          </svg>
        </div>
      </div>
      <p class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white mb-3">Rp 4.250.000</p>
      <div class="flex items-center gap-1.5 text-xs text-green-600">
        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h.01a1 1 0 110 2H12zm-3.976 7a1 1 0 11-1.414-1.414l2.83-2.83a1 1 0 111.414 1.414l-2.83 2.83zm5.657-5.657a1 1 0 001.414-1.414L12.343 6.343a1 1 0 00-1.414 1.414l2.83 2.83z"></path>
        </svg>
        <span>+12% dari bulan lalu</span>
      </div>
    </div>

    <!-- Pengeluaran Bulan Ini -->
    <div class="bg-white border border-gray-200 p-5 rounded-lg shadow-lg hover:shadow-xl transition-all duration-300 dark:bg-gray-800 dark:border-gray-700">
      <div class="flex items-start justify-between mb-4">
        <h3 class="text-xs font-semibold text-gray-600 dark:text-gray-400 tracking-wider">Pengeluaran Bulan Ini</h3>
        <div class="w-8 h-8 rounded-lg bg-red-500/10 flex items-center justify-center">
          <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17H5v8h14v-8h-6"></path>
          </svg>
        </div>
      </div>
      <p class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white mb-3">Rp 2.180.750</p>
      <div class="flex items-center gap-1.5 text-xs text-red-600">
        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M12 13a1 1 0 100-2h-.01a1 1 0 100 2H12zm3.976-7a1 1 0 111.414 1.414L12.657 14.657a1 1 0 01-1.414-1.414l2.83-2.83z"></path>
        </svg>
        <span>-3% dari bulan lalu</span>
      </div>
    </div>

    <!-- Tujuan Tabungan Aktif -->
    <div class="bg-white border border-gray-200 p-5 rounded-lg shadow-lg hover:shadow-xl transition-all duration-300 dark:bg-gray-800 dark:border-gray-700">
      <div class="flex items-start justify-between mb-4">
        <h3 class="text-xs font-semibold text-gray-600 dark:text-gray-400 tracking-wider">Tujuan Tabungan Aktif</h3>
        <div class="w-8 h-8 rounded-lg bg-blue-500/10 flex items-center justify-center">
          <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
          </svg>
        </div>
      </div>
      <p class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white mb-3">3</p>
      <div class="text-xs text-gray-600 dark:text-gray-400">
        <span>Berjalan lancar</span>
      </div>
    </div>
  </div>

  <!-- Recent Transactions & Widgets -->
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-3 md:gap-4">
    <!-- Recent Transactions -->
    <div class="lg:col-span-2 bg-white rounded-lg shadow-lg p-5 dark:bg-gray-800">
      <div class="flex items-center justify-between mb-5">
        <div>
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Transaksi Terbaru</h3>
          <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">5 transaksi terbaru Anda</p>
        </div>
      </div>

      <!-- Transaction List -->
      <div class="space-y-3">
        @php
          $transactions = [
            ['desc' => 'Kopi Starbucks', 'amount' => '-Rp 55.000', 'time' => 'Hari ini pukul 09:30', 'icon' => '☕', 'color' => 'orange'],
            ['desc' => 'Pembayaran Proyek Freelance', 'amount' => '+Rp 1.250.000', 'time' => 'Kemarin pukul 14:15', 'icon' => '💵', 'color' => 'green'],
            ['desc' => 'Belanja Online', 'amount' => '-Rp 874.500', 'time' => '2 hari lalu', 'icon' => '🛍️', 'color' => 'blue'],
            ['desc' => 'Tagihan Listrik', 'amount' => '-Rp 1.250.000', 'time' => '3 hari lalu', 'icon' => '⚡', 'color' => 'yellow'],
            ['desc' => 'Makan di Restoran', 'amount' => '-Rp 623.000', 'time' => '5 hari lalu', 'icon' => '🍽️', 'color' => 'red'],
          ];
        @endphp

        @foreach($transactions as $txn)
        <div class="flex items-center justify-between p-3 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-lg">
              {{ $txn['icon'] }}
            </div>
            <div>
              <p class="font-medium text-gray-900 dark:text-white">{{ $txn['desc'] }}</p>
              <p class="text-xs text-gray-500 dark:text-gray-400">{{ $txn['time'] }}</p>
            </div>
          </div>
          <p class="font-semibold {{ str_contains($txn['amount'], '+') ? 'text-green-600' : 'text-gray-900 dark:text-white' }}">
            {{ $txn['amount'] }}
          </p>
        </div>
        @endforeach
      </div>

      <button class="w-full mt-4 py-2 text-sm font-medium text-blue-600 hover:bg-blue-50 dark:hover:bg-gray-700 rounded-lg transition-colors">
        Lihat Semua Transaksi
      </button>
    </div>

    <!-- Budget Progress -->
    <div class="bg-white rounded-lg shadow-lg p-5 dark:bg-gray-800">
      <div>
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">Progres Anggaran</h3>
        <p class="text-xs text-gray-600 dark:text-gray-400 mb-5">Ringkasan pengeluaran bulan ini</p>
      </div>

      <div class="space-y-4">
        @php
          $budgets = [
            ['category' => 'Makanan', 'spent' => 240, 'budget' => 400, 'color' => 'orange'],
            ['category' => 'Belanja', 'spent' => 180, 'budget' => 300, 'color' => 'blue'],
            ['category' => 'Utilitas', 'spent' => 125, 'budget' => 150, 'color' => 'yellow'],
            ['category' => 'Hiburan', 'spent' => 95, 'budget' => 200, 'color' => 'purple'],
          ];
        @endphp

        @foreach($budgets as $budget)
        <div>
          <div class="flex items-center justify-between mb-2">
            <span className="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $budget['category'] }}</span>
            <span class="text-sm font-semibold text-gray-900 dark:text-white">
              Rp {{ $budget['spent'] }} / Rp {{ $budget['budget'] }}
            </span>
          </div>
          <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
            <div class="bg-{{ $budget['color'] }}-500 h-2 rounded-full" style="width: {{ ($budget['spent'] / $budget['budget']) * 100 }}%"></div>
          </div>
        </div>
        @endforeach
      </div>
    </div>
  </div>
</div>
@endsection
```

---

## Halaman Lainnya

Untuk halaman lainnya (Transaksi, Kategori, Anggaran, Tabungan, Laporan, E-Wallet), strukturnya similar dengan Dashboard. 

Setiap halaman perlu file:
- `resources/views/app/{nama-halaman}/index.blade.php`

Di dalamnya, gunakan:
```blade
@extends('layouts.app')

@section('title', 'Nama Halaman')

@section('content')
<!-- Konten halaman di sini -->
@endsection
```

---

## Customize Breeze Login

Edit `resources/views/auth/login.blade.php` untuk match design FinFlow. Ganti warna biru dengan primary blue (#1e3a8a atau rgb(30, 58, 138)), dan update styling sesuai kebutuhan.

---

## Copy Assets

```bash
# Copy logos ke public folder
cp /path/to/next-project/public/logos/* public/logos/
```

---

## Tips & Tricks

1. **Dynamic Navigation**: Sidebar sudah menggunakan `request()->routeIs()` untuk highlight active menu
2. **User Info**: Gunakan `auth()->user()->name` dan `auth()->user()->email` untuk dynamic user data
3. **Logout**: Logout button menggunakan form POST ke route logout yang sudah built-in Breeze
4. **Dark Mode**: Breeze sudah support dark mode via Tailwind, user bisa toggle via browser
5. **CSRF Protection**: Semua forms otomatis dilindungi CSRF (Breeze handle ini)

---

## Next Steps

1. Buat semua Blade files sesuai panduan di atas
2. Setup routes di `routes/web.php`
3. Copy logos
4. Test aplikasi di http://localhost:8000

Aplikasi siap digunakan! Logic API bisa disesuaikan kemudian sesuai kebutuhan database Anda.
