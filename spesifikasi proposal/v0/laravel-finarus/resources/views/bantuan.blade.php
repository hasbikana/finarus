@extends('layouts.app')
@section('title', 'Bantuan - FinFlow')
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
    </div>
</div>
@endsection
