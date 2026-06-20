@extends('layouts.app')

@section('title', 'Anggaran - Finarus')
@section('page-title', 'Rencana Anggaran')
@section('page-description', 'Tentukan batas pengeluaran untuk setiap kategori')

@section('page-actions')
<button onclick="openModal()" class="w-full sm:w-auto h-9 text-sm bg-primary text-primary-foreground hover:bg-primary/90 transition-all duration-300 px-4 rounded-md font-medium flex items-center justify-center gap-2">+ Tambah Anggaran</button>
@endsection

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    @forelse($budgets as $b)
    <div class="bg-card rounded-lg shadow-lg p-5 hover:shadow-2xl hover:scale-105 transition-all duration-500 ease-out">
        <h3 class="font-semibold mb-1">{{ $b->category->icon ?? '📋' }} {{ $b->category->name }}</h3>
        <p class="text-xs text-muted-foreground mb-3">{{ \DateTime::createFromFormat('!m',$b->month)->format('F') }} {{ $b->year }}</p>
        <div class="mb-3">
            <div class="flex justify-between text-xs mb-2"><span>Pengeluaran</span><span>Rp {{ number_format($b->spent,0,',','.') }} / Rp {{ number_format($b->amount,0,',','.') }}</span></div>
            <div class="w-full bg-muted rounded-full h-2"><div class="{{ $b->is_over_budget?'bg-red-500':'bg-blue-500' }} h-2 rounded-full" style="width:{{ min(100,$b->progress) }}%"></div></div>
            <div class="mt-1"><span class="text-xs font-medium {{ $b->is_over_budget?'text-red-500':'text-green-500' }}">{{ $b->is_over_budget?'Melebihi':'Berjalan lancar' }}</span></div>
        </div>
        <div class="flex gap-2">
            <button onclick="editBudget({{ $b->id }},{{ $b->category_id }},{{ $b->amount }},{{ $b->month }},{{ $b->year }})" class="flex-1 py-2 text-xs font-medium text-primary hover:bg-primary/5 rounded transition-colors">Edit</button>
            <form method="POST" action="{{ route('anggaran.destroy', $b) }}" class="flex-1" onsubmit="return confirm('Hapus?')">
                @csrf @method('DELETE')
                <button class="w-full py-2 text-xs font-medium text-red-500 hover:bg-red-50 rounded transition-colors">Hapus</button>
            </form>
        </div>
    </div>
    @empty
    <div class="col-span-full bg-card rounded-lg shadow-lg p-8 text-center"><p class="text-muted-foreground">Belum ada anggaran bulan ini</p></div>
    @endforelse
</div>

<dialog id="modal-budget" class="p-0 rounded-lg shadow-xl backdrop:bg-black/50 max-w-md w-full bg-card text-foreground">
    <form id="form-budget" method="POST" action="{{ route('anggaran.store') }}" class="p-6">
        @csrf
        <input type="hidden" name="_method" value="POST" id="budget-method">
        <div class="space-y-4">
            <h2 class="text-lg font-bold text-foreground" id="budget-title">Tambah Anggaran</h2>
            <div><label class="block text-sm font-medium mb-1">Kategori</label><select name="category_id" id="budget-cat" required class="w-full h-9 px-3 rounded-md border border-border bg-card text-foreground"><option value="">Pilih</option>@foreach($expenseCategories as $cat)<option value="{{ $cat->id }}">{{ $cat->icon }} {{ $cat->name }}</option>@endforeach</select></div>
            <div><label class="block text-sm font-medium mb-1">Jumlah Maks (Rp)</label><input type="number" name="amount" id="budget-amount" required min="1" step="1" placeholder="500000" class="w-full h-9 px-3 rounded-md border border-border bg-background text-foreground"></div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-sm font-medium mb-1">Bulan</label><select name="month" id="budget-month" required class="w-full h-9 px-3 rounded-md border border-border bg-card text-foreground">@for($m=1;$m<=12;$m++)<option value="{{ $m }}" {{ now()->month==$m?'selected':'' }}>{{ \DateTime::createFromFormat('!m',$m)->format('M') }}</option>@endfor</select></div>
                <div><label class="block text-sm font-medium mb-1">Tahun</label><input type="number" name="year" id="budget-year" required min="2020" max="2099" value="{{ now()->year }}" class="w-full h-9 px-3 rounded-md border border-border bg-background text-foreground"></div>
            </div>
            <div class="flex gap-2 justify-end pt-2">
                <button type="button" onclick="document.getElementById('modal-budget').close()" class="h-9 px-4 rounded-md border border-border hover:bg-muted transition-colors text-sm font-medium">Batal</button>
                <button type="submit" class="h-9 px-4 rounded-md bg-primary text-primary-foreground hover:bg-primary/90 transition-colors text-sm font-medium">Simpan</button>
            </div>
        </div>
    </form>
</dialog>
@endsection

@push('scripts')
<script>
function openModal() {
    document.getElementById('form-budget').action = '{{ route("anggaran.store") }}';
    document.getElementById('budget-method').value = 'POST';
    document.getElementById('budget-cat').value = ''; document.getElementById('budget-amount').value = '';
    document.getElementById('budget-title').textContent = 'Tambah Anggaran';
    document.getElementById('modal-budget').showModal();
}
function editBudget(id, catId, amount, month, year) {
    document.getElementById('form-budget').action = '{{ url("anggaran") }}/' + id;
    document.getElementById('budget-method').value = 'PUT';
    document.getElementById('budget-cat').value = catId; document.getElementById('budget-amount').value = amount;
    document.getElementById('budget-month').value = month; document.getElementById('budget-year').value = year;
    document.getElementById('budget-title').textContent = 'Edit Anggaran';
    document.getElementById('modal-budget').showModal();
}
</script>
@endpush
