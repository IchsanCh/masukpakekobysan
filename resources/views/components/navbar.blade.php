{{-- Top navbar for mobile toggle + user dropdown --}}
<div class="navbar bg-base-100 shadow-sm lg:shadow-none">
    <div class="flex-none lg:hidden">
        <label for="sidebar-toggle" class="btn btn-square btn-ghost">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </label>
    </div>

    <div class="flex-1">
        <h1 class="text-lg font-semibold px-2">{{ $title ?? '' }}</h1>
    </div>

    <div class="flex-none">
        <div class="dropdown dropdown-end">
            <div tabindex="0" role="button" class="btn btn-ghost gap-2">
                <div class="avatar placeholder">
                    <div class="bg-primary text-primary-content w-8 rounded-full">
                        <span class="text-sm">{{ substr(auth()->user()->name ?? 'U', 0, 1) }}</span>
                    </div>
                </div>
                <span class="hidden sm:inline">{{ auth()->user()->name ?? 'User' }}</span>
            </div>
            <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-50 w-52 p-2 shadow-lg">
                <li class="menu-title">{{ auth()->user()->jabatan_struktural ?? '-' }}</li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left">Logout</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</div>
