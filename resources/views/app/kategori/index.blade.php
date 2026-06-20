@extends('layouts.app')

@section('title', 'Kategori - Finarus')
@section('page-title', 'Kelola Kategori')
@section('page-description', 'Organisir transaksi dengan kategori kustom')

@section('page-actions')
<button onclick="openModal()" class="w-full sm:w-auto h-9 text-sm bg-primary text-primary-foreground hover:bg-primary/90 transition-all duration-300 px-4 rounded-md font-medium flex items-center justify-center gap-2">+ Tambah Kategori</button>
@endsection

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    @forelse($categories as $cat)
    <div class="bg-card rounded-lg shadow-lg p-5 hover:shadow-2xl hover:scale-105 transition-all duration-500 ease-out">
        <div class="flex items-center gap-3 mb-3">
            <div class="p-3 rounded-lg" style="background-color:{{ $cat->color ? $cat->color.'20' : '#f9731620' }}"><span class="text-xl">{{ $cat->icon ?? '📝' }}</span></div>
            <div><h3 class="font-semibold text-foreground">{{ $cat->name }}</h3><p class="text-xs text-muted-foreground">{{ $cat->transactions_count }} transaksi</p></div>
        </div>
        <div class="flex gap-2">
            <button onclick="editCat({{ $cat->id }},'{{ addslashes($cat->name) }}','{{ $cat->type }}','{{ addslashes($cat->icon??'') }}','{{ addslashes($cat->color??'') }}')" class="flex-1 py-2 text-xs font-medium text-primary hover:bg-primary/5 rounded transition-colors">Edit</button>
            <form method="POST" action="{{ route('kategori.destroy', $cat) }}" class="flex-1" onsubmit="return confirm('Hapus?')">
                @csrf @method('DELETE')
                <button class="w-full py-2 text-xs font-medium text-red-500 hover:bg-red-50 rounded transition-colors">Hapus</button>
            </form>
        </div>
    </div>
    @empty
    <div class="col-span-full text-center py-8 text-muted-foreground">Belum ada kategori</div>
    @endforelse
</div>

<dialog id="modal-cat" class="p-0 rounded-lg shadow-xl backdrop:bg-black/50 max-w-lg w-full bg-card text-foreground">
    <form id="form-cat" method="POST" action="{{ route('kategori.store') }}" class="p-6">
        @csrf
        <input type="hidden" name="_method" value="POST" id="cat-method">
        <input type="hidden" name="icon" id="cat-icon">
        <input type="hidden" name="color" id="cat-color" value="#6366f1">
        <div class="space-y-4">
            <h2 class="text-lg font-bold text-foreground" id="cat-title">Tambah Kategori</h2>
            <div><label class="block text-sm font-medium mb-1">Nama</label><input type="text" name="name" id="cat-name" required placeholder="Contoh: Makanan" class="w-full h-9 px-3 rounded-md border border-border bg-background text-foreground"></div>
            <div><label class="block text-sm font-medium mb-1">Tipe</label><select name="type" id="cat-type" required class="w-full h-9 px-3 rounded-md border border-border bg-card text-foreground"><option value="expense">Pengeluaran</option><option value="income">Pemasukan</option><option value="both">Keduanya</option></select></div>

            <div x-data="{ open: false, selected: '', pick(e) { this.selected = e; this.open = false; document.getElementById('cat-icon').value = e; } }">
                <label class="block text-sm font-medium mb-1">Icon</label>
                <button type="button" @click="open=!open" class="w-full h-12 px-3 rounded-md border border-border bg-background text-left hover:bg-secondary transition-colors flex items-center gap-3">
                    <span class="text-2xl" x-text="selected||'👇'"></span><span class="text-sm text-muted-foreground" x-show="!selected">Pilih Emoji</span>
                </button>
                <div x-show="open" @click.outside="open=false" class="mt-2 border border-border rounded-md bg-card shadow-lg p-3 max-h-60 overflow-y-auto">
                    <p class="text-[10px] font-semibold text-muted-foreground mb-1 uppercase tracking-wider">Makanan</p>
                    <div class="grid grid-cols-8 gap-1 mb-3">@foreach(['🍔','🍕','🍜','🍣','☕','🍩','🍗','🥗','🍎','🥐','🍱','🍦','🍰','🥩','🍿','🍺'] as $e)<button type="button" @click="pick('{{ $e }}')" class="h-9 w-9 flex items-center justify-center rounded hover:bg-secondary transition-colors text-lg">{{ $e }}</button>@endforeach</div>
                    <p class="text-[10px] font-semibold text-muted-foreground mb-1 uppercase tracking-wider">Transportasi</p>
                    <div class="grid grid-cols-8 gap-1 mb-3">@foreach(['🚗','🚌','✈️','🚕','⛽','🚲','🚄','🏍️','🛵','🚢','🚶','🚆'] as $e)<button type="button" @click="pick('{{ $e }}')" class="h-9 w-9 flex items-center justify-center rounded hover:bg-secondary transition-colors text-lg">{{ $e }}</button>@endforeach</div>
                    <p class="text-[10px] font-semibold text-muted-foreground mb-1 uppercase tracking-wider">Belanja</p>
                    <div class="grid grid-cols-8 gap-1 mb-3">@foreach(['🛒','👕','👟','💄','📱','💻','👜','👗','⌚','🎮','🎧','🪑'] as $e)<button type="button" @click="pick('{{ $e }}')" class="h-9 w-9 flex items-center justify-center rounded hover:bg-secondary transition-colors text-lg">{{ $e }}</button>@endforeach</div>
                    <p class="text-[10px] font-semibold text-muted-foreground mb-1 uppercase tracking-wider">Tagihan & Rumah</p>
                    <div class="grid grid-cols-8 gap-1 mb-3">@foreach(['⚡','💧','🏠','📞','📡','🔧','🛢️','🧹'] as $e)<button type="button" @click="pick('{{ $e }}')" class="h-9 w-9 flex items-center justify-center rounded hover:bg-secondary transition-colors text-lg">{{ $e }}</button>@endforeach</div>
                    <p class="text-[10px] font-semibold text-muted-foreground mb-1 uppercase tracking-wider">Kesehatan & Hiburan</p>
                    <div class="grid grid-cols-8 gap-1 mb-3">@foreach(['💊','🏥','💪','🎬','🎵','📚','🎨','🧘','🏋️','🎪','📰','🎯'] as $e)<button type="button" @click="pick('{{ $e }}')" class="h-9 w-9 flex items-center justify-center rounded hover:bg-secondary transition-colors text-lg">{{ $e }}</button>@endforeach</div>
                    <p class="text-[10px] font-semibold text-muted-foreground mb-1 uppercase tracking-wider">Keuangan</p>
                    <div class="grid grid-cols-8 gap-1">@foreach(['💰','💵','🏦','📈','💳','🏧','📊','💸'] as $e)<button type="button" @click="pick('{{ $e }}')" class="h-9 w-9 flex items-center justify-center rounded hover:bg-secondary transition-colors text-lg">{{ $e }}</button>@endforeach</div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Warna</label>
                <div class="flex flex-wrap gap-2 mb-2">
                    @foreach(['#ef4444','#f97316','#f59e0b','#eab308','#84cc16','#22c55e','#10b981','#14b8a6','#06b6d4','#3b82f6','#6366f1','#8b5cf6','#a855f7','#d946ef','#ec4899','#64748b'] as $i=>$c)
                    <button type="button" onclick="pickColor(this,'{{ $c }}')" class="swatch w-8 h-8 rounded-full border-2 border-border hover:scale-110 transition-transform {{ $c==='#6366f1'&&$i===10?'ring-2 ring-offset-2 ring-primary':'' }}" style="background-color:{{ $c }}"></button>
                    @endforeach
                    <div class="relative w-8 h-8 rounded-full border-2 border-dashed border-border flex items-center justify-center hover:scale-110 transition-transform cursor-pointer">
                        <input type="color" onchange="pickCustomColor(this)" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"><span class="text-[10px] text-muted-foreground">+</span>
                    </div>
                </div>
                <div class="flex items-center gap-2"><input type="text" id="cat-color-display" value="#6366f1" oninput="setColor(this.value)" class="flex-1 h-9 px-3 rounded-md border border-border bg-background text-foreground text-sm font-mono" placeholder="#6366f1"><div id="color-preview" class="w-9 h-9 rounded-md border border-border flex-shrink-0" style="background-color:#6366f1"></div></div>
            </div>
            <div class="flex gap-2 justify-end pt-2">
                <button type="button" onclick="document.getElementById('modal-cat').close()" class="h-9 px-4 rounded-md border border-border hover:bg-muted transition-colors text-sm font-medium">Batal</button>
                <button type="submit" class="h-9 px-4 rounded-md bg-primary text-primary-foreground hover:bg-primary/90 transition-colors text-sm font-medium">Simpan</button>
            </div>
        </div>
    </form>
