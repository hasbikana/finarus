@extends('layouts.app')
@section('title', 'E-Wallet & Bank - Finarus')
@section('page-title', 'E-Wallet & Banking')
@section('page-description', 'Kelola akun e-wallet dan rekening bank Anda')
@section('page-actions')<button onclick="openModal()" class="w-full sm:w-auto h-9 text-sm bg-primary text-primary-foreground hover:bg-primary/90 transition-all duration-300 px-4 rounded-md font-medium flex items-center justify-center gap-2">+ Tambah Akun</button>@endsection

@section('content')
<div class="space-y-4">
    @forelse($accounts as $account)
    <div class="bg-card rounded-lg shadow-lg p-5 hover:shadow-2xl hover:scale-105 transition-all duration-500 ease-out">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-lg bg-primary/10 flex items-center justify-center overflow-hidden">@if($account->logo)<img src="/logos/{{ $account->logo }}.png" alt="{{ $account->provider }}" class="w-full h-full object-contain rounded" onerror="this.style.display='none';this.parentElement.innerHTML='<span class=text-sm font-bold text-primary>{{ strtoupper(substr($account->provider??'XX',0,2)) }}</span>'">@else<span class="text-sm font-bold text-primary">{{ strtoupper(substr($account->provider??'XX',0,2)) }}</span>@endif</div>
                <div><h3 class="font-semibold text-foreground">{{ $account->name }}</h3><p class="text-xs text-muted-foreground">{{ $account->type==='cash'?'Cash':($account->type==='ewallet'?'E-Wallet':($account->type==='credit_card'?'Kartu Kredit':'Bank')) }} &bull; {{ $account->provider }}</p></div>
            </div>
            <div class="text-right"><p class="text-lg font-semibold text-foreground">Rp {{ number_format($account->balance,0,',','.') }}</p>
                <div class="flex gap-2 mt-2 justify-end">
                    <button onclick="editAcc(this)" data-id="{{ $account->id }}" data-name="{{ $account->name }}" data-provider="{{ $account->provider }}" data-type="{{ $account->type }}" data-num="{{ $account->account_number }}" data-balance="{{ $account->balance }}" data-logo="{{ $account->logo }}" class="text-primary hover:text-primary/80 text-xs font-medium">Edit</button>
                    <form method="POST" action="{{ route('dompet.destroy', $account) }}" class="inline" onsubmit="if(!confirm('Hapus?'))return false;var b=this.querySelector('button');b.disabled=true;b.textContent='...'">@csrf @method('DELETE')<button class="text-red-500 hover:text-red-700 text-xs font-medium">Hapus</button></form>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="bg-card rounded-lg shadow-lg p-8 text-center"><p class="text-muted-foreground">Belum ada akun</p></div>
    @endforelse
</div>
<div class="mt-6 bg-card rounded-lg shadow-lg p-5"><h3 class="font-semibold text-foreground">Total Saldo: Rp {{ number_format($totalBalance,0,',','.') }}</h3></div>

<dialog id="modal-acc" class="p-0 rounded-lg shadow-xl backdrop:bg-black/50 max-w-lg w-full bg-card text-foreground">
    <form id="form-acc" method="POST" action="{{ route('dompet.store') }}" class="p-6" onsubmit="var b=this.querySelector('button[type=submit]');b.disabled=true;b.innerHTML='⟳ Menyimpan...';b.classList.add('opacity-70')">
        @csrf<input type="hidden" name="_method" value="POST" id="acc-method"><input type="hidden" name="logo" id="acc-logo" value="{{ old('logo') }}">
        <div class="space-y-4">
            <h2 class="text-lg font-bold text-foreground" id="acc-title">Tambah Akun</h2>
            <div><label class="block text-sm font-medium mb-1">Logo</label>
                <div class="grid grid-cols-4 gap-2 mb-2">
                    @foreach(['bca','mandiri','bni','bri','gopay','ovo','dana','linkaja'] as $p)
                    <button type="button" onclick="pickLogo('{{ $p }}')" class="h-16 rounded-lg border-2 flex items-center justify-center bg-background hover:border-primary transition-colors p-2 logo-btn {{ old('logo')===$p?'border-primary ring-2 ring-primary/20':'border-border' }}"><img src="/logos/{{ $p }}.png" alt="{{ $p }}" class="max-w-full max-h-full object-contain" onerror="this.style.display='none';this.parentElement.innerHTML='<span class=text-xs font-bold>{{ substr($p,0,2) }}</span>'"></button>
                    @endforeach
                    <label class="h-16 rounded-lg border-2 border-dashed border-border flex items-center justify-center bg-background hover:border-primary transition-colors cursor-pointer"><input type="file" accept="image/*" class="hidden" onchange="uploadLogo(this)"><div class="text-center"><span class="block text-lg text-muted-foreground">📤</span><span class="text-[9px] text-muted-foreground">Upload</span></div></label>
                </div>
                <div class="flex items-center gap-2"><span class="text-xs text-muted-foreground">Custom:</span><input type="text" id="acc-logo-display" value="{{ old('logo') }}" placeholder="gopay / bca / custom" oninput="document.getElementById('acc-logo').value=this.value" class="flex-1 h-8 px-2 rounded-md border border-border bg-background text-foreground text-sm"></div>
            </div>
            <div><label class="block text-sm font-medium mb-1">Nama Akun</label><input type="text" name="name" id="acc-name" value="{{ old('name') }}" required placeholder="Contoh: Rekening Utama" maxlength="255" class="w-full h-9 px-3 rounded-md border border-border bg-background text-foreground"><x-input-error :messages="$errors->get('name')" class="text-sm mt-1" /></div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-sm font-medium mb-1">Provider</label><select name="provider" id="acc-provider" required class="w-full h-9 px-3 rounded-md border border-border bg-card text-foreground" onchange="autoFillLogo(this.value)"><option value="">Pilih</option>@foreach(['BCA','Mandiri','BNI','BRI','GoPay','OVO','DANA','LinkAja','ShopeePay'] as $p)<option value="{{ $p }}" {{ old('provider')===$p?'selected':'' }}>{{ $p }}</option>@endforeach</select><x-input-error :messages="$errors->get('provider')" class="text-sm mt-1" /></div>
                <div><label class="block text-sm font-medium mb-1">Tipe</label><select name="type" id="acc-type" required class="w-full h-9 px-3 rounded-md border border-border bg-card text-foreground"><option value="bank" {{ old('type')==='bank'?'selected':'' }}>Bank</option><option value="ewallet" {{ old('type')==='ewallet'?'selected':'' }}>E-Wallet</option><option value="credit_card" {{ old('type')==='credit_card'?'selected':'' }}>Kartu Kredit</option></select><x-input-error :messages="$errors->get('type')" class="text-sm mt-1" /></div>
            </div>
            <div><label class="block text-sm font-medium mb-1">Nomor Akun</label><input type="text" name="account_number" id="acc-num" value="{{ old('account_number') }}" placeholder="1234567890" maxlength="100" autocomplete="off" class="w-full h-9 px-3 rounded-md border border-border bg-background text-foreground"><x-input-error :messages="$errors->get('account_number')" class="text-sm mt-1" /></div>
            <div><label class="block text-sm font-medium mb-1">Saldo Awal (Rp)</label><input type="number" name="balance" id="acc-balance" value="{{ old('balance',0) }}" min="0" step="0.01" class="w-full h-9 px-3 rounded-md border border-border bg-background text-foreground"><x-input-error :messages="$errors->get('balance')" class="text-sm mt-1" /></div>
            <div class="flex gap-2 justify-end pt-2"><button type="button" onclick="document.getElementById('modal-acc').close()" class="h-9 px-4 rounded-md border border-border hover:bg-muted transition-colors text-sm font-medium">Batal</button><button type="submit" class="h-9 px-4 rounded-md bg-primary text-primary-foreground hover:bg-primary/90 transition-colors text-sm font-medium">Simpan</button></div>
        </div>
    </form>
