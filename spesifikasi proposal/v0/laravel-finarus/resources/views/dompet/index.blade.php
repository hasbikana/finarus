@extends('layouts.app')
@section('title', 'E-Wallet & Bank - FinFlow')
@section('page-title', 'E-Wallet & Banking')
@section('page-description', 'Kelola akun e-wallet dan rekening bank Anda')

@section('page-actions')
<button class="w-full sm:w-auto h-9 text-sm bg-primary text-primary-foreground hover:bg-primary/90 transition-all duration-300 px-4 rounded-md font-medium">
    + Tambah Akun
</button>
@endsection

@section('content')
<div class="space-y-4">
    <div class="bg-card rounded-lg shadow-lg p-5">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <img src="/logos/gopay.png" alt="GoPay" class="w-12 h-12">
                <div>
                    <h3 class="font-semibold">Dompet Digital</h3>
                    <p class="text-xs text-muted-foreground">E-Wallet • GoPay</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-lg font-semibold">Rp 5.000.000</p>
                <div class="flex gap-2 mt-2">
                    <button class="text-blue-500 hover:text-blue-700 text-xs">Edit</button>
                    <button class="text-red-500 hover:text-red-700 text-xs">Hapus</button>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-card rounded-lg shadow-lg p-5">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <img src="/logos/bca.png" alt="BCA" class="w-12 h-12">
                <div>
                    <h3 class="font-semibold">Rekening Utama</h3>
                    <p class="text-xs text-muted-foreground">Bank • BCA</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-lg font-semibold">Rp 25.000.000</p>
                <div class="flex gap-2 mt-2">
                    <button class="text-blue-500 hover:text-blue-700 text-xs">Edit</button>
                    <button class="text-red-500 hover:text-red-700 text-xs">Hapus</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-6 bg-card rounded-lg shadow-lg p-5">
    <h3 class="font-semibold mb-3">Total Saldo: Rp 30.000.000</h3>
</div>
@endsection
