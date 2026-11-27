{{--
    Bar aksi form surat. Di mobile menempel di bawah layar (jempol selalu bisa menjangkau Simpan);
    disembunyikan sementara saat keyboard ketik terbuka supaya tidak makan ruang.
    Memakai state form induk: submitting, fileSizeError, typing.
--}}
@props(['cancelUrl', 'label' => 'Simpan Surat'])

<div :class="typing ? 'max-sm:hidden' : ''"
    class="sticky bottom-0 z-20 -mx-4 -mb-4 mt-5 px-4 py-3 flex items-center gap-2 bg-base-200 border-t border-[var(--color-slate-200)]
           sm:static sm:mx-0 sm:mb-0 sm:p-0 sm:justify-end sm:bg-transparent sm:border-0">
    <a href="{{ $cancelUrl }}" :class="submitting ? 'pointer-events-none opacity-50' : ''"
        class="inline-flex items-center justify-center min-h-12 sm:min-h-0 px-4 sm:py-2.5 rounded-xl text-sm font-medium text-[var(--color-slate-500)] hover:bg-[var(--color-slate-100)] transition-colors">Batal</a>
    <button type="submit" :disabled="fileSizeError || submitting"
        class="flex-1 sm:flex-none min-h-12 sm:min-h-0 px-5 sm:py-2.5 rounded-xl text-sm font-semibold text-white bg-gradient-to-br from-[var(--color-blue-500)] to-[var(--color-blue-600)]
               shadow-[0_4px_10px_-2px_rgba(37,99,235,0.35)] hover:shadow-[0_8px_16px_-2px_rgba(37,99,235,0.45)] transition-all
               disabled:opacity-60 disabled:cursor-not-allowed disabled:shadow-none inline-flex items-center justify-center gap-2">
        <span x-show="submitting" class="loading loading-spinner loading-xs"></span>
        <span x-text="submitting ? 'Menyimpan...' : @js($label)"></span>
    </button>
</div>