<x-layouts.app title="Pengguna">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-[var(--color-navy-900)]">Pengguna</h1>
            <p class="text-sm text-[var(--color-slate-400)] mt-0.5">Kelola akun pengguna dan hak akses</p>
        </div>
        <button @click="$dispatch('open-user-modal')"
                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-medium text-white
                       bg-gradient-to-r from-[var(--color-blue-500)] to-[var(--color-blue-600)]
                       hover:shadow-lg hover:shadow-[var(--color-blue-500)]/25 transition-all duration-200">
            <x-icon name="plus" class="w-4 h-4" />
            Tambah Pengguna
        </button>
    </div>

    {{-- Search --}}
    <div class="bg-white rounded-2xl border border-[var(--color-slate-200)] p-4 mb-4">
        <form method="GET" class="flex gap-2">
            <div class="relative flex-1">
                <x-icon name="magnifying-glass" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-[var(--color-slate-400)]" />
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, username, atau email..."
                    class="w-full pl-9 pr-4 py-2 text-sm rounded-xl border border-[var(--color-slate-200)] bg-[var(--color-slate-50)]
                           placeholder:text-[var(--color-slate-400)] focus:outline-none focus:border-[var(--color-blue-500)] focus:ring-2 focus:ring-[var(--color-blue-500)]/10 transition-all" />
            </div>
            <button type="submit" class="px-4 py-2 rounded-xl text-sm font-medium bg-[var(--color-slate-100)] text-[var(--color-slate-600)] hover:bg-[var(--color-slate-200)] transition-colors">Cari</button>
            @if(request('search'))
                <a href="{{ route('users.index') }}" class="px-4 py-2 rounded-xl text-sm font-medium text-[var(--color-slate-400)] hover:text-[var(--color-slate-600)]">Reset</a>
            @endif
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-[var(--color-slate-200)] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-[var(--color-slate-100)]">
                        <th class="px-5 py-3 text-left text-xs font-semibold text-[var(--color-slate-400)] uppercase tracking-wider">Pengguna</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-[var(--color-slate-400)] uppercase tracking-wider">Unit</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-[var(--color-slate-400)] uppercase tracking-wider">Peran</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold text-[var(--color-slate-400)] uppercase tracking-wider">Status</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-[var(--color-slate-400)] uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--color-slate-100)]">
                    @forelse ($users as $u)
                        <tr class="hover:bg-[var(--color-slate-50)] transition-colors">
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-[var(--color-blue-100)] flex items-center justify-center shrink-0">
                                        <span class="text-xs font-bold text-[var(--color-blue-600)]">{{ strtoupper(substr($u->name, 0, 1)) }}</span>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-[var(--color-navy-900)] truncate">{{ $u->name }}</p>
                                        <p class="text-xs text-[var(--color-slate-400)] truncate">{{ $u->username }} · {{ $u->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3.5">
                                @foreach($u->units as $unit)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-[var(--color-slate-100)] text-[var(--color-slate-600)]">{{ $unit->nama_unit }}</span>
                                @endforeach
                            </td>
                            <td class="px-5 py-3.5">
                                @foreach($u->units as $unit)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-[var(--color-blue-50)] text-[var(--color-blue-600)]">{{ ucfirst($unit->pivot->peran) }}</span>
                                @endforeach
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                @if($u->is_active)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-[var(--color-success)]/10 text-[var(--color-success)]">Aktif</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-[var(--color-slate-200)] text-[var(--color-slate-500)]">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <button @click="$dispatch('open-user-modal', {
                                        id: {{ $u->id }}, name: '{{ addslashes($u->name) }}', username: '{{ addslashes($u->username) }}',
                                        email: '{{ addslashes($u->email) }}', no_wa: '{{ addslashes($u->no_wa ?? '') }}',
                                        jabatan_struktural: '{{ addslashes($u->jabatan_struktural ?? '') }}',
                                        is_active: {{ $u->is_active ? 'true' : 'false' }},
                                        unit_id: {{ $u->units->first()?->id ?? 'null' }}, peran: '{{ $u->units->first()?->pivot?->peran ?? '' }}'
                                    })" class="p-1.5 rounded-lg text-[var(--color-slate-400)] hover:text-[var(--color-blue-500)] hover:bg-[var(--color-blue-50)] transition-colors" title="Edit">
                                        <x-icon name="pencil-square" class="w-4 h-4" />
                                    </button>
                                    @if($u->id !== auth()->id())
                                        <button @click="$dispatch('confirm-delete', { url: '{{ route('users.destroy', $u) }}', name: '{{ addslashes($u->name) }}' })"
                                                class="p-1.5 rounded-lg text-[var(--color-slate-400)] hover:text-[var(--color-danger)] hover:bg-red-50 transition-colors" title="Hapus">
                                            <x-icon name="trash" class="w-4 h-4" />
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center">
                                <x-icon name="users" class="w-10 h-10 text-[var(--color-slate-300)] mx-auto mb-3" />
                                <p class="text-sm text-[var(--color-slate-400)]">Belum ada data pengguna.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
            <div class="px-5 py-3 border-t border-[var(--color-slate-100)]">{{ $users->links() }}</div>
        @endif
    </div>

    {{-- Create/Edit Modal --}}
    <div x-cloak
         x-data="{
            open: false, isEdit: false, id: null,
            name: '', username: '', email: '', no_wa: '', jabatan_struktural: '',
            is_active: true, unit_id: '', peran: '',
            reset() { this.isEdit = false; this.id = null; this.name = ''; this.username = ''; this.email = ''; this.no_wa = ''; this.jabatan_struktural = ''; this.is_active = true; this.unit_id = ''; this.peran = ''; }
         }"
         @open-user-modal.window="
            if ($event.detail?.id) { isEdit = true; Object.assign($data, $event.detail); } else { reset(); }
            open = true;
         ">

        <div x-show="open" x-transition.opacity class="fixed inset-0 bg-black/40 z-50" @click="open = false"></div>
        <div x-show="open"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg p-6 my-8" @click.stop>
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-lg font-bold text-[var(--color-navy-900)]" x-text="isEdit ? 'Edit Pengguna' : 'Tambah Pengguna Baru'"></h3>
                    <button @click="open = false" class="p-1 rounded-lg hover:bg-[var(--color-slate-100)] transition-colors"><x-icon name="x-mark" class="w-5 h-5 text-[var(--color-slate-400)]" /></button>
                </div>

                <form method="POST" :action="isEdit ? '{{ url('users') }}/' + id : '{{ route('users.store') }}'">
                    @csrf
                    <template x-if="isEdit"><input type="hidden" name="_method" value="PUT" /></template>

                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-[var(--color-navy-700)] mb-1.5">Nama Lengkap *</label>
                            <input type="text" name="name" x-model="name" required class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-[var(--color-slate-200)] focus:outline-none focus:border-[var(--color-blue-500)] focus:ring-2 focus:ring-[var(--color-blue-500)]/10 transition-all" />
                            @error('name') <p class="text-xs text-[var(--color-danger)] mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-[var(--color-navy-700)] mb-1.5">Username *</label>
                                <input type="text" name="username" x-model="username" required class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-[var(--color-slate-200)] focus:outline-none focus:border-[var(--color-blue-500)] focus:ring-2 focus:ring-[var(--color-blue-500)]/10 transition-all" />
                                @error('username') <p class="text-xs text-[var(--color-danger)] mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-[var(--color-navy-700)] mb-1.5">Email *</label>
                                <input type="email" name="email" x-model="email" required class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-[var(--color-slate-200)] focus:outline-none focus:border-[var(--color-blue-500)] focus:ring-2 focus:ring-[var(--color-blue-500)]/10 transition-all" />
                                @error('email') <p class="text-xs text-[var(--color-danger)] mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-[var(--color-navy-700)] mb-1.5" x-text="isEdit ? 'Password (opsional)' : 'Password *'"></label>
                                <input type="password" name="password" x-bind:required="!isEdit" class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-[var(--color-slate-200)] focus:outline-none focus:border-[var(--color-blue-500)] focus:ring-2 focus:ring-[var(--color-blue-500)]/10 transition-all" />
                                @error('password') <p class="text-xs text-[var(--color-danger)] mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-[var(--color-navy-700)] mb-1.5">Konfirmasi</label>
                                <input type="password" name="password_confirmation" class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-[var(--color-slate-200)] focus:outline-none focus:border-[var(--color-blue-500)] focus:ring-2 focus:ring-[var(--color-blue-500)]/10 transition-all" />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-[var(--color-navy-700)] mb-1.5">No. WhatsApp</label>
                                <input type="text" name="no_wa" x-model="no_wa" placeholder="08xxx" class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-[var(--color-slate-200)] focus:outline-none focus:border-[var(--color-blue-500)] focus:ring-2 focus:ring-[var(--color-blue-500)]/10 transition-all" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-[var(--color-navy-700)] mb-1.5">Jabatan</label>
                                <input type="text" name="jabatan_struktural" x-model="jabatan_struktural" class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-[var(--color-slate-200)] focus:outline-none focus:border-[var(--color-blue-500)] focus:ring-2 focus:ring-[var(--color-blue-500)]/10 transition-all" />
                            </div>
                        </div>

                        <div class="h-px bg-[var(--color-slate-100)] my-1"></div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-[var(--color-navy-700)] mb-1.5">Unit *</label>
                                <select name="unit_id" x-model="unit_id" required class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-[var(--color-slate-200)] focus:outline-none focus:border-[var(--color-blue-500)] focus:ring-2 focus:ring-[var(--color-blue-500)]/10 transition-all">
                                    <option value="" disabled>Pilih unit...</option>
                                    @foreach($units as $unit) <option value="{{ $unit->id }}">{{ $unit->nama_unit }}</option> @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-[var(--color-navy-700)] mb-1.5">Peran *</label>
                                <select name="peran" x-model="peran" required class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-[var(--color-slate-200)] focus:outline-none focus:border-[var(--color-blue-500)] focus:ring-2 focus:ring-[var(--color-blue-500)]/10 transition-all">
                                    <option value="" disabled>Pilih peran...</option>
                                    @foreach($roles as $role) <option value="{{ $role }}">{{ ucfirst($role) }}</option> @endforeach
                                </select>
                            </div>
                        </div>

                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="hidden" name="is_active" value="0" />
                            <input type="checkbox" name="is_active" value="1" class="toggle toggle-sm toggle-primary" x-bind:checked="is_active" />
                            <span class="text-sm text-[var(--color-slate-600)]">Pengguna Aktif</span>
                        </label>
                    </div>

                    <div class="flex gap-2 mt-6 justify-end">
                        <button type="button" @click="open = false" class="px-4 py-2.5 rounded-xl text-sm font-medium text-[var(--color-slate-500)] hover:bg-[var(--color-slate-100)] transition-colors">Batal</button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl text-sm font-medium text-white bg-gradient-to-r from-[var(--color-blue-500)] to-[var(--color-blue-600)] hover:shadow-lg hover:shadow-[var(--color-blue-500)]/25 transition-all" x-text="isEdit ? 'Simpan' : 'Tambah'"></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if($errors->any())
        <script>document.addEventListener('alpine:init', () => setTimeout(() => window.dispatchEvent(new CustomEvent('open-user-modal')), 100));</script>
    @endif
</x-layouts.app>
