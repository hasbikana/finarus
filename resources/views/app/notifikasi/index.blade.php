@extends('layouts.app')

@section('title', 'Notifikasi - Finarus')
@section('page-title', 'Notifikasi Otomatis')
@section('page-description', 'Konfirmasi transaksi dari notifikasi dan OCR sebelum tersimpan')

@section('content')
<div class="bg-card rounded-lg shadow-lg p-5">

    @if(session('success'))
    <div class="mb-4 px-4 py-3 rounded-md bg-green-100 dark:bg-green-900/20 text-green-700 dark:text-green-400 text-sm">{{ session('success') }}</div>
    @endif

    @if(session('error'))
    <div class="mb-4 px-4 py-3 rounded-md bg-red-100 dark:bg-red-900/20 text-red-700 dark:text-red-400 text-sm">{{ session('error') }}</div>
    @endif

    @if($errors->any())
    <div class="mb-4 px-4 py-3 rounded-md bg-red-100 dark:bg-red-900/20 text-red-700 dark:text-red-400 text-sm">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @forelse($notifications as $notif)
    <div class="border border-border rounded-lg p-4 mb-3">
        <div class="flex items-start justify-between gap-4">
            <div class="flex-1">
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-xs font-medium px-2 py-0.5 rounded {{ $notif->source === 'push_notif' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400' : 'bg-purple-100 text-purple-700 dark:bg-purple-900/20 dark:text-purple-400' }}">
                        {{ $notif->source === 'push_notif' ? 'Notif HP' : 'OCR' }}
                    </span>
                    <span class="text-xs font-medium px-2 py-0.5 rounded {{ $notif->type === 'income' ? 'bg-green-100 text-green-700 dark:bg-green-900/20 dark:text-green-400' : 'bg-red-100 text-red-700 dark:bg-red-900/20 dark:text-red-400' }}">
                        {{ $notif->type === 'income' ? 'Pemasukan' : 'Pengeluaran' }}
                    </span>
                    @if($notif->image_path)
                    <span class="text-xs text-muted-foreground">📷 Ada gambar</span>
                    @endif
                </div>
                <p class="font-semibold text-lg {{ $notif->type === 'income' ? 'text-green-500' : 'text-foreground' }}">
                    {{ $notif->type === 'income' ? '+' : '-' }}Rp {{ number_format($notif->amount, 0, ',', '.') }}
                </p>
                @if($notif->merchant)
                <p class="text-sm text-muted-foreground">{{ $notif->merchant }}</p>
                @endif
                @if($notif->description)
                <p class="text-xs text-muted-foreground mt-0.5">{{ $notif->description }}</p>
                @endif
                @if($notif->notification_date)
                <p class="text-xs text-muted-foreground mt-0.5">{{ $notif->notification_date->format('d M Y') }}</p>
                @endif
                @if($notif->raw_body)
                <details class="mt-2">
                    <summary class="text-xs text-muted-foreground cursor-pointer hover:text-foreground">Lihat teks asli</summary>
                    <pre class="mt-1 p-2 bg-muted rounded text-xs overflow-x-auto">{{ $notif->raw_body }}</pre>
                </details>
                @endif
            </div>
            @if($notif->image_path)
            <div class="shrink-0">
                <a href="{{ asset('storage/' . $notif->image_path) }}" target="_blank" class="block w-16 h-16 rounded-lg overflow-hidden border border-border">
                    <img src="{{ asset('storage/' . $notif->image_path) }}" class="w-full h-full object-cover" alt="Gambar struk">
                </a>
            </div>
            @endif
        </div>

        <form method="POST" action="{{ route('notifikasi.approve', $notif) }}" class="mt-4 border-t border-border pt-4">
            @csrf @method('PATCH')
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                <select name="category_id" required class="h-9 px-3 rounded-md border border-border bg-background text-foreground text-sm">
                    <option value="">Pilih Kategori</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ $cat->type === 'income' && $notif->type === 'income' ? 'selected' : ($cat->type !== 'income' && $notif->type === 'expense' ? 'selected' : '') }}>
                        {{ $cat->icon ?? '' }} {{ $cat->name }}
                    </option>
                    @endforeach
                </select>
                <select name="account_id" required class="h-9 px-3 rounded-md border border-border bg-background text-foreground text-sm">
                    <option value="">Pilih Akun</option>
                    @foreach($accounts as $acc)
                    <option value="{{ $acc->id }}">{{ $acc->name }} (Rp {{ number_format($acc->balance, 0, ',', '.') }})</option>
                    @endforeach
                </select>
            </div>
            <input type="text" name="description" placeholder="Deskripsi (opsional)" value="{{ $notif->merchant ?? $notif->description }}" class="w-full h-9 px-3 rounded-md border border-border bg-background text-foreground text-sm mb-3">
            <div class="flex gap-2">
                <button type="submit" class="h-9 px-4 rounded-md bg-primary text-primary-foreground hover:bg-primary/90 transition-colors text-sm font-medium">Setuju & Simpan</button>
                <button type="button" onclick="document.getElementById('reject-{{ $notif->id }}').submit()" class="h-9 px-4 rounded-md border border-red-300 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors text-sm font-medium">Tolak</button>
            </div>
        </form>
        <form id="reject-{{ $notif->id }}" method="POST" action="{{ route('notifikasi.reject', $notif) }}" class="hidden">@csrf @method('DELETE')</form>
    </div>
    @empty
    <div class="text-center py-10">
        <p class="text-muted-foreground">Tidak ada notifikasi yang menunggu konfirmasi</p>
    </div>
    @endforelse

    <div class="mt-4">
        {{ $notifications->links() }}
    </div>
</div>
@endsection
