@extends('layouts.app')
@section('title', 'Laporan - FinFlow')
@section('page-title', 'Laporan Keuangan')
@section('page-description', 'Analisis mendalam tentang keuangan Anda')

@section('page-actions')
<button class="w-full sm:w-auto h-9 text-sm bg-primary text-primary-foreground hover:bg-primary/90 transition-all duration-300 px-4 rounded-md font-medium flex items-center justify-center gap-2">
    <span>⬇️</span> Ekspor Laporan
</button>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    <div class="bg-card rounded-lg shadow-lg p-5">
        <h3 class="font-semibold mb-4">Ringkasan Bulanan</h3>
        <div class="space-y-3">
            <div class="flex justify-between">
                <span class="text-sm">Total Pemasukan</span>
                <span class="font-semibold text-green-500">+Rp 4.250.000</span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm">Total Pengeluaran</span>
                <span class="font-semibold text-red-500">-Rp 2.180.750</span>
            </div>
            <div class="border-t border-border pt-3 flex justify-between">
                <span class="text-sm font-semibold">Saldo Bersih</span>
                <span class="font-semibold text-blue-500">+Rp 2.069.250</span>
            </div>
        </div>
    </div>
    
    <div class="bg-card rounded-lg shadow-lg p-5">
        <h3 class="font-semibold mb-4">Distribusi Pengeluaran</h3>
        <div class="space-y-3">
            <div class="flex items-center gap-3">
                <div class="w-3 h-3 bg-orange-500 rounded-full"></div>
                <span class="text-sm">Makanan: 30%</span>
            </div>
            <div class="flex items-center gap-3">
                <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                <span class="text-sm">Belanja: 25%</span>
            </div>
            <div class="flex items-center gap-3">
                <div class="w-3 h-3 bg-purple-500 rounded-full"></div>
                <span class="text-sm">Lainnya: 45%</span>
            </div>
        </div>
    </div>
</div>
@endsection
