@extends('layouts.app')

@section('title', 'Transaksi - Finarus')
@section('page-title', 'Manajemen Transaksi')
@section('page-description', 'Kelola dan pantau semua transaksi keuangan Anda')

@section('page-actions')
<button onclick="openModal()" class="w-full sm:w-auto h-9 text-sm bg-primary text-primary-foreground hover:bg-primary/90 transition-all duration-300 px-4 rounded-md font-medium flex items-center justify-center gap-2">+ Tambah Transaksi</button>
@endsection

@section('content')
<div class="bg-card rounded-lg shadow-lg p-5">
    <form method="GET" action="{{ route('transaksi') }}" class="flex flex-col md:flex-row gap-4 mb-5">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari transaksi..." class="flex-1 h-9 px-3 rounded-md border border-border bg-background text-foreground">
        <select name="type" class="h-9 px-3 rounded-md border border-border bg-card text-foreground" onchange="this.form.submit()">
            <option value="">Semua Tipe</option><option value="income" {{ request('type')==='income'?'selected':'' }}>Pemasukan</option><option value="expense" {{ request('type')==='expense'?'selected':'' }}>Pengeluaran</option>
        </select>
        <div class="flex gap-2">
            <button type="submit" class="h-9 px-4 rounded-md bg-primary text-primary-foreground hover:bg-primary/90 transition-colors text-sm font-medium">Cari</button>
            @if(request('search')||request('type'))<a href="{{ route('transaksi') }}" class="h-9 px-4 rounded-md border border-border hover:bg-muted transition-colors text-sm font-medium flex items-center">Reset</a>@endif
        </div>
    </form>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="border-b border-border"><tr><th class="text-left py-3 px-4 font-semibold text-sm">Deskripsi</th><th class="text-left py-3 px-4 font-semibold text-sm">Kategori</th><th class="text-left py-3 px-4 font-semibold text-sm">Tipe</th><th class="text-left py-3 px-4 font-semibold text-sm">Tanggal</th><th class="text-right py-3 px-4 font-semibold text-sm">Jumlah</th><th class="text-center py-3 px-4 font-semibold text-sm">Aksi</th></tr></thead>
            <tbody>
                @forelse($transactions as $txn)
                <tr class="border-b border-border hover:bg-muted transition-colors">
                    <td class="py-3 px-4 text-foreground">{{ $txn->description ?? '-' }}</td>
                    <td class="py-3 px-4"><span class="bg-orange-100 text-orange-700 dark:bg-orange-900/20 dark:text-orange-400 px-2 py-1 rounded text-xs">{{ $txn->category?->name ?? '-' }}</span></td>
                    <td class="py-3 px-4"><span class="{{ $txn->type==='income'?'bg-green-100 text-green-700 dark:bg-green-900/20 dark:text-green-400':'bg-red-100 text-red-700 dark:bg-red-900/20 dark:text-red-400' }} px-2 py-1 rounded text-xs">{{ $txn->type==='income'?'Pemasukan':'Pengeluaran' }}</span></td>
                    <td class="py-3 px-4 text-sm text-muted-foreground">{{ $txn->transaction_date->format('d M Y') }}</td>
                    <td class="py-3 px-4 text-right font-semibold {{ $txn->type==='income'?'text-green-500':'text-foreground' }}">{{ $txn->type==='income'?'+':'-' }}Rp {{ number_format($txn->amount,0,',','.') }}</td>
                    <td class="py-3 px-4 text-center">
                        <button onclick="editTxn(this)" data-id="{{ $txn->id }}" data-desc="{{ $txn->description }}" data-amount="{{ $txn->amount }}" data-type="{{ $txn->type }}" data-date="{{ $txn->transaction_date->format('Y-m-d') }}" data-cat="{{ $txn->category_id }}" data-acc="{{ $txn->account_id }}" class="text-primary hover:text-primary/80 text-xs font-medium">Edit</button>
                        <span class="mx-1 text-muted-foreground">|</span>
                        <form method="POST" action="{{ route('transaksi.destroy', $txn) }}" class="inline" onsubmit="if(!confirm('Hapus?'))return false;var b=this.querySelector('button');b.disabled=true;b.textContent='...'">@csrf @method('DELETE')<button class="text-red-500 hover:text-red-700 text-xs font-medium">Hapus</button></form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="py-8 text-center text-muted-foreground">{{ request('search')||request('type')?'Tidak ada yang cocok.':'Belum ada transaksi' }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4 flex justify-center">{{ $transactions->withQueryString()->links() }}</div>
</div>

<dialog id="modal-txn" class="p-0 rounded-lg shadow-xl backdrop:bg-black/50 max-w-md w-full bg-card text-foreground">
    <form id="form-txn" method="POST" action="{{ route('transaksi.store') }}" class="p-6" onsubmit="var b=this.querySelector('button[type=submit]');b.disabled=true;b.innerHTML='⟳ Menyimpan...';b.classList.add('opacity-70')">
        @csrf
        <input type="hidden" name="_method" value="POST" id="txn-method">
        <div class="space-y-4">
            <h2 class="text-lg font-bold text-foreground" id="txn-title">Tambah Transaksi</h2>
            <div><label class="block text-sm font-medium mb-1">Deskripsi</label><input type="text" name="description" id="txn-desc" value="{{ old('description') }}" required placeholder="Contoh: Belanja Bulanan" maxlength="1000" class="w-full h-9 px-3 rounded-md border border-border bg-background text-foreground"><x-input-error :messages="$errors->get('description')" class="text-sm mt-1" /></div>
            <div><label class="block text-sm font-medium mb-1">Kategori</label><select name="category_id" id="txn-cat" required class="w-full h-9 px-3 rounded-md border border-border bg-card text-foreground"><option value="">Pilih</option>@foreach($categories as $cat)<option value="{{ $cat->id }}" {{ old('category_id')==$cat->id?'selected':'' }}>{{ $cat->icon }} {{ $cat->name }}</option>@endforeach</select><x-input-error :messages="$errors->get('category_id')" class="text-sm mt-1" /></div>
            <div><label class="block text-sm font-medium mb-1">Akun</label><select name="account_id" id="txn-acc" required class="w-full h-9 px-3 rounded-md border border-border bg-card text-foreground"><option value="">Pilih Akun</option>@foreach($accounts as $acc)<option value="{{ $acc->id }}" {{ old('account_id')==$acc->id?'selected':'' }}>{{ $acc->name }}</option>@endforeach</select><x-input-error :messages="$errors->get('account_id')" class="text-sm mt-1" /></div>
            <div><label class="block text-sm font-medium mb-1">Tipe</label><select name="type" id="txn-type" required class="w-full h-9 px-3 rounded-md border border-border bg-card text-foreground"><option value="expense" {{ old('type')!=='income'?'selected':'' }}>Pengeluaran</option><option value="income" {{ old('type')==='income'?'selected':'' }}>Pemasukan</option></select><x-input-error :messages="$errors->get('type')" class="text-sm mt-1" /></div>
            <div><label class="block text-sm font-medium mb-1">Jumlah (Rp)</label><input type="number" name="amount" id="txn-amount" value="{{ old('amount') }}" required min="1" step="0.01" placeholder="50000" class="w-full h-9 px-3 rounded-md border border-border bg-background text-foreground"><x-input-error :messages="$errors->get('amount')" class="text-sm mt-1" /></div>
            <div><label class="block text-sm font-medium mb-1">Tanggal</label><input type="date" name="transaction_date" id="txn-date" value="{{ old('transaction_date') }}" required class="w-full h-9 px-3 rounded-md border border-border bg-background text-foreground"><x-input-error :messages="$errors->get('transaction_date')" class="text-sm mt-1" /></div>
            <div class="flex gap-2 justify-end pt-2">
                <button type="button" onclick="document.getElementById('modal-txn').close()" class="h-9 px-4 rounded-md border border-border hover:bg-muted transition-colors text-sm font-medium">Batal</button>
                <button type="submit" class="h-9 px-4 rounded-md bg-primary text-primary-foreground hover:bg-primary/90 transition-colors text-sm font-medium">Simpan</button>
            </div>
        </div>
    </form>
</dialog>
@endsection

@push('scripts')
<script>
function openModal() {
    var f = document.getElementById('form-txn'); f.action = '{{ route("transaksi.store") }}';
    document.getElementById('txn-method').value = 'POST';
    document.getElementById('txn-desc').value = ''; document.getElementById('txn-cat').value = ''; document.getElementById('txn-acc').value = '';
    document.getElementById('txn-type').value = 'expense'; document.getElementById('txn-amount').value = ''; document.getElementById('txn-date').value = new Date().toISOString().split('T')[0];
    document.getElementById('txn-title').textContent = 'Tambah Transaksi';
    document.getElementById('modal-txn').showModal();
}
function editTxn(btn) {
    var d = btn.dataset;
    var f = document.getElementById('form-txn'); f.action = '{{ url("transaksi") }}/' + d.id;
    document.getElementById('txn-method').value = 'PUT';
    document.getElementById('txn-desc').value = d.desc; document.getElementById('txn-cat').value = d.cat||''; document.getElementById('txn-acc').value = d.acc||'';
    document.getElementById('txn-type').value = d.type; document.getElementById('txn-amount').value = d.amount; document.getElementById('txn-date').value = d.date;
    document.getElementById('txn-title').textContent = 'Edit Transaksi';
    document.getElementById('modal-txn').showModal();
}
@if($errors->any())
document.addEventListener('DOMContentLoaded', function(){
    @if(old('_method')==='PUT' && request()->route('transaction'))
    document.getElementById('form-txn').action = '{{ url("transaksi") }}/{{ request()->route("transaction")->id }}';
    document.getElementById('txn-method').value = 'PUT'; document.getElementById('txn-title').textContent = 'Edit Transaksi';
    @endif
    document.getElementById('modal-txn').showModal();
});
@endif
</script>
@endpush
