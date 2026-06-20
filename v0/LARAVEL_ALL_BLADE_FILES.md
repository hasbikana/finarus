# FinFlow Laravel - Blade Files untuk Semua Halaman

Ini berisi semua blade files yang diperlukan untuk FinFlow Laravel. Copy-paste ke lokasi yang sesuai.

---

## Halaman Transaksi (resources/views/app/transaksi/index.blade.php)

```blade
@extends('layouts.app')

@section('title', 'Transaksi')

@section('content')
<div class="mt-4 md:mt-5 space-y-4">
  <!-- Filter Section -->
  <div class="bg-white rounded-lg shadow p-4 dark:bg-gray-800">
    <div class="flex flex-col sm:flex-row gap-3">
      <div class="flex-1">
        <input type="text" placeholder="Cari transaksi..." class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700">
      </div>
      <select class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700">
        <option value="">Semua Tipe</option>
        <option value="income">Pemasukan</option>
        <option value="expense">Pengeluaran</option>
      </select>
      <button class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">
        + Tambah Transaksi
      </button>
    </div>
  </div>

  <!-- Transactions Table -->
  <div class="bg-white rounded-lg shadow overflow-hidden dark:bg-gray-800">
    <table class="w-full">
      <thead class="bg-gray-50 dark:bg-gray-700">
        <tr>
          <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Deskripsi</th>
          <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Kategori</th>
          <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Tipe</th>
          <th class="px-6 py-3 text-right text-sm font-semibold text-gray-900 dark:text-white">Jumlah</th>
          <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Tanggal</th>
          <th class="px-6 py-3 text-center text-sm font-semibold text-gray-900 dark:text-white">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
        @php
          $transactions = [
            ['desc' => 'Kopi Starbucks', 'cat' => 'Makanan', 'type' => 'Expense', 'amount' => '-Rp 55.000', 'date' => '2024-01-15'],
            ['desc' => 'Proyek Freelance', 'cat' => 'Pemasukan', 'type' => 'Income', 'amount' => '+Rp 1.250.000', 'date' => '2024-01-14'],
          ];
        @endphp
        @foreach($transactions as $txn)
        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
          <td class="px-6 py-3 text-sm text-gray-900 dark:text-white">{{ $txn['desc'] }}</td>
          <td class="px-6 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $txn['cat'] }}</td>
          <td class="px-6 py-3 text-sm">
            <span class="px-2 py-1 rounded text-xs font-medium {{ $txn['type'] == 'Income' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
              {{ $txn['type'] }}
            </span>
          </td>
          <td class="px-6 py-3 text-sm font-semibold text-right {{ str_contains($txn['amount'], '+') ? 'text-green-600' : 'text-gray-900 dark:text-white' }}">
            {{ $txn['amount'] }}
          </td>
          <td class="px-6 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $txn['date'] }}</td>
          <td class="px-6 py-3 text-center">
            <div class="flex justify-center gap-2">
              <button class="text-blue-600 hover:text-blue-700 text-sm">Edit</button>
              <button class="text-red-600 hover:text-red-700 text-sm">Hapus</button>
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection
```

---

## Halaman Kategori (resources/views/app/kategori/index.blade.php)

```blade
@extends('layouts.app')

@section('title', 'Kategori')

@section('content')
<div class="mt-4 md:mt-5 space-y-4">
  <!-- Add Button -->
  <div class="flex justify-end">
    <button class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">
      + Tambah Kategori
    </button>
  </div>

  <!-- Categories Grid -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
    @php
      $categories = [
        ['name' => 'Makanan', 'icon' => '🍔', 'color' => 'orange'],
        ['name' => 'Transportasi', 'icon' => '🚗', 'color' => 'blue'],
        ['name' => 'Belanja', 'icon' => '🛍️', 'color' => 'pink'],
        ['name' => 'Hiburan', 'icon' => '🎬', 'color' => 'purple'],
        ['name' => 'Kesehatan', 'icon' => '⚕️', 'color' => 'red'],
        ['name' => 'Pendidikan', 'icon' => '📚', 'color' => 'green'],
      ];
    @endphp

    @foreach($categories as $cat)
    <div class="bg-white rounded-lg shadow p-4 dark:bg-gray-800 hover:shadow-lg transition-shadow">
      <div class="flex items-start justify-between mb-3">
        <div class="text-4xl">{{ $cat['icon'] }}</div>
        <div class="flex gap-2">
          <button class="text-blue-600 hover:text-blue-700 text-sm">✏️</button>
          <button class="text-red-600 hover:text-red-700 text-sm">🗑️</button>
        </div>
      </div>
      <h3 class="font-semibold text-gray-900 dark:text-white">{{ $cat['name'] }}</h3>
      <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">12 transaksi</p>
    </div>
    @endforeach
  </div>
</div>
@endsection
```

