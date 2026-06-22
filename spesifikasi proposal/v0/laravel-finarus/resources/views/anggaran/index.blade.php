@extends('layouts.app')
@section('title', 'Anggaran - FinFlow')
@section('page-title', 'Rencana Anggaran')
@section('page-description', 'Tentukan batas pengeluaran untuk setiap kategori')

@section('page-actions')
<button class="w-full sm:w-auto h-9 text-sm bg-primary text-primary-foreground hover:bg-primary/90 transition-all duration-300 px-4 rounded-md font-medium">
    + Tambah Anggaran
</button>
@endsection

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="bg-card rounded-lg shadow-lg p-5">
        <h3 class="font-semibold mb-3">Makanan</h3>
        <div class="mb-3">
            <div class="flex justify-between text-xs mb-2">
                <span>Pengeluaran</span>
                <span>Rp 240 / Rp 400</span>
            </div>
            <div class="w-full bg-muted rounded-full h-2">
                <div class="bg-orange-500 h-2 rounded-full" style="width: 60%"></div>
            </div>
        </div>
        <div class="flex gap-2">
            <button class="flex-1 py-2 text-xs font-medium text-primary hover:bg-primary/5 rounded">Edit</button>
            <button class="flex-1 py-2 text-xs font-medium text-red-500 hover:bg-red-50">Hapus</button>
        </div>
    </div>
</div>
@endsection
