{{-- Custom pagination view — overrides Laravel's default pagination::tailwind globally.
     Used automatically by every {{ $paginator->links() }} call in the app. --}}
@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between flex-wrap gap-3">

        <p class="text-sm text-[var(--color-slate-500)]">
            Menampilkan
            <span class="font-semibold text-[var(--color-navy-900)]">{{ $paginator->firstItem() }}</span>
            &ndash;
            <span class="font-semibold text-[var(--color-navy-900)]">{{ $paginator->lastItem() }}</span>
            dari
            <span class="font-semibold text-[var(--color-navy-900)]">{{ $paginator->total() }}</span>
            data
        </p>

        <div class="flex items-center gap-1">
            {{-- Previous --}}
            @if ($paginator->onFirstPage())
                <span class="p-2 rounded-lg text-[var(--color-slate-300)] cursor-not-allowed">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                    class="p-2 rounded-lg text-[var(--color-slate-500)] hover:bg-[var(--color-slate-100)] hover:text-[var(--color-navy-900)] transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
            @endif

            {{-- Page Numbers --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span
                        class="w-8 h-8 flex items-center justify-center text-sm text-[var(--color-slate-300)]">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page"
                                class="w-8 h-8 flex items-center justify-center rounded-lg text-sm font-semibold text-white
                                         bg-gradient-to-br from-[var(--color-blue-500)] to-[var(--color-blue-600)]
                                         shadow-[0_2px_6px_-1px_rgba(37,99,235,0.4)]">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}"
                                class="w-8 h-8 flex items-center justify-center rounded-lg text-sm font-medium text-[var(--color-slate-500)]
                                      hover:bg-[var(--color-slate-100)] hover:text-[var(--color-navy-900)] transition-colors">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                    class="p-2 rounded-lg text-[var(--color-slate-500)] hover:bg-[var(--color-slate-100)] hover:text-[var(--color-navy-900)] transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            @else
                <span class="p-2 rounded-lg text-[var(--color-slate-300)] cursor-not-allowed">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </span>
            @endif
        </div>
    </nav>
@endif
