@extends('layouts.app')

@section('title', 'Dasbor - Finarus')
@section('page-title', 'Dasbor Keuangan')
@section('page-description', 'Selamat datang! Berikut adalah ringkasan keuangan Anda bulan ini.')

@section('page-actions')
<a href="{{ route('transaksi') }}" class="w-full sm:w-auto h-9 text-sm bg-primary text-primary-foreground hover:bg-primary/90 transition-all duration-300 hover:shadow-lg hover:shadow-primary/30 hover:scale-105 px-4 rounded-md font-medium flex items-center justify-center gap-2">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
    Tambah Transaksi
</a>
@endsection

@section('content')
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
    <div class="bg-primary text-primary-foreground p-5 rounded-lg shadow-lg hover:shadow-2xl hover:scale-105 transition-all duration-500 ease-out">
        <div class="flex items-start justify-between mb-4">
            <h3 class="text-xs font-semibold opacity-90 tracking-wider">Saldo Total</h3>
            <div class="w-8 h-8 rounded-lg bg-primary-foreground/20 flex items-center justify-center">
                <svg class="w-4 h-4 text-primary-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>
        <p class="text-2xl sm:text-3xl font-bold mb-3">Rp {{ number_format($balance, 0, ',', '.') }}</p>
        <div class="flex items-center gap-1.5 text-xs opacity-80">
            <svg class="w-3 h-3 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            <span>Total saldo dari semua akun</span>
        </div>
    </div>

    <div class="bg-secondary text-secondary-foreground p-5 rounded-lg shadow-lg hover:shadow-2xl hover:scale-105 transition-all duration-500 ease-out">
        <div class="flex items-start justify-between mb-4">
            <h3 class="text-xs font-semibold opacity-90 tracking-wider">Pemasukan Bulan Ini</h3>
            <div class="w-8 h-8 rounded-lg bg-primary/20 flex items-center justify-center">
                <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            </div>
        </div>
        <p class="text-2xl sm:text-3xl font-bold mb-3">Rp {{ number_format($totalIncome, 0, ',', '.') }}</p>
        <div class="flex items-center gap-1.5 text-xs opacity-80">
            <svg class="w-3 h-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            <span>Pemasukan bulan ini</span>
        </div>
    </div>

    <div class="bg-card text-foreground p-5 rounded-lg shadow-lg hover:shadow-2xl hover:scale-105 transition-all duration-500 ease-out">
        <div class="flex items-start justify-between mb-4">
            <h3 class="text-xs font-semibold opacity-90 tracking-wider">Pengeluaran Bulan Ini</h3>
            <div class="w-8 h-8 rounded-lg bg-primary/20 flex items-center justify-center">
                <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 14l-1-1H7m0 0L5 9m0 0l7-4 7 4m-9 3l-5-3"></path></svg>
            </div>
        </div>
        <p class="text-2xl sm:text-3xl font-bold mb-3">Rp {{ number_format($totalExpense, 0, ',', '.') }}</p>
        <div class="flex items-center gap-1.5 text-xs opacity-80">
            <svg class="w-3 h-3 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 14l1 1h6m0 0l2-3m0 0l-7 4-7-4m9-3l5 3"></path></svg>
            <span>Pengeluaran bulan ini</span>
        </div>
    </div>

    <div class="bg-card text-foreground p-5 rounded-lg shadow-lg hover:shadow-2xl hover:scale-105 transition-all duration-500 ease-out">
        <div class="flex items-start justify-between mb-4">
            <h3 class="text-xs font-semibold opacity-90 tracking-wider">Tujuan Tabungan Aktif</h3>
            <div class="w-8 h-8 rounded-lg bg-primary/20 flex items-center justify-center">
                <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            </div>
        </div>
        <p class="text-2xl sm:text-3xl font-bold mb-3">{{ $activeSavingGoals }}</p>
        <div class="flex items-center gap-1.5 text-xs opacity-80">
            <span>Berjalan lancar</span>
        </div>
    </div>
</div>

