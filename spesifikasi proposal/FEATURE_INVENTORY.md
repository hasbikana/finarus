# Feature Inventory: Finarus

Dokumen ini berisi inventaris seluruh fitur yang ada dalam aplikasi Finarus, dikelompokkan berdasarkan kategorinya.

## 1. Core Infrastructure / Auth (Breeze, Socialite/OAuth)
Fitur-fitur yang berkaitan dengan autentikasi pengguna, manajemen profil, dan integrasi dengan pihak ketiga (Google OAuth).

| Nama Fitur | Route/Endpoint | Controller | Method | Model yang Dipakai | Middleware/Policy | Lokasi File |
| --- | --- | --- | --- | --- | --- | --- |
| **Web Register** | `GET /register`, `POST /register` | `RegisteredUserController` | `create`, `store` | `User` | `guest` | `app/Http/Controllers/Auth/RegisteredUserController.php` |
| **Web Login** | `GET /login`, `POST /login` | `AuthenticatedSessionController` | `create`, `store` | `User` | `guest` | `app/Http/Controllers/Auth/AuthenticatedSessionController.php` |
| **Web Logout** | `POST /logout` | `AuthenticatedSessionController` | `destroy` | - | `auth` | `app/Http/Controllers/Auth/AuthenticatedSessionController.php` |
| **Forgot Password** | `GET /forgot-password`, `POST /forgot-password` | `PasswordResetLinkController` | `create`, `store` | `User` | `guest` | `app/Http/Controllers/Auth/PasswordResetLinkController.php` |
| **Reset Password** | `GET /reset-password/{token}`, `POST /reset-password` | `NewPasswordController` | `create`, `store` | `User` | `guest` | `app/Http/Controllers/Auth/NewPasswordController.php` |
| **Email Verification** | `GET /verify-email`, `GET /verify-email/{id}/{hash}`, `POST /email/verification-notification` | `EmailVerificationPromptController`, `VerifyEmailController`, `EmailVerificationNotificationController` | `__invoke`, `store` | `User` | `auth`, `signed`, `throttle:6,1` | `app/Http/Controllers/Auth/*` |
| **Confirm Password** | `GET /confirm-password`, `POST /confirm-password` | `ConfirmablePasswordController` | `show`, `store` | `User` | `auth` | `app/Http/Controllers/Auth/ConfirmablePasswordController.php` |
| **Update Password** | `PUT /password` | `PasswordController` | `update` | `User` | `auth` | `app/Http/Controllers/Auth/PasswordController.php` |
| **Web Profile Management** | `GET /profile`, `PATCH /profile`, `DELETE /profile` | `ProfileController` | `edit`, `update`, `destroy` | `User` | `auth` | `app/Http/Controllers/ProfileController.php` |
| **Google Login (Web)** | `GET /oauth/google/login` | `OAuthController` (Web/API) | `redirectToGoogle` | `User`, `UserOAuthToken` | - | `app/Http/Controllers/Api/OAuthController.php` |
| **Google Callback (Web)** | `GET /oauth/google/callback` | `OAuthController` (Web/API) | `handleGoogleCallback` | `User`, `UserOAuthToken` | - | `app/Http/Controllers/Api/OAuthController.php` |
| **Connect Google (Web)** | `GET /oauth/google/connect` | `OAuthController` (Web/API) | `redirectToGoogle` | `UserOAuthToken` | `auth` | `app/Http/Controllers/Api/OAuthController.php` |
| **Disconnect Google (Web)** | `POST /oauth/google/disconnect` | `OAuthController` (Web/API) | `disconnect` | `UserOAuthToken` | `auth` | `app/Http/Controllers/Api/OAuthController.php` |
| **API Register** | `POST /api/auth/register` | `AuthController` (API) | `register` | `User` | - | `app/Http/Controllers/Api/AuthController.php` |
| **API Login** | `POST /api/auth/login` | `AuthController` (API) | `login` | `User` | - | `app/Http/Controllers/Api/AuthController.php` |
| **API Google Login** | `POST /api/auth/google` | `OAuthController` (API) | `googleLogin` | `User`, `UserOAuthToken` | - | `app/Http/Controllers/Api/OAuthController.php` |
| **API Logout** | `POST /api/auth/logout` | `AuthController` (API) | `logout` | - | `auth:sanctum` | `app/Http/Controllers/Api/AuthController.php` |
| **API Get Me (Profile)**| `GET /api/auth/me` | `AuthController` (API) | `me` | `User` | `auth:sanctum` | `app/Http/Controllers/Api/AuthController.php` |
| **API OAuth Status** | `GET /api/oauth/status` | `OAuthController` (API) | `status` | `UserOAuthToken` | `auth:sanctum` | `app/Http/Controllers/Api/OAuthController.php` |
| **API OAuth Disconnect**| `DELETE /api/oauth/google` | `OAuthController` (API) | `disconnect` | `UserOAuthToken` | `auth:sanctum` | `app/Http/Controllers/Api/OAuthController.php` |

---

