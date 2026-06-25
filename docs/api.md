# Dokumentasi REST API

Semua API endpoint berada di prefix `/api`. Autentikasi menggunakan **Bearer Token** (Laravel Sanctum), kecuali endpoint register, login, dan Google login.

---

## Autentikasi

### Register

```
POST /api/auth/register
```

**Body:**

```json
{
  "name": "User Baru",
  "email": "user@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

**Response (201):**

```json
{
  "message": "Registrasi berhasil",
  "user": { "id": 1, "name": "User Baru", "email": "user@example.com" },
  "token": "1|abc123..."
}
```

### Login

```
POST /api/auth/login
```

**Body:**

```json
{
  "email": "user@example.com",
  "password": "password123"
}
```

**Response (200):**

```json
{
  "message": "Login berhasil",
  "user": { ... },
  "token": "1|abc123..."
}
```

### Google Login (id_token)

```
POST /api/auth/google
```

**Body:**

```json
{
  "id_token": "eyJhbGci..."
}
```

Menerima id_token Google (dari frontend SPA). Jika email sudah terdaftar, login; jika belum, registrasi otomatis.

### Logout

```
POST /api/logout
```

Headers: `Authorization: Bearer {token}`

Revokes token yang dipakai.

### Profile

```
GET /api/me
```

---

## Transaksi

Semua endpoint di bawah memerlukan autentikasi.

| Method | Endpoint | Keterangan |
|---|---|---|
| GET | `/api/transactions` | List transaksi (dengan filter) |
| POST | `/api/transactions` | Buat transaksi baru |
| GET | `/api/transactions/{id}` | Detail transaksi |
| PUT/PATCH | `/api/transactions/{id}` | Update transaksi |
| DELETE | `/api/transactions/{id}` | Hapus transaksi |

**Filter pada list:**

- `?type=income|expense`
- `?search=keyword` (cari di deskripsi)
- `?category_id=1`
- `?account_id=1`
- `?from=2024-01-01&to=2024-01-31`

**Body POST/PUT:**

```json
{
  "category_id": 1,
  "account_id": 2,
  "type": "expense",
  "amount": 150000,
  "description": "Makan siang",
  "transaction_date": "2024-07-01"
}
```

---

## Kategori

| Method | Endpoint | Keterangan |
|---|---|---|
| GET | `/api/categories` | List kategori |
| POST | `/api/categories` | Buat kategori |
| GET | `/api/categories/{id}` | Detail |
| PUT | `/api/categories/{id}` | Update |
| DELETE | `/api/categories/{id}` | Hapus |

**Body POST/PUT:**

```json
{
  "name": "Makanan",
  "type": "expense",
  "icon": "🍔",
  "color": "#ff6600"
}
```

---

## Akun (Dompet)

| Method | Endpoint | Keterangan |
|---|---|---|
| GET | `/api/accounts` | List akun |
| POST | `/api/accounts` | Buat akun |
| GET | `/api/accounts/{id}` | Detail |
| PUT | `/api/accounts/{id}` | Update |
| DELETE | `/api/accounts/{id}` | Hapus |

**Body POST/PUT:**

```json
{
  "name": "Rekening Utama",
  "provider": "BCA",
  "type": "bank",
  "account_number": "1234567890",
  "balance": 1000000,
  "logo": "bca",
  "email_scopes": ["info@bca.co.id"]
}
```

Tipe (`type`): `cash`, `ewallet`, `bank`, `credit_card`.

Kolom `email_scopes` adalah array alamat email pengirim yang akan difetch oleh GmailService untuk akun ini.

---

## Anggaran (Budget)

| Method | Endpoint | Keterangan |
|---|---|---|
| GET | `/api/budgets` | List budget |
| POST | `/api/budgets` | Buat |
| GET | `/api/budgets/{id}` | Detail |
| PUT | `/api/budgets/{id}` | Update |
| DELETE | `/api/budgets/{id}` | Hapus |

**Body:**

```json
{
  "category_id": 1,
  "amount": 1000000,
  "month": 7,
  "year": 2024
}
```

---

## Tabungan (Saving Goal)

| Method | Endpoint | Keterangan |
|---|---|---|
| GET | `/api/saving-goals` | List |
| POST | `/api/saving-goals` | Buat |
| GET | `/api/saving-goals/{id}` | Detail |
| PUT | `/api/saving-goals/{id}` | Update |
| DELETE | `/api/saving-goals/{id}` | Hapus |

**Body:**

```json
{
  "name": "Liburan",
  "target_amount": 10000000,
  "current_amount": 5000000,
  "deadline": "2024-12-31",
  "icon": "✈️"
}
```

---

## Dashboard

```
GET /api/dashboard
```

Mengembalikan ringkasan: total pemasukan, pengeluaran, saldo, transaksi terbaru, budget alerts.

---

## Laporan

| Method | Endpoint | Keterangan |
|---|---|---|
| GET | `/api/reports/monthly?month=7&year=2024` | Ringkasan bulanan |
| GET | `/api/reports/categories?type=expense&month=7&year=2024` | Breakdown per kategori |
| GET | `/api/reports/trend?year=2024` | Tren bulanan |
| GET | `/api/reports/export?month=7&year=2024` | Export CSV |

---

## Notifikasi (Pending Transactions)

| Method | Endpoint | Keterangan |
|---|---|---|
| GET | `/api/pending-notifications` | List notifikasi pending |
| POST | `/api/pending-notifications` | Buat manual |
| GET | `/api/pending-notifications/count` | Jumlah notifikasi pending |
| PATCH | `/api/pending-notifications/{id}/approve` | Setujui → buat transaksi |
| DELETE | `/api/pending-notifications/{id}/reject` | Tolak |

---

## OAuth & Settings

| Method | Endpoint | Keterangan |
|---|---|---|
| GET | `/api/oauth/status` | Status koneksi Google |
| DELETE | `/api/oauth/google` | Putuskan koneksi Google |
| GET | `/api/settings` | Lihat setting |
| PUT | `/api/settings` | Update setting |
| PUT | `/api/settings/password` | Ubah password |

---

## Alert

```
GET /api/alerts/daily
```

Mengembalikan alert harian (transaksi mencurigakan, dll).

---

## Upload File

```
POST /api/upload
```

**Body:** `file` (image, max 2MB). Menyimpan ke storage, mengembalikan URL publik.