@if($budgetAlerts->isNotEmpty())
<div class="space-y-2">
    @foreach($budgetAlerts as $alert)
    <div class="flex items-center gap-3 {{ $alert->is_over_budget ? 'bg-red-50 border border-red-200 dark:bg-red-900/10 dark:border-red-900/30' : 'bg-amber-50 border border-amber-200 dark:bg-amber-900/10 dark:border-amber-900/30' }} rounded-lg p-3">
        <span class="text-lg">{{ $alert->is_over_budget ? '🔴' : '🟡' }}</span>
        <div class="flex-1">
            <p class="text-sm font-medium text-foreground">
                {{ $alert->category->icon }} {{ $alert->category->name }}:
                {{ $alert->is_over_budget ? 'Melebihi anggaran!' : 'Hampir mencapai batas' }}
            </p>
            <p class="text-xs text-muted-foreground">
                Rp {{ number_format($alert->spent, 0, ',', '.') }} dari Rp {{ number_format($alert->amount, 0, ',', '.') }} ({{ $alert->progress }}%)
            </p>
        </div>
        <a href="{{ route('anggaran') }}" class="text-xs font-medium text-primary hover:underline whitespace-nowrap">Kelola</a>
    </div>
    @endforeach
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-3 md:gap-4">
    <div class="lg:col-span-2 bg-card rounded-lg shadow-lg p-5">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="text-lg font-semibold text-foreground">Transaksi Terbaru</h3>
                <p class="text-xs text-muted-foreground mt-1">5 transaksi terbaru Anda</p>
            </div>
        </div>

        <div class="space-y-3">
            @forelse($recentTransactions as $txn)
            <div class="flex items-center justify-between py-3 border-b border-border">
                <div class="flex items-center gap-3">
                    <div class="p-2.5 {{ $txn->type === 'income' ? 'bg-green-100 dark:bg-green-900/20' : 'bg-orange-100 dark:bg-orange-900/20' }} rounded-lg">
                        @if($txn->category)
                        <span class="text-lg">{{ $txn->category->icon ?? '📝' }}</span>
                        @else
                        <svg class="w-5 h-5 {{ $txn->type === 'income' ? 'text-green-500' : 'text-orange-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        @endif
                    </div>
                    <div>
                        <p class="text-sm font-medium text-foreground">{{ $txn->description ?? ($txn->category ? $txn->category->name : '-') }}</p>
                        <p class="text-xs text-muted-foreground">{{ $txn->transaction_date->format('d M Y') }}</p>
                    </div>
                </div>
                <p class="text-sm font-semibold {{ $txn->type === 'income' ? 'text-green-500' : 'text-foreground' }}">{{ $txn->type === 'income' ? '+' : '-' }}Rp {{ number_format($txn->amount, 0, ',', '.') }}</p>
            </div>
            @empty
            <p class="text-center text-muted-foreground py-4">Belum ada transaksi</p>
            @endforelse
        </div>

        <a href="{{ route('transaksi') }}" class="block w-full mt-4 py-2 text-sm font-medium text-primary hover:bg-primary/5 rounded-lg transition-colors duration-300 text-center">
            Lihat Semua Transaksi
        </a>
    </div>

    <div class="space-y-3 md:space-y-4">
        <div class="bg-card rounded-lg shadow-lg p-5">
            <div>
                <h3 class="text-lg font-semibold text-foreground mb-1">Progres Anggaran</h3>
                <p class="text-xs text-muted-foreground mb-5">Ringkasan pengeluaran bulan ini</p>
            </div>

            <div class="space-y-4">
                @forelse($budgetProgress as $budget)
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-semibold">{{ $budget->category->name }}</span>
                        <span class="text-sm font-semibold">Rp {{ number_format($budget->spent, 0, ',', '.') }} / Rp {{ number_format($budget->amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="w-full bg-muted rounded-full h-2">
                        <div class="{{ $budget->is_over_budget ? 'bg-red-500' : 'bg-blue-500' }} h-2 rounded-full" style="width: {{ min(100, $budget->progress) }}%"></div>
                    </div>
                </div>
                @empty
                <p class="text-center text-muted-foreground py-4">Belum ada anggaran</p>
                @endforelse
            </div>
        </div>

        <div class="bg-card rounded-lg shadow-lg p-5">
            <div>
                <h3 class="text-lg font-semibold text-foreground mb-1">Progres Tabungan</h3>
                <p class="text-xs text-muted-foreground mt-1">{{ $activeSavingGoals }} tujuan aktif</p>
            </div>

            <div class="space-y-4 mt-4">
                @php $savingGoals = auth()->user()->savingGoals->take(3); @endphp
                @forelse($savingGoals as $goal)
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-semibold">{{ $goal->name }}</span>
                        <span class="text-xs text-muted-foreground">{{ $goal->progress }}%</span>
                    </div>
                    <div class="w-full bg-muted rounded-full h-2">
                        <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $goal->progress }}%"></div>
                    </div>
                    <div class="flex items-center justify-between text-xs text-muted-foreground mt-1">
                        <span>Rp {{ number_format($goal->current_amount, 0, ',', '.') }}</span>
                        <span>Rp {{ number_format($goal->target_amount, 0, ',', '.') }}</span>
                    </div>
                </div>
                @empty
                <p class="text-center text-muted-foreground py-4">Belum ada tujuan tabungan</p>
                @endforelse
            </div>

            <a href="{{ route('tabungan') }}" class="block w-full mt-4 py-2 text-sm font-medium text-primary hover:bg-primary/5 rounded-lg transition-colors duration-300 text-center">
                Tambah Tujuan Baru
            </a>
        </div>
    </div>
</div>
@endsection