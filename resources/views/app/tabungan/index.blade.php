@extends('layouts.app')
@section('title', 'Tabungan - Finarus')
@section('page-title', 'Tujuan Tabungan')
@section('page-description', 'Tetapkan dan pantau tujuan keuangan Anda')
@section('page-actions')<button onclick="openModal()" class="w-full sm:w-auto h-9 text-sm bg-primary text-primary-foreground hover:bg-primary/90 transition-all duration-300 px-4 rounded-md font-medium flex items-center justify-center gap-2">+ Tambah Tujuan</button>@endsection

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    @forelse($savingGoals as $goal)
    <div class="bg-card rounded-lg shadow-lg p-5 hover:shadow-2xl hover:scale-105 transition-all duration-500 ease-out">
        <div class="flex gap-3 mb-3">@if($goal->image)<img src="{{ asset('storage/'.$goal->image) }}" alt="{{ $goal->name }}" class="w-14 h-14 rounded-lg object-cover" onerror="this.style.display='none'">@endif<div class="flex-1"><h3 class="font-semibold text-foreground">{{ $goal->icon??'🎯' }} {{ $goal->name }}</h3>@if($goal->deadline)<p class="text-xs text-muted-foreground">Target: {{ $goal->deadline->format('M Y') }}</p>@endif</div></div>
        <div class="mb-4"><div class="flex justify-between text-xs mb-2"><span>Progress</span><span>{{ $goal->progress }}%</span></div><div class="w-full bg-muted rounded-full h-2"><div class="bg-blue-500 h-2 rounded-full" style="width:{{ $goal->progress }}%"></div></div></div>
        <div class="grid grid-cols-2 gap-2 mb-3 text-xs text-muted-foreground"><div>Rp {{ number_format($goal->current_amount,0,',','.') }} terisi</div><div>Target Rp {{ number_format($goal->target_amount,0,',','.') }}</div></div>
        <div class="flex gap-2">
            <button onclick="openFund({{ $goal->id }},'{{ addslashes($goal->name) }}')" class="flex-1 py-2 text-xs font-medium bg-primary text-primary-foreground rounded hover:bg-primary/90 transition-colors">+ Tambah Dana</button>
            <button onclick="editGoal(this)" data-id="{{ $goal->id }}" data-name="{{ $goal->name }}" data-target="{{ $goal->target_amount }}" data-current="{{ $goal->current_amount }}" data-deadline="{{ $goal->deadline?->format('Y-m-d') }}" data-icon="{{ $goal->icon }}" data-image="{{ $goal->image }}" class="flex-1 py-2 text-xs font-medium text-primary hover:bg-primary/5 rounded transition-colors">Edit</button>
            <form method="POST" action="{{ route('tabungan.destroy', $goal) }}" class="flex-1" onsubmit="if(!confirm('Hapus?'))return false;var b=this.querySelector('button');b.disabled=true;b.textContent='...'">@csrf @method('DELETE')<button class="w-full py-2 text-xs font-medium text-red-500 hover:bg-red-50 rounded transition-colors">Hapus</button></form>
        </div>
    </div>
    @empty
    <div class="col-span-full bg-card rounded-lg shadow-lg p-8 text-center"><p class="text-muted-foreground">Belum ada tujuan tabungan</p></div>
    @endforelse
</div>

