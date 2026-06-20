@extends('layouts.app')

@section('title', 'Dasbor - FinFlow')
@section('page-title', 'Dasbor Keuangan')
@section('page-description', 'Selamat datang! Berikut adalah ringkasan keuangan Anda bulan ini.')

@section('page-actions')
<button class="w-full sm:w-auto h-9 text-sm bg-primary text-primary-foreground hover:bg-primary/90 transition-all duration-300 hover:shadow-lg hover:shadow-primary/30 hover:scale-105 px-4 rounded-md font-medium flex items-center justify-center gap-2">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
    Tambah Transaksi
</button>
@endsection

@section('content')
<!-- Stats Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
    <!-- Card 1: Saldo Total -->
    <div class="bg-primary text-primary-foreground p-5 rounded-lg shadow-lg hover:shadow-2xl hover:scale-105 transition-all duration-500 ease-out">
        <div class="flex items-start justify-between mb-4">
            <h3 class="text-xs font-semibold opacity-90 tracking-wider">Saldo Total</h3>
            <div class="w-8 h-8 rounded-lg bg-primary-foreground/20 flex items-center justify-center">
                <svg class="w-4 h-4 text-primary-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>
        <p class="text-2xl sm:text-3xl font-bold mb-3">Rp 12.459.500</p>
        <div class="flex items-center gap-1.5 text-xs opacity-80">
            <svg class="w-3 h-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            <span>+5.2% dari bulan lalu</span>
        </div>
    </div>

    <!-- Card 2: Pemasukan -->
    <div class="bg-secondary text-secondary-foreground p-5 rounded-lg shadow-lg hover:shadow-2xl hover:scale-105 transition-all duration-500 ease-out">
        <div class="flex items-start justify-between mb-4">
            <h3 class="text-xs font-semibold opacity-90 tracking-wider">Pemasukan Bulan Ini</h3>
            <div class="w-8 h-8 rounded-lg bg-primary/20 flex items-center justify-center">
                <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            </div>
        </div>
        <p class="text-2xl sm:text-3xl font-bold mb-3">Rp 4.250.000</p>
        <div class="flex items-center gap-1.5 text-xs opacity-80">
            <svg class="w-3 h-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            <span>+12% dari bulan lalu</span>
        </div>
    </div>

    <!-- Card 3: Pengeluaran -->
    <div class="bg-card text-foreground p-5 rounded-lg shadow-lg hover:shadow-2xl hover:scale-105 transition-all duration-500 ease-out">
        <div class="flex items-start justify-between mb-4">
            <h3 class="text-xs font-semibold opacity-90 tracking-wider">Pengeluaran Bulan Ini</h3>
            <div class="w-8 h-8 rounded-lg bg-primary/20 flex items-center justify-center">
                <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 14l-1-1H7m0 0L5 9m0 0l7-4 7 4m-9 3l-5-3"></path></svg>
            </div>
        </div>
        <p class="text-2xl sm:text-3xl font-bold mb-3">Rp 2.180.750</p>
        <div class="flex items-center gap-1.5 text-xs opacity-80">
            <svg class="w-3 h-3 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 14l1 1h6m0 0l2 -3m0 0l-7 4 -7 -4m9 -3l5 3"></path></svg>
            <span>-3% dari bulan lalu</span>
        </div>
    </div>

    <!-- Card 4: Tujuan Tabungan -->
    <div class="bg-card text-foreground p-5 rounded-lg shadow-lg hover:shadow-2xl hover:scale-105 transition-all duration-500 ease-out">
        <div class="flex items-start justify-between mb-4">
            <h3 class="text-xs font-semibold opacity-90 tracking-wider">Tujuan Tabungan Aktif</h3>
            <div class="w-8 h-8 rounded-lg bg-primary/20 flex items-center justify-center">
                <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            </div>
        </div>
        <p class="text-2xl sm:text-3xl font-bold mb-3">3</p>
        <div class="flex items-center gap-1.5 text-xs opacity-80">
            <span>Berjalan lancar</span>
        </div>
    </div>
</div>

