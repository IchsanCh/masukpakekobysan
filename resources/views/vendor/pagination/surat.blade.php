{{-- Pagination untuk halaman Surat Masuk & Surat Keluar.
     Mobile: tombol Sebelumnya / Berikutnya besar + posisi halaman.
     Tablet ke atas: memakai view pagination global (nomor halaman). --}}
@if ($paginator->hasPages())
    <div class="mt-4">
        <nav role="navigation" aria-label="Pagination Navigation" class="sm:hidden">
            <div class="flex items-stretch gap-2">
                @if ($paginator->onFirstPage())
                    <span aria-disabled="true"
                        class="flex-1 min-h-11 inline-flex items-center justify-center gap-1.5 rounded-xl text-sm font-medium border border-[var(--color-slate-200)] text-[var(--color-slate-300)]">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
                        Sebelumnya
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                        class="flex-1 min-h-11 inline-flex items-center justify-center gap-1.5 rounded-xl text-sm font-medium bg-white border border-[var(--color-slate-200)] text-[var(--color-slate-700)] active:bg-[var(--color-slate-100)] transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
                        Sebelumnya
                    </a>
                @endif

                <div class="px-1 flex flex-col items-center justify-center text-center leading-tight min-w-[4.5rem]">
                    <span class="text-sm font-semibold text-[var(--color-navy-900)]">{{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}</span>
                    <span class="text-xs text-[var(--color-slate-500)]">{{ $paginator->total() }} surat</span>
                </div>

                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                        class="flex-1 min-h-11 inline-flex items-center justify-center gap-1.5 rounded-xl text-sm font-medium bg-white border border-[var(--color-slate-200)] text-[var(--color-slate-700)] active:bg-[var(--color-slate-100)] transition-colors">
                        Berikutnya
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                    </a>
                @else
                    <span aria-disabled="true"
                        class="flex-1 min-h-11 inline-flex items-center justify-center gap-1.5 rounded-xl text-sm font-medium border border-[var(--color-slate-200)] text-[var(--color-slate-300)]">
                        Berikutnya
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                    </span>
                @endif
            </div>
        </nav>

        <div class="hidden sm:block">
            @include('vendor.pagination.tailwind')
        </div>
    </div>
@endif