<dialog id="modal-fund" class="p-0 rounded-lg shadow-xl backdrop:bg-black/50 max-w-sm w-full bg-card text-foreground">
    <form method="POST" id="form-fund" class="p-6" onsubmit="var b=this.querySelector('button[type=submit]');b.disabled=true;b.innerHTML='⟳ Menyimpan...';b.classList.add('opacity-70')">
        @csrf
        <div class="space-y-4">
            <h2 class="text-lg font-bold text-foreground">Tambah Dana - <span id="fund-name"></span></h2>
            <div><label class="block text-sm font-medium mb-1">Dari Akun</label><select name="account_id" required class="w-full h-9 px-3 rounded-md border border-border bg-card text-foreground"><option value="">Pilih Akun</option>@foreach($accounts as $acc)<option value="{{ $acc->id }}">{{ $acc->name }}</option>@endforeach</select></div>
            <div><label class="block text-sm font-medium mb-1">Jumlah (Rp)</label><input type="number" name="amount" required min="1" step="0.01" placeholder="500000" class="w-full h-9 px-3 rounded-md border border-border bg-background text-foreground"></div>
            <div><label class="block text-sm font-medium mb-1">Tanggal</label><input type="date" name="transaction_date" required value="{{ now()->format('Y-m-d') }}" class="w-full h-9 px-3 rounded-md border border-border bg-background text-foreground"></div>
            <div class="flex gap-2 justify-end pt-2"><button type="button" onclick="document.getElementById('modal-fund').close()" class="h-9 px-4 rounded-md border border-border hover:bg-muted transition-colors text-sm font-medium">Batal</button><button type="submit" class="h-9 px-4 rounded-md bg-primary text-primary-foreground hover:bg-primary/90 transition-colors text-sm font-medium">Simpan</button></div>
        </div>
    </form>
</dialog>

