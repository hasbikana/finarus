# FinFlow Laravel - Customisasi Breeze Login & Setup Final

## Customisasi Breeze Login Page

Edit file: `resources/views/auth/login.blade.php`

Replace semuanya dengan code di bawah ini untuk match design FinFlow:

```blade
<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-600 to-blue-700 px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-md">
            <!-- Logo & Title -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-lg bg-white/20 mb-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-white mb-2">FinFlow</h1>
                <p class="text-blue-100">Kelola Keuangan Anda dengan Mudah</p>
            </div>

            <!-- Login Form Card -->
            <div class="bg-white rounded-xl shadow-2xl p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Masuk ke Akun Anda</h2>

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Email Address -->
                    <div class="mb-4">
                        <x-input-label for="email" :value="__('Email')" class="text-gray-700 font-medium" />
                        <x-text-input 
                            id="email" 
                            class="block mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-transparent" 
                            type="email" 
                            name="email" 
                            :value="old('email')" 
                            required 
                            autofocus 
                            autocomplete="email"
                            placeholder="masukkan@email.com"
                        />
                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-600 text-sm" />
                    </div>

                    <!-- Password -->
                    <div class="mb-4">
                        <x-input-label for="password" :value="__('Password')" class="text-gray-700 font-medium" />
                        <x-text-input 
                            id="password" 
                            class="block mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-transparent"
                            type="password" 
                            name="password" 
                            required 
                            autocomplete="current-password"
                            placeholder="••••••••"
                        />
                        <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-600 text-sm" />
                    </div>

                    <!-- Remember Me -->
                    <div class="mb-6 flex items-center">
                        <input 
                            id="remember_me" 
                            type="checkbox" 
                            class="w-4 h-4 rounded border-gray-300 text-blue-600 cursor-pointer" 
                            name="remember"
                        >
                        <label for="remember_me" class="ml-2 text-sm text-gray-600 cursor-pointer">
                            {{ __('Ingat saya') }}
                        </label>
                    </div>

                    <!-- Login Button -->
                    <button 
                        type="submit" 
                        class="w-full py-2 px-4 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors duration-300 mb-4"
                    >
                        {{ __('Masuk') }}
                    </button>

                    <!-- Forgot Password Link -->
                    @if (Route::has('password.request'))
                        <div class="text-center">
                            <a href="{{ route('password.request') }}" class="text-sm text-blue-600 hover:text-blue-700">
                                {{ __('Lupa Password?') }}
                            </a>
                        </div>
                    @endif
                </form>

                <!-- Register Link -->
                <div class="mt-6 text-center text-sm text-gray-600">
                    Belum punya akun?
                    <a href="{{ route('register') }}" class="text-blue-600 hover:text-blue-700 font-medium">
                        Daftar di sini
                    </a>
                </div>
            </div>

            <!-- Footer -->
            <div class="mt-8 text-center text-blue-100 text-sm">
                <p>&copy; 2024 FinFlow. Semua hak dilindungi.</p>
            </div>
        </div>
    </div>
</x-guest-layout>
```

---

## Update Register Page (Opsional)

Edit file: `resources/views/auth/register.blade.php` dengan design yang similar:

