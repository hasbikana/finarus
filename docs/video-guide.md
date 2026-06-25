# Panduan Presentasi Video – Finarus

Dokumen ini berisi **skrip / narasi per scene** plus **potongan kode yang perlu ditunjuk** untuk menjelaskan logika tiap fitur.

---

## Scene 1 – Gambaran Umum & Tech Stack

**Durasi ~1 menit**

**Narasi:**

> Finarus adalah aplikasi pencatat keuangan pribadi berbasis web. Dibangun dengan Laravel 13 di sisi backend, Blade + Tailwind di frontend, dan MySQL sebagai database. Fitur unggulannya adalah kemampuan membaca transaksi langsung dari email bank dan e-wallet secara otomatis menggunakan Gmail API.

**Tunjuk kode:**

- `composer.json` → tunjukkan package utama: `laravel/framework`, `laravel/socialite`, `google/apiclient`.
- `config/app.php` → `'url' => env('APP_URL')`.

---

## Scene 2 – Autentikasi (Manual & Google)

**Durasi ~2 menit**

### Login Manual (30 detik)

**Narasi:**

> Autentikasi web menggunakan session. Route login menggunakan middleware guest. Saat submit, LoginRequest memanggil Auth::attempt dan jika sukses session diregenerasi untuk mencegah session fixation.

**Tunjuk kode:** `routes/auth.php` line 20-23 (route login POST).  
`app/Http/Controllers/Auth/AuthenticatedSessionController.php` baris 27-31 (store method).

### Register (30 detik)

**Narasi:**

> Registrasi membuat user baru, langsung membuat akun Cash / Dompet default, lalu login otomatis.

**Tunjuk kode:** `app/Http/Controllers/Auth/RegisteredUserController.php` baris 39-55 (create, event, Auth::login, redirect).

### Google Login Web (1 menit)

**Narasi:**

> Login Google menggunakan Laravel Socialite. Saat user klik tombol, diarahkan ke Google. Setelah callback, sistem mengecek apakah state cocok. Jika email sudah terdaftar langsung login; jika belum, user baru dibuat. Akun baru yang dibuat via Google langsung memiliki `email_verified_at`.

**Tunjuk kode:**
- `routes/web.php` → dua route OAuth: `/oauth/google/login` dan `/oauth/google/callback`.
- `app/Http/Controllers/Api/OAuthController.php`:
  - `redirectToGoogle()` (baris 22-32) → tunjukkan `prompt=select_account` untuk memudahkan ganti akun.
  - `handleGoogleCallback()` (baris 34-48) → try-catch, logging error.
  - `handleLoginFlow()` (baris 74-105) → User::create, Auth::login, redirect dashboard.
- `resources/views/auth/login.blade.php` → tunjukkan tombol Google dan error message yang sudah ditampilkan.

---

## Scene 3 – Email Fetching & Parsing (Fitur Unggulan)

**Durasi ~4 menit**

### Alur Fetch (1,5 menit)

**Narasi:**

> Fetch email dijalankan melalui job queue. Setiap user yang sudah connect Google dan mengaktifkan email fetch akan memiliki job FetchBankEmails. Job ini mengambil token OAuth, mengumpulkan email scope dari tiap akun user, lalu memanggil Gmail API untuk mencari email dari pengirim yang sudah ditentukan.

**Tunjuk kode:**
- `app/Jobs/FetchBankEmails.php`:
  - Baris 28-61 → method `handle()`.
  - Baris 36-40: dapatkan `$scopes` dari akun user.
  - Baris 43: panggil `$gmail->fetchNewEmails($token, $scopes, $this->maxEmails)`.
- `app/Services/GmailService.php`:
  - Baris 24-48: `fetchNewEmails()` → query Gmail dengan filter sender.
  - Baris 102-113: `buildSenderQuery()` → buat query `{from:email1 from:email2 ...}`.

### Parsing & Scoping Akun (1,5 menit)

**Narasi:**

> Setiap email yang terambil akan diparse. Parser dipilih berdasarkan alamat pengirim. Hasil parsing berupa objek ParsedTransaction yang berisi tipe (income/expense), nominal, deskripsi, tanggal, dan provider. Setelah itu, sistem menentukan akun mana yang terkait: pertama dicocokkan berdasarkan email scope (whereJsonContains), baru fallback ke provider name.

**Tunjuk kode:**
- `app/Services/EmailParserService.php`:
  - `parseEmail()` baris 26-38 → loop parser, canParse, parse.
  - `processParsedTransaction()` baris 40-79 → cari akun via `whereJsonContains('email_scopes', $fromEmail)` lalu fallback `where('provider', $parsed->provider)`.
- `app/Parsers/BcaParser.php` → contoh parser sederhana: `canParse()` periksa domain email, `parse()` ambil amount, tipe debit/topup, deskripsi, date.
- `app/Parsers/BaseParser.php` → tunjukkan helper `extractAmount()`, `extractDate()`, `isDebit()`.

### Email Scope (1 menit)

**Narasi:**

> Fitur Email Scope memungkinkan user menentukan alamat email pengirim mana saja yang ingin difetch per akun. Scope disimpan sebagai JSON di kolom `email_scopes` tabel accounts. Jika user belum mengisi scope, fallback menggunakan daftar global default (BCA, Mandiri, GoPay, dll).

**Tunjuk kode:**
- `app/Models/Account.php` → cast `email_scopes => array`, fillable mencakup `email_scopes`.
- `app/Http/Requests/StoreAccountRequest.php` → validasi `email_scopes.*` = email format.
- `resources/views/app/dompet-digital/index.blade.php` → tunjukkan:
  - Tombol "Isi email default provider" (call `fillDefaultScopes()`).
  - Input tag + render.
  - Badge "3 email scope" di daftar akun (baris 14).