<dialog id="modal-goal" class="p-0 rounded-lg shadow-xl backdrop:bg-black/50 max-w-lg w-full bg-card text-foreground">
    <form id="form-goal" method="POST" action="{{ route('tabungan.store') }}" class="p-6" onsubmit="var b=this.querySelector('button[type=submit]');b.disabled=true;b.innerHTML='⟳ Menyimpan...';b.classList.add('opacity-70')">
        @csrf<input type="hidden" name="_method" value="POST" id="goal-method"><input type="hidden" name="icon" id="goal-icon"><input type="hidden" name="image" id="goal-image">
        <div class="space-y-4">
            <h2 class="text-lg font-bold text-foreground" id="goal-title">Tambah Tujuan</h2>
            <div><label class="block text-sm font-medium mb-1">Foto Tujuan <span class="text-muted-foreground">(opsional)</span></label>
                <div class="flex items-start gap-3">
                    <div id="img-preview" class="w-20 h-20 rounded-lg border-2 border-dashed border-border flex items-center justify-center bg-background flex-shrink-0 overflow-hidden"><span class="text-2xl text-muted-foreground" id="img-placeholder">📷</span><img id="img-preview-img" src="{{ old('image') ? asset('storage/'.old('image')) : '' }}" class="w-full h-full object-cover {{ old('image')?'':'hidden' }}" onerror="this.style.display='none';document.getElementById('img-placeholder').style.display='block'"></div>
                    <div class="flex-1"><label class="inline-flex items-center h-9 px-4 rounded-md border border-border hover:bg-muted transition-colors text-sm font-medium cursor-pointer"><svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg> Pilih Foto<input type="file" accept="image/*" class="hidden" onchange="uploadImage(this)"></label><button type="button" onclick="clearImage()" class="inline-flex items-center h-9 px-4 rounded-md text-red-500 hover:bg-red-50 transition-colors text-sm font-medium mt-2" id="btn-clear-img" style="display:{{ old('image')?'inline-flex':'none' }}">Hapus Foto</button></div>
                </div></div>
            <div><label class="block text-sm font-medium mb-1">Nama Tujuan</label><input type="text" name="name" id="goal-name" value="{{ old('name') }}" required placeholder="Contoh: Liburan ke Bali" maxlength="255" class="w-full h-9 px-3 rounded-md border border-border bg-background text-foreground"><x-input-error :messages="$errors->get('name')" class="text-sm mt-1" /></div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-sm font-medium mb-1">Target (Rp)</label><input type="number" name="target_amount" id="goal-target" value="{{ old('target_amount') }}" required min="1" step="0.01" placeholder="5000000" class="w-full h-9 px-3 rounded-md border border-border bg-background text-foreground"><x-input-error :messages="$errors->get('target_amount')" class="text-sm mt-1" /></div>
                <div><label class="block text-sm font-medium mb-1">Terkumpul (Rp)</label><input type="number" name="current_amount" id="goal-current" value="{{ old('current_amount',0) }}" min="0" step="0.01" class="w-full h-9 px-3 rounded-md border border-border bg-background text-foreground"><x-input-error :messages="$errors->get('current_amount')" class="text-sm mt-1" /></div>
            </div>
            <div><label class="block text-sm font-medium mb-1">Target Tanggal</label><input type="date" name="deadline" id="goal-deadline" value="{{ old('deadline') }}" class="w-full h-9 px-3 rounded-md border border-border bg-background text-foreground"><x-input-error :messages="$errors->get('deadline')" class="text-sm mt-1" /></div>
            <div x-data="{ open: false, selected: '{{ old('icon') }}', pick(e) { this.selected = e; this.open = false; document.getElementById('goal-icon').value = e; } }">
                <label class="block text-sm font-medium mb-1">Icon</label><button type="button" @click="open=!open" class="w-full h-12 px-3 rounded-md border border-border bg-background text-left hover:bg-secondary transition-colors flex items-center gap-3"><span class="text-2xl" x-text="selected||'👇'"></span><span class="text-sm text-muted-foreground" x-show="!selected">Pilih Emoji</span></button>
                <div x-show="open" @click.outside="open=false" class="mt-2 border border-border rounded-md bg-card shadow-lg p-3 max-h-60 overflow-y-auto">
                    <p class="text-[10px] font-semibold text-muted-foreground mb-1 uppercase tracking-wider">Keuangan</p><div class="grid grid-cols-8 gap-1 mb-3">@foreach(['💰','💵','🏦','📈','💳','🏧','📊','💸','🪙','💎','🏠','🚗','✈️','🎓','💻','📱'] as $e)<button type="button" @click="pick('{{ $e }}')" class="h-9 w-9 flex items-center justify-center rounded hover:bg-secondary transition-colors text-lg">{{ $e }}</button>@endforeach</div>
                    <p class="text-[10px] font-semibold text-muted-foreground mb-1 uppercase tracking-wider">Travel</p><div class="grid grid-cols-8 gap-1 mb-3">@foreach(['🏖️','🏔️','🌴','🗽','🎌','🏯','🗼','🏝️','⛵','🚢','🎢','🎡'] as $e)<button type="button" @click="pick('{{ $e }}')" class="h-9 w-9 flex items-center justify-center rounded hover:bg-secondary transition-colors text-lg">{{ $e }}</button>@endforeach</div>
                    <p class="text-[10px] font-semibold text-muted-foreground mb-1 uppercase tracking-wider">Gaya Hidup</p><div class="grid grid-cols-8 gap-1 mb-3">@foreach(['🎵','🎨','📚','🎬','🎮','⚽','🎾','🏀','🎸','🎹','🧘','🏋️'] as $e)<button type="button" @click="pick('{{ $e }}')" class="h-9 w-9 flex items-center justify-center rounded hover:bg-secondary transition-colors text-lg">{{ $e }}</button>@endforeach</div>
                    <p class="text-[10px] font-semibold text-muted-foreground mb-1 uppercase tracking-wider">Target</p><div class="grid grid-cols-8 gap-1 mb-3">@foreach(['🎯','🏆','⭐','🌟','🔥','💪','🚀','🎉','🛡️','❤️','🎁','✨'] as $e)<button type="button" @click="pick('{{ $e }}')" class="h-9 w-9 flex items-center justify-center rounded hover:bg-secondary transition-colors text-lg">{{ $e }}</button>@endforeach</div>
                    <p class="text-[10px] font-semibold text-muted-foreground mb-1 uppercase tracking-wider">Pendidikan</p><div class="grid grid-cols-8 gap-1">@foreach(['🎓','📖','🔬','🧮','🎒','✏️','🏫','📝'] as $e)<button type="button" @click="pick('{{ $e }}')" class="h-9 w-9 flex items-center justify-center rounded hover:bg-secondary transition-colors text-lg">{{ $e }}</button>@endforeach</div>
                </div>
            </div>
            <div class="flex gap-2 justify-end pt-2"><button type="button" onclick="document.getElementById('modal-goal').close()" class="h-9 px-4 rounded-md border border-border hover:bg-muted transition-colors text-sm font-medium">Batal</button><button type="submit" class="h-9 px-4 rounded-md bg-primary text-primary-foreground hover:bg-primary/90 transition-colors text-sm font-medium">Simpan</button></div>
        </div>
    </form>
