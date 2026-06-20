@extends('layouts.app')

@section('title', 'Bantuan - Finarus')
@section('page-title', 'Pusat Bantuan')
@section('page-description', 'Temukan jawaban atas pertanyaan Anda')

@section('content')
<div class="max-w-2xl bg-card rounded-lg shadow-lg p-5">
    <div class="space-y-4">
        <div class="border border-border rounded-lg p-4">
            <h3 class="font-semibold mb-2">Bagaimana cara menambah transaksi?</h3>
            <p class="text-sm text-muted-foreground">Klik tombol "+ Tambah Transaksi" pada halaman Transaksi, kemudian isi form dengan detail transaksi Anda.</p>
        </div>

        <div class="border border-border rounded-lg p-4">
            <h3 class="font-semibold mb-2">Bagaimana cara mengatur anggaran?</h3>
            <p class="text-sm text-muted-foreground">Pergi ke halaman Rencana Anggaran dan tentukan batas pengeluaran untuk setiap kategori.</p>
        </div>

        <div class="border border-border rounded-lg p-4">
            <h3 class="font-semibold mb-2">Bagaimana cara membuat tujuan tabungan?</h3>
            <p class="text-sm text-muted-foreground">Klik "+ Tambah Tujuan" pada halaman Tujuan Tabungan, isi nama dan target jumlah tabungan.</p>
        </div>

        <div class="border border-border rounded-lg p-4">
            <h3 class="font-semibold mb-2">Bagaimana cara menambahkan akun e-wallet atau bank?</h3>
            <p class="text-sm text-muted-foreground">Pergi ke halaman E-Wallet & Bank dan klik "+ Tambah Akun" untuk menambahkan akun baru.</p>
        </div>

        <div class="border border-border rounded-lg p-4">
            <h3 class="font-semibold mb-2">API Endpoints</h3>
            <p class="text-sm text-muted-foreground mb-2">Berikut endpoint yang tersedia:</p>
            <ul class="text-sm text-muted-foreground space-y-1">
                <li><code class="bg-muted px-1 rounded">POST /api/auth/register</code> - Registrasi</li>
                <li><code class="bg-muted px-1 rounded">POST /api/auth/login</code> - Login</li>
                <li><code class="bg-muted px-1 rounded">GET /api/dashboard</code> - Dashboard</li>
                <li><code class="bg-muted px-1 rounded">CRUD /api/categories</code> - Kategori</li>
                <li><code class="bg-muted px-1 rounded">CRUD /api/transactions</code> - Transaksi</li>
                <li><code class="bg-muted px-1 rounded">CRUD /api/budgets</code> - Anggaran</li>
                <li><code class="bg-muted px-1 rounded">CRUD /api/saving-goals</code> - Tujuan Tabungan</li>
                <li><code class="bg-muted px-1 rounded">CRUD /api/accounts</code> - E-Wallet & Bank</li>
                <li><code class="bg-muted px-1 rounded">GET /api/reports/*</code> - Laporan</li>
                <li><code class="bg-muted px-1 rounded">GET/PUT /api/settings</code> - Pengaturan</li>
            </ul>
        </div>
    </div>
</div>
@endsection