<!-- Recent Transactions & Widgets -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-3 md:gap-4">
    <!-- Recent Transactions -->
    <div class="lg:col-span-2 bg-card rounded-lg shadow-lg p-5">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="text-lg font-semibold text-foreground">Transaksi Terbaru</h3>
                <p class="text-xs text-muted-foreground mt-1">5 transaksi terbaru Anda</p>
            </div>
        </div>
        
        <div class="space-y-3">
            <!-- Transaction Item 1 -->
            <div class="flex items-center justify-between py-3 border-b border-border">
                <div class="flex items-center gap-3">
                    <div class="p-2.5 bg-orange-100 dark:bg-orange-900/20 rounded-lg">
                        <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1m-16 0H1m15.364 1.636l.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-foreground">Kopi Starbucks</p>
                        <p class="text-xs text-muted-foreground">Hari ini pukul 09:30</p>
                    </div>
                </div>
                <p class="text-sm font-semibold text-foreground">-Rp 55.000</p>
            </div>

            <!-- Transaction Item 2 -->
            <div class="flex items-center justify-between py-3 border-b border-border">
                <div class="flex items-center gap-3">
                    <div class="p-2.5 bg-green-100 dark:bg-green-900/20 rounded-lg">
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-foreground">Pembayaran Proyek Freelance</p>
                        <p class="text-xs text-muted-foreground">Kemarin pukul 14:15</p>
                    </div>
                </div>
                <p class="text-sm font-semibold text-green-500">+Rp 1.250.000</p>
            </div>

            <!-- Transaction Item 3 -->
            <div class="flex items-center justify-between py-3 border-b border-border">
                <div class="flex items-center gap-3">
                    <div class="p-2.5 bg-blue-100 dark:bg-blue-900/20 rounded-lg">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-foreground">Belanja Online</p>
                        <p class="text-xs text-muted-foreground">2 hari lalu</p>
                    </div>
                </div>
                <p class="text-sm font-semibold text-foreground">-Rp 874.500</p>
            </div>

            <!-- Transaction Item 4 -->
            <div class="flex items-center justify-between py-3 border-b border-border">
                <div class="flex items-center gap-3">
                    <div class="p-2.5 bg-yellow-100 dark:bg-yellow-900/20 rounded-lg">
                        <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-foreground">Tagihan Listrik</p>
                        <p class="text-xs text-muted-foreground">3 hari lalu</p>
                    </div>
                </div>
                <p class="text-sm font-semibold text-foreground">-Rp 1.250.000</p>
            </div>

            <!-- Transaction Item 5 -->
            <div class="flex items-center justify-between py-3">
                <div class="flex items-center gap-3">
                    <div class="p-2.5 bg-red-100 dark:bg-red-900/20 rounded-lg">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-foreground">Makan di Restoran</p>
                        <p class="text-xs text-muted-foreground">5 hari lalu</p>
                    </div>
                </div>
                <p class="text-sm font-semibold text-foreground">-Rp 623.000</p>
            </div>
        </div>

        <button class="w-full mt-4 py-2 text-sm font-medium text-primary hover:bg-primary/5 rounded-lg transition-colors duration-300">
            Lihat Semua Transaksi
        </button>
    </div>

    <!-- Sidebar Widgets -->
    <div class="space-y-3 md:space-y-4">
        <!-- Budget Progress -->
        <div class="bg-card rounded-lg shadow-lg p-5">
            <div>
                <h3 class="text-lg font-semibold text-foreground mb-1">Progres Anggaran</h3>
                <p class="text-xs text-muted-foreground mb-5">Ringkasan pengeluaran bulan ini</p>
            </div>
            
            <div class="space-y-4">
                <!-- Budget Item 1 -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-semibold">Makanan</span>
                        <span class="text-sm font-semibold">Rp 240 / Rp 400</span>
                    </div>
                    <div class="w-full bg-muted rounded-full h-2">
                        <div class="bg-orange-500 h-2 rounded-full" style="width: 60%"></div>
                    </div>
                </div>

                <!-- Budget Item 2 -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-semibold">Belanja</span>
                        <span class="text-sm font-semibold">Rp 180 / Rp 300</span>
                    </div>
                    <div class="w-full bg-muted rounded-full h-2">
                        <div class="bg-blue-500 h-2 rounded-full" style="width: 60%"></div>
                    </div>
                </div>

                <!-- Budget Item 3 -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-semibold">Utilitas</span>
                        <span class="text-sm font-semibold">Rp 125 / Rp 150</span>
                    </div>
                    <div class="w-full bg-muted rounded-full h-2">
                        <div class="bg-yellow-500 h-2 rounded-full" style="width: 83%"></div>
                    </div>
                </div>

                <!-- Budget Item 4 -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-semibold">Hiburan</span>
                        <span class="text-sm font-semibold">Rp 95 / Rp 200</span>
                    </div>
                    <div class="w-full bg-muted rounded-full h-2">
                        <div class="bg-purple-500 h-2 rounded-full" style="width: 48%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Saving Goals Progress -->
        <div class="bg-card rounded-lg shadow-lg p-5">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="text-lg font-semibold text-foreground">Progres Tabungan</h3>
                    <p class="text-xs text-muted-foreground mt-1">3 tujuan aktif</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-secondary flex items-center justify-center">
                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
            </div>

            <div class="space-y-4">
                <!-- Goal 1 -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-semibold">Liburan ke Bali</span>
                        <span class="text-xs text-muted-foreground">64%</span>
                    </div>
                    <div class="w-full bg-muted rounded-full h-2">
                        <div class="bg-blue-500 h-2 rounded-full" style="width: 64%"></div>
                    </div>
                    <div class="flex items-center justify-between text-xs text-muted-foreground mt-1">
                        <span>Rp 3,200,000</span>
                        <span>Rp 5,000,000</span>
                    </div>
                </div>

                <!-- Goal 2 -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-semibold">Beli Laptop Baru</span>
                        <span class="text-xs text-muted-foreground">75%</span>
                    </div>
                    <div class="w-full bg-muted rounded-full h-2">
                        <div class="bg-purple-500 h-2 rounded-full" style="width: 75%"></div>
                    </div>
                    <div class="flex items-center justify-between text-xs text-muted-foreground mt-1">
                        <span>Rp 1,500,000</span>
                        <span>Rp 2,000,000</span>
                    </div>
                </div>

                <!-- Goal 3 -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-semibold">Dana Darurat</span>
                        <span class="text-xs text-muted-foreground">78%</span>
                    </div>
                    <div class="w-full bg-muted rounded-full h-2">
                        <div class="bg-green-500 h-2 rounded-full" style="width: 78%"></div>
                    </div>
                    <div class="flex items-center justify-between text-xs text-muted-foreground mt-1">
                        <span>Rp 7,850,000</span>
                        <span>Rp 10,000,000</span>
                    </div>
                </div>
            </div>

            <button class="w-full mt-4 py-2 text-sm font-medium text-primary hover:bg-primary/5 rounded-lg transition-colors duration-300">
                Tambah Tujuan Baru
            </button>
        </div>
    </div>
</div>
@endsection
