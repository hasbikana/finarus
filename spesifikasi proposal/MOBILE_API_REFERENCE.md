# Referensi API & Database untuk Aplikasi Mobile (Flutter)

Dokumen ini ditujukan sebagai panduan atau *blueprint* untuk membangun aplikasi mobile (misal dengan Flutter) yang berkomunikasi dengan *backend* Finarus.

## 1. Alur Komunikasi (Flow)

### Alur Autentikasi (Sanctum)
1. **Login:** Aplikasi mobile mengirim `email` dan `password` (JSON) ke endpoint `POST /api/auth/login`.
2. **Token:** Jika berhasil, server akan merespons dengan objek `user` dan sebuah `token` bertipe *Bearer*.
3. **Storage:** Simpan token tersebut secara aman di mobile (contoh: menggunakan `flutter_secure_storage`).
4. **Authorisasi:** Untuk setiap *request* data (seperti mengambil transaksi atau menambah kategori), mobile wajib menyematkan token tersebut pada Header request:
   ```http
   Authorization: Bearer <TOKEN_ANDA>
   Accept: application/json
   ```
5. **Logout:** Memanggil `POST /api/auth/logout` akan menghapus token dari server. Jangan lupa hapus token di lokal device.

### Alur Sinkronisasi Data (CRUD)
- Data disajikan dalam bentuk JSON terstruktur. 
- Saat menekan tombol simpan di form aplikasi mobile, buat request `POST` atau `PUT` dengan *body* JSON sesuai struktur tabel di bawah.
- Jika ada *upload* gambar (seperti logo dompet atau bukti transaksi), gunakan endpoint spesifik `/api/upload` dengan format `multipart/form-data`, lalu gunakan path URL yang dikembalikan ke dalam request JSON utama.

---

## 2. Struktur Database (Data Models)

Berikut adalah mapping atribut (*fillable fields*) untuk memudahkan pembuatan Model Class / Data Class di Flutter (misal menggunakan JSON Serializable atau Freezed):

| Entitas / Tabel | Atribut Utama (Fields) | Keterangan |
| --- | --- | --- |
| **User** | `id`, `name`, `email`, `password_set_at` | Profil dasar pengguna. |
| **UserSetting** | `user_id`, `email_notifications`, `budget_alerts`, `theme`, `email_fetch_enabled` | Pengaturan preferensi user. |
| **Account** *(Dompet)* | `id`, `name`, `provider`, `type`, `account_number`, `balance`, `logo` | Sumber dana (cash, bank, ewallet). |
| **Category** | `id`, `name`, `type` (income/expense), `icon`, `color` | Klasifikasi pemasukan/pengeluaran. |
| **Transaction** | `id`, `category_id`, `account_id`, `saving_goal_id`, `type`, `amount`, `description`, `transaction_date`, `is_pending`, `pending_source` | Riwayat mutasi/arus kas. |
| **Budget** | `id`, `category_id`, `amount`, `month`, `year` | Target batasan pengeluaran bulanan. |
| **SavingGoal** | `id`, `name`, `target_amount`, `current_amount`, `deadline`, `icon`, `image` | Target menabung dengan batas waktu. |
| **PendingNotification** | `id`, `sender_email`, `merchant`, `amount`, `notification_date`, `description`, `status` (pending/confirmed/rejected), `source` | Hasil parser email sebelum di-approve. |
| **UserOAuthToken** | `provider`, `access_token`, `refresh_token`, `expires_at`, `email` | Token otorisasi Google API. |

---

## 3. Daftar Endpoint API

Berikut adalah daftar endpoint lengkap dengan awalan `/api` (Semua kecuali `/auth/login` & `/auth/register` membutuhkan Bearer Token).

### A. Autentikasi & Profil
- `POST /api/auth/login` - Masuk menggunakan email & password.
- `POST /api/auth/register` - Pendaftaran akun baru.
- `POST /api/auth/google` - Login otomatis dari aplikasi mobile menggunakan Firebase/Google Auth (`id_token`).
- `POST /api/auth/logout` - Menghapus sesi token aktif.
- `GET /api/auth/me` - Mengambil detail profil pengguna yang sedang login.

### B. Dasbor & Ringkasan
- `GET /api/dashboard` - Mengambil rangkuman saldo, grafik bulan berjalan, dan peringatan *budget* (Sangat berguna untuk halaman utama).

### C. Resource Inti (CRUD Lengkap)
Berlaku format standar RESTful untuk: `GET` (List), `POST` (Create), `GET /{id}` (Detail), `PUT /{id}` (Update), `DELETE /{id}` (Hapus).
- **Kategori:** `/api/categories`
- **Transaksi:** `/api/transactions`
- **Anggaran (Budget):** `/api/budgets`
- **Target Tabungan:** `/api/saving-goals`
- **Akun (Dompet/Bank):** `/api/accounts`

### D. Pelaporan & Analitik (Report)
- `GET /api/reports/monthly` - Ringkasan total uang masuk/keluar bulan tertentu (Query params: `month`, `year`).
- `GET /api/reports/categories` - Proporsi (pie chart) pengeluaran per kategori (Query params: `type`, `month`, `year`).
- `GET /api/reports/trend` - Tren data 12 bulan terakhir dalam setahun untuk grafik garis (Query params: `year`).

### E. Pengaturan & Utilitas
- `GET /api/settings` - Mengambil data preferensi/pengaturan aplikasi.
- `PUT /api/settings` - Menyimpan pembaruan pengaturan.
- `PUT /api/settings/password` - Mengganti kata sandi.
- `POST /api/upload` - Mengunggah file (gambar/icon) tunggal.

### F. Manajemen Notifikasi Sinkronisasi
- `GET /api/pending-notifications` - Mengambil daftar transaksi tertunda hasil parser email.
- `GET /api/pending-notifications/count` - Mengambil jumlah notifikasi yang belum dibaca (untuk *badge* icon bel).
- `PATCH /api/pending-notifications/{id}/approve` - Konfirmasi mengubah notifikasi tertunda menjadi transaksi sah.
- `DELETE /api/pending-notifications/{id}/reject` - Menolak/menghapus notifikasi yang salah terdeteksi.