---

## Scene 4 – CRUD Akun, Transaksi, Kategori, Anggaran, Tabungan

**Durasi ~2 menit**

**Narasi:**

> Manajemen akun keuangan menggunakan form modal yang sama untuk tambah dan edit. Data tervalidasi oleh Form Request. Akun tipe cash otomatis tidak punya email scope.

**Tunjuk kode:**
- `app/Http/Controllers/WebCrudController.php`:
  - `storeDompet()` baris 146-151 → create akun dengan data tervalidasi.
  - `updateDompet()` baris 153-163 → update akun.
- `app/Http/Requests/StoreAccountRequest.php` → rule `type` enum `cash,ewallet,bank,credit_card`.

**Narasi (Transaksi):**

> Transaksi bisa dibuat manual atau otomatis dari email. Semua transaksi tersimpan di tabel `transactions` dan bisa difilter berdasarkan tipe, kategori, akun, atau rentang tanggal.

**Tunjuk kode:**
- `app/Services/TransactionService.php` → method `createTransaction()`.

**Narasi (Kategori):**

> Kategori bersifat unik per user, memiliki icon emoji dan warna hex, digunakan pula untuk grouping pada laporan.

**Narasi (Anggaran):**

> Anggaran adalah limit bulanan per kategori. Jika pengeluaran melebihi 80% limit atau over budget, muncul alert di dashboard.

**Tunjuk kode:**
- `app/Models/Budget.php` (model relationships).

**Narasi (Tabungan):**

> Tabungan menampung target nominal, progress, dan deadline. User bisa menambah dana via form "Add Fund" yang otomatis membuat transaksi expense.

**Tunjuk kode:**
- `app/Http/Controllers/WebCrudController.php` → `addFund()` baris 116-144.

---

## Scene 5 – Notifikasi & Persetujuan Transaksi

**Durasi ~1 menit**

**Narasi:**

> Transaksi hasil parsing email masuk ke sistem sebagai pending. User perlu approve di halaman Notifikasi. Saat approve, user memilih kategori dan akun tujuan, lalu transaksi final dibuat. Notifikasi yang sudah diproses akan berubah status menjadi confirmed atau rejected.

**Tunjuk kode:**
- `app/Http/Controllers/WebPageController.php`:
  - `notifikasi()` baris 183-196 → list pending notifications.
  - `approveNotification()` baris 198-231 → validasi, panggil `$transactionService->createTransaction()`, update status.
  - `rejectNotification()` baris 233-244 → update status rejected.
- `app/Models/PendingNotification.php` → enum status `pending, confirmed, rejected`.

---

## Scene 6 – Laporan & Dashboard

**Durasi ~1 menit**

**Narasi:**

> Dashboard menampilkan ringkasan keuangan: total pemasukan, pengeluaran, saldo per tipe akun, transaksi terbaru, budget alerts, dan jumlah notifikasi pending. Laporan bisa diakses di halaman laporan dengan 3 tab: Ringkasan Bulanan, Breakdown Per Kategori, dan Tren Tahunan. Data di-load via AJAX.

**Tunjuk kode:**
- `app/Http/Controllers/WebPageController.php` → `dashboard()` baris 25-82.
- `app/Services/DashboardService.php` (API).
- `app/Services/ReportService.php` → method `getMonthlySummary()`, `getCategoryBreakdown()`, `getMonthlyTrend()`.

---

## Scene 7 – API Endpoint

**Durasi ~1 menit**

**Narasi:**

> Semua resource juga tersedia melalui REST API. Autentikasi menggunakan Bearer Token dari Laravel Sanctum. Pengecualian untuk register, login, dan google login. Endpoint API menggunakan prefix `/api`. Untuk user yang login via web, ada juga route OAuth connect via web.

**Tunjuk kode:**
- `routes/api.php` → tampilkan semua route. Tunjukkan middleware auth:sanctum.
- `app/Http/Controllers/Api/AuthController.php` → `login()`, `register()`, `logout()`, `me()`.

---

## Scene 8 – Database

**Durasi ~30 detik**

**Narasi:**

> Database menggunakan MySQL dengan 17+ tabel inti. Relasi foreign key menggunakan cascade delete. Kolom `email_scopes` pada tabel accounts adalah JSON yang menyimpan daftar email pengirim. Tabel transaksi memiliki index pada `transaction_date` dan `user_id` untuk performa query laporan.

**Tunjuk kode:**
- `docs/database.md` → tunjukkan halaman dokumentasi.
- `database/migrations/` → tunjukkan 2-3 file migrasi utama.

---

## Ringkasan Poin yang Wajib Ditunjukkan di Video

| No | Poin | Kode |
|---|---|---|
| 1 | Alur fetch email | `app/Jobs/FetchBankEmails.php` |
| 2 | Parsing email + scope akun | `app/Services/EmailParserService.php` |
| 3 | Google login callback | `app/Http/Controllers/Api/OAuthController.php` |
| 4 | Email scope di akun | `resources/views/app/dompet-digital/index.blade.php` |
| 5 | CRUD transaksi/akun | `app/Http/Controllers/WebCrudController.php` |
| 6 | Notifikasi approve/reject | `app/Http/Controllers/WebPageController.php` |
| 7 | Dashboard summary | `app/Http/Controllers/WebPageController.php` → `dashboard()` |