</dialog>
@endsection

@push('scripts')
<script>
function setColor(v) { document.getElementById('cat-color').value = v; document.getElementById('cat-color-display').value = v; document.getElementById('color-preview').style.backgroundColor = v; }
function pickColor(btn, c) { document.querySelectorAll('#modal-cat .swatch').forEach(b=>b.classList.remove('ring-2','ring-offset-2','ring-primary')); btn.classList.add('ring-2','ring-offset-2','ring-primary'); setColor(c); }
function pickCustomColor(input) { document.querySelectorAll('#modal-cat .swatch').forEach(b=>b.classList.remove('ring-2','ring-offset-2','ring-primary')); setColor(input.value); }
function openModal() {
    document.getElementById('form-cat').action = '{{ route("kategori.store") }}';
    document.getElementById('cat-method').value = 'POST';
    document.getElementById('cat-name').value = ''; document.getElementById('cat-type').value = 'expense';
    document.getElementById('cat-icon').value = ''; document.getElementById('cat-color').value = '#6366f1';
    document.getElementById('cat-color-display').value = '#6366f1'; document.getElementById('color-preview').style.backgroundColor = '#6366f1';
    document.getElementById('cat-title').textContent = 'Tambah Kategori';
    document.querySelectorAll('#modal-cat .swatch').forEach(b=>b.classList.remove('ring-2','ring-offset-2','ring-primary'));
    var def = document.querySelector('#modal-cat .swatch[style*="#6366f1"]'); if(def) def.classList.add('ring-2','ring-offset-2','ring-primary');
    document.getElementById('modal-cat').showModal();
}
function editCat(id, name, type, icon, color) {
    document.getElementById('form-cat').action = '{{ url("kategori") }}/' + id;
    document.getElementById('cat-method').value = 'PUT';
    document.getElementById('cat-name').value = name; document.getElementById('cat-type').value = type;
    document.getElementById('cat-icon').value = icon||''; setColor(color||'#6366f1');
    document.getElementById('cat-title').textContent = 'Edit Kategori';
    document.querySelectorAll('#modal-cat .swatch').forEach(b=>b.classList.remove('ring-2','ring-offset-2','ring-primary'));
    var m = document.querySelector('#modal-cat .swatch[style*="'+(color||'#6366f1')+'"]'); if(m) m.classList.add('ring-2','ring-offset-2','ring-primary');
    document.getElementById('modal-cat').showModal();
}
</script>
@endpush
