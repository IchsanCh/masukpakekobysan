<div class="sticky top-0 z-30 flex items-center h-14 px-4 bg-white/80 backdrop-blur-md border-b border-[var(--color-slate-200)]">
    <label for="sidebar-toggle" class="btn btn-ghost btn-sm btn-square lg:hidden mr-2">
        <x-icon name="bars-3" class="w-5 h-5 text-[var(--color-slate-500)]" />
    </label>

    <div class="flex-1"></div>

    <div class="lg:hidden">
        <div class="dropdown dropdown-end">
            <div tabindex="0" role="button" class="btn btn-ghost btn-sm btn-circle">
                <div class="w-7 h-7 rounded-full bg-[var(--color-blue-100)] flex items-center justify-center">
                    <span class="text-[var(--color-blue-600)] text-xs font-bold">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</span>
                </div>
            </div>
            <ul tabindex="0" class="dropdown-content menu bg-white rounded-xl z-50 w-48 p-1.5 shadow-lg border border-[var(--color-slate-200)]">
                <li class="px-3 py-2">
                    <p class="text-xs font-medium text-[var(--color-navy-900)]">{{ auth()->user()->name }}</p>
                </li>
                <div class="divider my-0.5"></div>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-[var(--color-danger)]">Logout</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</div>
