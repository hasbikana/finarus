<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('dashboard');
})->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/transaksi', function () {
        return view('transaksi.index');
    })->name('transaksi.index');

    Route::get('/kategori', function () {
        return view('kategori.index');
    })->name('kategori.index');

    Route::get('/anggaran', function () {
        return view('anggaran.index');
    })->name('anggaran.index');

    Route::get('/tabungan', function () {
        return view('tabungan.index');
    })->name('tabungan.index');

    Route::get('/laporan', function () {
        return view('laporan.index');
    })->name('laporan.index');

    Route::get('/dompet-digital', function () {
        return view('dompet.index');
    })->name('dompet.index');

    Route::get('/pengaturan', function () {
        return view('pengaturan');
    })->name('pengaturan');

    Route::get('/bantuan', function () {
        return view('bantuan');
    })->name('bantuan');
});

require __DIR__.'/auth.php';
