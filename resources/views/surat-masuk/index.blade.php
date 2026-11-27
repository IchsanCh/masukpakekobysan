@php
    $sifatMap = [
        'sangat_segera' => ['Sangat Segera', 'danger'],
        'segera' => ['Segera', 'warning'],
        'rahasia' => ['Rahasia', 'navy'],
        'biasa' => ['Biasa', 'slate'],
    ];
    $statusMap = [
        'selesai' => ['Selesai', 'success'],
        'diproses' => ['Diproses', 'info'],
        'baru' => ['Baru', 'slate'],
    ];
    $statusOptions = ['' => 'Semua status', 'baru' => 'Baru', 'diproses' => 'Diproses', 'selesai' => 'Selesai'];
    $labelDisp = ['menunggu' => 'Menunggu', 'diterima' => 'Diterima', 'diproses' => 'Diproses'];

    $hasFilter = request('search') || request('status');
    $total = $suratMasuks->total();

    $card =
        'bg-white rounded-2xl border border-[var(--color-slate-200)]/70 shadow-[0_1px_2px_rgba(15,23,42,0.04),0_12px_28px_-10px_rgba(15,23,42,0.10)]';
    $chip =
        'inline-flex items-center gap-1.5 min-h-10 max-w-full pl-3 pr-2.5 rounded-full border border-[var(--color-blue-500)]/30 bg-[var(--color-blue-50)] text-sm font-medium text-[var(--color-blue-600)]';
    $act =
        'flex flex-col items-center justify-center gap-0.5 min-h-14 text-xs font-medium hover:bg-[var(--color-slate-50)] active:bg-[var(--color-slate-100)] transition-colors';
@endphp

