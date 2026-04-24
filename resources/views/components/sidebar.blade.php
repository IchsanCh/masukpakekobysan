{{-- Sidebar drawer --}}
<div class="drawer-side z-40">
    <label for="sidebar-toggle" aria-label="close sidebar" class="drawer-overlay"></label>

    <aside class="bg-base-100 min-h-full w-64 border-r border-base-300">
        {{-- Logo --}}
        <div class="p-4 border-b border-base-300">
            <a href="{{ route('dashboard') }}" class="text-xl font-bold text-primary">
                DPMPTSP
            </a>
            <p class="text-xs text-base-content/60 mt-1">Sistem Persuratan</p>
        </div>

        {{-- Menu --}}
        <ul class="menu p-4 gap-1">
            <li>
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <x-icon name="home" />
                    Dashboard
                </a>
            </li>

            <li class="menu-title mt-4">Persuratan</li>
            <li>
                <a href="#" class="{{ request()->routeIs('surat-masuk.*') ? 'active' : '' }}">
                    <x-icon name="inbox" />
                    Surat Masuk
                </a>
            </li>
            <li>
                <a href="#" class="{{ request()->routeIs('surat-keluar.*') ? 'active' : '' }}">
                    <x-icon name="send" />
                    Surat Keluar
                </a>
            </li>

            @if (auth()->user()->canDisposisi())
                <li class="menu-title mt-4">Disposisi</li>
                <li>
                    <a href="#" class="{{ request()->routeIs('disposisi.*') ? 'active' : '' }}">
                        <x-icon name="forward" />
                        Disposisi
                    </a>
                </li>
            @endif

            <li class="menu-title mt-4">Tindak Lanjut</li>
            <li>
                <a href="#" class="{{ request()->routeIs('tindak-lanjut.*') ? 'active' : '' }}">
                    <x-icon name="check-circle" />
                    Tindak Lanjut
                </a>
            </li>

            @if (auth()->user()->hasRole('agendaris'))
                <li class="menu-title mt-4">Master Data</li>
                <li>
                    <a href="#" class="{{ request()->routeIs('users.*') ? 'active' : '' }}">
                        <x-icon name="users" />
                        Pengguna
                    </a>
                </li>
                <li>
                    <a href="#" class="{{ request()->routeIs('units.*') ? 'active' : '' }}">
                        <x-icon name="building" />
                        Unit / Bidang
                    </a>
                </li>
                <li>
                    <a href="#" class="{{ request()->routeIs('retensi.*') ? 'active' : '' }}">
                        <x-icon name="archive" />
                        Retensi Arsip
                    </a>
                </li>

                <li class="menu-title mt-4">Pengaturan</li>
                <li>
                    <a href="#" class="{{ request()->routeIs('config.fonnte.*') ? 'active' : '' }}">
                        <x-icon name="settings" />
                        Fonnte & Scheduler
                    </a>
                </li>
                <li>
                    <a href="#" class="{{ request()->routeIs('config.template.*') ? 'active' : '' }}">
                        <x-icon name="message" />
                        Template WhatsApp
                    </a>
                </li>
            @endif
        </ul>
    </aside>
</div>
