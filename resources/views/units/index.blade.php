<x-layouts.app title="Master Unit / Bidang">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="font-[var(--font-heading)] text-2xl font-bold text-[var(--color-navy-900)] tracking-tight">Unit /
                Bidang</h1>
            <p class="text-sm text-[var(--color-slate-500)] mt-0.5">Kelola daftar unit dan bidang instansi</p>
        </div>
        <button @click="$dispatch('open-unit-modal')"
            class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-sm font-semibold text-white
                       bg-gradient-to-br from-[var(--color-blue-500)] to-[var(--color-blue-600)]
                       shadow-[0_1px_2px_rgba(15,23,42,0.1),0_4px_10px_-2px_rgba(37,99,235,0.35)]
                       hover:shadow-[0_1px_2px_rgba(15,23,42,0.1),0_8px_16px_-2px_rgba(37,99,235,0.45)]
                       hover:-translate-y-px active:translate-y-0 transition-all duration-200">
            <x-icon name="plus" class="w-4 h-4" />
            Tambah Unit
        </button>
    </div>

    {{-- Panel: toolbar + table merged into one surface --}}
    <div
        class="bg-white rounded-2xl border border-[var(--color-slate-200)]/70
                shadow-[0_1px_2px_rgba(15,23,42,0.04),0_12px_28px_-10px_rgba(15,23,42,0.10)] overflow-hidden">

        {{-- Toolbar --}}
        <div class="px-5 py-4 border-b border-[var(--color-slate-100)] bg-[var(--color-slate-50)]/50">
            <form method="GET" class="flex gap-2">
                <div class="relative flex-1 max-w-sm">
                    <x-icon name="magnifying-glass"
                        class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-[var(--color-slate-400)]" />
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama unit..."
                        class="w-full pl-9 pr-4 py-2 text-sm rounded-xl border border-[var(--color-slate-200)] bg-white
                               placeholder:text-[var(--color-slate-400)] focus:outline-none focus:border-[var(--color-blue-500)] focus:ring-2 focus:ring-[var(--color-blue-500)]/10 transition-all" />
                </div>
                <button type="submit"
                    class="px-4 py-2 rounded-xl text-sm font-medium bg-white border border-[var(--color-slate-200)] text-[var(--color-slate-600)] hover:border-[var(--color-slate-300)] hover:text-[var(--color-navy-900)] transition-colors">Cari</button>
                @if (request('search'))
                    <a href="{{ route('units.index') }}"
                        class="px-4 py-2 rounded-xl text-sm font-medium text-[var(--color-slate-400)] hover:text-[var(--color-slate-600)]">Reset</a>
                @endif
            </form>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-[var(--color-slate-50)]/60 border-b border-[var(--color-slate-100)]">
                        <th
                            class="px-5 py-3 text-left text-[11px] font-semibold text-[var(--color-slate-500)] uppercase tracking-wider">
                            Nama Unit</th>
                        <th
                            class="px-5 py-3 text-left text-[11px] font-semibold text-[var(--color-slate-500)] uppercase tracking-wider">
                            Singkatan</th>
                        <th
                            class="px-5 py-3 text-center text-[11px] font-semibold text-[var(--color-slate-500)] uppercase tracking-wider">
                            Jumlah User</th>
                        <th
                            class="px-5 py-3 text-center text-[11px] font-semibold text-[var(--color-slate-500)] uppercase tracking-wider">
                            Status</th>
                        <th
                            class="px-5 py-3 text-right text-[11px] font-semibold text-[var(--color-slate-500)] uppercase tracking-wider">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--color-slate-100)]">
                    @forelse ($units as $unit)
                        <tr
                            class="even:bg-[var(--color-slate-50)]/40 hover:bg-[var(--color-blue-50)]/40 transition-colors">
                            <td class="px-5 py-3.5 text-sm font-semibold text-[var(--color-navy-900)]">
                                {{ $unit->nama_unit }}</td>
                            <td class="px-5 py-3.5">
                                @if ($unit->singkatan)
                                    <span
                                        class="font-mono text-xs font-semibold text-[var(--color-blue-600)] bg-[var(--color-blue-50)] px-2 py-0.5 rounded-md">{{ $unit->singkatan }}</span>
                                @else
                                    <span class="text-sm text-[var(--color-slate-300)]">-</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-sm text-[var(--color-slate-500)] text-center">
                                {{ $unit->users_count }}</td>
                            <td class="px-5 py-3.5 text-center">
                                @if ($unit->is_active)
                                    <span
                                        class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium bg-[var(--color-success)]/10 text-[var(--color-success)]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[var(--color-success)]"></span>Aktif
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium bg-[var(--color-slate-100)] text-[var(--color-slate-500)]">
                                        <span
                                            class="w-1.5 h-1.5 rounded-full bg-[var(--color-slate-400)]"></span>Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <button
                                        @click="$dispatch('open-unit-modal', {
                                        id: {{ $unit->id }},
                                        nama_unit: '{{ addslashes($unit->nama_unit) }}',
                                        singkatan: '{{ addslashes($unit->singkatan ?? '') }}',
                                        is_active: {{ $unit->is_active ? 'true' : 'false' }}
                                    })"
                                        class="p-1.5 rounded-lg text-[var(--color-slate-400)] hover:text-[var(--color-blue-500)] hover:bg-[var(--color-blue-50)] transition-colors"
                                        title="Edit">
                                        <x-icon name="pencil-square" class="w-4 h-4" />
                                    </button>
                                    <button
                                        @click="$dispatch('confirm-delete', {
                                        url: '{{ route('units.destroy', $unit) }}',
                                        name: '{{ addslashes($unit->nama_unit) }}'
                                    })"
                                        class="p-1.5 rounded-lg text-[var(--color-slate-400)] hover:text-[var(--color-danger)] hover:bg-red-50 transition-colors"
                                        title="Hapus">
                                        <x-icon name="trash" class="w-4 h-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-14 text-center">
                                <div
                                    class="w-12 h-12 rounded-2xl bg-[var(--color-slate-100)] flex items-center justify-center mx-auto mb-3">
                                    <x-icon name="building-office" class="w-5 h-5 text-[var(--color-slate-400)]" />
                                </div>
                                <p class="text-sm font-medium text-[var(--color-slate-500)]">Belum ada data unit.</p>
                                <p class="text-xs text-[var(--color-slate-400)] mt-0.5">Tambahkan unit/bidang baru untuk
                                    mulai mengelola struktur.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($units->hasPages())
            <div class="px-5 py-3 border-t border-[var(--color-slate-100)] bg-[var(--color-slate-50)]/50">
                {{ $units->links() }}</div>
        @endif
    </div>

    {{-- Create/Edit Modal --}}
    <div x-cloak x-data="{
        open: false,
        isEdit: false,
        id: null,
        nama_unit: '',
        singkatan: '',
        is_active: true,
    
        reset() {
            this.isEdit = false;
            this.id = null;
            this.nama_unit = '';
            this.singkatan = '';
            this.is_active = true;
        }
    }"
        @open-unit-modal.window="
            if ($event.detail && $event.detail.id) {
                isEdit = true;
                id = $event.detail.id;
                nama_unit = $event.detail.nama_unit;
                singkatan = $event.detail.singkatan;
                is_active = $event.detail.is_active;
            } else {
                reset();
            }
            open = true;
         ">

        {{-- Backdrop --}}
        <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-[var(--color-navy-900)]/50 backdrop-blur-[2px] z-50" @click="open = false"></div>

        {{-- Modal --}}
        <div x-show="open" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95" class="fixed inset-0 z-50 flex items-center justify-center p-4"
            @click="open = false">

            <div class="bg-white rounded-2xl shadow-[0_20px_60px_-15px_rgba(11,25,44,0.35)] w-full max-w-md p-6 max-h-[90vh] overflow-y-auto"
                @click.stop>
                <div class="flex items-center justify-between mb-5">
                    <h3 class="font-[var(--font-heading)] text-lg font-bold text-[var(--color-navy-900)] tracking-tight"
                        x-text="isEdit ? 'Edit Unit' : 'Tambah Unit Baru'"></h3>
                    <button @click="open = false"
                        class="p-1 rounded-lg hover:bg-[var(--color-slate-100)] transition-colors"><x-icon
                            name="x-mark" class="w-5 h-5 text-[var(--color-slate-400)]" /></button>
                </div>

                <form method="POST" :action="isEdit ? '{{ url('units') }}/' + id : '{{ route('units.store') }}'">
                    @csrf
                    <template x-if="isEdit"><input type="hidden" name="_method" value="PUT" /></template>

                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-[var(--color-navy-700)] mb-1.5">Nama Unit
                                *</label>
                            <input type="text" name="nama_unit" x-model="nama_unit" required
                                placeholder="Contoh: Bidang Perizinan"
                                class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-[var(--color-slate-200)] focus:outline-none focus:border-[var(--color-blue-500)] focus:ring-2 focus:ring-[var(--color-blue-500)]/10 transition-all" />
                            @error('nama_unit')
                                <p class="text-xs text-[var(--color-danger)] mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label
                                class="block text-sm font-medium text-[var(--color-navy-700)] mb-1.5">Singkatan</label>
                            <input type="text" name="singkatan" x-model="singkatan" placeholder="Contoh: BIDIZIN"
                                class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-[var(--color-slate-200)] focus:outline-none focus:border-[var(--color-blue-500)] focus:ring-2 focus:ring-[var(--color-blue-500)]/10 transition-all" />
                        </div>

                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="hidden" name="is_active" value="0" />
                            <input type="checkbox" name="is_active" value="1"
                                class="toggle toggle-sm toggle-primary" x-bind:checked="is_active" />
                            <span class="text-sm text-[var(--color-slate-600)]">Unit Aktif</span>
                        </label>
                    </div>

                    <div class="flex gap-2 mt-6 justify-end">
                        <button type="button" @click="open = false"
                            class="px-4 py-2.5 rounded-xl text-sm font-medium text-[var(--color-slate-500)] hover:bg-[var(--color-slate-100)] transition-colors">Batal</button>
                        <button type="submit"
                            class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-gradient-to-br from-[var(--color-blue-500)] to-[var(--color-blue-600)] shadow-[0_4px_10px_-2px_rgba(37,99,235,0.35)] hover:shadow-[0_8px_16px_-2px_rgba(37,99,235,0.45)] transition-all"
                            x-text="isEdit ? 'Simpan' : 'Tambah'"></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Auto-open modal on validation error --}}
    @if ($errors->any())
        <script>
            document.addEventListener('alpine:init', () => {
                setTimeout(() => window.dispatchEvent(new CustomEvent('open-unit-modal')), 100);
            });
        </script>
    @endif
</x-layouts.app>