---

## Halaman Anggaran (resources/views/app/anggaran/index.blade.php)

```blade
@extends('layouts.app')

@section('title', 'Rencana Anggaran')

@section('content')
<div class="mt-4 md:mt-5 space-y-4">
  <!-- Add Button -->
  <div class="flex justify-end">
    <button class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">
      + Buat Rencana Anggaran
    </button>
  </div>

  <!-- Budget Cards -->
  <div class="space-y-3">
    @php
      $budgets = [
        ['category' => 'Makanan', 'spent' => 240, 'budget' => 400, 'month' => 'Januari 2024', 'status' => 'on-track'],
        ['category' => 'Belanja', 'spent' => 340, 'budget' => 300, 'month' => 'Januari 2024', 'status' => 'over'],
        ['category' => 'Utilitas', 'spent' => 125, 'budget' => 150, 'month' => 'Januari 2024', 'status' => 'on-track'],
      ];
    @endphp

    @foreach($budgets as $budget)
    <div class="bg-white rounded-lg shadow p-4 dark:bg-gray-800">
      <div class="flex items-start justify-between mb-3">
        <div>
          <h3 class="font-semibold text-gray-900 dark:text-white">{{ $budget['category'] }}</h3>
          <p class="text-xs text-gray-600 dark:text-gray-400">{{ $budget['month'] }}</p>
        </div>
        <div class="flex gap-2">
          <button class="text-blue-600 hover:text-blue-700 text-sm">✏️</button>
          <button class="text-red-600 hover:text-red-700 text-sm">🗑️</button>
        </div>
      </div>

      <div class="flex items-center justify-between mb-2">
        <span class="text-sm text-gray-700 dark:text-gray-300">Rp {{ $budget['spent'] }} / Rp {{ $budget['budget'] }}</span>
        <span class="text-xs font-medium {{ $budget['status'] == 'over' ? 'text-red-600' : 'text-green-600' }}">
          {{ $budget['status'] == 'over' ? 'Melebihi' : 'Berjalan Lancar' }}
        </span>
      </div>

      <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
        <div class="bg-{{ $budget['status'] == 'over' ? 'red' : 'green' }}-500 h-2 rounded-full" style="width: {{ min(100, ($budget['spent'] / $budget['budget']) * 100) }}%"></div>
      </div>
    </div>
    @endforeach
  </div>
</div>
@endsection
```

---

## Halaman Tujuan Tabungan (resources/views/app/tabungan/index.blade.php)

```blade
@extends('layouts.app')

@section('title', 'Tujuan Tabungan')

@section('content')
<div class="mt-4 md:mt-5 space-y-4">
  <!-- Add Button -->
  <div class="flex justify-end">
    <button class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">
      + Tambah Tujuan Tabungan
    </button>
  </div>

  <!-- Saving Goals Grid -->
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    @php
      $savingGoals = [
        ['name' => 'Liburan ke Bali', 'target' => 10000000, 'current' => 6400000, 'deadline' => '2024-06-30', 'icon' => '✈️'],
        ['name' => 'Beli Laptop Baru', 'target' => 15000000, 'current' => 11250000, 'deadline' => '2024-09-30', 'icon' => '💻'],
        ['name' => 'Dana Darurat', 'target' => 20000000, 'current' => 15600000, 'deadline' => '2024-12-31', 'icon' => '🛡️'],
      ];
    @endphp

    @foreach($savingGoals as $goal)
    <div class="bg-white rounded-lg shadow p-4 dark:bg-gray-800 hover:shadow-lg transition-shadow">
      <div class="flex items-start justify-between mb-3">
        <div class="text-3xl">{{ $goal['icon'] }}</div>
        <div class="flex gap-2">
          <button class="text-blue-600 hover:text-blue-700 text-sm">✏️</button>
          <button class="text-red-600 hover:text-red-700 text-sm">🗑️</button>
        </div>
      </div>

      <h3 class="font-semibold text-gray-900 dark:text-white mb-1">{{ $goal['name'] }}</h3>
      <p class="text-xs text-gray-600 dark:text-gray-400 mb-3">Target: {{ date('M Y', strtotime($goal['deadline'])) }}</p>

      <div class="mb-2">
        <div class="flex items-center justify-between text-xs text-gray-600 dark:text-gray-400 mb-1">
          <span>Rp {{ number_format($goal['current']) }}</span>
          <span>Rp {{ number_format($goal['target']) }}</span>
        </div>
        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
          <div class="bg-blue-500 h-2 rounded-full" style="width: {{ ($goal['current'] / $goal['target']) * 100 }}%"></div>
        </div>
      </div>

      <div class="text-xs font-medium text-blue-600">
        {{ round(($goal['current'] / $goal['target']) * 100, 1) }}% Tercapai
      </div>
    </div>
    @endforeach
  </div>
</div>
@endsection
```

