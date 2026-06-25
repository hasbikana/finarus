<?php

use App\Http\Controllers\Api\OAuthController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WebCrudController;
use App\Http\Controllers\WebPageController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/oauth/google/login', [OAuthController::class, 'redirectToGoogle'])->name('oauth.google.login');
Route::get('/oauth/google/callback', [OAuthController::class, 'handleGoogleCallback'])->name('oauth.google.callback');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [WebPageController::class, 'dashboard'])->name('dashboard');
    Route::get('/transaksi', [WebPageController::class, 'transaksi'])->name('transaksi');
    Route::get('/kategori', [WebPageController::class, 'kategori'])->name('kategori');
    Route::get('/anggaran', [WebPageController::class, 'anggaran'])->name('anggaran');
    Route::get('/tabungan', [WebPageController::class, 'tabungan'])->name('tabungan');
    Route::get('/laporan', [WebPageController::class, 'laporan'])->name('laporan');
    Route::get('/dompet-digital', [WebPageController::class, 'dompetDigital'])->name('dompet-digital');
    Route::get('/pengaturan', [WebPageController::class, 'pengaturan'])->name('pengaturan');
    Route::get('/bantuan', [WebPageController::class, 'bantuan'])->name('bantuan');
    Route::get('/notifikasi', [WebPageController::class, 'notifikasi'])->name('notifikasi');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/oauth/google/connect', [OAuthController::class, 'redirectToGoogle'])->name('oauth.google.connect');
    Route::post('/oauth/google/disconnect', [OAuthController::class, 'disconnect'])->name('oauth.google.disconnect');

    Route::post('/transaksi', [WebCrudController::class, 'storeTransaksi'])->name('transaksi.store');
    Route::put('/transaksi/{transaction}', [WebCrudController::class, 'updateTransaksi'])->name('transaksi.update');
    Route::delete('/transaksi/{transaction}', [WebCrudController::class, 'destroyTransaksi'])->name('transaksi.destroy');

    Route::post('/kategori', [WebCrudController::class, 'storeKategori'])->name('kategori.store');
    Route::put('/kategori/{category}', [WebCrudController::class, 'updateKategori'])->name('kategori.update');
    Route::delete('/kategori/{category}', [WebCrudController::class, 'destroyKategori'])->name('kategori.destroy');

    Route::post('/anggaran', [WebCrudController::class, 'storeAnggaran'])->name('anggaran.store');
    Route::put('/anggaran/{budget}', [WebCrudController::class, 'updateAnggaran'])->name('anggaran.update');
    Route::delete('/anggaran/{budget}', [WebCrudController::class, 'destroyAnggaran'])->name('anggaran.destroy');

    Route::post('/tabungan', [WebCrudController::class, 'storeTabungan'])->name('tabungan.store');
    Route::put('/tabungan/{savingGoal}', [WebCrudController::class, 'updateTabungan'])->name('tabungan.update');
    Route::delete('/tabungan/{savingGoal}', [WebCrudController::class, 'destroyTabungan'])->name('tabungan.destroy');
    Route::post('/tabungan/{savingGoal}/add-fund', [WebCrudController::class, 'addFund'])->name('tabungan.add-fund');

    Route::post('/dompet', [WebCrudController::class, 'storeDompet'])->name('dompet.store');
    Route::put('/dompet/{account}', [WebCrudController::class, 'updateDompet'])->name('dompet.update');
    Route::delete('/dompet/{account}', [WebCrudController::class, 'destroyDompet'])->name('dompet.destroy');

    Route::get('/laporan/data/monthly', [WebPageController::class, 'reportMonthly'])->name('laporan.monthly');
    Route::get('/laporan/data/categories', [WebPageController::class, 'reportCategories'])->name('laporan.categories');
    Route::get('/laporan/data/trend', [WebPageController::class, 'reportTrend'])->name('laporan.trend');
    Route::get('/laporan/export', [ReportController::class, 'export'])->name('laporan.export');

    Route::post('/upload', [WebCrudController::class, 'uploadFile'])->name('upload.file');

    Route::put('/pengaturan/settings', [WebPageController::class, 'updateSettings'])->name('pengaturan.settings.update');

    Route::patch('/notifikasi/{pending_notification}/approve', [WebPageController::class, 'approveNotification'])->name('notifikasi.approve');
    Route::delete('/notifikasi/{pending_notification}/reject', [WebPageController::class, 'rejectNotification'])->name('notifikasi.reject');

    Route::post('/pengaturan/fetch-emails', [WebPageController::class, 'fetchEmails'])->name('pengaturan.fetch-emails');

    Route::patch('/transaksi/pending/{transaction}/approve', [WebPageController::class, 'approvePendingTransaction'])->name('transaksi.pending.approve');
    Route::delete('/transaksi/pending/{transaction}/reject', [WebPageController::class, 'rejectPendingTransaction'])->name('transaksi.pending.reject');
});

require __DIR__.'/auth.php';
