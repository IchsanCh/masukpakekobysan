<x-layouts.app title="Nasib Akhir Arsip">

    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <div>
            <h1 class="font-[var(--font-heading)] text-2xl font-bold text-[var(--color-navy-900)] tracking-tight">Nasib
                Akhir Arsip</h1>
            <p class="text-sm text-[var(--color-slate-500)] mt-0.5">Peninjauan, persetujuan pemusnahan, dan riwayat
                arsip</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('arsip.export-csv', request()->query()) }}"
                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-sm font-medium bg-white border border-[var(--color-slate-200)] text-[var(--color-slate-600)] hover:border-[var(--color-slate-300)] transition-colors">
                <x-icon name="arrow-down-tray" class="w-4 h-4" /> CSV
            </a>
            <a href="{{ route('arsip.export-pdf', request()->query()) }}"
                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-sm font-medium bg-white border border-[var(--color-slate-200)] text-[var(--color-slate-600)] hover:border-[var(--color-slate-300)] transition-colors">
                <x-icon name="arrow-down-tray" class="w-4 h-4" /> PDF
            </a>
        </div>
    </div>

    @if ($perluDitinjauMasuk + $perluDitinjauKeluar + $menungguMasuk + $menungguKeluar > 0)
        <div class="flex items-center gap-3 px-4 py-3 rounded-2xl bg-[var(--color-warning)]/10 border border-[var(--color-warning)]/20 mb-5">
            <x-icon name="bell" class="w-5 h-5 text-[var(--color-warning)] shrink-0" />
            <p class="text-sm text-[var(--color-navy-900)]">
                <span class="font-semibold">{{ $perluDitinjauMasuk + $perluDitinjauKeluar }}</span> arsip perlu
                ditinjau, dan
                <span class="font-semibold">{{ $menungguMasuk + $menungguKeluar }}</span> arsip menunggu persetujuan
                pemusnahan.
            </p>
        </div>
    @endif

    {{-- View tabs --}}
    <div class="flex gap-1.5 mb-4 overflow-x-auto">
        <a href="{{ route('arsip.index', ['tab' => 'tinjau', 'jenis' => $jenis]) }}"
            class="shrink-0 px-4 py-2 rounded-xl text-sm font-medium transition-colors
                  {{ $tab === 'tinjau' ? 'bg-[var(--color-blue-500)] text-white' : 'bg-white border border-[var(--color-slate-200)] text-[var(--color-slate-600)] hover:bg-[var(--color-slate-50)]' }}">
            Perlu Ditinjau
            @if ($perluDitinjauMasuk + $perluDitinjauKeluar > 0)
                <span class="ml-1 text-xs opacity-80">({{ $perluDitinjauMasuk + $perluDitinjauKeluar }})</span>
            @endif
        </a>
        <a href="{{ route('arsip.index', ['tab' => 'approval', 'jenis' => $jenis]) }}"
            class="shrink-0 px-4 py-2 rounded-xl text-sm font-medium transition-colors
                  {{ $tab === 'approval' ? 'bg-[var(--color-blue-500)] text-white' : 'bg-white border border-[var(--color-slate-200)] text-[var(--color-slate-600)] hover:bg-[var(--color-slate-50)]' }}">
            Menunggu Persetujuan
            @if ($menungguMasuk + $menungguKeluar > 0)
                <span class="ml-1 text-xs opacity-80">({{ $menungguMasuk + $menungguKeluar }})</span>
            @endif
        </a>
        <a href="{{ route('arsip.index', ['tab' => 'semua', 'jenis' => $jenis]) }}"
            class="shrink-0 px-4 py-2 rounded-xl text-sm font-medium transition-colors
                  {{ $tab === 'semua' ? 'bg-[var(--color-blue-500)] text-white' : 'bg-white border border-[var(--color-slate-200)] text-[var(--color-slate-600)] hover:bg-[var(--color-slate-50)]' }}">
            Semua Arsip
        </a>
        <a href="{{ route('arsip.index', ['tab' => 'riwayat']) }}"
            class="shrink-0 px-4 py-2 rounded-xl text-sm font-medium transition-colors
                  {{ $tab === 'riwayat' ? 'bg-[var(--color-blue-500)] text-white' : 'bg-white border border-[var(--color-slate-200)] text-[var(--color-slate-600)] hover:bg-[var(--color-slate-50)]' }}">
            Riwayat Keputusan
        </a>
    </div>

    {{-- Jenis toggle (gak relevan buat tab riwayat, itu udah lintas jenis) --}}
    @if ($tab !== 'riwayat')
        <div class="flex gap-1.5 mb-4">
            <a href="{{ route('arsip.index', array_merge(request()->query(), ['jenis' => 'masuk'])) }}"
                class="px-3 py-1.5 rounded-lg text-xs font-medium transition-colors
                      {{ $jenis === 'masuk' ? 'bg-[var(--color-navy-700)] text-white' : 'bg-[var(--color-slate-100)] text-[var(--color-slate-500)]' }}">
                Surat Masuk
            </a>
            <a href="{{ route('arsip.index', array_merge(request()->query(), ['jenis' => 'keluar'])) }}"
                class="px-3 py-1.5 rounded-lg text-xs font-medium transition-colors
                      {{ $jenis === 'keluar' ? 'bg-[var(--color-navy-700)] text-white' : 'bg-[var(--color-slate-100)] text-[var(--color-slate-500)]' }}">
                Surat Keluar
            </a>
        </div>
    @endif

    <div
        class="bg-white rounded-2xl border border-[var(--color-slate-200)]/70
                shadow-[0_1px_2px_rgba(15,23,42,0.04),0_12px_28px_-10px_rgba(15,23,42,0.10)] overflow-hidden">

        {{-- Advanced filter --}}
        <div x-data="{ open: {{ request()->hasAny(['search','status_arsip','tanggal_dari','tanggal_sampai','jenis_log','keputusan_log']) ? 'true' : 'false' }} }"
            class="border-b border-[var(--color-slate-100)] bg-[var(--color-slate-50)]/50">
            <button type="button" @click="open = !open"
                class="w-full flex items-center justify-between px-5 py-3 text-sm font-medium text-[var(--color-slate-600)]">
                <span class="inline-flex items-center gap-1.5"><x-icon name="magnifying-glass" class="w-4 h-4" /> Filter
                    Lanjutan</span>
                <span class="inline-flex transition-transform" :class="open ? 'rotate-90' : ''">
                    <x-icon name="chevron-right" class="w-4 h-4" />
                </span>
            </button>
            <div x-show="open" x-cloak class="px-5 pb-4">
                <form method="GET" class="flex flex-wrap gap-2 items-end">
                    <input type="hidden" name="tab" value="{{ $tab }}">
                    @if ($tab !== 'riwayat')
                        <input type="hidden" name="jenis" value="{{ $jenis }}">
                    @endif

                    <div class="min-w-[180px]">
                        <label class="block text-[11px] font-medium text-[var(--color-slate-500)] mb-1">Cari</label>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Nomor surat / isi ringkasan"
                            class="w-full px-3 py-2 text-sm rounded-xl border border-[var(--color-slate-200)] bg-white focus:outline-none focus:border-[var(--color-blue-500)]" />
                    </div>

                    @if ($tab === 'semua')
                        <div>
                            <label class="block text-[11px] font-medium text-[var(--color-slate-500)] mb-1">Status
                                Arsip</label>
                            <select name="status_arsip"
                                class="px-3 py-2 text-sm rounded-xl border border-[var(--color-slate-200)] bg-white focus:outline-none focus:border-[var(--color-blue-500)]">
                                <option value="">Semua</option>
                                <option value="aktif" @selected(request('status_arsip') === 'aktif')>Aktif</option>
                                <option value="inaktif" @selected(request('status_arsip') === 'inaktif')>Inaktif
                                </option>
                                <option value="perlu_ditinjau" @selected(request('status_arsip') === 'perlu_ditinjau')>
                                    Perlu Ditinjau</option>
                                <option value="menunggu_musnah"
                                    @selected(request('status_arsip') === 'menunggu_musnah')>Menunggu Persetujuan
                                </option>
                                <option value="permanen" @selected(request('status_arsip') === 'permanen')>Permanen
                                </option>
                            </select>
                        </div>
                    @endif

                    @if ($tab === 'riwayat')
                        <div>
                            <label class="block text-[11px] font-medium text-[var(--color-slate-500)] mb-1">Keputusan</label>
                            <select name="keputusan_log"
                                class="px-3 py-2 text-sm rounded-xl border border-[var(--color-slate-200)] bg-white focus:outline-none focus:border-[var(--color-blue-500)]">
                                <option value="">Semua</option>
                                <option value="musnah" @selected(request('keputusan_log') === 'musnah')>Dimusnahkan</option>
                                <option value="permanen" @selected(request('keputusan_log') === 'permanen')>Ditetapkan Permanen</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-medium text-[var(--color-slate-500)] mb-1">Jenis</label>
                            <select name="jenis_log"
                                class="px-3 py-2 text-sm rounded-xl border border-[var(--color-slate-200)] bg-white focus:outline-none focus:border-[var(--color-blue-500)]">
                                <option value="">Semua</option>
                                <option value="masuk" @selected(request('jenis_log') === 'masuk')>Surat Masuk</option>
                                <option value="keluar" @selected(request('jenis_log') === 'keluar')>Surat Keluar
                                </option>
                            </select>
                        </div>
                    @endif

                    <div>
                        <label class="block text-[11px] font-medium text-[var(--color-slate-500)] mb-1">
                            {{ $tab === 'riwayat' ? 'Keputusan Dari' : 'Tanggal Surat Dari' }}</label>
                        <input type="date" name="tanggal_dari" value="{{ request('tanggal_dari') }}"
                            class="px-3 py-2 text-sm rounded-xl border border-[var(--color-slate-200)] bg-white focus:outline-none focus:border-[var(--color-blue-500)]" />
                    </div>
                    <div>
                        <label class="block text-[11px] font-medium text-[var(--color-slate-500)] mb-1">
                            {{ $tab === 'riwayat' ? 'Keputusan Sampai' : 'Tanggal Surat Sampai' }}</label>
                        <input type="date" name="tanggal_sampai" value="{{ request('tanggal_sampai') }}"
                            class="px-3 py-2 text-sm rounded-xl border border-[var(--color-slate-200)] bg-white focus:outline-none focus:border-[var(--color-blue-500)]" />
                    </div>

                    <button type="submit"
                        class="px-4 py-2 rounded-xl text-sm font-medium bg-[var(--color-blue-500)] text-white hover:bg-[var(--color-blue-600)] transition-colors">Terapkan</button>
                    @if (request()->hasAny(['search', 'status_arsip', 'tanggal_dari', 'tanggal_sampai', 'jenis_log', 'keputusan_log']))
                        <a href="{{ route('arsip.index', ['tab' => $tab, 'jenis' => $jenis]) }}"
                            class="px-4 py-2 rounded-xl text-sm font-medium text-[var(--color-slate-400)] hover:text-[var(--color-slate-600)]">Reset</a>
                    @endif
                </form>
            </div>
        </div>

