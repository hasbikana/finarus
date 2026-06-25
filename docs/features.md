# Penjelasan Fitur & Alur Logika

## 1. Autentikasi

### Login Manual

`routes/auth.php` → `AuthenticatedSessionController`

- User mengisi form login → POST `/login` → `LoginRequest::authenticate()` memanggil `Auth::attempt()`.
- Jika berhasil, session diregenerasi (`$request->session()->regenerate()`), redirect ke `/dashboard`.

### Register

`RegisteredUserController`:

- Validasi input (name, email, password, confirmation).
- `User::create()` lalu membuat akun `Cash / Dompet` default.
- `event(new Registered($user))` → listener default mengirim email verifikasi (tidak aktif karena model tidak implementasi `MustVerifyEmail`).
- `Auth::login($user)`, session regenerate, redirect dashboard.

### Login Google (Web – Socialite)

- Tombol di halaman login: `/oauth/google/login` → `OAuthController::redirectToGoogle()`.
- Socialite mengarahkan user ke Google consent, menyimpan `state` di session.
- Google redirect ke `/oauth/google/callback` → `handleGoogleCallback()`.
  - Error? Redirect ke `/login` dengan flash `error` (sekarang sudah ditampilkan di view).
  - Jika user sudah login (Auth::check) → `handleConnectFlow()` (mengaitkan Google ke akun yang ada).
  - Jika user baru → `handleLoginFlow()`:
    - Email sudah terdaftar? `Auth::login()` langsung.
    - Email belum terdaftar? Buat user baru + setting + akun cash default, lalu login.

### Login Google (API – id_token)

`POST /api/auth/google` → `OAuthController::googleLogin()`.

- Verifikasi `id_token` dengan `Google\Client::verifyIdToken()`.
- `User::firstOrCreate` berdasarkan email → buat setting & akun cash jika baru.
- Generate `plainTextToken` dengan Sanctum dan kembalikan ke client.

### Logout (Web)

`POST /logout` → `AuthenticatedSessionController::destroy()`:

- `Auth::logout()`, invalidate session, regenerate token, redirect `/`.

### Logout (API)

`POST /api/logout` → `AuthController::logout()` → `$request->user()->currentAccessToken()->delete()`.

---

## 2. Manajemen Akun Keuangan (Dompet Digital)

Halaman: `GET /dompet-digital` → `WebPageController::dompetDigital()`.

CRUD via `WebCrudController`:

| Aksi | Endpoint | Class |
|---|---|---|
| Tambah | POST `/dompet` | `storeDompet()` |
| Edit | PUT `/dompet/{account}` | `updateDompet()` |
| Hapus | DELETE `/dompet/{account}` | `destroyDompet()` |

- Data tervalidasi oleh `StoreAccountRequest` / `UpdateAccountRequest`.
- Setiap akun bisa memiliki `email_scopes` (array alamat email) yang menentukan email pengirim mana yang relevan untuk akun itu.
- Untuk tipe `cash`, `email_scopes` otomatis di-null kan di controller.

---

## 3. Transaksi

### Manual

CRUD via `WebCrudController` dan `TransactionService`.

- `StoreTransactionRequest`/`UpdateTransactionRequest` memvalidasi.
- Pemanggilan `$this->txnService->createTransaction($data)` → membuat record `transactions`.
- `is_pending` di-set `false` untuk transaksi manual.

### Auto dari Email (Fetch + Parse)

Proses:

1. **Artisan Command / Queue** → `FetchBankEmails` job.
2. Cek `user.settings.email_fetch_enabled` dan `UserOAuthToken` provider `google`.
3. Refresh token jika expired.
4. Kumpulkan `email_scopes` dari semua akun user (non-cash).
5. Panggil `GmailService::fetchNewEmails($token, $scopes)` → query Gmail API dengan filter `from:email1 from:email2 ...`.
6. Untuk setiap email, ambil isi → `GmailService::fetchMessageContent()`.
7. Parse dengan `EmailParserService::parseEmail($from, $subject, $body)` → loop `parsers`, panggil `canParse()` lalu `parse()`.
8. Hasil `ParsedTransaction` → `processParsedTransaction()`.
9. Cari akun:
   - Jika `$fromEmail` cocok dengan `email_scopes` akun (via `whereJsonContains`), gunakan akun itu.
   - Fallback: cari akun berdasarkan `provider` (misal 'bca').
