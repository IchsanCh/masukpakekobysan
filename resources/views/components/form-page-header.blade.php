{{-- Header halaman form (tambah/edit): tombol kembali berukuran 44px + judul. Judul dan subjudul tidak melebar keluar layar. --}}
@props(['title', 'subtitle' => null, 'backUrl', 'backLabel' => 'Kembali'])

<div class="flex items-center gap-2 mb-4 sm:mb-6">
    <a href="{{ $backUrl }}" aria-label="{{ $backLabel }}"
        class="shrink-0 size-11 inline-flex items-center justify-center rounded-xl text-[var(--color-slate-500)]
               hover:bg-[var(--color-slate-100)] hover:text-[var(--color-navy-900)] active:bg-[var(--color-slate-100)]
               focus:outline-none focus-visible:ring-2 focus-visible:ring-[var(--color-blue-500)]/30 transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
            stroke-width="2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
        </svg>
    </a>
    <div class="min-w-0">
        <h1
            class="font-[var(--font-heading)] text-xl sm:text-2xl font-bold text-[var(--color-navy-900)] tracking-tight">
            {{ $title }}</h1>
        @if ($subtitle)
            <p class="text-sm text-[var(--color-slate-500)] mt-0.5 truncate">{{ $subtitle }}</p>
        @endif
    </div>
</div>