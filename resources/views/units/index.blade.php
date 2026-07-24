<x-layouts.app title="Master Unit / Bidang">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Unit / Bidang</h1>
        <button @click="$dispatch('open-unit-modal')" class="btn btn-primary btn-sm">+ Tambah Unit</button>
    </div>

    {{-- Search --}}
    <x-card compact class="mb-4">
        <form method="GET" class="flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama unit..."
                class="input input-bordered input-sm flex-1" />
            <button type="submit" class="btn btn-sm btn-ghost">Cari</button>
            @if(request('search'))
                <a href="{{ route('units.index') }}" class="btn btn-sm btn-ghost">Reset</a>
            @endif
        </form>
    </x-card>

    {{-- Table --}}
    <x-card>
        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nama Unit</th>
                        <th>Singkatan</th>
                        <th>Jumlah User</th>
                        <th>Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($units as $unit)
                        <tr class="hover">
                            <td class="font-medium">{{ $unit->nama_unit }}</td>
                            <td>{{ $unit->singkatan ?? '-' }}</td>
                            <td>{{ $unit->users_count }}</td>
                            <td>
                                @if($unit->is_active)
                                    <span class="badge badge-success badge-sm">Aktif</span>
                                @else
                                    <span class="badge badge-ghost badge-sm">Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <button @click="$dispatch('open-unit-modal', {
                                    id: {{ $unit->id }},
                                    nama_unit: '{{ addslashes($unit->nama_unit) }}',
                                    singkatan: '{{ addslashes($unit->singkatan ?? '') }}',
                                    is_active: {{ $unit->is_active ? 'true' : 'false' }}
                                })" class="btn btn-ghost btn-xs">Edit</button>

                                <button @click="$dispatch('confirm-delete', {
                                    url: '{{ route('units.destroy', $unit) }}',
                                    name: '{{ addslashes($unit->nama_unit) }}'
                                })" class="btn btn-ghost btn-xs text-error">Hapus</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-base-content/50 py-8">Belum ada data unit.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $units->links() }}</div>
    </x-card>

    {{-- Create/Edit Modal --}}
    <div x-cloak
         x-data="{
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
        <div x-show="open"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black/40 z-50" @click="open = false"></div>

        {{-- Modal --}}
        <div x-show="open"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
             class="fixed inset-0 z-50 flex items-center justify-center p-4">

            <div class="bg-base-100 rounded-2xl shadow-xl w-full max-w-md p-6" @click.stop>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold" x-text="isEdit ? 'Edit Unit' : 'Tambah Unit Baru'"></h3>
                    <button @click="open = false" class="btn btn-ghost btn-sm btn-circle">✕</button>
                </div>

                <form method="POST"
                      :action="isEdit ? '{{ url('units') }}/' + id : '{{ route('units.store') }}'">
                    @csrf
                    <template x-if="isEdit"><input type="hidden" name="_method" value="PUT" /></template>

                    <div class="space-y-4">
                        <div>
                            <label class="label"><span class="label-text font-medium">Nama Unit *</span></label>
                            <input type="text" name="nama_unit" x-model="nama_unit"
                                class="input input-bordered w-full" placeholder="Contoh: Bidang Perizinan" required />
                            @error('nama_unit') <p class="text-error text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="label"><span class="label-text font-medium">Singkatan</span></label>
                            <input type="text" name="singkatan" x-model="singkatan"
                                class="input input-bordered w-full" placeholder="Contoh: BIDIZIN" />
                        </div>

                        <div class="form-control">
                            <label class="label cursor-pointer justify-start gap-3">
                                <input type="hidden" name="is_active" value="0" />
                                <input type="checkbox" name="is_active" value="1"
                                    class="toggle toggle-primary" x-bind:checked="is_active" />
                                <span class="label-text">Unit Aktif</span>
                            </label>
                        </div>
                    </div>

                    <div class="flex gap-2 mt-6 justify-end">
                        <button type="button" @click="open = false" class="btn btn-ghost">Batal</button>
                        <button type="submit" class="btn btn-primary" x-text="isEdit ? 'Simpan' : 'Tambah'"></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Auto-open modal on validation error --}}
    @if($errors->any())
        <script>
            document.addEventListener('alpine:init', () => {
                setTimeout(() => window.dispatchEvent(new CustomEvent('open-unit-modal')), 100);
            });
        </script>
    @endif
</x-layouts.app>
