# Script Presentasi: Kelompok Auth & OAuth (Breeze, Socialite, Gmail Parser)

Halo semua, perkenalkan saya akan mendemonstrasikan bagaimana sistem autentikasi dan integrasi Google berjalan di aplikasi Finarus ini. Kita akan membedah tiga fitur utama: Autentikasi standar bawaan Laravel Breeze, Integrasi Google OAuth dengan Socialite, dan fitur Gmail Parser untuk menarik data transaksi.

---

## 1. Standar Autentikasi (Breeze Auth)

**Tujuan Fitur:**
Memberikan akses masuk dan daftar bagi pengguna menggunakan kombinasi email dan password secara aman.

**Alur:**
Saat pengguna mengisi form registrasi atau login di antarmuka web, *request* dikirim melalui rute `POST /register` atau `POST /login`. Rute ini ditangani oleh `RegisteredUserController` atau `AuthenticatedSessionController`. Controller kemudian memvalidasi input, mencocokkan data menggunakan model `User` (termasuk *hashing* password), dan jika berhasil, akan membuat *session* aktif lalu mengarahkan pengguna ke halaman Dashboard.

**Baris Kode Kunci (Highlight di Video):**
- Pada `RegisteredUserController::store()`, highlight bagian di mana password di-hash menggunakan `Hash::make()` sebelum disimpan ke database.
- Pada `AuthenticatedSessionController::store()`, highlight metode `authenticate()` dari request form yang menangani validasi *credentials* pengguna.

**Penjelasan Keamanan (Middleware/Policy):**
Fitur ini dilindungi oleh middleware `guest` untuk halaman login/register dan `auth` untuk proses logout.
*Analogi Sederhana:* Middleware `guest` itu seperti satpam yang berjaga di pintu masuk ruang pendaftaran; kalau Anda sudah punya kartu anggota (sudah login), satpam akan melarang Anda mendaftar lagi dan langsung menyuruh masuk ke dalam. Sebaliknya, middleware `auth` memastikan hanya orang yang ada di dalam ruangan yang bisa menekan tombol keluar (logout).

**Kemungkinan Pertanyaan Dosen:**
> *Pertanyaan:* Bagaimana sistem memastikan password pengguna aman kalau sewaktu-waktu database bocor?
> *Draft Jawaban:* Di Laravel, password tidak pernah disimpan dalam bentuk *plaintext*. Kami menggunakan algoritma *hashing* (secara default Bcrypt) melalui fasad `Hash`. Jadi meskipun database bocor, penyerang hanya akan melihat string acak yang tidak bisa dikembalikan menjadi password asli tanpa proses komputasi yang sangat mustahil.

---

## 2. Google OAuth Login & Connect (Socialite)

**Tujuan Fitur:**
Memudahkan pengguna untuk login atau menyambungkan akun mereka hanya dengan satu klik menggunakan akun Google tanpa perlu menghafal password.

**Alur:**
Saat saya mengklik tombol "Login with Google", rute `GET /oauth/google/login` akan memicu `OAuthController::redirectToGoogle()`. Controller ini meminta Laravel Socialite mengarahkan pengguna ke halaman *consent* Google. Setelah saya setuju, Google melempar kembali ke rute `GET /oauth/google/callback`. Di sini, `OAuthController::handleGoogleCallback()` menangkap profil Google saya. Jika email saya belum terdaftar, sistem akan membuatkan akun otomatis di model `User`. Jika saya menautkan akun (Connect), sistem akan menyimpan token ke model `UserOAuthToken` dan mengaktifkan fitur pembacaan email di `UserSetting`.

**Baris Kode Kunci (Highlight di Video):**
- Pada `OAuthController.php` baris **26-32** di metode `redirectToGoogle()`, highlight penambahan *scopes* `https://www.googleapis.com/auth/gmail.readonly`. Ini penting untuk menunjukkan bahwa kita meminta izin spesifik hanya untuk *membaca* email jika user ingin menautkan Gmail.
- Pada `OAuthController.php` baris **68-76** di metode `handleConnectFlow()`, highlight `UserOAuthToken::updateOrCreate(...)` di mana kita menyimpan `access_token` dan `refresh_token`.

**Penjelasan Keamanan (Middleware/Policy):**
Pada rute penautan (`/oauth/google/connect`), kita menggunakan middleware `auth`.
*Analogi Sederhana:* Socialite ini seperti kita menggunakan KTP elektronik. Alih-alih mengisi formulir panjang secara manual, petugas (aplikasi kita) langsung scan e-KTP kita ke catatan sipil (Google) untuk memverifikasi identitas. Middleware `auth` di sini memastikan bahwa hanya "orang yang sudah masuk ke dalam gedung" yang boleh menempelkan kartu akses tambahannya.