---

## Halaman Laporan (resources/views/app/laporan/index.blade.php)

```blade
@extends('layouts.app')

@section('title', 'Laporan')

@section('content')
<div class="mt-4 md:mt-5 space-y-4">
  <!-- Export Button -->
  <div class="flex justify-end">
    <button class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 flex items-center gap-2">
      📥 Ekspor Laporan
    </button>
  </div>

  <!-- Period Filter -->
  <div class="bg-white rounded-lg shadow p-4 dark:bg-gray-800">
    <div class="flex flex-col sm:flex-row gap-3">
      <select class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700">
        <option value="week">Minggu Ini</option>
        <option value="month" selected>Bulan Ini</option>
        <option value="year">Tahun Ini</option>
      </select>
    </div>
  </div>

  <!-- Summary Cards -->
  <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    <div class="bg-white rounded-lg shadow p-4 dark:bg-gray-800">
      <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">Total Pemasukan</p>
      <p class="text-2xl font-bold text-green-600 mb-1">Rp 5.500.000</p>
      <p class="text-xs text-green-600">+15% dari bulan lalu</p>
    </div>

    <div class="bg-white rounded-lg shadow p-4 dark:bg-gray-800">
      <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">Total Pengeluaran</p>
      <p class="text-2xl font-bold text-red-600 mb-1">Rp 3.180.750</p>
      <p class="text-xs text-red-600">-5% dari bulan lalu</p>
    </div>

    <div class="bg-white rounded-lg shadow p-4 dark:bg-gray-800">
      <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">Net Savings</p>
      <p class="text-2xl font-bold text-blue-600 mb-1">Rp 2.319.250</p>
      <p class="text-xs text-blue-600">+25% dari bulan lalu</p>
    </div>
  </div>

  <!-- Chart Placeholder -->
  <div class="bg-white rounded-lg shadow p-4 dark:bg-gray-800">
    <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Tren Bulanan</h3>
    <div class="h-64 bg-gray-100 dark:bg-gray-700 rounded flex items-center justify-center text-gray-600 dark:text-gray-400">
      [Chart visualization akan ditampilkan di sini]
    </div>
  </div>
</div>
@endsection
```

---

## Halaman E-Wallet & Bank (resources/views/app/dompet-digital/index.blade.php)

```blade
@extends('layouts.app')

@section('title', 'E-Wallet & Bank')

@section('content')
<div class="mt-4 md:mt-5 space-y-4">
  <!-- Add Button -->
  <div class="flex justify-end">
    <button class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">
      + Tambah Akun
    </button>
  </div>

  <!-- Total Balance Card -->
  <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg shadow p-6 text-white">
    <p class="text-sm opacity-90 mb-1">Total Saldo</p>
    <p class="text-3xl font-bold">Rp 38.500.000</p>
  </div>

  <!-- Accounts Grid -->
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    @php
      $accounts = [
        ['name' => 'Dompet Digital', 'provider' => 'GoPay', 'balance' => 5000000, 'accountNo' => '087812345678', 'logo' => 'gopay'],
        ['name' => 'Rekening Utama', 'provider' => 'Bank BCA', 'balance' => 25000000, 'accountNo' => '1234567890', 'logo' => 'bca'],
        ['name' => 'Kartu Kredit', 'provider' => 'BCA Card', 'balance' => 8500000, 'accountNo' => '4532 **** **** 1234', 'logo' => 'bca'],
      ];
    @endphp

    @foreach($accounts as $account)
    <div class="bg-white rounded-lg shadow p-4 dark:bg-gray-800 hover:shadow-lg transition-shadow">
      <div class="flex items-start justify-between mb-3">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center overflow-hidden">
            <img src="{{ asset('logos/' . $account['logo'] . '.png') }}" alt="{{ $account['provider'] }}" class="w-full h-full object-contain">
          </div>
          <div>
            <h3 class="font-semibold text-gray-900 dark:text-white">{{ $account['name'] }}</h3>
            <p class="text-xs text-gray-600 dark:text-gray-400">{{ $account['provider'] }}</p>
          </div>
        </div>
        <div class="flex gap-2">
          <button class="text-blue-600 hover:text-blue-700">✏️</button>
          <button class="text-red-600 hover:text-red-700">🗑️</button>
        </div>
      </div>

      <div class="mb-3">
        <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">Nomor Akun</p>
        <p class="font-mono text-sm text-gray-900 dark:text-white">{{ $account['accountNo'] }}</p>
      </div>

      <div class="pt-3 border-t border-gray-200 dark:border-gray-700">
        <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">Saldo</p>
        <p class="text-lg font-bold text-gray-900 dark:text-white">Rp {{ number_format($account['balance']) }}</p>
      </div>
    </div>
    @endforeach
  </div>
</div>
@endsection
```