```blade
<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-600 to-blue-700 px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-md">
            <!-- Logo & Title -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-lg bg-white/20 mb-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-white mb-2">FinFlow</h1>
                <p class="text-blue-100">Daftar Akun Baru</p>
            </div>

            <!-- Register Form Card -->
            <div class="bg-white rounded-xl shadow-2xl p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Buat Akun Baru</h2>

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <!-- Name -->
                    <div class="mb-4">
                        <x-input-label for="name" :value="__('Nama Lengkap')" class="text-gray-700 font-medium" />
                        <x-text-input 
                            id="name" 
                            class="block mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-transparent" 
                            type="text" 
                            name="name" 
                            :value="old('name')" 
                            required 
                            autofocus
                            autocomplete="name"
                            placeholder="Masukkan nama Anda"
                        />
                        <x-input-error :messages="$errors->get('name')" class="mt-2 text-red-600 text-sm" />
                    </div>

                    <!-- Email Address -->
                    <div class="mb-4">
                        <x-input-label for="email" :value="__('Email')" class="text-gray-700 font-medium" />
                        <x-text-input 
                            id="email" 
                            class="block mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-transparent" 
                            type="email" 
                            name="email" 
                            :value="old('email')" 
                            required
                            autocomplete="email"
                            placeholder="masukkan@email.com"
                        />
                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-600 text-sm" />
                    </div>

                    <!-- Password -->
                    <div class="mb-4">
                        <x-input-label for="password" :value="__('Password')" class="text-gray-700 font-medium" />
                        <x-text-input 
                            id="password" 
                            class="block mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-transparent"
                            type="password" 
                            name="password" 
                            required
                            autocomplete="new-password"
                            placeholder="••••••••"
                        />
                        <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-600 text-sm" />
                    </div>

                    <!-- Confirm Password -->
                    <div class="mb-6">
                        <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" class="text-gray-700 font-medium" />
                        <x-text-input 
                            id="password_confirmation" 
                            class="block mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-transparent"
                            type="password" 
                            name="password_confirmation" 
                            required
                            autocomplete="new-password"
                            placeholder="••••••••"
                        />
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-red-600 text-sm" />
                    </div>

                    <!-- Register Button -->
                    <button 
                        type="submit" 
                        class="w-full py-2 px-4 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors duration-300 mb-4"
                    >
                        {{ __('Daftar') }}
                    </button>
                </form>

                <!-- Login Link -->
                <div class="mt-6 text-center text-sm text-gray-600">
                    Sudah punya akun?
                    <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-700 font-medium">
                        Masuk di sini
                    </a>
                </div>
            </div>

            <!-- Footer -->
            <div class="mt-8 text-center text-blue-100 text-sm">
                <p>&copy; 2024 FinFlow. Semua hak dilindungi.</p>
            </div>
        </div>
    </div>
</x-guest-layout>
```

---

## Setup & Configuration Checklist

- [ ] Create fresh Laravel project dengan Breeze
- [ ] Setup database di `.env`
- [ ] Copy semua blade files dari dokumentasi
- [ ] Update routes di `routes/web.php`
- [ ] Copy logo files ke `public/logos/`
- [ ] Customize login & register pages (code di atas)
- [ ] Update navigation links (sidebar components)
- [ ] Run migrations: `php artisan migrate`
- [ ] Start dev server: `npm run dev` dan `php artisan serve`
- [ ] Test aplikasi di `http://localhost:8000`

---

## Navigation Links Reference

Di sidebar dan header, gunakan `route()` helper untuk generate links:

```blade
<!-- Link contoh -->
<a href="{{ route('dashboard') }}">Dasbor</a>
<a href="{{ route('transaksi') }}">Transaksi</a>
<a href="{{ route('logout') }}">Logout</a>

<!-- Form contoh -->
<form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit">Logout</button>
</form>
```

---

## Tailwind CSS Dark Mode

Breeze sudah setup Tailwind dengan dark mode support. User bisa toggle dengan:
- Manual toggle di settings
- Browser preference (prefers-color-scheme)

Gunakan class `dark:` untuk styling dark mode:
```blade
<div class="bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
    Ini akan berubah di dark mode
</div>
```

---

## Assets Management

Semua assets (images, logos) disimpan di folder `public/`. Reference dengan `asset()` helper:

```blade
<img src="{{ asset('logos/gopay.png') }}" alt="GoPay">
```

---

## Next Steps

1. Setup project Laravel Breeze Anda
2. Copy semua files dari dokumentasi ini
3. Update database connection
4. Jalankan migrations
5. Test authentication flow
6. Customize logic API sesuai kebutuhan Anda

Semua frontend views sudah siap! Tinggal connect ke backend API Anda.