</dialog>
@endsection

@push('scripts')
<script>
function openFund(id, name) {
    document.getElementById('form-fund').action = '{{ url("tabungan") }}/' + id + '/add-fund';
    document.getElementById('fund-name').textContent = name;
    document.getElementById('modal-fund').showModal();
}
async function uploadImage(i){var f=i.files[0];if(!f)return;var fd=new FormData();fd.append('file',f);try{var r=await fetch('/upload',{method:'POST',headers:{'X-CSRF-TOKEN':Finarus.csrf()},body:fd});var j=await r.json();if(r.ok){document.getElementById('goal-image').value=j.path;document.getElementById('img-preview-img').src=j.url;document.getElementById('img-preview-img').classList.remove('hidden');document.getElementById('img-placeholder').style.display='none';document.getElementById('btn-clear-img').style.display='inline-flex';Finarus.toast('Foto terupload')}else Finarus.toast('Upload gagal','error')}catch(e){Finarus.toast('Upload gagal','error')}}
function clearImage(){document.getElementById('goal-image').value='';document.getElementById('img-preview-img').classList.add('hidden');document.getElementById('img-placeholder').style.display='block';document.getElementById('btn-clear-img').style.display='none'}
function openModal(){document.getElementById('form-goal').action='{{ route("tabungan.store") }}';document.getElementById('goal-method').value='POST';document.getElementById('goal-name').value='';document.getElementById('goal-target').value='';document.getElementById('goal-current').value='0';document.getElementById('goal-deadline').value='';document.getElementById('goal-icon').value='';document.getElementById('goal-image').value='';document.getElementById('img-preview-img').classList.add('hidden');document.getElementById('img-placeholder').style.display='block';document.getElementById('btn-clear-img').style.display='none';document.getElementById('goal-title').textContent='Tambah Tujuan';document.getElementById('modal-goal').showModal()}
function editGoal(btn){var d=btn.dataset;document.getElementById('form-goal').action='{{ url("tabungan") }}/'+d.id;document.getElementById('goal-method').value='PUT';document.getElementById('goal-name').value=d.name;document.getElementById('goal-target').value=d.target;document.getElementById('goal-current').value=d.current;document.getElementById('goal-deadline').value=d.deadline;document.getElementById('goal-icon').value=d.icon||'';document.getElementById('goal-title').textContent='Edit Tujuan';if(d.image){document.getElementById('goal-image').value=d.image;document.getElementById('img-preview-img').src='/storage/'+d.image;document.getElementById('img-preview-img').classList.remove('hidden');document.getElementById('img-placeholder').style.display='none';document.getElementById('btn-clear-img').style.display='inline-flex'}else clearImage();document.getElementById('modal-goal').showModal()}
@if($errors->any())document.addEventListener('DOMContentLoaded',function(){@if(old('_method')==='PUT' && request()->route('savingGoal'))document.getElementById('form-goal').action='{{ url("tabungan") }}/{{ request()->route("savingGoal")->id }}';document.getElementById('goal-method').value='PUT';document.getElementById('goal-title').textContent='Edit Tujuan';@endif document.getElementById('modal-goal').showModal()});@endif
</script>
@endpush