{{-- tab perlu ditinjau --}}
        @if ($tab === 'tinjau')
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-[var(--color-slate-50)]/60 border-b border-[var(--color-slate-100)]">
                            <th class="px-5 py-3 text-left text-[11px] font-semibold text-[var(--color-slate-500)] uppercase tracking-wider">Surat</th>
                            <th class="px-5 py-3 text-left text-[11px] font-semibold text-[var(--color-slate-500)] uppercase tracking-wider">Klasifikasi Retensi</th>
                            <th class="px-5 py-3 text-right text-[11px] font-semibold text-[var(--color-slate-500)] uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--color-slate-100)]">
                        @forelse ($suratList as $s)
                            <tr class="even:bg-[var(--color-slate-50)]/40 align-top">
                                <td class="px-5 py-3.5">
                                    <p class="text-sm font-semibold text-[var(--color-navy-900)]">{{ $s->nomor_surat }}</p>
                                    <p class="text-[11px] text-[var(--color-slate-400)] mt-0.5">{{ $s->tanggal_surat->format('d M Y') }}</p>
                                </td>
                                <td class="px-5 py-3.5">
                                    @if ($s->referensiRetensi)
                                        <p class="text-xs font-mono text-[var(--color-slate-500)]">{{ $s->referensiRetensi->kode_klasifikasi }}</p>
                                        @if ($s->referensiRetensi->keterangan_nasib_akhir)
                                            <p class="text-[11px] text-[var(--color-warning)] mt-1 italic">"{{ $s->referensiRetensi->keterangan_nasib_akhir }}"</p>
                                        @endif
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <form method="POST" action="{{ route('arsip.permanen', ['jenis' => $jenis, 'id' => $s->id]) }}">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-medium border border-[var(--color-navy-700)]/20 text-[var(--color-navy-700)] hover:bg-[var(--color-navy-700)]/5 transition-colors whitespace-nowrap">Tetapkan Permanen</button>
                                        </form>
                                        <form method="POST" action="{{ route('arsip.musnah', ['jenis' => $jenis, 'id' => $s->id]) }}" onsubmit="return confirm('Tandai surat ini untuk dimusnahkan? Masih perlu persetujuan terpisah sebelum beneran dihapus.')">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-medium bg-[var(--color-warning)]/10 text-[var(--color-warning)] hover:bg-[var(--color-warning)]/20 transition-colors whitespace-nowrap">Tandai Musnah</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-5 py-14 text-center text-sm text-[var(--color-slate-500)]">Gak ada surat yang perlu ditinjau.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif

