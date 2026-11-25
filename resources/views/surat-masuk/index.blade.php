<x-layouts.app title="Surat Masuk">

    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <div>
            <h1 class="font-[var(--font-heading)] text-2xl font-bold text-[var(--color-navy-900)] tracking-tight">Surat
                Masuk</h1>
            <p class="text-sm text-[var(--color-slate-500)] mt-0.5">
                @if ($canManage)
                    Daftar surat masuk yang tercatat
                @elseif ($canViewAll)
                    Semua surat masuk (hanya lihat)
                @elseif ($user->hasRole('kabid'))
                    Surat yang didisposisikan ke bidang Anda
                @else
                    Surat yang didisposisikan kepada Anda
                @endif
            </p>
        </div>
        @if ($canManage)
        <a href="{{ route('surat-masuk.create') }}"
            class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-sm font-semibold text-white
                  bg-gradient-to-br from-[var(--color-blue-500)] to-[var(--color-blue-600)]
                  shadow-[0_1px_2px_rgba(15,23,42,0.1),0_4px_10px_-2px_rgba(37,99,235,0.35)]
                  hover:shadow-[0_1px_2px_rgba(15,23,42,0.1),0_8px_16px_-2px_rgba(37,99,235,0.45)]
                  hover:-translate-y-px active:translate-y-0 transition-all duration-200">
            <x-icon name="plus" class="w-4 h-4" />
            Tambah Surat
        </a>
        @endif
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
                        placeholder="Cari no. surat atau pengirim..."
                        class="w-full pl-9 pr-4 py-2 text-sm rounded-xl border border-[var(--color-slate-200)] bg-white
                               placeholder:text-[var(--color-slate-400)] focus:outline-none focus:border-[var(--color-blue-500)] focus:ring-2 focus:ring-[var(--color-blue-500)]/10 transition-all" />
                </div>
                <select name="status" onchange="this.form.submit()"
                    class="px-3 py-2 text-sm rounded-xl border border-[var(--color-slate-200)] bg-white text-[var(--color-slate-600)]
                           focus:outline-none focus:border-[var(--color-blue-500)] focus:ring-2 focus:ring-[var(--color-blue-500)]/10 transition-all">
                    <option value="">Semua Status</option>
                    <option value="baru" @selected(request('status') === 'baru')>Baru</option>
                    <option value="diproses" @selected(request('status') === 'diproses')>Diproses</option>
                    <option value="selesai" @selected(request('status') === 'selesai')>Selesai</option>
                </select>
                <button type="submit"
                    class="px-4 py-2 rounded-xl text-sm font-medium bg-white border border-[var(--color-slate-200)] text-[var(--color-slate-600)] hover:border-[var(--color-slate-300)] hover:text-[var(--color-navy-900)] transition-colors">Cari</button>
                @if (request('search') || request('status'))
                    <a href="{{ route('surat-masuk.index') }}"
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
                            Pengirim</th>
                        <th
                            class="px-5 py-3 text-center text-[11px] font-semibold text-[var(--color-slate-500)] uppercase tracking-wider">
                            Sifat</th>
                        <th
                            class="px-5 py-3 text-center text-[11px] font-semibold text-[var(--color-slate-500)] uppercase tracking-wider">
                            Status</th>
                        @unless ($canViewAll)
                            <th
                                class="px-5 py-3 text-center text-[11px] font-semibold text-[var(--color-slate-500)] uppercase tracking-wider">
                                Disposisi Saya</th>
                        @endunless
                        <th
                            class="px-5 py-3 text-center text-[11px] font-semibold text-[var(--color-slate-500)] uppercase tracking-wider">
                            File</th>
                        <th
                            class="px-5 py-3 text-right text-[11px] font-semibold text-[var(--color-slate-500)] uppercase tracking-wider">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--color-slate-100)]">
                    @forelse ($suratMasuks as $s)
                        <tr
                            class="even:bg-[var(--color-slate-50)]/40 hover:bg-[var(--color-blue-50)]/40 transition-colors">
                            <td class="px-5 py-3.5">
                                <p class="text-sm font-semibold text-[var(--color-navy-900)]">{{ $s->nomor_surat }}</p>
                                <p class="text-[11px] text-[var(--color-slate-400)] mt-0.5">
                                    Diterima {{ $s->tanggal_diterima->format('d M Y') }}</p>
                                <p class="text-xs text-[var(--color-slate-400)] truncate max-w-xs">
                                    {{ \Illuminate\Support\Str::limit($s->isi_ringkasan, 50) }}</p>
                            </td>
                            <td class="px-5 py-3.5 text-sm text-[var(--color-slate-600)]">{{ $s->pengirim }}</td>
                            <td class="px-5 py-3.5 text-center">
                                @switch($s->sifat_surat)
                                    @case('sangat_segera')
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium bg-[var(--color-danger)]/10 text-[var(--color-danger)]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[var(--color-danger)]"></span>Sangat Segera
                                        </span>
                                    @break

                                    @case('segera')
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium bg-[var(--color-warning)]/10 text-[var(--color-warning)]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[var(--color-warning)]"></span>Segera
                                        </span>
                                    @break

                                    @case('rahasia')
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium bg-[var(--color-navy-700)]/10 text-[var(--color-navy-700)]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[var(--color-navy-700)]"></span>Rahasia
                                        </span>
                                    @break

                                    @default
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium bg-[var(--color-slate-100)] text-[var(--color-slate-500)]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[var(--color-slate-400)]"></span>Biasa
                                        </span>
                                @endswitch
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                @switch($s->status_disposisi)
                                    @case('selesai')
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium bg-[var(--color-success)]/10 text-[var(--color-success)]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[var(--color-success)]"></span>Selesai
                                        </span>
                                    @break

                                    @case('diproses')
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium bg-[var(--color-blue-500)]/10 text-[var(--color-blue-600)]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[var(--color-blue-500)]"></span>Diproses
                                        </span>
                                    @break

                                    @default
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium bg-[var(--color-slate-100)] text-[var(--color-slate-500)]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[var(--color-slate-400)]"></span>Baru
                                        </span>
                                @endswitch
                            </td>
                            @unless ($canViewAll)
                                <td class="px-5 py-3.5 text-center">
                                    @php
                                        $mine = $s->disposisi->filter(fn ($d) => $d->canBeActedBy($user));
                                        $pending = $mine->first(fn ($d) => ! $d->isTerminal());
                                        $labelDisp = ['menunggu' => 'Menunggu', 'diterima' => 'Diterima', 'diproses' => 'Diproses'];
                                    @endphp
                                    @if ($pending)
                                        <a href="{{ route('disposisi.show', $s) }}"
                                            class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium bg-[var(--color-warning)]/10 text-[var(--color-warning)] hover:bg-[var(--color-warning)]/20 transition-colors"
                                            title="Buka untuk menindaklanjuti">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[var(--color-warning)]"></span>{{ $labelDisp[$pending->status] ?? $pending->status }}
                                        </a>
                                    @elseif ($mine->isNotEmpty())
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium bg-[var(--color-success)]/10 text-[var(--color-success)]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[var(--color-success)]"></span>Selesai
                                        </span>
                                    @elseif ($user->hasRole('kabid'))
                                        <span class="text-xs text-[var(--color-slate-400)]">Pantauan bidang</span>
                                    @else
                                        <span class="text-xs text-[var(--color-slate-300)]">-</span>
                                    @endif
                                </td>
                            @endunless
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
                                    <a href="{{ route('disposisi.show', $s) }}"
                                        class="p-1.5 rounded-lg text-[var(--color-slate-400)] hover:text-[var(--color-blue-500)] hover:bg-[var(--color-blue-50)] transition-colors"
                                        title="{{ $canViewAll ? 'Disposisi' : 'Lihat rantai disposisi' }}">
                                        <x-icon name="arrow-right-circle" class="w-4 h-4" />
                                    </a>
                                    @if ($canManage)
                                    <a href="{{ route('surat-masuk.edit', $s) }}"
                                        class="p-1.5 rounded-lg text-[var(--color-slate-400)] hover:text-[var(--color-blue-500)] hover:bg-[var(--color-blue-50)] transition-colors"
                                        title="Edit">
                                        <x-icon name="pencil-square" class="w-4 h-4" />
                                    </a>
                                    <button
                                        @click="$dispatch('confirm-delete', {
                                        url: '{{ route('surat-masuk.destroy', $s) }}',
                                        name: '{{ addslashes($s->nomor_surat) }}'
                                    })"
                                        class="p-1.5 rounded-lg text-[var(--color-slate-400)] hover:text-[var(--color-danger)] hover:bg-red-50 transition-colors"
                                        title="Hapus">
                                        <x-icon name="trash" class="w-4 h-4" />
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $canViewAll ? 6 : 7 }}" class="px-5 py-14 text-center">
                                    <div
                                        class="w-12 h-12 rounded-2xl bg-[var(--color-slate-100)] flex items-center justify-center mx-auto mb-3">
                                        <x-icon name="inbox" class="w-5 h-5 text-[var(--color-slate-400)]" />
                                    </div>
                                    <p class="text-sm font-medium text-[var(--color-slate-500)]">{{ $canManage || $canViewAll ? 'Belum ada surat masuk.' : 'Belum ada surat yang didisposisikan.' }}</p>
                                    <p class="text-xs text-[var(--color-slate-400)] mt-0.5">
                                        @if ($canManage)
                                            Klik "Tambah Surat" untuk mencatat surat masuk pertama.
                                        @elseif ($canViewAll)
                                            Surat akan muncul setelah agendaris mencatatnya.
                                        @else
                                            Surat akan muncul di sini setelah didisposisikan kepada Anda.
                                        @endif
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($suratMasuks->hasPages())
                <div class="px-5 py-3 border-t border-[var(--color-slate-100)] bg-[var(--color-slate-50)]/50">
                    {{ $suratMasuks->links() }}</div>
            @endif
        </div>

    </x-layouts.app>