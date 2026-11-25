@props(['badgeDisposisi' => 0, 'badgeArsipTinjau' => 0, 'badgeArsipApproval' => 0])

<div class="drawer-side z-40">
    <label for="sidebar-toggle" aria-label="close sidebar" class="drawer-overlay"></label>

    <aside class="h-full w-[260px] max-w-[85vw] bg-[var(--color-slate-50)] border-r border-[var(--color-slate-200)] flex flex-col">
        <div class="shrink-0 flex items-center justify-between gap-2 pl-5 pr-3 lg:pr-5 py-5 border-b border-[var(--color-slate-200)]">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5">
                <div
                    class="w-8 h-8 rounded-lg bg-gradient-to-br from-[var(--color-blue-500)] to-[var(--color-blue-600)] flex items-center justify-center shadow-sm">
                    <span class="text-white font-bold text-sm">M</span>
                </div>
                <div>
                    <p
                        class="font-[var(--font-heading)] text-[15px] font-bold text-[var(--color-navy-900)] leading-tight">
                        MasukPakEko V0.1</p>
                    <p class="text-[11px] text-[var(--color-slate-500)] tracking-wide uppercase">DPMPTSP Pekalongan</p>
                </div>
            </a>

            <label for="sidebar-toggle" aria-label="Tutup menu navigasi"
                class="btn btn-ghost btn-square size-11 rounded-xl lg:hidden cursor-pointer -my-2">
                <x-icon name="x-mark" class="w-5 h-5 text-[var(--color-slate-500)]" />
            </label>
        </div>

        <nav class="flex-1 overflow-y-auto p-3 pb-6 space-y-1">
            @php $active = request()->routeIs('dashboard'); @endphp
            <a href="{{ route('dashboard') }}"
                @if ($active) aria-current="page" @endif
                class="flex items-center gap-3 px-3 py-3 lg:py-2.5 rounded-xl text-sm font-medium transition-all duration-150
                      {{ $active ? 'bg-[var(--color-blue-50)] text-[var(--color-blue-600)]' : 'text-[var(--color-slate-500)] hover:bg-[var(--color-slate-100)] hover:text-[var(--color-slate-700)]' }}">
                <x-icon name="home" :solid="$active" class="w-[18px] h-[18px]" />
                Dashboard
            </a>

            <p
                class="px-3 pt-5 pb-1.5 text-[11px] font-semibold text-[var(--color-slate-500)] uppercase tracking-wider">
                Persuratan</p>

            @php $active = request()->routeIs('surat-masuk.*'); @endphp
            <a href="{{ route('surat-masuk.index') }}"
                @if ($active) aria-current="page" @endif
                class="flex items-center gap-3 px-3 py-3 lg:py-2.5 rounded-xl text-sm font-medium transition-all duration-150
                      {{ $active ? 'bg-[var(--color-blue-50)] text-[var(--color-blue-600)]' : 'text-[var(--color-slate-500)] hover:bg-[var(--color-slate-100)] hover:text-[var(--color-slate-700)]' }}">
                <x-icon name="inbox" :solid="$active" class="w-[18px] h-[18px]" />
                Surat Masuk
            </a>

            @php $active = request()->routeIs('surat-keluar.*'); @endphp
            <a href="{{ route('surat-keluar.index') }}"
                @if ($active) aria-current="page" @endif
                class="flex items-center gap-3 px-3 py-3 lg:py-2.5 rounded-xl text-sm font-medium transition-all duration-150
                      {{ $active ? 'bg-[var(--color-blue-50)] text-[var(--color-blue-600)]' : 'text-[var(--color-slate-500)] hover:bg-[var(--color-slate-100)] hover:text-[var(--color-slate-700)]' }}">
                <x-icon name="paper-airplane" :solid="$active" class="w-[18px] h-[18px]" />
                Surat Keluar
            </a>

            @php $active = request()->routeIs('disposisi.*'); @endphp
            <a href="{{ route('disposisi.inbox') }}"
                @if ($active) aria-current="page" @endif
                class="flex items-center gap-3 px-3 py-3 lg:py-2.5 rounded-xl text-sm font-medium transition-all duration-150
                      {{ $active ? 'bg-[var(--color-blue-50)] text-[var(--color-blue-600)]' : 'text-[var(--color-slate-500)] hover:bg-[var(--color-slate-100)] hover:text-[var(--color-slate-700)]' }}">
                <x-icon name="arrow-right-circle" :solid="$active" class="w-[18px] h-[18px]" />
                Disposisi
                <x-nav-badge :count="$badgeDisposisi" label="disposisi menunggu tindak lanjut"
                    title="{{ $badgeDisposisi }} disposisi menunggu tindak lanjut" />
            </a>

            @php $active = request()->routeIs('tindak-lanjut.*'); @endphp
            <a href="{{ route('tindak-lanjut.index') }}"
                @if ($active) aria-current="page" @endif
                class="flex items-center gap-3 px-3 py-3 lg:py-2.5 rounded-xl text-sm font-medium transition-all duration-150
                      {{ $active ? 'bg-[var(--color-blue-50)] text-[var(--color-blue-600)]' : 'text-[var(--color-slate-500)] hover:bg-[var(--color-slate-100)] hover:text-[var(--color-slate-700)]' }}">
                <x-icon name="check-circle" :solid="$active" class="w-[18px] h-[18px]" />
                Tindak Lanjut
            </a>

            @if (auth()->user()->hasRole('agendaris'))
                <p
                    class="px-3 pt-5 pb-1.5 text-[11px] font-semibold text-[var(--color-slate-500)] uppercase tracking-wider">
                    Master Data</p>

                @php $active = request()->routeIs('users.*'); @endphp
                <a href="{{ route('users.index') }}"
                    @if ($active) aria-current="page" @endif
                    class="flex items-center gap-3 px-3 py-3 lg:py-2.5 rounded-xl text-sm font-medium transition-all duration-150
                          {{ $active ? 'bg-[var(--color-blue-50)] text-[var(--color-blue-600)]' : 'text-[var(--color-slate-500)] hover:bg-[var(--color-slate-100)] hover:text-[var(--color-slate-700)]' }}">
                    <x-icon name="users" :solid="$active" class="w-[18px] h-[18px]" />
                    Pengguna
                </a>

                @php $active = request()->routeIs('units.*'); @endphp
                <a href="{{ route('units.index') }}"
                    @if ($active) aria-current="page" @endif
                    class="flex items-center gap-3 px-3 py-3 lg:py-2.5 rounded-xl text-sm font-medium transition-all duration-150
                          {{ $active ? 'bg-[var(--color-blue-50)] text-[var(--color-blue-600)]' : 'text-[var(--color-slate-500)] hover:bg-[var(--color-slate-100)] hover:text-[var(--color-slate-700)]' }}">
                    <x-icon name="building-office" :solid="$active" class="w-[18px] h-[18px]" />
                    Bidang
                </a>

                @php $active = request()->routeIs('retensi.*'); @endphp
                <a href="{{ route('retensi.index') }}"
                    @if ($active) aria-current="page" @endif
                    class="flex items-center gap-3 px-3 py-3 lg:py-2.5 rounded-xl text-sm font-medium transition-all duration-150
                          {{ $active ? 'bg-[var(--color-blue-50)] text-[var(--color-blue-600)]' : 'text-[var(--color-slate-500)] hover:bg-[var(--color-slate-100)] hover:text-[var(--color-slate-700)]' }}">
                    <x-icon name="archive-box" :solid="$active" class="w-[18px] h-[18px]" />
                    Retensi Arsip
                </a>

                <p
                    class="px-3 pt-5 pb-1.5 text-[11px] font-semibold text-[var(--color-slate-500)] uppercase tracking-wider">
                    Pengaturan</p>

                @php $active = request()->routeIs('configuration.*'); @endphp
                <a href="{{ route('configuration.index') }}"
                    @if ($active) aria-current="page" @endif
                    class="flex items-center gap-3 px-3 py-3 lg:py-2.5 rounded-xl text-sm font-medium transition-all duration-150
                          {{ $active ? 'bg-[var(--color-blue-50)] text-[var(--color-blue-600)]' : 'text-[var(--color-slate-500)] hover:bg-[var(--color-slate-100)] hover:text-[var(--color-slate-700)]' }}">
                    <x-icon name="cog-6-tooth" :solid="$active" class="w-[18px] h-[18px]" />
                    Konfigurasi
                </a>

                @php $active = request()->routeIs('notifikasi.*'); @endphp
                <a href="{{ route('notifikasi.index') }}"
                    @if ($active) aria-current="page" @endif
                    class="flex items-center gap-3 px-3 py-3 lg:py-2.5 rounded-xl text-sm font-medium transition-all duration-150
                          {{ $active ? 'bg-[var(--color-blue-50)] text-[var(--color-blue-600)]' : 'text-[var(--color-slate-500)] hover:bg-[var(--color-slate-100)] hover:text-[var(--color-slate-700)]' }}">
                    <x-icon name="bell" :solid="$active" class="w-[18px] h-[18px]" />
                    Log Notifikasi
                </a>

                @php $active = request()->routeIs('arsip.*'); @endphp
                <a href="{{ route('arsip.index') }}"
                    @if ($active) aria-current="page" @endif
                    class="flex items-center gap-3 px-3 py-3 lg:py-2.5 rounded-xl text-sm font-medium transition-all duration-150
                          {{ $active ? 'bg-[var(--color-blue-50)] text-[var(--color-blue-600)]' : 'text-[var(--color-slate-500)] hover:bg-[var(--color-slate-100)] hover:text-[var(--color-slate-700)]' }}">
                    <x-icon name="eye" :solid="$active" class="w-[18px] h-[18px]" />
                    Nasib Akhir Arsip
                    <x-nav-badge :count="$badgeArsipTinjau + $badgeArsipApproval"
                        label="arsip perlu ditangani (ditinjau atau disetujui musnah)"
                        title="{{ $badgeArsipTinjau }} perlu ditinjau, {{ $badgeArsipApproval }} menunggu persetujuan musnah" />
                </a>
            @endif
        </nav>
    </aside>
</div>