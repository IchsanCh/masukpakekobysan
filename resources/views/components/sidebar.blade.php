<div class="drawer-side z-40">
    <label for="sidebar-toggle" aria-label="close sidebar" class="drawer-overlay"></label>

    <aside class="min-h-full w-[260px] bg-[var(--color-slate-50)] border-r border-[var(--color-slate-200)]">
        <div class="px-5 py-5 border-b border-[var(--color-slate-200)]">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5">
                <div
                    class="w-8 h-8 rounded-lg bg-gradient-to-br from-[var(--color-blue-500)] to-[var(--color-blue-600)] flex items-center justify-center shadow-sm">
                    <span class="text-white font-bold text-sm">M</span>
                </div>
                <div>
                    <p
                        class="font-[var(--font-heading)] text-[15px] font-bold text-[var(--color-navy-900)] leading-tight">
                        MasukPakEko</p>
                    <p class="text-[10px] text-[var(--color-slate-400)] tracking-wide uppercase">DPMPTSP Pekalongan</p>
                </div>
            </a>
        </div>

        <nav class="p-3 space-y-1">
            @php $active = request()->routeIs('dashboard'); @endphp
            <a href="{{ route('dashboard') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150
                      {{ $active ? 'bg-[var(--color-blue-50)] text-[var(--color-blue-600)]' : 'text-[var(--color-slate-500)] hover:bg-[var(--color-slate-100)] hover:text-[var(--color-slate-700)]' }}">
                <x-icon name="home" :solid="$active" class="w-[18px] h-[18px]" />
                Dashboard
            </a>

            <p
                class="px-3 pt-5 pb-1.5 text-[10px] font-semibold text-[var(--color-slate-400)] uppercase tracking-wider">
                Persuratan</p>

            @php $active = request()->routeIs('surat-masuk.*'); @endphp
            <a href="{{ route('surat-masuk.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150
                      {{ $active ? 'bg-[var(--color-blue-50)] text-[var(--color-blue-600)]' : 'text-[var(--color-slate-500)] hover:bg-[var(--color-slate-100)] hover:text-[var(--color-slate-700)]' }}">
                <x-icon name="inbox" :solid="$active" class="w-[18px] h-[18px]" />
                Surat Masuk
            </a>

            @php $active = request()->routeIs('surat-keluar.*'); @endphp
            <a href="#"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150
                      {{ $active ? 'bg-[var(--color-blue-50)] text-[var(--color-blue-600)]' : 'text-[var(--color-slate-500)] hover:bg-[var(--color-slate-100)] hover:text-[var(--color-slate-700)]' }}">
                <x-icon name="paper-airplane" :solid="$active" class="w-[18px] h-[18px]" />
                Surat Keluar
            </a>

            @php $active = request()->routeIs('disposisi.*'); @endphp
            <a href="#"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150
                      {{ $active ? 'bg-[var(--color-blue-50)] text-[var(--color-blue-600)]' : 'text-[var(--color-slate-500)] hover:bg-[var(--color-slate-100)] hover:text-[var(--color-slate-700)]' }}">
                <x-icon name="arrow-right-circle" :solid="$active" class="w-[18px] h-[18px]" />
                Disposisi
            </a>

            @php $active = request()->routeIs('tindak-lanjut.*'); @endphp
            <a href="#"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150
                      {{ $active ? 'bg-[var(--color-blue-50)] text-[var(--color-blue-600)]' : 'text-[var(--color-slate-500)] hover:bg-[var(--color-slate-100)] hover:text-[var(--color-slate-700)]' }}">
                <x-icon name="check-circle" :solid="$active" class="w-[18px] h-[18px]" />
                Tindak Lanjut
            </a>

            @if (auth()->user()->hasRole('agendaris'))
                <p
                    class="px-3 pt-5 pb-1.5 text-[10px] font-semibold text-[var(--color-slate-400)] uppercase tracking-wider">
                    Master Data</p>

                @php $active = request()->routeIs('users.*'); @endphp
                <a href="{{ route('users.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150
                          {{ $active ? 'bg-[var(--color-blue-50)] text-[var(--color-blue-600)]' : 'text-[var(--color-slate-500)] hover:bg-[var(--color-slate-100)] hover:text-[var(--color-slate-700)]' }}">
                    <x-icon name="users" :solid="$active" class="w-[18px] h-[18px]" />
                    Pengguna
                </a>

                @php $active = request()->routeIs('units.*'); @endphp
                <a href="{{ route('units.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150
                          {{ $active ? 'bg-[var(--color-blue-50)] text-[var(--color-blue-600)]' : 'text-[var(--color-slate-500)] hover:bg-[var(--color-slate-100)] hover:text-[var(--color-slate-700)]' }}">
                    <x-icon name="building-office" :solid="$active" class="w-[18px] h-[18px]" />
                    Unit / Bidang
                </a>

                @php $active = request()->routeIs('retensi.*'); @endphp
                <a href="{{ route('retensi.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150
                          {{ $active ? 'bg-[var(--color-blue-50)] text-[var(--color-blue-600)]' : 'text-[var(--color-slate-500)] hover:bg-[var(--color-slate-100)] hover:text-[var(--color-slate-700)]' }}">
                    <x-icon name="archive-box" :solid="$active" class="w-[18px] h-[18px]" />
                    Retensi Arsip
                </a>

                <p
                    class="px-3 pt-5 pb-1.5 text-[10px] font-semibold text-[var(--color-slate-400)] uppercase tracking-wider">
                    Pengaturan</p>

                <a href="#"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-[var(--color-slate-500)] hover:bg-[var(--color-slate-100)] hover:text-[var(--color-slate-700)] transition-all duration-150">
                    <x-icon name="cog-6-tooth" class="w-[18px] h-[18px]" />
                    Konfigurasi
                </a>
            @endif
        </nav>

        <div
            class="absolute bottom-0 left-0 right-0 p-3 border-t border-[var(--color-slate-200)] bg-[var(--color-slate-50)]">
            <div class="flex items-center gap-3 px-2">
                <div class="w-8 h-8 rounded-full bg-[var(--color-blue-100)] flex items-center justify-center">
                    <span
                        class="text-[var(--color-blue-600)] text-xs font-bold">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-[var(--color-navy-900)] truncate">{{ auth()->user()->name }}</p>
                    <p class="text-[10px] text-[var(--color-slate-400)] truncate">
                        {{ auth()->user()->jabatan_struktural ?? '-' }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="p-1.5 rounded-lg text-[var(--color-slate-400)] hover:text-[var(--color-danger)] hover:bg-[var(--color-slate-100)] transition-colors"
                        title="Logout">
                        <x-icon name="arrow-right-start-on-rectangle" class="w-4 h-4" />
                    </button>
                </form>
            </div>
        </div>
    </aside>
</div>
