{{--
    Confirm Delete Modal — taruh di layout app.blade.php (sekali saja)

    Trigger dari mana saja:
    <button @click="$dispatch('confirm-delete', {
        url: '/units/1',
        name: 'Sekretariat'
    })" class="btn btn-error btn-xs">Hapus</button>
--}}

<div x-cloak x-data="{ open: false, url: '', name: '', loading: false }"
    @confirm-delete.window="open = true; url = $event.detail.url; name = $event.detail.name; loading = false">

    {{-- Backdrop --}}
    <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-[var(--color-navy-900)]/50 backdrop-blur-[2px] z-50" @click="open = false">
    </div>

    {{-- Modal --}}
    <div x-show="open" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95" class="fixed inset-0 z-50 flex items-center justify-center p-4"
        @click="open = false">

        <div class="bg-white rounded-2xl shadow-[0_20px_60px_-15px_rgba(11,25,44,0.35)] max-w-sm w-full p-6"
            @click.stop>
            <div class="flex justify-center mb-4">
                <div class="bg-red-50 rounded-2xl p-3 ring-1 ring-[var(--color-danger)]/10">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-[var(--color-danger)]" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
            </div>

            <h3
                class="font-[var(--font-heading)] text-lg font-bold text-center text-[var(--color-navy-900)] tracking-tight mb-2">
                Konfirmasi Hapus</h3>
            <p class="text-center text-[var(--color-slate-500)] text-sm mb-6 leading-relaxed">
                Yakin ingin menghapus <span class="font-semibold text-[var(--color-navy-900)]" x-text="name"></span>?
                Data yang sudah dihapus tidak bisa dikembalikan.
            </p>

            <div class="flex gap-2">
                <button @click="open = false"
                    class="flex-1 px-4 py-2.5 rounded-xl text-sm font-medium text-[var(--color-slate-500)] hover:bg-[var(--color-slate-100)] transition-colors"
                    :disabled="loading">Batal</button>
                <form :action="url" method="POST" class="flex-1" @submit="loading = true">
                    @csrf @method('DELETE')
                    <button type="submit"
                        class="w-full px-4 py-2.5 rounded-xl text-sm font-semibold text-white bg-[var(--color-danger)] shadow-[0_4px_10px_-2px_rgba(220,38,38,0.35)] hover:shadow-[0_8px_16px_-2px_rgba(220,38,38,0.4)] transition-all disabled:opacity-60"
                        :disabled="loading">
                        <span x-show="!loading">Hapus</span>
                        <span x-show="loading" class="loading loading-spinner loading-sm"></span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