**Kemungkinan Pertanyaan Dosen:**
> *Pertanyaan:* Apa bedanya proses `access_token` dan `refresh_token` yang kalian simpan dari Google?
> *Draft Jawaban:* `access_token` adalah kunci masuk sementara yang masa berlakunya singkat (biasanya 1 jam) untuk memanggil API Google. Sedangkan `refresh_token` adalah "kunci master" jangka panjang yang kami gunakan untuk meminta `access_token` baru tanpa perlu meminta pengguna login ke Google lagi.

---

## 3. Gmail Parser (Fetch Emails)

**Tujuan Fitur:**
Secara otomatis menarik riwayat transaksi dari email mutasi bank (seperti BCA/Mandiri) agar pengguna tidak perlu mencatat pengeluarannya secara manual.

**Alur:**
Saya bisa memicu penarikan ini dengan menekan tombol "Sinkronkan" di pengaturan. Aksi ini menembak rute `POST /pengaturan/fetch-emails`. Di dalam `WebPageController::fetchEmails()`, sistem mengecek apakah saya punya token Google yang aktif. Jika ada, controller akan memanggil *job* di *background* bernama `FetchBankEmails::dispatchSync()`. Job ini menggunakan service untuk membaca kotak masuk Gmail saya, mengekstrak nominal dan deskripsi transaksi, lalu menyimpannya sebagai notifikasi transaksi tertunda di model `PendingNotification`.

**Baris Kode Kunci (Highlight di Video):**
- Pada `WebPageController.php` baris **252-258** di metode `fetchEmails()`, highlight pengecekan validasi token `UserOAuthToken` dan pemanggilan `FetchBankEmails::dispatchSync(Auth::id())`. Tunjukkan bahwa proses ini di-*dispatch* ke antrean atau berjalan tersinkronisasi.

**Penjelasan Keamanan (Middleware/Policy):**
Fitur ini dilindungi dengan middleware `auth` dan *policy* bahwa pengguna hanya bisa men-trigger atau melihat *pending notification* miliknya sendiri.
*Analogi Sederhana:* Kami bertindak seperti asisten pribadi. Kami hanya membaca surat yang ditujukan khusus ke laci meja Anda (dengan izin baca saja) dan meletakkan rangkuman catatan pengeluaran di buku agenda pribadi Anda. Tidak ada pengguna lain yang bisa melihat atau memicu asisten ini untuk laci Anda.

**Kemungkinan Pertanyaan Dosen:**
> *Pertanyaan:* Mengapa kalian tidak langsung memasukkan hasil bacaan email bank ke tabel transaksi utama, malah dimasukkan ke tabel `pending_notification` (notifikasi tertunda) dulu?
> *Draft Jawaban:* Ini adalah langkah preventif. Parser mungkin salah mengenali format email baru atau membedakan pengeluaran dengan transfer pribadi. Dengan menyimpannya sebagai *pending notification*, kami memaksa pengguna melakukan "Approval" (validasi manusia). Pengguna dapat memilih kategori secara manual sebelum data benar-benar masuk ke laporan keuangan utama, sehingga data tetap 100% akurat.

---

## Sekilas: Main Business Logic (Web Interface)
*Bagian ini dapat saya sebutkan secara singkat (overview) di video untuk menunjukkan kelengkapan sistem.*

- **Dashboard & Laporan:** Menampilkan ringkasan kondisi keuangan pengguna (saldo, pengeluaran, pemasukan) secara realtime dengan grafik interaktif. Semua data diagregasi dari riwayat transaksi yang valid.
- **Manajemen Transaksi & Kategori:** Fitur pencatatan utama di mana pengguna merekam arus kas harian, serta mengelompokkannya ke dalam berbagai kategori yang bisa dikustomisasi.
- **Anggaran & Tabungan (Budget & Goals):** Fitur perencanaan finansial yang memungkinkan pengguna mengunci batas pengeluaran bulanan dan menyisihkan dana untuk target tabungan di masa depan.
- **Dompet Digital (Accounts):** Fitur untuk memetakan sumber dana asli pengguna (uang tunai, rekening bank, e-wallet) agar pencatatan saldo lebih terorganisir per akun.

---

## Sekilas: Data / API Layer
*Bagian ini dapat saya sebutkan sekilas untuk menunjukkan bahwa sistem sudah siap di-scale ke aplikasi mobile.*

- **RESTful API Resources:** Menyediakan kumpulan *endpoint* JSON untuk semua operasi CRUD (Transaksi, Kategori, Anggaran) yang sepenuhnya diamankan menggunakan token dari Laravel Sanctum.
- **API Analytics & Reporting:** Endpoint khusus yang bertugas menghitung dan merangkum tren pengeluaran bulanan, sehingga sisi *client* (seperti aplikasi mobile) hanya tinggal merender grafiknya tanpa komputasi berat.
