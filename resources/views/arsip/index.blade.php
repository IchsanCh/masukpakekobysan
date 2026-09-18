<x-layouts.app title="Nasib Akhir Arsip">

    <div class="mb-6">
        <h1 class="font-[var(--font-heading)] text-2xl font-bold text-[var(--color-navy-900)] tracking-tight">Nasib
            Akhir Arsip</h1>
        <p class="text-sm text-[var(--color-slate-500)] mt-0.5">Peninjauan arsip yang masa retensinya sudah habis</p>
    </div>

    @if ($perluDitinjauMasuk + $perluDitinjauKeluar > 0)
        <div class="flex items-center gap-3 px-4 py-3 rounded-2xl bg-[var(--color-warning)]/10 border border-[var(--color-warning)]/20 mb-5">
            <x-icon name="bell" class="w-5 h-5 text-[var(--color-warning)] shrink-0" />
            <p class="text-sm text-[var(--color-navy-900)]">
                Ada <span class="font-semibold">{{ $perluDitinjauMasuk + $perluDitinjauKeluar }} arsip</span> yang
                perlu ditinjau manual ({{ $perluDitinjauMasuk }} surat masuk, {{ $perluDitinjauKeluar }} surat keluar)
                — masa retensinya habis tapi nasib akhirnya butuh keputusan berdasarkan pengecualian di kolom
                keterangan.
            </p>
        </div>
    @endif

    {{-- Tabs jenis --}}
    <div class="flex gap-1.5 mb-5">
        <a href="{{ route('arsip.index', ['jenis' => 'masuk']) }}"
            class="px-4 py-2 rounded-xl text-sm font-medium transition-colors
                  {{ $jenis === 'masuk' ? 'bg-[var(--color-blue-500)] text-white' : 'bg-white border border-[var(--color-slate-200)] text-[var(--color-slate-600)] hover:bg-[var(--color-slate-50)]' }}">
            Surat Masuk
            @if ($perluDitinjauMasuk > 0)
                <span class="ml-1 text-xs opacity-80">({{ $perluDitinjauMasuk }})</span>
            @endif
        </a>
        <a href="{{ route('arsip.index', ['jenis' => 'keluar']) }}"
            class="px-4 py-2 rounded-xl text-sm font-medium transition-colors
                  {{ $jenis === 'keluar' ? 'bg-[var(--color-blue-500)] text-white' : 'bg-white border border-[var(--color-slate-200)] text-[var(--color-slate-600)] hover:bg-[var(--color-slate-50)]' }}">
            Surat Keluar
            @if ($perluDitinjauKeluar > 0)
                <span class="ml-1 text-xs opacity-80">({{ $perluDitinjauKeluar }})</span>
            @endif
        </a>
    </div>

    <div
        class="bg-white rounded-2xl border border-[var(--color-slate-200)]/70
                shadow-[0_1px_2px_rgba(15,23,42,0.04),0_12px_28px_-10px_rgba(15,23,42,0.10)] overflow-hidden">

        {{-- Toolbar --}}
        <div class="px-5 py-4 border-b border-[var(--color-slate-100)] bg-[var(--color-slate-50)]/50">
            <form method="GET" class="flex flex-wrap gap-2">
                <input type="hidden" name="jenis" value="{{ $jenis }}">
                <div class="relative flex-1 min-w-[200px] max-w-sm">
                    <x-icon name="magnifying-glass"
                        class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-[var(--color-slate-400)]" />
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari nomor surat atau isi ringkasan..."
                        class="w-full pl-9 pr-4 py-2 text-sm rounded-xl border border-[var(--color-slate-200)] bg-white
                               placeholder:text-[var(--color-slate-400)] focus:outline-none focus:border-[var(--color-blue-500)] focus:ring-2 focus:ring-[var(--color-blue-500)]/10 transition-all" />
                </div>
                <select name="status_arsip" onchange="this.form.submit()"
                    class="px-3 py-2 text-sm rounded-xl border border-[var(--color-slate-200)] bg-white text-[var(--color-slate-600)]
                           focus:outline-none focus:border-[var(--color-blue-500)] focus:ring-2 focus:ring-[var(--color-blue-500)]/10 transition-all">
                    <option value="">Semua Status</option>
                    <option value="perlu_ditinjau" @selected(request('status_arsip') === 'perlu_ditinjau')>Perlu Ditinjau</option>
                    <option value="aktif" @selected(request('status_arsip') === 'aktif')>Aktif</option>
                    <option value="inaktif" @selected(request('status_arsip') === 'inaktif')>Inaktif</option>
                    <option value="permanen" @selected(request('status_arsip') === 'permanen')>Permanen</option>
                </select>
                <button type="submit"
                    class="px-4 py-2 rounded-xl text-sm font-medium bg-white border border-[var(--color-slate-200)] text-[var(--color-slate-600)] hover:border-[var(--color-slate-300)] hover:text-[var(--color-navy-900)] transition-colors">Cari</button>
                @if (request('search') || request('status_arsip'))
                    <a href="{{ route('arsip.index', ['jenis' => $jenis]) }}"
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
                            Surat</th>
                        <th
                            class="px-5 py-3 text-left text-[11px] font-semibold text-[var(--color-slate-500)] uppercase tracking-wider">
                            Klasifikasi Retensi</th>
                        <th
                            class="px-5 py-3 text-center text-[11px] font-semibold text-[var(--color-slate-500)] uppercase tracking-wider">
                            Status Arsip</th>
                        <th
                            class="px-5 py-3 text-right text-[11px] font-semibold text-[var(--color-slate-500)] uppercase tracking-wider">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--color-slate-100)]">
                    @forelse ($suratList as $s)
                        <tr class="even:bg-[var(--color-slate-50)]/40 hover:bg-[var(--color-blue-50)]/40 transition-colors align-top">
                            <td class="px-5 py-3.5">
                                <p class="text-sm font-semibold text-[var(--color-navy-900)]">{{ $s->nomor_surat }}</p>
                                <p class="text-[11px] text-[var(--color-slate-400)] mt-0.5">
                                    {{ $s->tanggal_surat->format('d M Y') }}</p>
                            </td>
                            <td class="px-5 py-3.5">
                                @if ($s->referensiRetensi)
                                    <p class="text-xs font-mono text-[var(--color-slate-500)]">
                                        {{ $s->referensiRetensi->kode_klasifikasi }}</p>
                                    <p class="text-xs text-[var(--color-slate-600)] mt-0.5">
                                        {{ \Illuminate\Support\Str::limit($s->referensiRetensi->nama_kegiatan, 50) }}
                                    </p>
                                    @if ($s->status_arsip === 'perlu_ditinjau' && $s->referensiRetensi->keterangan_nasib_akhir)
                                        <p class="text-[11px] text-[var(--color-warning)] mt-1.5 italic">
                                            "{{ $s->referensiRetensi->keterangan_nasib_akhir }}"
                                        </p>
                                    @endif
                                @else
                                    <span class="text-xs text-[var(--color-slate-300)]">Tanpa referensi retensi</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                @switch($s->status_arsip)
                                    @case('perlu_ditinjau')
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium bg-[var(--color-warning)]/10 text-[var(--color-warning)]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[var(--color-warning)]"></span>Perlu
                                            Ditinjau
                                        </span>
                                    @break

                                    @case('permanen')
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium bg-[var(--color-navy-700)]/10 text-[var(--color-navy-700)]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[var(--color-navy-700)]"></span>Permanen
                                        </span>
                                    @break

                                    @case('inaktif')
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium bg-[var(--color-slate-100)] text-[var(--color-slate-500)]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[var(--color-slate-400)]"></span>Inaktif
                                        </span>
                                    @break

                                    @default
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium bg-[var(--color-success)]/10 text-[var(--color-success)]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[var(--color-success)]"></span>Aktif
                                        </span>
                                @endswitch
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                @if ($s->status_arsip === 'perlu_ditinjau')
                                    <div class="flex items-center justify-end gap-1.5">
                                        <form method="POST" action="{{ route('arsip.permanen', ['jenis' => $jenis, 'id' => $s->id]) }}">
                                            @csrf
                                            <button type="submit"
                                                class="px-3 py-1.5 rounded-lg text-xs font-medium border border-[var(--color-navy-700)]/20 text-[var(--color-navy-700)] hover:bg-[var(--color-navy-700)]/5 transition-colors whitespace-nowrap">
                                                Tetapkan Permanen
                                            </button>
                                        </form>
                                        <button type="button"
                                            @click="$dispatch('confirm-delete', {
                                                url: '{{ route('arsip.musnah', ['jenis' => $jenis, 'id' => $s->id]) }}',
                                                name: 'arsip {{ addslashes($s->nomor_surat) }} (file & record akan dihapus permanen)'
                                            })"
                                            class="px-3 py-1.5 rounded-lg text-xs font-medium bg-[var(--color-danger)]/10 text-[var(--color-danger)] hover:bg-[var(--color-danger)]/20 transition-colors whitespace-nowrap">
                                            Musnahkan
                                        </button>
                                    </div>
                                @else
                                    <a href="{{ $jenis === 'keluar' ? route('surat-keluar.edit', $s) : route('surat-masuk.edit', $s) }}"
                                        class="text-xs font-medium text-[var(--color-blue-600)] hover:underline">
                                        Lihat/Edit
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-14 text-center">
                                <div
                                    class="w-12 h-12 rounded-2xl bg-[var(--color-slate-100)] flex items-center justify-center mx-auto mb-3">
                                    <x-icon name="archive-box" class="w-5 h-5 text-[var(--color-slate-400)]" />
                                </div>
                                <p class="text-sm font-medium text-[var(--color-slate-500)]">Tidak ada surat yang
                                    cocok.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($suratList->hasPages())
            <div class="px-5 py-3 border-t border-[var(--color-slate-100)] bg-[var(--color-slate-50)]/50">
                {{ $suratList->links() }}
            </div>
        @endif
    </div>

</x-layouts.app>