10. Buat `Transaction` dengan `is_pending = true`, `pending_source = 'email'`.
11. Transaksi pending muncul di halaman Notifikasi → user bisa approve (pilih kategori & akun) atau reject.

### Parser yang tersedia

| Provider | Parser | File |
|---|---|---|
| BCA | `BcaParser` | `app/Parsers/BcaParser.php` |
| Mandiri | `MandiriParser` | `app/Parsers/MandiriParser.php` |
| BNI | `BniParser` | `app/Parsers/BniParser.php` |
| BRI | `BriParser` | `app/Parsers/BriParser.php` |
| GoPay | `GopayParser` | `app/Parsers/GopayParser.php` |
| OVO | `OvoParser` | `app/Parsers/OvoParser.php` |
| DANA | `DanaParser` | `app/Parsers/DanaParser.php` |

Semua parser extends `BaseParser` yang menyediakan helper: `extractAmount()`, `extractDescription()`, `extractDate()`, `isDebit()`, `isTopup()`.

---

## 4. Kategori & Anggaran

### Kategori

CRUD di halaman `/kategori`. Setiap kategori milik user dan memiliki tipe (`income`/`expense`/`both`), icon (emoji), dan warna (hex).

### Anggaran (Budget)

CRUD di halaman `/anggaran`. Budget per kategori per bulan/tahun. Dashboard menampilkan progress & alert jika budget > 80% atau over budget.

---

## 5. Tabungan (Saving Goal)

Halaman `/tabungan`. Setiap goal punya target nominal, progress, deadline, icon, dan opsi gambar. User bisa menambahkan dana ke goal melalui form `addFund` yang membuat transaksi `expense` dan menambah `current_amount`.

---

## 6. Notifikasi

Halaman `/notifikasi` menampilkan pending transactions dari hasil parsing email atau OCR. User bisa:
- **Approve**: memilih kategori & akun → transaksi final dibuat.
- **Reject**: status jadi rejected.

---

## 7. Laporan

Halaman `/laporan` terdiri dari:

| Fitur | Endpoint | Keterangan |
|---|---|---|
| Ringkasan Bulanan | AJAX `/laporan/data/monthly` | Total pemasukan/pengeluaran |
| Breakdown Kategori | AJAX `/laporan/data/categories` | Per kategori |
| Tren | AJAX `/laporan/data/trend` | Tren per bulan dalam setahun |
| Export CSV | GET `/laporan/export` | Download CSV laporan |

---

## 8. Email Scope (Fitur Per Akun)

Setiap akun `type != cash` memiliki kolom `email_scopes` (JSON array). Saat `FetchBankEmails` dijalankan:

1. Mengumpulkan semua `email_scopes` dari akun user.
2. Hanya mengirim daftar email scope itu ke GmailService.
3. Jika user belum mengisi scope, fallback menggunakan daftar global hardcoded di `GmailService::$senderEmails`.

Di `EmailParserService::processParsedTransaction()`, saat menentukan akun:

1. Coba cari akun yang `email_scopes` mengandung `$fromEmail` (pakai `whereJsonContains`).
2. Jika tidak ada fallback ke pencarian berdasarkan `provider`.

---

## 9. Autentikasi Google Gmail (OAuth)

### Connect

- User sudah login (web) → `/oauth/google/connect` → redirect ke Google dengan scope `gmail.readonly` + `access_type=offline`.
- Callback → `handleConnectFlow()` → simpan `access_token`, `refresh_token`, `expires_at` ke `user_oauth_tokens`. Aktifkan `email_fetch_enabled` di settings.

### Disconnect

`POST /oauth/google/disconnect` → hapus token + nonaktifkan fetch.

### Status

`GET /api/oauth/status` → cek apakah ada token valid.

---

## 10. Pengaturan & Profil

Halaman `/pengaturan` menampilkan:
- Informasi profile (edit/delete).
- Toggle email fetch (master switch).
- Koneksi Google (connect/disconnect).
- Tema (light/dark).

Halaman `/profile` untuk update nama, email, hapus akun.
