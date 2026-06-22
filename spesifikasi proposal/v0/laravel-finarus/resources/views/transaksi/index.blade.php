@extends('layouts.app')
@section('title', 'Transaksi - FinFlow')
@section('page-title', 'Manajemen Transaksi')
@section('page-description', 'Kelola dan pantau semua transaksi keuangan Anda')

@section('page-actions')
<button class="w-full sm:w-auto h-9 text-sm bg-primary text-primary-foreground hover:bg-primary/90 transition-all duration-300 px-4 rounded-md font-medium">
    + Tambah Transaksi
</button>
@endsection

@section('content')
<div class="bg-card rounded-lg shadow-lg p-5">
    <div class="flex flex-col md:flex-row gap-4 mb-5">
        <input type="text" placeholder="Cari transaksi..." class="flex-1 h-9 px-3 rounded-md border border-border bg-background text-foreground">
        <select class="h-9 px-3 rounded-md border border-border bg-card text-foreground">
            <option>Semua Tipe</option>
            <option>Pemasukan</option>
            <option>Pengeluaran</option>
        </select>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="border-b border-border">
                <tr>
                    <th class="text-left py-3 px-4 font-semibold text-sm">Deskripsi</th>
                    <th class="text-left py-3 px-4 font-semibold text-sm">Kategori</th>
                    <th class="text-left py-3 px-4 font-semibold text-sm">Tanggal</th>
                    <th class="text-right py-3 px-4 font-semibold text-sm">Jumlah</th>
                    <th class="text-center py-3 px-4 font-semibold text-sm">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr class="border-b border-border hover:bg-muted transition-colors">
                    <td class="py-3 px-4">Kopi Starbucks</td>
                    <td class="py-3 px-4"><span class="bg-orange-100 text-orange-700 px-2 py-1 rounded text-xs">Makanan</span></td>
                    <td class="py-3 px-4 text-sm text-muted-foreground">17 Jun 2024</td>
                    <td class="py-3 px-4 text-right text-red-500 font-semibold">-Rp 55.000</td>
                    <td class="py-3 px-4 text-center">
                        <button class="text-blue-500 hover:text-blue-700 text-xs">Edit</button>
                        <span class="mx-1 text-muted-foreground">|</span>
                        <button class="text-red-500 hover:text-red-700 text-xs">Hapus</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
