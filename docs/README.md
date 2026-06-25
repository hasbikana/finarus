# Finarus – Aplikasi Pencatat Keuangan Pribadi

Finarus adalah aplikasi berbasis web untuk mengelola keuangan pribadi. Mendukung pencatatan transaksi otomatis melalui pembacaan email perbankan/e-wallet, rekonsiliasi otomatis, serta visualisasi laporan keuangan.

## Tech Stack

| Lapisan | Teknologi |
|---|---|
| Backend | PHP 8.3, Laravel 13, MySQL 8 |
| Frontend | Blade + Tailwind CSS |
| API Auth | Laravel Sanctum (token) |
| Web Auth | Session based |
| OAuth Google | Socialite (login) + Google Client (Gmail) |

## Fitur Utama

- **Autentikasi** – Login manual (email + password), register, login Google OAuth.
- **Akun Keuangan** – Cash, E-Wallet, Bank, Kartu Kredit.
- **Transaksi** – Pencatatan pemasukan & pengeluaran, filter, pagination.
- **Kategori** – Kustom ikon & warna.
- **Anggaran (Budget)** – Per kategori per bulan.
- **Tabungan (Saving Goal)** – Target nominal + progress.
- **Email Fetching** – Ambil transaksi otomatis dari email bank/e-wallet via Gmail API.
- **Email Scope** – Filter pengirim email per akun.
- **Notifikasi** – Persetujuan transaksi dari hasil parsing email.
- **Laporan** – Ringkasan bulanan, kategori, tren.
- **Dompet Digital** – Kelola semua akun non-cash.

## Struktur Direktori Utama

```
app/
├── Console/Commands/        # Artisan command
├── Contracts/               # Interface (EmailParser)
├── DTO/                     # ParsedTransaction data object
├── Http/
│   ├── Controllers/
│   │   ├── Api/             # API controllers
│   │   └── Auth/            # Web auth controllers
│   └── Requests/            # Form requests
├── Jobs/                    # FetchBankEmails (queue)
├── Models/                  # Eloquent models
├── Observers/               # Model observers
├── Parsers/                 # Email parsers (BCA, Mandiri, dll)
├── Policies/                # Authorization policies
├── Providers/               # Service provider
└── Services/                # Business logic (GmailService, EmailParserService, dll)
config/                      # Laravel config files
database/
├── migrations/              # Database migrations
└── seeders/                 # Seeder demo data
resources/views/             # Blade templates (auth, app)
routes/
├── web.php                  # Web routes
├── api.php                  # API routes
└── auth.php                 # Auth routes
```

## Migrasi Database

```bash
php artisan migrate
```

## Demo

Seeder menyediakan data demo:

```bash
php artisan db:seed --class=DemoDataSeeder
```

Akun demo: `demo@finarus.com` / `password`
