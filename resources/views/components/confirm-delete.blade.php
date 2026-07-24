{{--
    Confirm Delete Modal — taruh di layout app.blade.php (sekali saja)

    Trigger dari mana saja:
    <button @click="$dispatch('confirm-delete', {
        url: '/units/1',
        name: 'Sekretariat'
    })" class="btn btn-error btn-xs">Hapus</button>
--}}

<div x-cloak
     x-data="{ open: false, url: '', name: '', loading: false }"
     @confirm-delete.window="open = true; url = $event.detail.url; name = $event.detail.name; loading = false">

    {{-- Backdrop --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/40 z-50" @click="open = false">
    </div>

    {{-- Modal --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-50 flex items-center justify-center p-4">

        <div class="bg-base-100 rounded-2xl shadow-xl max-w-sm w-full p-6" @click.stop>
            <div class="flex justify-center mb-4">
                <div class="bg-error/10 rounded-full p-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-error" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
            </div>

            <h3 class="text-lg font-bold text-center mb-2">Konfirmasi Hapus</h3>
            <p class="text-center text-base-content/70 text-sm mb-6">
                Yakin ingin menghapus <span class="font-semibold text-base-content" x-text="name"></span>?
                Data yang sudah dihapus tidak bisa dikembalikan.
            </p>

            <div class="flex gap-3">
                <button @click="open = false" class="btn btn-ghost flex-1" :disabled="loading">Batal</button>
                <form :action="url" method="POST" class="flex-1" @submit="loading = true">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-error w-full" :disabled="loading">
                        <span x-show="!loading">Hapus</span>
                        <span x-show="loading" class="loading loading-spinner loading-sm"></span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