<x-layouts.app title="Surat Masuk">

    <div x-data="{ filterOpen: false }" @keydown.escape.window="filterOpen = false">

        {{-- Header. Tombol tambah di mobile pindah ke bar bawah (zona jempol). --}}
        <div class="flex items-start justify-between gap-3 mb-4 md:mb-6">
            <div class="min-w-0">
                <h1
                    class="font-[var(--font-heading)] text-xl md:text-2xl font-bold text-[var(--color-navy-900)] tracking-tight">
                    Surat Masuk</h1>
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
                    class="hidden md:inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-sm font-semibold text-white
                          bg-gradient-to-br from-[var(--color-blue-500)] to-[var(--color-blue-600)]
                          shadow-[0_1px_2px_rgba(15,23,42,0.1),0_4px_10px_-2px_rgba(37,99,235,0.35)]
                          hover:shadow-[0_1px_2px_rgba(15,23,42,0.1),0_8px_16px_-2px_rgba(37,99,235,0.45)]
                          hover:-translate-y-px active:translate-y-0 transition-all duration-200">
                    <x-icon name="plus" class="w-4 h-4" />
                    Tambah Surat
                </a>
            @endif
        </div>

        {{-- Mobile: ringkasan hasil + filter aktif (selalu terlihat di atas, bisa dihapus satu per satu) --}}
        <div class="md:hidden mb-3">
            <p class="text-sm text-[var(--color-slate-500)]">{{ number_format($total) }} surat{{ $hasFilter ? ' sesuai filter' : '' }}</p>
            @if ($hasFilter)
                <div class="mt-2 flex flex-wrap gap-2">
                    @if (request('search'))
                        <a href="{{ request()->fullUrlWithQuery(['search' => null, 'page' => null]) }}"
                            aria-label="Hapus pencarian" class="{{ $chip }}">
                            <span class="truncate">Cari: “{{ \Illuminate\Support\Str::limit(request('search'), 24) }}”</span>
                            <x-icon name="x-mark" class="w-4 h-4 shrink-0" />
                        </a>
                    @endif
                    @if (request('status'))
                        <a href="{{ request()->fullUrlWithQuery(['status' => null, 'page' => null]) }}"
                            aria-label="Hapus filter status" class="{{ $chip }}">
                            <span class="truncate">Status: {{ $statusOptions[request('status')] ?? request('status') }}</span>
                            <x-icon name="x-mark" class="w-4 h-4 shrink-0" />
                        </a>
                    @endif
                    <a href="{{ route('surat-masuk.index') }}"
                        class="inline-flex items-center min-h-10 px-2 text-sm font-medium text-[var(--color-slate-500)] underline underline-offset-2">Reset
                        semua</a>
                </div>
            @endif
        </div>

        <div
            class="md:bg-white md:rounded-2xl md:border md:border-[var(--color-slate-200)]/70 md:shadow-[0_1px_2px_rgba(15,23,42,0.04),0_12px_28px_-10px_rgba(15,23,42,0.10)] md:overflow-hidden">

            {{-- Toolbar tablet/desktop --}}
            <div class="hidden md:block px-5 py-4 border-b border-[var(--color-slate-100)] bg-[var(--color-slate-50)]/50">
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
                        @foreach ($statusOptions as $value => $text)
                            <option value="{{ $value }}" @selected((string) request('status') === (string) $value)>
                                {{ $value === '' ? 'Semua Status' : $text }}</option>
                        @endforeach
                    </select>
                    <button type="submit"
                        class="px-4 py-2 rounded-xl text-sm font-medium bg-white border border-[var(--color-slate-200)] text-[var(--color-slate-600)] hover:border-[var(--color-slate-300)] hover:text-[var(--color-navy-900)] transition-colors">Cari</button>
                    @if ($hasFilter)
                        <a href="{{ route('surat-masuk.index') }}"
                            class="px-4 py-2 rounded-xl text-sm font-medium text-[var(--color-slate-400)] hover:text-[var(--color-slate-600)]">Reset</a>
                    @endif
                </form>
            </div>

            @if ($suratMasuks->isEmpty())
                {{-- Kosong --}}
                <div class="{{ $card }} md:shadow-none md:border-0 md:rounded-none px-5 py-14 text-center">
                    <div
                        class="w-12 h-12 rounded-2xl bg-[var(--color-slate-100)] flex items-center justify-center mx-auto mb-3">
                        <x-icon name="inbox" class="w-5 h-5 text-[var(--color-slate-400)]" />
                    </div>
                    @if ($hasFilter)
                        <p class="text-sm font-medium text-[var(--color-slate-500)]">Tidak ada surat yang cocok dengan filter.</p>
                        <p class="text-xs text-[var(--color-slate-500)] mt-0.5">Coba kata kunci lain atau tampilkan semua surat.</p>
                        <a href="{{ route('surat-masuk.index') }}"
                            class="inline-flex items-center justify-center min-h-11 px-4 mt-3 rounded-xl text-sm font-medium text-[var(--color-blue-600)] hover:bg-[var(--color-blue-50)]">Reset
                            filter</a>
                    @else
                        <p class="text-sm font-medium text-[var(--color-slate-500)]">{{ $canManage || $canViewAll ? 'Belum ada surat masuk.' : 'Belum ada surat yang didisposisikan.' }}</p>
                        <p class="text-xs text-[var(--color-slate-500)] mt-0.5">
                            @if ($canManage)
                                <span class="md:hidden">Ketuk tombol + di bawah untuk mencatat surat masuk pertama.</span>
                                <span class="hidden md:inline">Klik "Tambah Surat" untuk mencatat surat masuk pertama.</span>
                            @elseif ($canViewAll)
                                Surat akan muncul setelah agendaris mencatatnya.
                            @else
                                Surat akan muncul di sini setelah didisposisikan kepada Anda.
                            @endif
                        </p>
                    @endif
                </div>
            @else
                {{-- Kartu: mobile & tablet. Semua info muat di lebar layar, tanpa geser samping. --}}
                <div class="xl:hidden md:p-4">
                    <div class="grid gap-3 md:grid-cols-2">
                        @foreach ($suratMasuks as $s)
                            @php
                                [$sifatLabel, $sifatTone] = $sifatMap[$s->sifat_surat] ?? $sifatMap['biasa'];
                                [$statLabel, $statTone] = $statusMap[$s->status_disposisi] ?? $statusMap['baru'];
                                if (! $canViewAll) {
                                    $mine = $s->disposisi->filter(fn($d) => $d->canBeActedBy($user));
                                    $pending = $mine->first(fn($d) => ! $d->isTerminal());
                                }
                            @endphp
                            <article class="{{ $card }} overflow-hidden flex flex-col">
                                <div class="p-4 space-y-2.5 flex-1">
                                    <div class="flex items-start justify-between gap-3">
                                        <p class="min-w-0 text-base font-semibold leading-snug text-[var(--color-navy-900)] break-words">
                                            {{ $s->nomor_surat }}</p>
                                        <x-status-pill :label="$statLabel" :tone="$statTone" class="shrink-0" />
                                    </div>
                                    <div>
                                        <p class="text-sm text-[var(--color-slate-600)]">{{ $s->pengirim }}</p>
                                        <p class="text-xs text-[var(--color-slate-500)] mt-0.5">Diterima
                                            {{ $s->tanggal_diterima->format('d M Y') }}</p>
                                    </div>
                                    <p class="text-sm text-[var(--color-slate-500)] line-clamp-2">{{ $s->isi_ringkasan }}</p>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <x-status-pill :label="$sifatLabel" :tone="$sifatTone" />
                                        @unless ($canViewAll)
                                            @if ($pending)
                                                <a href="{{ route('disposisi.show', $s) }}"
                                                    class="inline-flex items-center gap-1.5 min-h-9 px-3 rounded-full text-xs font-medium bg-[var(--color-warning)]/10 text-[var(--color-warning)] active:bg-[var(--color-warning)]/20">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-[var(--color-warning)]"></span>Disposisi
                                                    saya: {{ $labelDisp[$pending->status] ?? $pending->status }}
                                                </a>
                                            @elseif ($mine->isNotEmpty())
                                                <x-status-pill label="Disposisi saya selesai" tone="success" />
                                            @elseif ($user->hasRole('kabid'))
                                                <span class="text-xs text-[var(--color-slate-500)]">Pantauan bidang</span>
                                            @endif
                                        @endunless
                                    </div>
                                </div>

                                <div
                                    class="grid grid-flow-col auto-cols-fr divide-x divide-[var(--color-slate-100)] border-t border-[var(--color-slate-100)]">
                                    <a href="{{ route('disposisi.show', $s) }}"
                                        title="{{ $canViewAll ? 'Disposisi' : 'Lihat rantai disposisi' }}"
                                        class="{{ $act }} text-[var(--color-blue-600)]">
                                        <x-icon name="arrow-right-circle" class="w-5 h-5" />Disposisi
                                    </a>
                                    @if ($s->file_surat)
                                        <a href="{{ asset('storage/' . $s->file_surat) }}" target="_blank" rel="noopener"
                                            class="{{ $act }} text-[var(--color-slate-600)]">
                                            <x-icon name="eye" class="w-5 h-5" />File
                                        </a>
                                    @endif
                                    @if ($canManage)
                                        <a href="{{ route('surat-masuk.edit', $s) }}" class="{{ $act }} text-[var(--color-slate-600)]">
                                            <x-icon name="pencil-square" class="w-5 h-5" />Edit
                                        </a>
                                        <button type="button"
                                            @click="$dispatch('confirm-delete', {
                                                url: '{{ route('surat-masuk.destroy', $s) }}',
                                                name: '{{ addslashes($s->nomor_surat) }}'
                                            })"
                                            class="{{ $act }} text-[var(--color-danger)]">
                                            <x-icon name="trash" class="w-5 h-5" />Hapus
                                        </button>
                                    @endif
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>

                {{-- Tabel: layar lebar saja (xl ke atas) --}}
                <div class="hidden xl:block overflow-x-auto">
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
                            @foreach ($suratMasuks as $s)
                                @php
                                    [$sifatLabel, $sifatTone] = $sifatMap[$s->sifat_surat] ?? $sifatMap['biasa'];
                                    [$statLabel, $statTone] = $statusMap[$s->status_disposisi] ?? $statusMap['baru'];
                                @endphp
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
                                        <x-status-pill :label="$sifatLabel" :tone="$sifatTone" />
                                    </td>
                                    <td class="px-5 py-3.5 text-center">
                                        <x-status-pill :label="$statLabel" :tone="$statTone" />
                                    </td>
                                    @unless ($canViewAll)
                                        <td class="px-5 py-3.5 text-center">
                                            @php
                                                $mine = $s->disposisi->filter(fn($d) => $d->canBeActedBy($user));
                                                $pending = $mine->first(fn($d) => ! $d->isTerminal());
                                            @endphp
                                            @if ($pending)
                                                <a href="{{ route('disposisi.show', $s) }}"
                                                    class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium bg-[var(--color-warning)]/10 text-[var(--color-warning)] hover:bg-[var(--color-warning)]/20 transition-colors"
                                                    title="Buka untuk menindaklanjuti">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-[var(--color-warning)]"></span>{{ $labelDisp[$pending->status] ?? $pending->status }}
                                                </a>
                                            @elseif ($mine->isNotEmpty())
                                                <x-status-pill label="Selesai" tone="success" />
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
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            @if ($suratMasuks->hasPages())
                <div
                    class="mt-4 md:mt-0 md:px-5 md:py-3 md:border-t md:border-[var(--color-slate-100)] md:bg-[var(--color-slate-50)]/50">
                    {{ $suratMasuks->links() }}</div>
            @endif
        </div>

        {{-- Mobile: bar bawah (zona jempol) berisi cari, filter, dan tambah. Jarak 20px dari tepi layar
             supaya tidak bentrok dengan gestur geser-kembali sistem. --}}
        <div
            class="md:hidden sticky bottom-0 z-20 -mx-4 -mb-4 mt-4 px-5 pt-3 pb-[max(0.75rem,env(safe-area-inset-bottom))] bg-base-200 border-t border-[var(--color-slate-200)]">
            <form method="GET" class="flex items-center gap-2">
                @if (request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}" />
                @endif
                <div class="relative flex-1 min-w-0">
                    <x-icon name="magnifying-glass"
                        class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-[var(--color-slate-400)] pointer-events-none" />
                    <input type="search" name="search" value="{{ request('search') }}"
                        placeholder="Cari no. surat atau pengirim" aria-label="Cari no. surat atau pengirim"
                        enterkeyhint="search" autocomplete="off"
                        class="w-full h-12 pl-10 pr-3 text-base rounded-xl border border-[var(--color-slate-200)] bg-white appearance-none
                               placeholder:text-[var(--color-slate-500)] focus:outline-none focus:border-[var(--color-blue-500)] focus:ring-2 focus:ring-[var(--color-blue-500)]/10 transition-all" />
                </div>
                <button type="button" @click="filterOpen = true" aria-label="Filter status"
                    class="relative shrink-0 size-12 inline-flex items-center justify-center rounded-xl border transition-colors
                           {{ request('status') ? 'border-[var(--color-blue-500)] bg-[var(--color-blue-50)] text-[var(--color-blue-600)]' : 'border-[var(--color-slate-200)] bg-white text-[var(--color-slate-600)]' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5"
                        aria-hidden="true">
                        <path
                            d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 0 1-.659 1.591l-5.432 5.432a2.25 2.25 0 0 0-.659 1.591v2.927a2.25 2.25 0 0 1-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 0 0-.659-1.591L3.659 7.409A2.25 2.25 0 0 1 3 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0 1 12 3Z" />
                    </svg>
                    @if (request('status'))
                        <span
                            class="absolute top-2 right-2 w-2.5 h-2.5 rounded-full bg-[var(--color-blue-500)] ring-2 ring-[var(--color-blue-50)]"></span>
                    @endif
                </button>
                @if ($canManage)
                    <a href="{{ route('surat-masuk.create') }}" aria-label="Tambah surat masuk"
                        class="shrink-0 size-12 inline-flex items-center justify-center rounded-xl text-white
                              bg-gradient-to-br from-[var(--color-blue-500)] to-[var(--color-blue-600)]
                              shadow-[0_4px_10px_-2px_rgba(37,99,235,0.35)] active:scale-95 transition-transform">
                        <x-icon name="plus" class="w-5 h-5" />
                    </a>
                @endif
            </form>
        </div>

        {{-- Mobile: lembar filter status dari bawah. Satu ketukan langsung menerapkan filter. --}}
        <div x-show="filterOpen" x-cloak x-transition.opacity @click="filterOpen = false"
            class="md:hidden fixed inset-0 z-50 bg-black/50" aria-hidden="true"></div>
        <div x-show="filterOpen" x-cloak x-transition:enter="transition ease-out duration-200 motion-reduce:transition-none"
            x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0"
            x-transition:leave="transition ease-in duration-150 motion-reduce:transition-none"
            x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full" role="dialog"
            aria-modal="true" aria-label="Filter status surat"
            class="md:hidden fixed inset-x-0 bottom-0 z-50 bg-white rounded-t-3xl shadow-[0_-12px_40px_-8px_rgba(11,25,44,0.3)] pb-[max(1rem,env(safe-area-inset-bottom))]">
            <div class="flex items-center justify-between pl-5 pr-2 pt-3 pb-1">
                <h2 class="font-[var(--font-heading)] text-base font-bold text-[var(--color-navy-900)]">Filter status</h2>
                <button type="button" @click="filterOpen = false" aria-label="Tutup"
                    class="size-11 inline-flex items-center justify-center rounded-xl text-[var(--color-slate-500)] hover:bg-[var(--color-slate-100)]">
                    <x-icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>
            <form method="GET">
                @if (request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}" />
                @endif
                <fieldset>
                    <legend class="sr-only">Status surat</legend>
                    @foreach ($statusOptions as $value => $text)
                        @php $isSel = (string) request('status') === (string) $value; @endphp
                        <label
                            class="flex items-center justify-between gap-3 min-h-14 px-5 cursor-pointer border-t border-[var(--color-slate-100)] active:bg-[var(--color-slate-100)]">
                            <span
                                class="text-base {{ $isSel ? 'font-semibold text-[var(--color-navy-900)]' : 'text-[var(--color-slate-600)]' }}">{{ $text }}</span>
                            <input type="radio" name="status" value="{{ $value }}" class="peer sr-only"
                                onchange="this.form.submit()" @checked($isSel) />
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                                aria-hidden="true"
                                class="w-5 h-5 text-[var(--color-blue-600)] opacity-0 peer-checked:opacity-100">
                                <path d="m4.5 12.75 6 6 9-13.5" />
                            </svg>
                        </label>
                    @endforeach
                </fieldset>
            </form>
        </div>
    </div>

</x-layouts.app>