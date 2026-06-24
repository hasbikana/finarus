# Backend Fix: 403 Forbidden saat Approve/Reject Pending Notification

> Dokumen ini untuk AI agent backend. Issue: Flutter kirim request approve/reject pending notification dengan benar, tapi backend return `403 This action is unauthorized.`

---

## 1. Endpoint & Request dari Flutter

### Approve (Terima)
```http
PATCH /api/pending-notifications/{id}/approve
Authorization: Bearer <token>
Content-Type: application/json

{
  "category_id": 12,
  "account_id": 5,
  "description": "optional override"
}
```

### Reject (Tolak)
```http
DELETE /api/pending-notifications/{id}/reject
Authorization: Bearer <token>
```

### Log Error dari Flutter
```
[PendingService] PATCH /pending-notifications/4/approve
[PendingService] Approve response: 403 - {
    "message": "This action is unauthorized.",
    "exception": "Symfony\\Component\\HttpKernel\\Exception\\AccessDeniedHttpException"
}
```

Flutter sudah benar. **Token terkirim, endpoint benar, body valid.** Issue ada di backend authorization.

---

## 2. Suspected Root Cause

Ada 2 kemungkinan utama:

### A. Policy Ability `approve` / `reject` Tidak Ada
Dari `docs/POLICY.md`, `PendingNotificationPolicy` hanya punya:
- `viewAny`, `view`, `create`, `update`, `delete`

Tidak ada method `approve()` atau `reject()`.

Kalau controller pakai:
```php
$this->authorize('approve', $pendingNotification);
// atau
$this->authorize('reject', $pendingNotification);
```

Laravel akan deny karena ability tidak ditemukan di policy → **403**.

### B. `user_id` Pending Notification Tidak Tersimpan / Salah
Kalau controller authorize pakai `update`/`delete` yang sudah ada, maka 403 berarti:
```php
$user->id !== $pendingNotification->user_id
```

Kemungkinan:
- Saat `POST /api/pending-notifications`, `user_id` tidak di-set ke `Auth::id()`.
- List endpoint tidak filter berdasarkan user, sehingga notifikasi user lain muncul di Flutter.

---

## 3. Recommended Fix

### Option 1: Gunakan Policy Ability yang Sudah Ada (Recommended)

Di `app/Http/Controllers/Api/PendingNotificationController.php`:

```php
public function approve(Request $request, PendingNotification $pendingNotification)
{
    // Approve = mengubah status pending → confirmed, pakai ability 'update'
    $this->authorize('update', $pendingNotification);

    $validated = $request->validate([
        'category_id' => ['required', Rule::exists('categories', 'id')->where('user_id', Auth::id())],
        'account_id' => ['required', Rule::exists('accounts', 'id')->where('user_id', Auth::id())],
        'description' => ['nullable', 'string', 'max:1000'],
    ]);

    if ($pendingNotification->status !== 'pending') {
        return response()->json(['message' => 'Notifikasi sudah diproses'], 400);
    }

    // Buat transaksi
    $transaction = Transaction::create([
        'user_id' => Auth::id(),
        'category_id' => $validated['category_id'],
        'account_id' => $validated['account_id'],
        'type' => $pendingNotification->type,
        'amount' => $pendingNotification->amount,
        'description' => $validated['description'] ?? $pendingNotification->merchant ?? $pendingNotification->description,
        'transaction_date' => $pendingNotification->notification_date ?? now()->toDateString(),
        'is_pending' => false,
        'pending_source' => $pendingNotification->source,
    ]);

    // Update saldo akun
    $account = Account::find($validated['account_id']);
    if ($pendingNotification->type === 'income') {
        $account->balance += $pendingNotification->amount;
    } else {
        $account->balance -= $pendingNotification->amount;
    }
    $account->save();

    // Update status notifikasi
    $pendingNotification->status = 'confirmed';
    $pendingNotification->save();

    return response()->json([
        'message' => 'Transaksi berhasil dibuat dari notifikasi',
        'transaction' => $transaction,
    ]);
}

public function reject(PendingNotification $pendingNotification)
{
    // Reject = menghapus/menandai notifikasi, pakai ability 'delete'
    $this->authorize('delete', $pendingNotification);

    if ($pendingNotification->status !== 'pending') {
        return response()->json(['message' => 'Notifikasi sudah diproses'], 400);
    }

    $pendingNotification->status = 'rejected';
    $pendingNotification->save();

    return response()->json(['message' => 'Notifikasi ditolak']);
}
```

