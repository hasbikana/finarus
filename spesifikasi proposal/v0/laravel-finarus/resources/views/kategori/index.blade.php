@extends('layouts.app')
@section('title', 'Kategori - FinFlow')
@section('page-title', 'Kelola Kategori')
@section('page-description', 'Organisir transaksi dengan kategori kustom')

@section('page-actions')
<button class="w-full sm:w-auto h-9 text-sm bg-primary text-primary-foreground hover:bg-primary/90 transition-all duration-300 px-4 rounded-md font-medium">
    + Tambah Kategori
</button>
@endsection

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    <div class="bg-card rounded-lg shadow-lg p-5">
        <div class="flex items-center gap-3 mb-3">
            <div class="p-3 bg-orange-100 rounded-lg">
                <span class="text-xl">☕</span>
            </div>
            <div>
                <h3 class="font-semibold text-foreground">Makanan</h3>
                <p class="text-xs text-muted-foreground">12 transaksi</p>
            </div>
        </div>
        <div class="flex gap-2">
            <button class="flex-1 py-2 text-xs font-medium text-primary hover:bg-primary/5 rounded">Edit</button>
            <button class="flex-1 py-2 text-xs font-medium text-red-500 hover:bg-red-50">Hapus</button>
        </div>
    </div>
</div>
@endsection
