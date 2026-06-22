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
        <div class="flex items-center gap-3 mb-3"><div class="p-3 rounded-lg" style="background-color:{{ $cat->color ? $cat->color.'20' : '#f9731620' }}"><span class="text-xl">{{ $cat->icon ?? '📝' }}</span></div><div><h3 class="font-semibold text-foreground">{{ $cat->name }}</h3><p class="text-xs text-muted-foreground">{{ $cat->transactions_count }} transaksi</p></div></div>
        <div class="flex gap-2">
            <button onclick="editCat(this)" data-id="{{ $cat->id }}" data-name="{{ $cat->name }}" data-type="{{ $cat->type }}" data-icon="{{ $cat->icon }}" data-color="{{ $cat->color }}" class="flex-1 py-2 text-xs font-medium text-primary hover:bg-primary/5 rounded transition-colors">Edit</button>
            <form method="POST" action="{{ route('kategori.destroy', $cat) }}" class="flex-1" onsubmit="if(!confirm('Hapus?'))return false;var b=this.querySelector('button');b.disabled=true;b.textContent='...'">@csrf @method('DELETE')<button class="w-full py-2 text-xs font-medium text-red-500 hover:bg-red-50 rounded transition-colors">Hapus</button></form>
        </div>
    </div>
    @empty
    <div class="col-span-full bg-card rounded-lg shadow-lg p-8 text-center"><p class="text-muted-foreground">Belum ada kategori</p></div>
    @endforelse
</div>

