<x-layouts.app title="Surat Keluar">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="font-[var(--font-heading)] text-2xl font-bold text-[var(--color-navy-900)] tracking-tight">Surat
                Keluar</h1>
            <p class="text-sm text-[var(--color-slate-500)] mt-0.5">Daftar surat keluar yang tercatat</p>
        </div>
        <a href="{{ route('surat-keluar.create') }}"
            class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-sm font-semibold text-white
                  bg-gradient-to-br from-[var(--color-blue-500)] to-[var(--color-blue-600)]
                  shadow-[0_1px_2px_rgba(15,23,42,0.1),0_4px_10px_-2px_rgba(37,99,235,0.35)]
                  hover:shadow-[0_1px_2px_rgba(15,23,42,0.1),0_8px_16px_-2px_rgba(37,99,235,0.45)]
                  hover:-translate-y-px active:translate-y-0 transition-all duration-200">
            <x-icon name="plus" class="w-4 h-4" />
            Tambah Surat
        </a>
    </div>

    <div
        class="bg-white rounded-2xl border border-[var(--color-slate-200)]/70
                shadow-[0_1px_2px_rgba(15,23,42,0.04),0_12px_28px_-10px_rgba(15,23,42,0.10)] overflow-hidden">

        {{-- Toolbar --}}
        <div class="px-5 py-4 border-b border-[var(--color-slate-100)] bg-[var(--color-slate-50)]/50">
            <form method="GET" class="flex flex-wrap gap-2">
                <div class="relative flex-1 min-w-[200px] max-w-sm">
                    <x-icon name="magnifying-glass"
                        class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-[var(--color-slate-400)]" />
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari no. surat, tujuan, atau isi..."
                        class="w-full pl-9 pr-4 py-2 text-sm rounded-xl border border-[var(--color-slate-200)] bg-white
                               placeholder:text-[var(--color-slate-400)] focus:outline-none focus:border-[var(--color-blue-500)] focus:ring-2 focus:ring-[var(--color-blue-500)]/10 transition-all" />
                </div>
                <select name="status_arsip" onchange="this.form.submit()"
                    class="px-3 py-2 text-sm rounded-xl border border-[var(--color-slate-200)] bg-white text-[var(--color-slate-600)]
                           focus:outline-none focus:border-[var(--color-blue-500)] focus:ring-2 focus:ring-[var(--color-blue-500)]/10 transition-all">
                    <option value="">Semua Status Arsip</option>
                    <option value="aktif" @selected(request('status_arsip') === 'aktif')>Aktif</option>
                    <option value="inaktif" @selected(request('status_arsip') === 'inaktif')>Inaktif</option>
                    <option value="permanen" @selected(request('status_arsip') === 'permanen')>Permanen</option>
                    <option value="musnah" @selected(request('status_arsip') === 'musnah')>Musnah</option>
                </select>
                <button type="submit"
                    class="px-4 py-2 rounded-xl text-sm font-medium bg-white border border-[var(--color-slate-200)] text-[var(--color-slate-600)] hover:border-[var(--color-slate-300)] hover:text-[var(--color-navy-900)] transition-colors">Cari</button>
                @if (request('search') || request('status_arsip'))
                    <a href="{{ route('surat-keluar.index') }}"
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
                            Kepada</th>
                        <th
                            class="px-5 py-3 text-left text-[11px] font-semibold text-[var(--color-slate-500)] uppercase tracking-wider">
                            Dibuat Oleh</th>
                        <th
                            class="px-5 py-3 text-center text-[11px] font-semibold text-[var(--color-slate-500)] uppercase tracking-wider">
                            Status Arsip</th>
                        <th
                            class="px-5 py-3 text-center text-[11px] font-semibold text-[var(--color-slate-500)] uppercase tracking-wider">
                            File</th>
                        <th
                            class="px-5 py-3 text-right text-[11px] font-semibold text-[var(--color-slate-500)] uppercase tracking-wider">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--color-slate-100)]">
                    @forelse ($suratKeluars as $s)
                        <tr
                            class="even:bg-[var(--color-slate-50)]/40 hover:bg-[var(--color-blue-50)]/40 transition-colors">
                            <td class="px-5 py-3.5">
                                <p class="text-sm font-semibold text-[var(--color-navy-900)]">{{ $s->nomor_surat }}</p>
                                <p class="text-[11px] text-[var(--color-slate-400)] mt-1">
                                    {{ $s->tanggal_surat->format('d M Y') }}</p>
                            </td>
                            <td class="px-5 py-3.5">
                                <p class="text-sm text-[var(--color-slate-600)]">{{ $s->kepada }}</p>
                                <p class="text-xs text-[var(--color-slate-400)] truncate max-w-xs">
                                    {{ \Illuminate\Support\Str::limit($s->isi_ringkasan, 50) }}</p>
                            </td>
                            <td class="px-5 py-3.5 text-sm text-[var(--color-slate-600)]">
                                {{ $s->pembuat->name ?? '—' }}</td>
                            <td class="px-5 py-3.5 text-center">
                                @switch($s->status_arsip)
                                    @case('permanen')
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium bg-[var(--color-navy-700)]/10 text-[var(--color-navy-700)]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[var(--color-navy-700)]"></span>Permanen
                                        </span>
                                    @break

                                    @case('perlu_ditinjau')
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium bg-[var(--color-warning)]/10 text-[var(--color-warning)]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[var(--color-warning)]"></span>Perlu
                                            Ditinjau
                                        </span>
                                    @break

                                    @case('musnah')
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium bg-[var(--color-danger)]/10 text-[var(--color-danger)]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[var(--color-danger)]"></span>Musnah
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
                            <td class="px-5 py-3.5 text-center">
                                @if ($s->file_surat)
                                    <a href="{{ asset('storage/' . $s->file_surat) }}" target="_blank"
                                        class="inline-flex p-1.5 rounded-lg text-[var(--color-slate-400)] hover:text-[var(--color-blue-500)] hover:bg-[var(--color-blue-50)] transition-colors"
                                        title="Lihat File">
                                        <x-icon name="eye" class="w-4 h-4" />
                                    </a>
                                @else
                                    <span class="text-xs text-[var(--color-slate-300)]">-</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('surat-keluar.edit', $s) }}"
                                        class="p-1.5 rounded-lg text-[var(--color-slate-400)] hover:text-[var(--color-blue-500)] hover:bg-[var(--color-blue-50)] transition-colors"
                                        title="Edit">
                                        <x-icon name="pencil-square" class="w-4 h-4" />
                                    </a>
                                    <button
                                        @click="$dispatch('confirm-delete', {
                                        url: '{{ route('surat-keluar.destroy', $s) }}',
                                        name: '{{ addslashes($s->nomor_surat) }}'
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
                                <td colspan="6" class="px-5 py-14 text-center">
                                    <div
                                        class="w-12 h-12 rounded-2xl bg-[var(--color-slate-100)] flex items-center justify-center mx-auto mb-3">
                                        <x-icon name="paper-airplane" class="w-5 h-5 text-[var(--color-slate-400)]" />
                                    </div>
                                    <p class="text-sm font-medium text-[var(--color-slate-500)]">Belum ada surat keluar.
                                    </p>
                                    <p class="text-xs text-[var(--color-slate-400)] mt-0.5">Klik "Tambah Surat" untuk
                                        mencatat surat keluar pertama.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($suratKeluars->hasPages())
                <div class="px-5 py-3 border-t border-[var(--color-slate-100)] bg-[var(--color-slate-50)]/50">
                    {{ $suratKeluars->links() }}</div>
            @endif
        </div>

    </x-layouts.app>