{{-- tab approval --}}
        @if ($tab === 'approval')
            <form method="POST" action="{{ route('arsip.bulk-approve') }}" x-data="{ selected: [] }"
                onsubmit="return confirm('Setujui & musnahkan semua arsip terpilih? File dan data akan DIHAPUS PERMANEN dan tidak bisa dikembalikan.')">
                @csrf @method('DELETE')
                <div class="px-5 py-3 border-b border-[var(--color-slate-100)] flex items-center justify-between bg-[var(--color-slate-50)]/40">
                    <label class="inline-flex items-center gap-2 text-xs text-[var(--color-slate-500)]">
                        <input type="checkbox" @click="selected = $event.target.checked ? Array.from(document.querySelectorAll('.approval-checkbox')).map(el => el.value) : []"
                            class="w-4 h-4 rounded border-[var(--color-slate-300)]" />
                        Pilih semua di halaman ini
                    </label>
                    <button type="submit" :disabled="selected.length === 0"
                        class="px-4 py-2 rounded-xl text-sm font-semibold text-white bg-[var(--color-danger)] disabled:opacity-40 disabled:cursor-not-allowed transition-opacity">
                        Setujui &amp; Musnahkan Terpilih (<span x-text="selected.length"></span>)
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-[var(--color-slate-50)]/60 border-b border-[var(--color-slate-100)]">
                                <th class="px-5 py-3 w-10"></th>
                                <th class="px-5 py-3 text-left text-[11px] font-semibold text-[var(--color-slate-500)] uppercase tracking-wider">Surat</th>
                                <th class="px-5 py-3 text-left text-[11px] font-semibold text-[var(--color-slate-500)] uppercase tracking-wider">Klasifikasi</th>
                                <th class="px-5 py-3 text-right text-[11px] font-semibold text-[var(--color-slate-500)] uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[var(--color-slate-100)]">
                            @forelse ($suratList as $s)
                                <tr class="even:bg-[var(--color-slate-50)]/40 align-top">
                                    <td class="px-5 py-3.5">
                                        <input type="checkbox" name="items[]" value="{{ $jenis }}:{{ $s->id }}" x-model="selected"
                                            class="approval-checkbox w-4 h-4 rounded border-[var(--color-slate-300)]" />
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <p class="text-sm font-semibold text-[var(--color-navy-900)]">{{ $s->nomor_surat }}</p>
                                        <p class="text-[11px] text-[var(--color-slate-400)] mt-0.5">{{ $s->tanggal_surat->format('d M Y') }}</p>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <p class="text-xs font-mono text-[var(--color-slate-500)]">{{ $s->referensiRetensi->kode_klasifikasi ?? '-' }}</p>
                                    </td>
                                    <td class="px-5 py-3.5 text-right">
                                        <button type="button"
                                            @click="if (confirm('Setujui & musnahkan surat ini sekarang? Tidak bisa dikembalikan.')) { document.getElementById('approve-{{ $jenis }}-{{ $s->id }}').submit() }"
                                            class="px-3 py-1.5 rounded-lg text-xs font-medium bg-[var(--color-danger)]/10 text-[var(--color-danger)] hover:bg-[var(--color-danger)]/20 transition-colors whitespace-nowrap">
                                            Setujui &amp; Musnahkan
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-5 py-14 text-center text-sm text-[var(--color-slate-500)]">Gak ada arsip yang menunggu persetujuan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </form>
            {{-- Form approve tunggal, terpisah dari form bulk di atas (biar gak nested form) --}}
            @foreach ($suratList as $s)
                <form id="approve-{{ $jenis }}-{{ $s->id }}" method="POST" action="{{ route('arsip.approve', ['jenis' => $jenis, 'id' => $s->id]) }}" class="hidden">
                    @csrf @method('DELETE')
                </form>
            @endforeach
        @endif

