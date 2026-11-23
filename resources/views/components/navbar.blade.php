@php
    $user = auth()->user();
    $initial = mb_strtoupper(mb_substr($user->name ?? 'U', 0, 1));
    $peran = ucfirst($user->peran ?? '');
    $onProfilePage = request()->routeIs('profile.*');
@endphp

{{-- Top bar. Kiri: tombol menu + brand (hanya mobile, di desktop brand sudah ada di sidebar).
     Kanan: ganti tema + menu akun. Target sentuh 44px di mobile, 40px di layar lebar. --}}
<header
    class="sticky top-0 z-30 flex items-center gap-1 h-14 px-2 sm:px-4 bg-white/80 backdrop-blur-md border-b border-[var(--color-slate-200)]">
    <label for="sidebar-toggle" aria-label="Buka menu navigasi"
        class="btn btn-ghost btn-square size-11 rounded-xl lg:hidden cursor-pointer">
        <x-icon name="bars-3" class="w-5 h-5 text-[var(--color-slate-500)]" />
    </label>

    <a href="{{ route('dashboard') }}" class="lg:hidden flex items-center gap-2 min-w-0 rounded-lg py-1 pr-1">
        <span
            class="w-8 h-8 rounded-lg bg-gradient-to-br from-[var(--color-blue-500)] to-[var(--color-blue-600)] flex items-center justify-center shadow-sm shrink-0">
            <span class="text-white font-bold text-sm">M</span>
        </span>
        <span
            class="font-[var(--font-heading)] text-[15px] font-bold text-[var(--color-navy-900)] leading-tight truncate">MasukPakEko</span>
    </a>

    <div class="flex-1"></div>

    <x-theme-toggle class="size-11 sm:size-10 rounded-xl" />

    {{-- Menu akun --}}
    <div x-data="{ open: false }" @click.outside="open = false"
        @keydown.escape.window="if (open) { open = false; $refs.trigger.focus() }"
        @focusin.window="if (open && !$root.contains($event.target)) open = false" class="relative">
        <button id="account-trigger" x-ref="trigger" type="button" @click="open = !open" :aria-expanded="open"
            aria-controls="account-panel" aria-label="Menu akun {{ $user->name }}"
            class="flex items-center gap-2 h-11 sm:h-10 pl-1.5 pr-1.5 sm:pr-2.5 rounded-xl cursor-pointer
                   hover:bg-[var(--color-slate-100)] transition-colors"
            :class="open && 'bg-[var(--color-slate-100)]'">
            <span
                class="w-8 h-8 rounded-full bg-gradient-to-br from-[var(--color-blue-500)] to-[var(--color-blue-600)] flex items-center justify-center shrink-0">
                <span class="text-white text-xs font-bold">{{ $initial }}</span>
            </span>
            <span class="hidden sm:block min-w-0 max-w-[11rem] text-left">
                <span
                    class="block text-sm font-medium leading-tight text-[var(--color-navy-900)] truncate">{{ $user->name }}</span>
                <span class="block text-xs leading-tight text-[var(--color-slate-500)] truncate">{{ $peran }}</span>
            </span>
            <span class="hidden sm:block shrink-0 transition-transform duration-200 motion-reduce:transition-none"
                :class="open && 'rotate-180'">
                <x-icon name="chevron-down" class="w-4 h-4 text-[var(--color-slate-400)]" />
            </span>
        </button>

        <div id="account-panel" x-show="open" style="display: none;"
            x-transition:enter="transition ease-out duration-150 motion-reduce:duration-0"
            x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-100 motion-reduce:duration-0"
            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
            class="absolute right-0 top-full mt-2 z-50 w-72 max-w-[calc(100vw-1rem)] origin-top-right rounded-2xl p-1.5
                   bg-white border border-[var(--color-slate-200)]
                   shadow-[0_2px_4px_rgba(15,23,42,0.06),0_16px_32px_-8px_rgba(15,23,42,0.20)]">

            <div class="flex items-center gap-3 px-3 py-3">
                <span
                    class="w-10 h-10 rounded-full bg-gradient-to-br from-[var(--color-blue-500)] to-[var(--color-blue-600)] flex items-center justify-center shrink-0">
                    <span class="text-white text-sm font-bold">{{ $initial }}</span>
                </span>
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-[var(--color-navy-900)] truncate">{{ $user->name }}</p>
                    <p class="text-xs text-[var(--color-slate-500)] truncate">{{ $user->email }}</p>
                    <span
                        class="mt-1.5 inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium border border-[var(--color-blue-500)]/20 bg-[var(--color-blue-500)]/5 text-[var(--color-blue-600)]">{{ $peran }}</span>
                </div>
            </div>

            <div class="my-1 h-px bg-[var(--color-slate-200)]"></div>

            <a href="{{ route('profile.show') }}" @if ($onProfilePage) aria-current="page" @endif
                class="flex items-center gap-3 px-3 min-h-11 rounded-xl text-sm font-medium transition-colors
                      {{ $onProfilePage ? 'bg-[var(--color-blue-50)] text-[var(--color-blue-600)]' : 'text-[var(--color-slate-700)] hover:bg-[var(--color-slate-100)]' }}">
                <x-icon name="user-circle" class="w-[18px] h-[18px] shrink-0" />
                Data Akun
            </a>

            <div class="my-1 h-px bg-[var(--color-slate-200)]"></div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="w-full flex items-center gap-3 px-3 min-h-11 rounded-xl text-sm font-medium cursor-pointer
                           text-[var(--color-danger)] hover:bg-red-50 transition-colors">
                    <x-icon name="arrow-right-start-on-rectangle" class="w-[18px] h-[18px] shrink-0" />
                    Logout
                </button>
            </form>
        </div>
    </div>
</header>