</dialog>
@endsection

@push('scripts')
<script>
function pickLogo(v){document.getElementById('acc-logo').value=v;document.getElementById('acc-logo-display').value=v;document.querySelectorAll('.logo-btn').forEach(b=>b.classList.remove('border-primary','ring-2','ring-primary/20'));event.target.closest('.logo-btn')?.classList.add('border-primary','ring-2','ring-primary/20')}
function autoFillLogo(v){var m={BCA:'bca',Mandiri:'mandiri',BNI:'bni',BRI:'bri',GoPay:'gopay',OVO:'ovo',DANA:'dana',LinkAja:'linkaja'};if(m[v]){document.getElementById('acc-logo').value=m[v];document.getElementById('acc-logo-display').value=m[v]}}
async function uploadLogo(i){var f=i.files[0];if(!f)return;var fd=new FormData();fd.append('file',f);try{var r=await fetch('/upload',{method:'POST',headers:{'X-CSRF-TOKEN':Finarus.csrf()},body:fd});var j=await r.json();if(r.ok){var n=j.path.replace('uploads/','');document.getElementById('acc-logo').value=n;document.getElementById('acc-logo-display').value=n;Finarus.toast('Logo terupload')}else Finarus.toast('Upload gagal','error')}catch(e){Finarus.toast('Upload gagal','error')}}
function openModal(){document.getElementById('form-acc').action='{{ route("dompet.store") }}';document.getElementById('acc-method').value='POST';document.getElementById('acc-name').value='';document.getElementById('acc-provider').value='';document.getElementById('acc-type').value='bank';document.getElementById('acc-num').value='';document.getElementById('acc-balance').value='0';document.getElementById('acc-logo').value='';document.getElementById('acc-logo-display').value='';document.getElementById('acc-title').textContent='Tambah Akun';document.querySelectorAll('.logo-btn').forEach(b=>b.classList.remove('border-primary','ring-2','ring-primary/20'));document.getElementById('modal-acc').showModal()}
function editAcc(btn){var d=btn.dataset;document.getElementById('form-acc').action='{{ url("dompet") }}/'+d.id;document.getElementById('acc-method').value='PUT';document.getElementById('acc-name').value=d.name;document.getElementById('acc-provider').value=d.provider;document.getElementById('acc-type').value=d.type;document.getElementById('acc-num').value=d.num;document.getElementById('acc-balance').value=d.balance;document.getElementById('acc-logo').value=d.logo;document.getElementById('acc-logo-display').value=d.logo;document.getElementById('acc-title').textContent='Edit Akun';document.querySelectorAll('.logo-btn').forEach(b=>b.classList.remove('border-primary','ring-2','ring-primary/20'));var m=Array.from(document.querySelectorAll('.logo-btn')).find(b=>b.querySelector('img')?.alt===d.logo);if(m)m.classList.add('border-primary','ring-2','ring-primary/20');document.getElementById('modal-acc').showModal()}
@if($errors->any())document.addEventListener('DOMContentLoaded',function(){@if(old('_method')==='PUT' && request()->route('account'))document.getElementById('form-acc').action='{{ url("dompet") }}/{{ request()->route("account")->id }}';document.getElementById('acc-method').value='PUT';document.getElementById('acc-title').textContent='Edit Akun';@endif document.getElementById('modal-acc').showModal()});@endif
</script>
@endpush