{{-- tab semua arsip --}}
        @if ($tab === 'semua')
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-[var(--color-slate-50)]/60 border-b border-[var(--color-slate-100)]">
                            <th class="px-5 py-3 text-left text-[11px] font-semibold text-[var(--color-slate-500)] uppercase tracking-wider">Surat</th>
                            <th class="px-5 py-3 text-left text-[11px] font-semibold text-[var(--color-slate-500)] uppercase tracking-wider">Klasifikasi</th>
                            <th class="px-5 py-3 text-center text-[11px] font-semibold text-[var(--color-slate-500)] uppercase tracking-wider">Status</th>
                            <th class="px-5 py-3 text-right text-[11px] font-semibold text-[var(--color-slate-500)] uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--color-slate-100)]">
                        @forelse ($suratList as $s)
                            @php
                                $badge = [
                                    'aktif' => ['success', 'Aktif'],
                                    'inaktif' => ['slate', 'Inaktif'],
                                    'perlu_ditinjau' => ['warning', 'Perlu Ditinjau'],
                                    'menunggu_musnah' => ['warning', 'Menunggu Persetujuan'],
                                    'permanen' => ['navy', 'Permanen'],
                                ][$s->status_arsip] ?? ['slate', $s->status_arsip];
                                $badgeColor = ['success' => 'bg-[var(--color-success)]/10 text-[var(--color-success)]', 'warning' => 'bg-[var(--color-warning)]/10 text-[var(--color-warning)]', 'navy' => 'bg-[var(--color-navy-700)]/10 text-[var(--color-navy-700)]', 'slate' => 'bg-[var(--color-slate-100)] text-[var(--color-slate-500)]'][$badge[0]];
                            @endphp
                            <tr class="even:bg-[var(--color-slate-50)]/40 align-top">
                                <td class="px-5 py-3.5">
                                    <p class="text-sm font-semibold text-[var(--color-navy-900)]">{{ $s->nomor_surat }}</p>
                                    <p class="text-[11px] text-[var(--color-slate-400)] mt-0.5">{{ $s->tanggal_surat->format('d M Y') }}</p>
                                </td>
                                <td class="px-5 py-3.5">
                                    <p class="text-xs font-mono text-[var(--color-slate-500)]">{{ $s->referensiRetensi->kode_klasifikasi ?? '-' }}</p>
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $badgeColor }}">{{ $badge[1] }}</span>
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <a href="{{ $jenis === 'keluar' ? route('surat-keluar.edit', $s) : route('surat-masuk.edit', $s) }}" class="text-xs font-medium text-[var(--color-blue-600)] hover:underline">Lihat/Edit</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-5 py-14 text-center text-sm text-[var(--color-slate-500)]">Tidak ada surat yang cocok.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif

        {{-- tab riwayat keputusan (dimusnahkan + ditetapkan permanen) --}}
        @if ($tab === 'riwayat')
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-[var(--color-slate-50)]/60 border-b border-[var(--color-slate-100)]">
                            <th class="px-5 py-3 text-left text-[11px] font-semibold text-[var(--color-slate-500)] uppercase tracking-wider">Keputusan</th>
                            <th class="px-5 py-3 text-left text-[11px] font-semibold text-[var(--color-slate-500)] uppercase tracking-wider">Jenis</th>
                            <th class="px-5 py-3 text-left text-[11px] font-semibold text-[var(--color-slate-500)] uppercase tracking-wider">Surat</th>
                            <th class="px-5 py-3 text-left text-[11px] font-semibold text-[var(--color-slate-500)] uppercase tracking-wider">Klasifikasi</th>
                            <th class="px-5 py-3 text-left text-[11px] font-semibold text-[var(--color-slate-500)] uppercase tracking-wider">Waktu Keputusan</th>
                            <th class="px-5 py-3 text-left text-[11px] font-semibold text-[var(--color-slate-500)] uppercase tracking-wider">Diputuskan Oleh</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--color-slate-100)]">
                        @forelse ($suratList as $l)
                            <tr class="even:bg-[var(--color-slate-50)]/40 align-top">
                                <td class="px-5 py-3.5">
                                    @if ($l->keputusan === 'permanen')
                                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium bg-[var(--color-navy-700)]/10 text-[var(--color-navy-700)]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[var(--color-navy-700)]"></span>Permanen
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium bg-[var(--color-danger)]/10 text-[var(--color-danger)]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[var(--color-danger)]"></span>Dimusnahkan
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-[var(--color-slate-100)] text-[var(--color-slate-500)]">{{ $l->jenis === 'keluar' ? 'Surat Keluar' : 'Surat Masuk' }}</span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <p class="text-sm font-semibold text-[var(--color-navy-900)]">{{ $l->nomor_surat }}</p>
                                    <p class="text-[11px] text-[var(--color-slate-400)] mt-0.5">{{ $l->tanggal_surat->format('d M Y') }}</p>
                                </td>
                                <td class="px-5 py-3.5">
                                    <p class="text-xs font-mono text-[var(--color-slate-500)]">{{ $l->kode_klasifikasi ?? '-' }}</p>
                                </td>
                                <td class="px-5 py-3.5">
                                    <p class="text-xs text-[var(--color-slate-600)] font-medium">{{ $l->created_at->format('d M Y, H:i') }}</p>
                                </td>
                                <td class="px-5 py-3.5 text-sm text-[var(--color-slate-600)]">{{ $l->pemutus->name ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-5 py-14 text-center text-sm text-[var(--color-slate-500)]">Belum ada keputusan arsip yang tercatat.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif

        @if ($suratList->hasPages())
            <div class="px-5 py-3 border-t border-[var(--color-slate-100)] bg-[var(--color-slate-50)]/50">{{ $suratList->links() }}</div>
        @endif
    </div>

</x-layouts.app>