<dialog id="modal-cat" class="p-0 rounded-lg shadow-xl backdrop:bg-black/50 max-w-lg w-full bg-card text-foreground">
    <form id="form-cat" method="POST" action="{{ route('kategori.store') }}" class="p-6" onsubmit="var b=this.querySelector('button[type=submit]');b.disabled=true;b.innerHTML='⟳ Menyimpan...';b.classList.add('opacity-70')">
        @csrf<input type="hidden" name="_method" value="POST" id="cat-method"><input type="hidden" name="icon" id="cat-icon"><input type="hidden" name="color" id="cat-color" value="{{ old('color','#6366f1') }}">
        <div class="space-y-4">
            <h2 class="text-lg font-bold text-foreground" id="cat-title">Tambah Kategori</h2>
            <div><label class="block text-sm font-medium mb-1">Nama</label><input type="text" name="name" id="cat-name" value="{{ old('name') }}" required placeholder="Contoh: Makanan" maxlength="255" class="w-full h-9 px-3 rounded-md border border-border bg-background text-foreground"><x-input-error :messages="$errors->get('name')" class="text-sm mt-1" /></div>
            <div><label class="block text-sm font-medium mb-1">Tipe</label><select name="type" id="cat-type" required class="w-full h-9 px-3 rounded-md border border-border bg-card text-foreground"><option value="expense" {{ old('type')==='expense'?'selected':'' }}>Pengeluaran</option><option value="income" {{ old('type')==='income'?'selected':'' }}>Pemasukan</option><option value="both" {{ old('type')==='both'?'selected':'' }}>Keduanya</option></select><x-input-error :messages="$errors->get('type')" class="text-sm mt-1" /></div>

            <div x-data="{ open: false, selected: '{{ old('icon') }}', pick(e) { this.selected = e; this.open = false; document.getElementById('cat-icon').value = e; } }">
                <label class="block text-sm font-medium mb-1">Icon</label>
                <button type="button" @click="open=!open" class="w-full h-12 px-3 rounded-md border border-border bg-background text-left hover:bg-secondary transition-colors flex items-center gap-3"><span class="text-2xl" x-text="selected||'👇'"></span><span class="text-sm text-muted-foreground" x-show="!selected">Pilih Emoji</span></button>
                <div x-show="open" @click.outside="open=false" class="mt-2 border border-border rounded-md bg-card shadow-lg p-3 max-h-60 overflow-y-auto">
                    <p class="text-[10px] font-semibold text-muted-foreground mb-1 uppercase tracking-wider">Makanan</p><div class="grid grid-cols-8 gap-1 mb-3">@foreach(['🍔','🍕','🍜','🍣','☕','🍩','🍗','🥗','🍎','🥐','🍱','🍦','🍰','🥩','🍿','🍺'] as $e)<button type="button" @click="pick('{{ $e }}')" class="h-9 w-9 flex items-center justify-center rounded hover:bg-secondary transition-colors text-lg">{{ $e }}</button>@endforeach</div>
                    <p class="text-[10px] font-semibold text-muted-foreground mb-1 uppercase tracking-wider">Transportasi</p><div class="grid grid-cols-8 gap-1 mb-3">@foreach(['🚗','🚌','✈️','🚕','⛽','🚲','🚄','🏍️','🛵','🚢','🚶','🚆'] as $e)<button type="button" @click="pick('{{ $e }}')" class="h-9 w-9 flex items-center justify-center rounded hover:bg-secondary transition-colors text-lg">{{ $e }}</button>@endforeach</div>
                    <p class="text-[10px] font-semibold text-muted-foreground mb-1 uppercase tracking-wider">Belanja</p><div class="grid grid-cols-8 gap-1 mb-3">@foreach(['🛒','👕','👟','💄','📱','💻','👜','👗','⌚','🎮','🎧','🪑'] as $e)<button type="button" @click="pick('{{ $e }}')" class="h-9 w-9 flex items-center justify-center rounded hover:bg-secondary transition-colors text-lg">{{ $e }}</button>@endforeach</div>
                    <p class="text-[10px] font-semibold text-muted-foreground mb-1 uppercase tracking-wider">Tagihan & Rumah</p><div class="grid grid-cols-8 gap-1 mb-3">@foreach(['⚡','💧','🏠','📞','📡','🔧','🛢️','🧹'] as $e)<button type="button" @click="pick('{{ $e }}')" class="h-9 w-9 flex items-center justify-center rounded hover:bg-secondary transition-colors text-lg">{{ $e }}</button>@endforeach</div>
                    <p class="text-[10px] font-semibold text-muted-foreground mb-1 uppercase tracking-wider">Kesehatan & Hiburan</p><div class="grid grid-cols-8 gap-1 mb-3">@foreach(['💊','🏥','💪','🎬','🎵','📚','🎨','🧘','🏋️','🎪','📰','🎯'] as $e)<button type="button" @click="pick('{{ $e }}')" class="h-9 w-9 flex items-center justify-center rounded hover:bg-secondary transition-colors text-lg">{{ $e }}</button>@endforeach</div>
                    <p class="text-[10px] font-semibold text-muted-foreground mb-1 uppercase tracking-wider">Keuangan</p><div class="grid grid-cols-8 gap-1">@foreach(['💰','💵','🏦','📈','💳','🏧','📊','💸'] as $e)<button type="button" @click="pick('{{ $e }}')" class="h-9 w-9 flex items-center justify-center rounded hover:bg-secondary transition-colors text-lg">{{ $e }}</button>@endforeach</div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Warna</label>
                <div class="flex flex-wrap gap-2 mb-2">
                    @foreach(['#ef4444','#f97316','#f59e0b','#eab308','#84cc16','#22c55e','#10b981','#14b8a6','#06b6d4','#3b82f6','#6366f1','#8b5cf6','#a855f7','#d946ef','#ec4899','#64748b'] as $c)
                    <button type="button" onclick="pickColor(this,'{{ $c }}')" class="swatch w-8 h-8 rounded-full border-2 border-border hover:scale-110 transition-transform {{ old('color',$c)==$c?'ring-2 ring-offset-2 ring-primary':'' }}" style="background-color:{{ $c }}"></button>
                    @endforeach
                    <div class="relative w-8 h-8 rounded-full border-2 border-dashed border-border flex items-center justify-center hover:scale-110 transition-transform cursor-pointer"><input type="color" onchange="pickCustomColor(this)" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"><span class="text-[10px] text-muted-foreground">+</span></div>
                </div>
                <div class="flex items-center gap-2"><input type="text" id="cat-color-display" value="{{ old('color','#6366f1') }}" oninput="setColor(this.value)" class="flex-1 h-9 px-3 rounded-md border border-border bg-background text-foreground text-sm font-mono"><div id="color-preview" class="w-9 h-9 rounded-md border border-border flex-shrink-0" style="background-color:{{ old('color','#6366f1') }}"></div></div>
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
function setColor(v){document.getElementById('cat-color').value=v;document.getElementById('cat-color-display').value=v;document.getElementById('color-preview').style.backgroundColor=v}
function pickColor(btn,c){document.querySelectorAll('#modal-cat .swatch').forEach(b=>b.classList.remove('ring-2','ring-offset-2','ring-primary'));btn.classList.add('ring-2','ring-offset-2','ring-primary');setColor(c)}
function pickCustomColor(i){document.querySelectorAll('#modal-cat .swatch').forEach(b=>b.classList.remove('ring-2','ring-offset-2','ring-primary'));setColor(i.value)}
function openModal(){
    document.getElementById('form-cat').action='{{ route("kategori.store") }}';document.getElementById('cat-method').value='POST';
    document.getElementById('cat-name').value='';document.getElementById('cat-type').value='expense';document.getElementById('cat-icon').value='';setColor('#6366f1');
    document.getElementById('cat-title').textContent='Tambah Kategori';
    document.querySelectorAll('#modal-cat .swatch').forEach(b=>b.classList.remove('ring-2','ring-offset-2','ring-primary'));
    var d=document.querySelector('#modal-cat .swatch[style*="#6366f1"]');if(d)d.classList.add('ring-2','ring-offset-2','ring-primary');
    document.getElementById('modal-cat').showModal();
}
function editCat(btn){
    var d=btn.dataset;document.getElementById('form-cat').action='{{ url("kategori") }}/'+d.id;document.getElementById('cat-method').value='PUT';
    document.getElementById('cat-name').value=d.name;document.getElementById('cat-type').value=d.type;document.getElementById('cat-icon').value=d.icon||'';setColor(d.color||'#6366f1');
    document.getElementById('cat-title').textContent='Edit Kategori';
    document.querySelectorAll('#modal-cat .swatch').forEach(b=>b.classList.remove('ring-2','ring-offset-2','ring-primary'));
    var m=document.querySelector('#modal-cat .swatch[style*="'+(d.color||'#6366f1')+'"]');if(m)m.classList.add('ring-2','ring-offset-2','ring-primary');
    document.getElementById('modal-cat').showModal();
}
@if($errors->any())
document.addEventListener('DOMContentLoaded',function(){
    @if(old('_method')==='PUT' && request()->route('category'))document.getElementById('form-cat').action='{{ url("kategori") }}/{{ request()->route("category")->id }}';document.getElementById('cat-method').value='PUT';document.getElementById('cat-title').textContent='Edit Kategori';@endif
    document.getElementById('modal-cat').showModal();
});
@endif
</script>
@endpush
