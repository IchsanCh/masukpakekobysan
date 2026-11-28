@props(['up' => false])
{{-- Menu aksi per baris untuk tampilan mobile. Isi menu lewat slot (pakai class "row-menu-item"). --}}
<div x-data="{ open: false }" @keydown.escape.window="open = false" @click.outside="open = false" class="group/tip relative shrink-0">
    <button type="button" @click="open = !open" :aria-expanded="open" aria-haspopup="menu" aria-label="Aksi surat"
        class="-mr-2 -mt-1 w-11 h-11 flex items-center justify-center rounded-xl text-[var(--color-slate-500)]
               hover:bg-[var(--color-slate-100)] hover:text-[var(--color-navy-900)] active:bg-[var(--color-slate-100)] transition-colors
               focus:outline-none focus-visible:ring-2 focus-visible:ring-[var(--color-blue-500)]/40">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
            <circle cx="12" cy="5.5" r="1.7" />
            <circle cx="12" cy="12" r="1.7" />
            <circle cx="12" cy="18.5" r="1.7" />
        </svg>
    </button>
    <span role="tooltip" :class="open && 'hidden'"
        class="pointer-events-none absolute right-0 bottom-full mb-1 z-30 whitespace-nowrap rounded-md px-2 py-1
               text-xs font-medium text-white bg-[#0f172a] border border-white/10 shadow-lg
               opacity-0 translate-y-0.5 transition duration-150
               group-hover/tip:opacity-100 group-hover/tip:translate-y-0 group-hover/tip:delay-300
               group-focus-within/tip:opacity-100 group-focus-within/tip:translate-y-0">Aksi surat</span>
    <div x-show="open" style="display: none" x-transition.opacity.duration.150ms role="menu"
        class="absolute right-0 {{ $up ? 'bottom-full mb-1' : 'top-full mt-1' }} z-20 w-56 py-1 rounded-xl bg-white border border-[var(--color-slate-200)]
               shadow-[0_4px_8px_-2px_rgba(15,23,42,0.08),0_16px_32px_-8px_rgba(15,23,42,0.18)]
               [&_.row-menu-item]:flex [&_.row-menu-item]:w-full [&_.row-menu-item]:items-center [&_.row-menu-item]:gap-3
               [&_.row-menu-item]:min-h-11 [&_.row-menu-item]:px-4 [&_.row-menu-item]:text-sm [&_.row-menu-item]:text-left
               [&_.row-menu-item]:text-[var(--color-slate-700)] [&_.row-menu-item:hover]:bg-[var(--color-slate-50)]">
        {{ $slot }}
    </div>
</div>