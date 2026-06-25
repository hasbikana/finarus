# Skema Database

Struktur ini diambil dari folder `database/migrations/`.

## Entity Relationship

```
┌──────────┐       ┌───────────────┐       ┌──────────────┐
│  users   │──1:N──│  accounts     │──1:N──│ transactions  │
└──────────┘       │ email_scopes  │       └──────────────┘
     │             └───────────────┘              │
     │              ┌──────────────┐              │
     ├──1:N────────│  categories   │◄─N:1─────────┘
     │             └──────────────┘
     ├──1:1────────│ user_settings  │
     │             └────────────────┘
     ├──1:N────────│  budgets       │
     ├──1:N────────│  saving_goals  │
     ├──1:N────────│ pending_notif  │
     └──1:1────────│ user_oauth_tok │
                   └────────────────┘
```

## Daftar Tabel

### `users`

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| name | varchar(255) | |
| email | varchar(255) UNIQUE | |
| email_verified_at | timestamp nullable | |
| password | varchar(255) | sudah di-hash |
| password_set_at | timestamp nullable | menandai user sudah set password (non-Google) |
| remember_token | varchar(100) nullable | |
| created_at / updated_at | timestamp | |

### `accounts`

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| user_id | bigint FK → users.id CASCADE | |
| name | varchar(255) | nama akun |
| provider | varchar(255) | nama provider (BCA, GoPay, dll) |
| type | enum: cash, ewallet, bank, credit_card | default 'bank' |
| account_number | varchar(255) nullable | |
| balance | decimal(15,2) default 0 | |
| logo | varchar(255) nullable | logo key (bca, gopay, dll) |
| email_scopes | JSON nullable | array email pengirim untuk scope fetch |

### `transactions`

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| user_id | bigint FK → users.id CASCADE INDEX | |
| category_id | bigint FK → categories.id CASCADE INDEX | |
| account_id | bigint FK → accounts.id SET NULL INDEX nullable | |
| saving_goal_id | bigint FK → saving_goals.id SET NULL nullable | |
| type | enum: income, expense | |
| amount | decimal(15,2) | |
| description | varchar(255) nullable | |
| email_message_id | varchar(255) UNIQUE nullable | untuk deduplikasi email |
| source | varchar(255) nullable | 'email', 'manual', 'import' |
| is_pending | boolean default true | |
| pending_source | varchar(255) nullable | 'email', 'push_notif', 'ocr' |
| transaction_date | date INDEX | |

### `categories`

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| user_id | bigint FK → users.id CASCADE INDEX | |
| name | varchar(255) | |
| type | enum: income, expense, both | default 'both' |
| icon | varchar(255) nullable | emoji |
| color | varchar(255) nullable | hex color |

### `budgets`

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| user_id | bigint FK → users.id CASCADE | |
| category_id | bigint FK → categories.id CASCADE | |
| amount | decimal(15,2) | batas anggaran |
| month | unsignedSmallInt | |
| year | unsignedSmallInt | |
| UNIQUE | (user_id, category_id, month, year) | |

### `saving_goals`

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| user_id | bigint FK → users.id CASCADE INDEX | |
| name | varchar(255) | |
| target_amount | decimal(15,2) | |
| current_amount | decimal(15,2) default 0 | |
| deadline | date nullable | |
| icon | varchar(255) nullable | emoji |
| image | varchar(255) nullable | custom image |

### `user_settings`

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| user_id | bigint FK → users.id UNIQUE CASCADE | |
| email_notifications | boolean default true | |
| budget_alerts | boolean default true | |
| theme | enum: light, dark | default 'light' |
| email_fetch_enabled | boolean default false | master switch fetch Gmail |

### `user_oauth_tokens`

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| user_id | bigint FK → users.id CASCADE | |
| provider | varchar(255) default 'google' | |
| access_token | text | |
| refresh_token | text nullable | |
| expires_at | timestamp nullable | |
| email | varchar(255) nullable | email yang terkoneksi |
| scopes | text nullable | scope Gmail |
| UNIQUE | (user_id, provider) | |

### `pending_notifications`

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| user_id | bigint FK → users.id CASCADE INDEX | |
| type | enum: income, expense | |
| amount | decimal(15,2) | |
| description | varchar(255) nullable | |
| merchant | varchar(255) nullable | |
| notification_date | date nullable | |
| raw_body | text nullable | isi email mentah |
| image_path | varchar(255) nullable | |
| source | enum: push_notif, ocr | |
| status | enum: pending, confirmed, rejected | default 'pending', INDEX |

### `sessions`

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | varchar PK | |
| user_id | bigint FK → users.id nullable INDEX | |
| ip_address | varchar(45) nullable | |
| user_agent | text nullable | |
| payload | longtext | |
| last_activity | int INDEX | |

### Lainnya

Tabel `personal_access_tokens`, `cache`, `cache_locks`, `jobs`, `job_batches`, `failed_jobs`, `password_reset_tokens` adalah tabel bawaan Laravel dan Sanctum.