## 2. Main Business Logic (Web Interface)
Antarmuka web utama tempat logika bisnis aplikasi berjalan, melayani tampilan (pages) dan aksi CRUD standar melalui antarmuka web.

| Nama Fitur | Route/Endpoint | Controller | Method | Model yang Dipakai | Middleware/Policy | Lokasi File |
| --- | --- | --- | --- | --- | --- | --- |
| **Root/Redirect** | `GET /` | (Closure) | - | - | - | `routes/web.php` |
| **Dashboard Web** | `GET /dashboard` | `WebPageController` | `dashboard` | `Transaction`, `Budget`, `SavingGoal`, `Account` | `auth` | `app/Http/Controllers/WebPageController.php` |
| **Halaman Transaksi** | `GET /transaksi` | `WebPageController` | `transaksi` | `Transaction`, `Category`, `Account` | `auth` | `app/Http/Controllers/WebPageController.php` |
| **CRUD Transaksi** | `POST /transaksi`, `PUT /transaksi/{transaction}`, `DELETE /transaksi/{transaction}` | `WebCrudController` | `storeTransaksi`, `updateTransaksi`, `destroyTransaksi` | `Transaction`, `Account` | `auth` | `app/Http/Controllers/WebCrudController.php` |
| **Halaman Kategori** | `GET /kategori` | `WebPageController` | `kategori` | `Category` | `auth` | `app/Http/Controllers/WebPageController.php` |
| **CRUD Kategori** | `POST /kategori`, `PUT /kategori/{category}`, `DELETE /kategori/{category}` | `WebCrudController` | `storeKategori`, `updateKategori`, `destroyKategori`| `Category` | `auth` | `app/Http/Controllers/WebCrudController.php` |
| **Halaman Anggaran** | `GET /anggaran` | `WebPageController` | `anggaran` | `Budget`, `Category` | `auth` | `app/Http/Controllers/WebPageController.php` |
| **CRUD Anggaran** | `POST /anggaran`, `PUT /anggaran/{budget}`, `DELETE /anggaran/{budget}` | `WebCrudController` | `storeAnggaran`, `updateAnggaran`, `destroyAnggaran`| `Budget` | `auth` | `app/Http/Controllers/WebCrudController.php` |
| **Halaman Tabungan** | `GET /tabungan` | `WebPageController` | `tabungan` | `SavingGoal` | `auth` | `app/Http/Controllers/WebPageController.php` |
| **CRUD Tabungan** | `POST /tabungan`, `PUT /tabungan/{savingGoal}`, `DELETE /tabungan/{savingGoal}` | `WebCrudController` | `storeTabungan`, `updateTabungan`, `destroyTabungan`| `SavingGoal` | `auth` | `app/Http/Controllers/WebCrudController.php` |
| **Tambah Dana Tabungan**| `POST /tabungan/{savingGoal}/add-fund` | `WebCrudController` | `addFund` | `SavingGoal` | `auth` | `app/Http/Controllers/WebCrudController.php` |
| **Halaman Dompet (Akun)**| `GET /dompet-digital` | `WebPageController` | `dompetDigital` | `Account` | `auth` | `app/Http/Controllers/WebPageController.php` |
| **CRUD Dompet (Akun)** | `POST /dompet`, `PUT /dompet/{account}`, `DELETE /dompet/{account}` | `WebCrudController` | `storeDompet`, `updateDompet`, `destroyDompet` | `Account` | `auth` | `app/Http/Controllers/WebCrudController.php` |
| **Halaman Laporan** | `GET /laporan` | `WebPageController` | `laporan` | `Transaction`, `Category` | `auth` | `app/Http/Controllers/WebPageController.php` |
| **Data Laporan (Web)** | `GET /laporan/data/monthly`, `GET /laporan/data/categories`, `GET /laporan/data/trend` | `WebPageController` | `reportMonthly`, `reportCategories`, `reportTrend` | `Transaction`, `Category` | `auth` | `app/Http/Controllers/WebPageController.php` |
| **Export Laporan (Web)** | `GET /laporan/export` | `ReportController` | `export` | `Transaction` | `auth` | `app/Http/Controllers/Api/ReportController.php` |
| **Halaman Pengaturan** | `GET /pengaturan` | `WebPageController` | `pengaturan` | `UserSetting` | `auth` | `app/Http/Controllers/WebPageController.php` |
| **Update Pengaturan** | `PUT /pengaturan/settings` | `WebPageController` | `updateSettings` | `UserSetting` | `auth` | `app/Http/Controllers/WebPageController.php` |
| **Fetch Emails** | `POST /pengaturan/fetch-emails` | `WebPageController` | `fetchEmails` | `UserOAuthToken`, `PendingNotification` | `auth` | `app/Http/Controllers/WebPageController.php` |
| **Halaman Bantuan** | `GET /bantuan` | `WebPageController` | `bantuan` | - | `auth` | `app/Http/Controllers/WebPageController.php` |
| **Halaman Notifikasi** | `GET /notifikasi` | `WebPageController` | `notifikasi` | `PendingNotification` | `auth` | `app/Http/Controllers/WebPageController.php` |
| **Approval Notifikasi** | `PATCH /notifikasi/{pending_notification}/approve`, `DELETE /notifikasi/{pending_notification}/reject` | `WebPageController` | `approveNotification`, `rejectNotification` | `PendingNotification`, `Transaction` | `auth` | `app/Http/Controllers/WebPageController.php` |
| **Upload File (Web)** | `POST /upload` | `WebCrudController` | `uploadFile` | - | `auth` | `app/Http/Controllers/WebCrudController.php` |

