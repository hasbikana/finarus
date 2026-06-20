<div
    x-data="{
        toasts: [],
        add(e) {
            const id = Date.now();
            this.toasts.push({ id, message: e.detail.message, type: e.detail.type, visible: true });
            setTimeout(() => this.remove(id), 4000);
        },
        remove(id) {
            const i = this.toasts.findIndex(t => t.id === id);
            if (i > -1) this.toasts[i].visible = false;
            setTimeout(() => { this.toasts = this.toasts.filter(t => t.id !== id); }, 300);
        }
    }"
    @finarus-toast.window="add($event)"
    class="fixed bottom-4 right-4 z-50 flex flex-col-reverse gap-2 max-w-sm"
>
    <template x-for="t in toasts" :key="t.id">
        <div
            x-show="t.visible"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="translate-x-full opacity-0"
            x-transition:enter-end="translate-x-0 opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="translate-x-0 opacity-100"
            x-transition:leave-end="translate-x-full opacity-0"
            :class="t.type === 'error' ? 'bg-red-600 text-white' : 'bg-green-600 text-white'"
            class="px-4 py-3 rounded-lg shadow-lg flex items-center gap-2 cursor-pointer text-sm font-medium"
            @click="remove(t.id)"
        >
            <svg x-show="t.type === 'success'" class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <svg x-show="t.type === 'error'" class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            <span x-text="t.message"></span>
        </div>
    </template>
</div>

@if(session('success'))
<script>document.addEventListener('DOMContentLoaded', () => Finarus.toast('{{ session('success') }}', 'success'))</script>
@endif
@if(session('error'))
<script>document.addEventListener('DOMContentLoaded', () => Finarus.toast('{{ session('error') }}', 'error'))</script>
@endif
