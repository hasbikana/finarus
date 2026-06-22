@extends('layouts.app')
@section('title', 'Pengaturan - FinFlow')
@section('page-title', 'Pengaturan')
@section('page-description', 'Kelola preferensi dan keamanan akun Anda')

@section('content')
<div class="max-w-2xl bg-card rounded-lg shadow-lg p-5">
    <div class="space-y-6">
        <div>
            <h3 class="font-semibold mb-3">Notifikasi</h3>
            <div class="space-y-2">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" checked class="rounded">
                    <span class="text-sm">Notifikasi Transaksi</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" checked class="rounded">
                    <span class="text-sm">Notifikasi Anggaran</span>
                </label>
            </div>
        </div>

        <hr class="border-border">

        <div>
            <h3 class="font-semibold mb-3">Tampilan</h3>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" checked class="rounded">
                <span class="text-sm">Mode Gelap</span>
            </label>
        </div>

        <hr class="border-border">

        <div class="flex justify-end gap-3">
            <button class="h-9 px-4 rounded-md border border-border hover:bg-muted transition-colors text-sm font-medium">
                Batal
            </button>
            <button class="h-9 px-4 rounded-md bg-primary text-primary-foreground hover:bg-primary/90 transition-colors text-sm font-medium">
                Simpan
            </button>
        </div>
    </div>
</div>
@endsection