---

## 3. Data / API Layer
Endpoint API yang diakses oleh mobile client, SPA, atau integrasi pihak ketiga untuk mengambil atau mengubah data.

| Nama Fitur | Route/Endpoint | Controller | Method | Model yang Dipakai | Middleware/Policy | Lokasi File |
| --- | --- | --- | --- | --- | --- | --- |
| **API Dashboard** | `GET /api/dashboard` | `DashboardController` (API) | `index` | `Transaction`, `Budget`, `Account`, dll | `auth:sanctum` | `app/Http/Controllers/Api/DashboardController.php` |
| **API Categories** | `GET/POST/PUT/DELETE /api/categories` | `CategoryController` (API) | `index`, `store`, `show`, `update`, `destroy` | `Category` | `auth:sanctum` | `app/Http/Controllers/Api/CategoryController.php` |
| **API Transactions** | `GET/POST/PUT/DELETE /api/transactions` | `TransactionController` (API)| `index`, `store`, `show`, `update`, `destroy` | `Transaction`, `Account` | `auth:sanctum` | `app/Http/Controllers/Api/TransactionController.php` |
| **API Budgets** | `GET/POST/PUT/DELETE /api/budgets` | `BudgetController` (API) | `index`, `store`, `show`, `update`, `destroy` | `Budget` | `auth:sanctum` | `app/Http/Controllers/Api/BudgetController.php` |
| **API Saving Goals** | `GET/POST/PUT/DELETE /api/saving-goals` | `SavingGoalController` (API) | `index`, `store`, `show`, `update`, `destroy` | `SavingGoal` | `auth:sanctum` | `app/Http/Controllers/Api/SavingGoalController.php` |
| **API Accounts (Dompet)**| `GET/POST/PUT/DELETE /api/accounts` | `AccountController` (API) | `index`, `store`, `show`, `update`, `destroy` | `Account` | `auth:sanctum` | `app/Http/Controllers/Api/AccountController.php` |
| **API Report Monthly** | `GET /api/reports/monthly` | `ReportController` (API) | `monthly` | `Transaction` | `auth:sanctum` | `app/Http/Controllers/Api/ReportController.php` |
| **API Report Categories**| `GET /api/reports/categories` | `ReportController` (API) | `categories` | `Transaction`, `Category` | `auth:sanctum` | `app/Http/Controllers/Api/ReportController.php` |
| **API Report Trend** | `GET /api/reports/trend` | `ReportController` (API) | `trend` | `Transaction` | `auth:sanctum` | `app/Http/Controllers/Api/ReportController.php` |
| **API Report Export** | `GET /api/reports/export` | `ReportController` (API) | `export` | `Transaction` | `auth:sanctum` | `app/Http/Controllers/Api/ReportController.php` |
| **API Get Settings** | `GET /api/settings` | `SettingController` (API) | `show` | `UserSetting` | `auth:sanctum` | `app/Http/Controllers/Api/SettingController.php` |
| **API Update Settings** | `PUT /api/settings` | `SettingController` (API) | `update` | `UserSetting` | `auth:sanctum` | `app/Http/Controllers/Api/SettingController.php` |
| **API Change Password** | `PUT /api/settings/password` | `SettingController` (API) | `changePassword` | `User` | `auth:sanctum` | `app/Http/Controllers/Api/SettingController.php` |
| **API Upload** | `POST /api/upload` | `UploadController` (API) | `upload` | - | `auth:sanctum` | `app/Http/Controllers/Api/UploadController.php` |
| **API Pending Notif.** | `GET/POST /api/pending-notifications` | `PendingNotificationController`| `index`, `store` | `PendingNotification` | `auth:sanctum` | `app/Http/Controllers/Api/PendingNotificationController.php` |
| **API Notif. Action** | `PATCH .../approve`, `DELETE .../reject`| `PendingNotificationController`| `approve`, `reject` | `PendingNotification`, `Transaction` | `auth:sanctum` | `app/Http/Controllers/Api/PendingNotificationController.php` |
| **API Notif. Count** | `GET /api/pending-notifications/count` | `PendingNotificationController`| `count` | `PendingNotification` | `auth:sanctum` | `app/Http/Controllers/Api/PendingNotificationController.php` |
| **API Daily Alerts** | `GET /api/alerts/daily` | `AlertController` (API) | `daily` | `Budget`, `SavingGoal`, dsb | `auth:sanctum` | `app/Http/Controllers/Api/AlertController.php` |