### Option 2: Tambah Method Policy Baru

Kalau controller sudah pakai `$this->authorize('approve', ...)` / `$this->authorize('reject', ...)`, tambahkan di `app/Policies/PendingNotificationPolicy.php`:

```php
public function approve(User $user, PendingNotification $pendingNotification): bool
{
    return $user->id === $pendingNotification->user_id;
}

public function reject(User $user, PendingNotification $pendingNotification): bool
{
    return $user->id === $pendingNotification->user_id;
}
```

---

## 4. Pastikan Create Menyimpan `user_id` dengan Benar

Di `PendingNotificationController::store()`:

```php
$validated = $request->validate([
    'type' => ['required', 'in:income,expense'],
    'amount' => ['required', 'numeric', 'gt:0'],
    'source' => ['required', 'in:push_notif,ocr'],
    'description' => ['nullable', 'string', 'max:1000'],
    'merchant' => ['nullable', 'string', 'max:255'],
    'notification_date' => ['nullable', 'date'],
    'raw_body' => ['nullable', 'string'],
    'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'],
]);

$pendingNotification = PendingNotification::create([
    'user_id' => Auth::id(), // <-- PASTIKAN INI ADA
    'type' => $validated['type'],
    'amount' => $validated['amount'],
    'source' => $validated['source'],
    'status' => 'pending',
    'description' => $validated['description'] ?? null,
    'merchant' => $validated['merchant'] ?? null,
    'notification_date' => $validated['notification_date'] ?? now()->toDateString(),
    'raw_body' => $validated['raw_body'] ?? null,
    'image_path' => $request->hasFile('image') ? $request->file('image')->store('ocr-receipts') : null,
]);
```

---

## 5. Pastikan List Filter by User

Di `PendingNotificationController::index()`:

```php
public function index(Request $request)
{
    $pendingNotifications = Auth::user()
        ->pendingNotifications() // relasi user->pendingNotifications()
        ->where('status', 'pending')
        ->paginate($request->get('per_page', 20));

    return response()->json($pendingNotifications);
}
```

Atau:

```php
PendingNotification::where('user_id', Auth::id())
    ->where('status', 'pending')
    ->paginate(...);
```

---

## 6. File-file yang Perlu Dicek

| File | Yang Dicek |
|------|-----------|
| `app/Http/Controllers/Api/PendingNotificationController.php` | Method `approve()` dan `reject()`, authorize ability |
| `app/Policies/PendingNotificationPolicy.php` | Ada tidaknya method `approve()` / `reject()` |
| `app/Models/PendingNotification.php` | Relasi `user()` dan fillable `user_id` |
| `database/migrations/xxxx_create_pending_notifications_table.php` | Kolom `user_id` ada dan foreign key benar |
| `routes/api.php` | Route parameter binding `{pending_notification}` atau `{id}` |

---

## 7. Quick Debug

Tambahkan log sementara di controller approve:

```php
\Log::info('Approve debug', [
    'auth_id' => auth()->id(),
    'notif_id' => $pendingNotification->id,
    'notif_user_id' => $pendingNotification->user_id,
    'status' => $pendingNotification->status,
]);
```

Lalu cek `storage/logs/laravel.log` saat Flutter kirim request approve.

---

## 8. Expected Response

### Approve Sukses
```json
HTTP 200
{
  "message": "Transaksi berhasil dibuat dari notifikasi",
  "transaction": { ... }
}
```

### Reject Sukses
```json
HTTP 200
{
  "message": "Notifikasi ditolak"
}
```

### Sudah Diproses
```json
HTTP 400
{
  "message": "Notifikasi sudah diproses"
}
```

---

## Summary

- Flutter request sudah benar.
- 403 kemungkinan dari policy ability yang tidak ada (`approve`/`reject`) atau `user_id` tidak cocok.
- **Fix paling cepat**: di controller approve pakai `$this->authorize('update', $pendingNotification)`, di reject pakai `$this->authorize('delete', $pendingNotification)`.
- Jangan lupa pastikan `user_id` tersimpan saat create dan list difilter by user.
