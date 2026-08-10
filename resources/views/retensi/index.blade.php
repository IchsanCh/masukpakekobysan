<x-layouts.app title="Retensi Arsip">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="font-[var(--font-heading)] text-2xl font-bold text-[var(--color-navy-900)] tracking-tight">Retensi
                Arsip</h1>
            <p class="text-sm text-[var(--color-slate-500)] mt-0.5">Jadwal retensi sesuai Perbup No. 10/2023 & No.
                63/2024</p>
        </div>
        <button @click="$dispatch('open-retensi-modal')"
            class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-sm font-semibold text-white
                       bg-gradient-to-br from-[var(--color-blue-500)] to-[var(--color-blue-600)]
                       shadow-[0_1px_2px_rgba(15,23,42,0.1),0_4px_10px_-2px_rgba(37,99,235,0.35)]
                       hover:shadow-[0_1px_2px_rgba(15,23,42,0.1),0_8px_16px_-2px_rgba(37,99,235,0.45)]
                       hover:-translate-y-px active:translate-y-0 transition-all duration-200">
            <x-icon name="plus" class="w-4 h-4" />
            Tambah Retensi
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
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari kode klasifikasi atau nama kegiatan..."
                        class="w-full pl-9 pr-4 py-2 text-sm rounded-xl border border-[var(--color-slate-200)] bg-white
                               placeholder:text-[var(--color-slate-400)] focus:outline-none focus:border-[var(--color-blue-500)] focus:ring-2 focus:ring-[var(--color-blue-500)]/10 transition-all" />
                </div>
                <button type="submit"
                    class="px-4 py-2 rounded-xl text-sm font-medium bg-white border border-[var(--color-slate-200)] text-[var(--color-slate-600)] hover:border-[var(--color-slate-300)] hover:text-[var(--color-navy-900)] transition-colors">Cari</button>
                @if (request('search'))
                    <a href="{{ route('retensi.index') }}"
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
                            Kode</th>
                        <th
                            class="px-5 py-3 text-left text-[11px] font-semibold text-[var(--color-slate-500)] uppercase tracking-wider">
                            Jenis Arsip</th>
                        <th
                            class="px-5 py-3 text-center text-[11px] font-semibold text-[var(--color-slate-500)] uppercase tracking-wider">
                            Aktif</th>
                        <th
                            class="px-5 py-3 text-center text-[11px] font-semibold text-[var(--color-slate-500)] uppercase tracking-wider">
                            Inaktif</th>
                        <th
                            class="px-5 py-3 text-left text-[11px] font-semibold text-[var(--color-slate-500)] uppercase tracking-wider">
                            Nasib Akhir</th>
                        <th
                            class="px-5 py-3 text-center text-[11px] font-semibold text-[var(--color-slate-500)] uppercase tracking-wider">
                            Batas</th>
                        <th
                            class="px-5 py-3 text-right text-[11px] font-semibold text-[var(--color-slate-500)] uppercase tracking-wider">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--color-slate-100)]">
                    @forelse ($retensis as $r)
                        <tr
                            class="even:bg-[var(--color-slate-50)]/40 hover:bg-[var(--color-blue-50)]/40 transition-colors">
                            <td class="px-5 py-3.5">
                                <span
                                    class="font-mono text-sm font-semibold text-[var(--color-blue-600)] bg-[var(--color-blue-50)] px-2 py-0.5 rounded-md">{{ $r->kode_klasifikasi }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-sm text-[var(--color-navy-900)] max-w-xs">
                                {{ $r->nama_kegiatan }}</td>
                            <td class="px-5 py-3.5 text-sm text-[var(--color-slate-500)] text-center">
                                {{ $r->masa_aktif }} th</td>
                            <td class="px-5 py-3.5 text-sm text-[var(--color-slate-500)] text-center">
                                {{ $r->masa_inaktif }} th</td>
                            <td class="px-5 py-3.5">
                                @switch($r->nasib_akhir_default)
                                    @case('musnah')
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium bg-[var(--color-danger)]/10 text-[var(--color-danger)]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[var(--color-danger)]"></span>Musnah
                                        </span>
                                    @break

                                    @case('permanen')
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium bg-[var(--color-success)]/10 text-[var(--color-success)]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[var(--color-success)]"></span>Permanen
                                        </span>
                                    @break

                                    @case('dinilai_kembali')
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium bg-[var(--color-warning)]/10 text-[var(--color-warning)]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[var(--color-warning)]"></span>Dinilai
                                            Kembali
                                        </span>
                                    @break
                                @endswitch
                                @if ($r->keterangan_nasib_akhir)
                                    <p
                                        class="text-[11px] text-[var(--color-slate-400)] mt-0.5 leading-tight max-w-[200px]">
                                        {{ $r->keterangan_nasib_akhir }}</p>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-sm text-[var(--color-slate-500)] text-center">
                                {{ $r->default_batas_waktu_hari ?? '-' }} hr</td>
                            <td class="px-5 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <button
                                        @click="$dispatch('open-retensi-modal', {
                                        id: {{ $r->id }},
                                        kode_klasifikasi: '{{ addslashes($r->kode_klasifikasi) }}',
                                        nama_kegiatan: '{{ addslashes($r->nama_kegiatan) }}',
                                        masa_aktif: {{ $r->masa_aktif }},
                                        masa_inaktif: {{ $r->masa_inaktif }},
                                        nasib_akhir_default: '{{ $r->nasib_akhir_default }}',
                                        keterangan_nasib_akhir: '{{ addslashes($r->keterangan_nasib_akhir ?? '') }}',
                                        default_batas_waktu_hari: {{ $r->default_batas_waktu_hari ?? 14 }}
                                    })"
                                        class="p-1.5 rounded-lg text-[var(--color-slate-400)] hover:text-[var(--color-blue-500)] hover:bg-[var(--color-blue-50)] transition-colors"
                                        title="Edit">
                                        <x-icon name="pencil-square" class="w-4 h-4" />
                                    </button>
                                    <button
                                        @click="$dispatch('confirm-delete', {
                                        url: '{{ route('retensi.destroy', $r) }}',
                                        name: '{{ addslashes($r->kode_klasifikasi . ' - ' . $r->nama_kegiatan) }}'
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
                                <td colspan="7" class="px-5 py-14 text-center">
                                    <div
                                        class="w-12 h-12 rounded-2xl bg-[var(--color-slate-100)] flex items-center justify-center mx-auto mb-3">
                                        <x-icon name="archive-box" class="w-5 h-5 text-[var(--color-slate-400)]" />
                                    </div>
                                    <p class="text-sm font-medium text-[var(--color-slate-500)]">Belum ada data retensi
                                        arsip.</p>
                                    <p class="text-xs text-[var(--color-slate-400)] mt-0.5">Tambahkan jadwal retensi baru
                                        sesuai regulasi.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($retensis->hasPages())
                <div class="px-5 py-3 border-t border-[var(--color-slate-100)] bg-[var(--color-slate-50)]/50">
                    {{ $retensis->links() }}</div>
            @endif
        </div>

        {{-- Create/Edit Modal --}}
        <div x-cloak x-data="{
            open: false,
            isEdit: false,
            id: null,
            submitting: false,
            kode_klasifikasi: '',
            nama_kegiatan: '',
            masa_aktif: 2,
            masa_inaktif: 3,
            nasib_akhir_default: 'musnah',
            keterangan_nasib_akhir: '',
            default_batas_waktu_hari: 14,
            reset() { this.isEdit = false;
                this.id = null;
                this.kode_klasifikasi = '';
                this.nama_kegiatan = '';
                this.masa_aktif = 2;
                this.masa_inaktif = 3;
                this.nasib_akhir_default = 'musnah';
                this.keterangan_nasib_akhir = '';
                this.default_batas_waktu_hari = 14; }
        }"
            @open-retensi-modal.window="
            submitting = false;
            if ($event.detail?.id) { isEdit = true; Object.assign($data, $event.detail); } else { reset(); }
            open = true;
         ">

            <div x-show="open" x-transition.opacity
                class="fixed inset-0 bg-[var(--color-navy-900)]/50 backdrop-blur-[2px] z-50" @click="open = false"></div>
            <div x-show="open" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95" class="fixed inset-0 z-50 flex items-center justify-center p-4"
                @click="open = false">
                <div class="bg-white rounded-2xl shadow-[0_20px_60px_-15px_rgba(11,25,44,0.35)] w-full max-w-lg p-6 max-h-[90vh] overflow-y-auto"
                    @click.stop>
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="font-[var(--font-heading)] text-lg font-bold text-[var(--color-navy-900)] tracking-tight"
                            x-text="isEdit ? 'Edit Retensi' : 'Tambah Retensi Baru'"></h3>
                        <button @click="open = false"
                            class="p-1 rounded-lg hover:bg-[var(--color-slate-100)] transition-colors"><x-icon
                                name="x-mark" class="w-5 h-5 text-[var(--color-slate-400)]" /></button>
                    </div>

                    <form method="POST" :action="isEdit ? '{{ url('retensi') }}/' + id : '{{ route('retensi.store') }}'"
                        @submit="submitting = true">
                        @csrf
                        <template x-if="isEdit"><input type="hidden" name="_method" value="PUT" /></template>

                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-[var(--color-navy-700)] mb-1.5">Kode
                                    Klasifikasi *</label>
                                <input type="text" name="kode_klasifikasi" x-model="kode_klasifikasi" required
                                    class="w-full px-3.5 py-2.5 text-sm font-mono rounded-xl border border-[var(--color-slate-200)]
                                       focus:outline-none focus:border-[var(--color-blue-500)] focus:ring-2 focus:ring-[var(--color-blue-500)]/10 transition-all"
                                    placeholder="Contoh: 500.16.7.2" />
                                <p class="text-[11px] text-[var(--color-slate-400)] mt-1">Sesuai Perbup No. 10 Tahun 2023
                                </p>
                                @error('kode_klasifikasi')
                                    <p class="text-xs text-[var(--color-danger)] mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-[var(--color-navy-700)] mb-1.5">Jenis Arsip /
                                    Nama Kegiatan *</label>
                                <input type="text" name="nama_kegiatan" x-model="nama_kegiatan" required
                                    class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-[var(--color-slate-200)]
                                       focus:outline-none focus:border-[var(--color-blue-500)] focus:ring-2 focus:ring-[var(--color-blue-500)]/10 transition-all"
                                    placeholder="Pelayanan Perizinan (Izin Usaha, IMB, dll)" />
                                @error('nama_kegiatan')
                                    <p class="text-xs text-[var(--color-danger)] mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="h-px bg-[var(--color-slate-100)] my-1"></div>

                            <div class="grid grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-sm font-medium text-[var(--color-navy-700)] mb-1.5">Aktif (th)
                                        *</label>
                                    <input type="number" name="masa_aktif" x-model="masa_aktif" min="0" required
                                        class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-[var(--color-slate-200)]
                                           focus:outline-none focus:border-[var(--color-blue-500)] focus:ring-2 focus:ring-[var(--color-blue-500)]/10 transition-all" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-[var(--color-navy-700)] mb-1.5">Inaktif
                                        (th) *</label>
                                    <input type="number" name="masa_inaktif" x-model="masa_inaktif" min="0"
                                        required
                                        class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-[var(--color-slate-200)]
                                           focus:outline-none focus:border-[var(--color-blue-500)] focus:ring-2 focus:ring-[var(--color-blue-500)]/10 transition-all" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-[var(--color-navy-700)] mb-1.5">Batas
                                        (hari)</label>
                                    <input type="number" name="default_batas_waktu_hari"
                                        x-model="default_batas_waktu_hari" min="1"
                                        class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-[var(--color-slate-200)]
                                           focus:outline-none focus:border-[var(--color-blue-500)] focus:ring-2 focus:ring-[var(--color-blue-500)]/10 transition-all" />
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-[var(--color-navy-700)] mb-1.5">Nasib Akhir
                                    *</label>
                                <select name="nasib_akhir_default" x-model="nasib_akhir_default" required
                                    class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-[var(--color-slate-200)]
                                       focus:outline-none focus:border-[var(--color-blue-500)] focus:ring-2 focus:ring-[var(--color-blue-500)]/10 transition-all">
                                    <option value="musnah">Musnah</option>
                                    <option value="permanen">Permanen</option>
                                    <option value="dinilai_kembali">Dinilai Kembali</option>
                                </select>
                            </div>

                            <div x-show="nasib_akhir_default === 'dinilai_kembali'" x-transition x-cloak>
                                <label class="block text-sm font-medium text-[var(--color-navy-700)] mb-1.5">Keterangan
                                    Nasib Akhir</label>
                                <input type="text" name="keterangan_nasib_akhir" x-model="keterangan_nasib_akhir"
                                    class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-[var(--color-slate-200)]
                                       focus:outline-none focus:border-[var(--color-blue-500)] focus:ring-2 focus:ring-[var(--color-blue-500)]/10 transition-all"
                                    placeholder="Musnah, Kecuali Notulen terkait Kebijakan Permanen" />
                                <p class="text-[11px] text-[var(--color-slate-400)] mt-1">Verbatim dari Perbup No. 63/2024
                                </p>
                            </div>
                        </div>

                        <div class="flex gap-2 mt-6 justify-end">
                            <button type="button" @click="open = false" :disabled="submitting"
                                class="px-4 py-2.5 rounded-xl text-sm font-medium text-[var(--color-slate-500)] hover:bg-[var(--color-slate-100)] transition-colors disabled:opacity-50 disabled:cursor-not-allowed">Batal</button>
                            <button type="submit" :disabled="submitting"
                                class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-gradient-to-br from-[var(--color-blue-500)] to-[var(--color-blue-600)] shadow-[0_4px_10px_-2px_rgba(37,99,235,0.35)] hover:shadow-[0_8px_16px_-2px_rgba(37,99,235,0.45)] transition-all disabled:opacity-60 disabled:cursor-not-allowed inline-flex items-center gap-2">
                                <span x-show="submitting" class="loading loading-spinner loading-xs"></span>
                                <span x-text="submitting ? 'Menyimpan...' : (isEdit ? 'Simpan' : 'Tambah')"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @if ($errors->any())
            <script>
                document.addEventListener('alpine:init', () => setTimeout(() => window.dispatchEvent(new CustomEvent(
                    'open-retensi-modal')), 100));
            </script>
        @endif
    </x-layouts.app>
