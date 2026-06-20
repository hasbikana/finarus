# FinFlow - Panduan Konversi ke Laravel dengan Breeze

## Setup Awal

### 1. Buat Project Laravel Baru dengan Breeze
```bash
composer create-project laravel/laravel finflow-laravel
cd finflow-laravel
composer require laravel/breeze --dev
php artisan breeze:install blade
npm install && npm run dev
```

### 2. Setup Database
Di file `.env`:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=finflow_db
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 3. Tailwind CSS (Sudah termasuk dengan Breeze)
Breeze sudah mengkonfigurasi Tailwind CSS untuk Anda.

---

## File Structure

```
finflow-laravel/
├── resources/views/
│   ├── layouts/
│   │   ├── app.blade.php
│   │   ├── auth.blade.php
│   ├── components/
│   │   ├── sidebar.blade.php
│   │   ├── header.blade.php
│   │   ├── card.blade.php
│   │   ├── button.blade.php
│   ├── app/
│   │   ├── dashboard.blade.php
│   │   ├── transaksi.blade.php
│   │   ├── kategori.blade.php
│   │   ├── anggaran.blade.php
│   │   ├── tabungan.blade.php
│   │   ├── laporan.blade.php
│   │   ├── dompet-digital.blade.php
│   ├── auth/
│   │   ├── login.blade.php (sudah ada, bisa di-customize)
├── routes/
│   ├── web.php
├── app/Http/Controllers/
│   ├── DashboardController.php
│   ├── TransactionController.php
│   ├── etc...
├── public/
│   ├── logos/
│   │   ├── gopay.png
│   │   ├── ovo.png
│   │   ├── dana.png
│   │   ├── linkaja.png
│   │   ├── bca.png
│   │   ├── bni.png
│   │   ├── mandiri.png
│   │   ├── bri.png
```

---

## Langkah Implementasi

### 1. Copy Logos ke Public Folder
Copy semua file `.png` dari project Next.js ke `public/logos/`

### 2. Update Tailwind Config
File `tailwind.config.js` di Laravel Breeze sudah siap. Tidak perlu perubahan khusus untuk warna custom (gunakan class-based styling).

### 3. Buat Routes (routes/web.php)

```php
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('app.dashboard');
    })->name('dashboard');
    
    Route::get('/transaksi', function () {
        return view('app.transaksi');
    })->name('transaksi');
    
    Route::get('/kategori', function () {
        return view('app.kategori');
    })->name('kategori');
    
    Route::get('/anggaran', function () {
        return view('app.anggaran');
    })->name('anggaran');
    
    Route::get('/tabungan', function () {
        return view('app.tabungan');
    })->name('tabungan');
    
    Route::get('/laporan', function () {
        return view('app.laporan');
    })->name('laporan');
    
    Route::get('/dompet-digital', function () {
        return view('app.dompet-digital');
    })->name('dompet-digital');
    
    Route::get('/pengaturan', function () {
        return view('app.pengaturan');
    })->name('pengaturan');
    
    Route::get('/bantuan', function () {
        return view('app.bantuan');
    })->name('bantuan');
});

// Redirect home ke dashboard
Route::get('/', function () {
    return redirect()->route('dashboard');
});
```

### 4. Update Navigation Links
Di file `resources/views/components/sidebar.blade.php`, update href menjadi route helper:
```blade
<a href="{{ route('transaksi') }}">Transaksi</a>
```

---

## Blade Components yang Perlu Dibuat

Semua files Blade sudah disediakan di bagian berikutnya dari panduan ini. Copy-paste ke folder masing-masing.

---

## Tips & Tricks

1. **Dark Mode**: Gunakan `x-ray` atau browser DevTools untuk toggle dark mode (Breeze mendukung dark mode)
2. **Static Data**: Untuk saat ini, gunakan variabel PHP dalam Blade untuk data sampai API siap
3. **Images**: Gunakan `asset('path')` untuk referensi file publik
4. **Links**: Gunakan `route('name')` untuk generate route URLs
5. **CSRF**: Breeze sudah handle CSRF automatically

---

## Environment Config

Setelah setup, pastikan untuk:
```bash
php artisan migrate
npm run dev # untuk development dengan hot reload
php artisan serve # di terminal berbeda
```

Selesai! Aplikasi Anda siap berjalan di `http://localhost:8000`

---

## File Blade Code

Lihat bagian berikutnya untuk semua code Blade yang diperlukan.