---

## Halaman Pengaturan (resources/views/app/pengaturan.blade.php)

```blade
@extends('layouts.app')

@section('title', 'Pengaturan')

@section('content')
<div class="mt-4 md:mt-5 max-w-2xl">
  <div class="space-y-4">
    <!-- Notifikasi -->
    <div class="bg-white rounded-lg shadow p-6 dark:bg-gray-800">
      <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Notifikasi</h3>
      <div class="space-y-4">
        <label class="flex items-center gap-3 cursor-pointer">
          <input type="checkbox" checked class="w-4 h-4">
          <div>
            <p class="text-sm font-medium text-gray-900 dark:text-white">Notifikasi Email</p>
            <p class="text-xs text-gray-600 dark:text-gray-400">Terima update tentang transaksi Anda</p>
          </div>
        </label>
        <label class="flex items-center gap-3 cursor-pointer">
          <input type="checkbox" checked class="w-4 h-4">
          <div>
            <p class="text-sm font-medium text-gray-900 dark:text-white">Alert Anggaran</p>
            <p class="text-xs text-gray-600 dark:text-gray-400">Notifikasi ketika anggaran hampir habis</p>
          </div>
        </label>
      </div>
    </div>

    <!-- Keamanan -->
    <div class="bg-white rounded-lg shadow p-6 dark:bg-gray-800">
      <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Keamanan</h3>
      <button class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">
        Ubah Password
      </button>
    </div>

    <!-- Tampilan -->
    <div class="bg-white rounded-lg shadow p-6 dark:bg-gray-800">
      <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Tampilan</h3>
      <div class="space-y-3">
        <label class="flex items-center gap-3 cursor-pointer">
          <input type="radio" name="theme" checked class="w-4 h-4">
          <span class="text-sm text-gray-700 dark:text-gray-300">Light Mode</span>
        </label>
        <label class="flex items-center gap-3 cursor-pointer">
          <input type="radio" name="theme" class="w-4 h-4">
          <span class="text-sm text-gray-700 dark:text-gray-300">Dark Mode</span>
        </label>
      </div>
    </div>
  </div>
</div>
@endsection
```

---

## Halaman Bantuan (resources/views/app/bantuan.blade.php)

```blade
@extends('layouts.app')

@section('title', 'Bantuan')

@section('content')
<div class="mt-4 md:mt-5 max-w-2xl">
  <!-- FAQ -->
  <div class="space-y-3">
    <div class="bg-white rounded-lg shadow p-6 dark:bg-gray-800">
      <h3 class="font-semibold text-gray-900 dark:text-white mb-2">Bagaimana cara menambahkan transaksi?</h3>
      <p class="text-sm text-gray-600 dark:text-gray-400">Klik tombol "Tambah Transaksi" di halaman Transaksi, lalu isi detail transaksi Anda termasuk jumlah, kategori, dan tanggal.</p>
    </div>

    <div class="bg-white rounded-lg shadow p-6 dark:bg-gray-800">
      <h3 class="font-semibold text-gray-900 dark:text-white mb-2">Bagaimana cara membuat rencana anggaran?</h3>
      <p class="text-sm text-gray-600 dark:text-gray-400">Pergi ke halaman Rencana Anggaran, klik "Buat Rencana Anggaran", pilih kategori, dan set batas anggaran untuk bulan tersebut.</p>
    </div>

    <div class="bg-white rounded-lg shadow p-6 dark:bg-gray-800">
      <h3 class="font-semibold text-gray-900 dark:text-white mb-2">Bisakah saya mengekspor laporan?</h3>
      <p class="text-sm text-gray-600 dark:text-gray-400">Ya, di halaman Laporan ada tombol "Ekspor Laporan" untuk mendownload data keuangan Anda dalam format PDF atau Excel.</p>
    </div>
  </div>

  <!-- Contact -->
  <div class="mt-8 bg-blue-50 dark:bg-gray-800 rounded-lg p-6 border border-blue-200 dark:border-gray-700">
    <h3 class="font-semibold text-gray-900 dark:text-white mb-2">Butuh Bantuan Lebih Lanjut?</h3>
    <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">Hubungi tim support kami untuk pertanyaan atau masalah apa pun.</p>
    <a href="mailto:support@finflow.com" class="text-blue-600 hover:text-blue-700 text-sm font-medium">support@finflow.com</a>
  </div>
</div>
@endsection
```

---

Semua blade files sudah siap! Copy-paste ke folder masing-masing di project Laravel Breeze Anda.
