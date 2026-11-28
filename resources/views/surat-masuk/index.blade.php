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

    {{-- Pencarian + filter status: di atas data, di luar kartu --}}
    <div class="mb-4 space-y-2">
        <div class="flex flex-col gap-3 md:flex-row md:items-center">
            <form method="GET" action="{{ route('surat-masuk.index') }}" role="search" class="flex gap-2 md:w-96">
                @if (request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                <div class="relative flex-1 min-w-0">
                    <x-icon name="magnifying-glass"
                        class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-[var(--color-slate-400)] pointer-events-none" />
                    <input type="search" name="search" value="{{ request('search') }}" enterkeyhint="search"
                        placeholder="Cari nomor atau pengirim" aria-label="Cari surat"
                        class="w-full min-h-11 pl-9 pr-10 text-base md:text-sm rounded-xl border border-[var(--color-slate-200)] bg-white
                               [&::-webkit-search-cancel-button]:appearance-none
                               placeholder:text-[var(--color-slate-400)] focus:outline-none focus:border-[var(--color-blue-500)] focus:ring-2 focus:ring-[var(--color-blue-500)]/10 transition-all" />
                    @if (request('search'))
                        <a href="{{ route('surat-masuk.index', array_filter(['status' => request('status')])) }}" aria-label="Hapus pencarian"
                            class="absolute right-0.5 top-1/2 -translate-y-1/2 w-10 h-10 flex items-center justify-center rounded-lg text-[var(--color-slate-400)] hover:text-[var(--color-slate-600)]">
                            <x-icon name="x-mark" class="w-4 h-4" />
                        </a>
                    @endif
                </div>
                <button type="submit" aria-label="Cari"
                    class="shrink-0 w-11 h-11 flex items-center justify-center rounded-xl text-white
                           bg-gradient-to-br from-[var(--color-blue-500)] to-[var(--color-blue-600)]
                           active:scale-95 transition-transform focus:outline-none focus-visible:ring-2 focus-visible:ring-[var(--color-blue-500)]/40">
                    <x-icon name="magnifying-glass" class="w-4 h-4" />
                </button>
            </form>

            {{-- Filter status: semua pilihan selalu terlihat (tanpa geser) --}}
            <nav aria-label="Filter status"
                class="grid grid-cols-4 gap-1 p-1 rounded-xl bg-[var(--color-slate-100)] md:inline-grid md:grid-cols-[repeat(4,auto)]">
                @foreach ([
                '' => 'Semua',
                'baru' => 'Baru',
                'diproses' => 'Diproses',
                'selesai' => 'Selesai'
                ] as $val => $label)
                    @php $active = (string) request('status') === (string) $val; @endphp
                    <a href="{{ route('surat-masuk.index', array_filter(['search' => request('search'), 'status' => $val])) }}"
                        @if ($active) aria-current="true" @endif
                        class="min-h-10 inline-flex items-center justify-center px-2 md:px-4 rounded-lg text-sm font-medium transition-colors
                               focus:outline-none focus-visible:ring-2 focus-visible:ring-[var(--color-blue-500)]/40
                               {{ $active
                                   ? 'bg-[var(--color-navy-700)] text-white'
                                   : 'text-[var(--color-slate-600)] hover:text-[var(--color-navy-900)]' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </nav>
        </div>

        @if (request('search') || request('status'))
            <div class="flex items-center justify-between gap-3 text-sm">
                <p class="text-[var(--color-slate-600)]" role="status">{{ $suratMasuks->total() }} surat ditemukan</p>
                <a href="{{ route('surat-masuk.index') }}"
                    class="min-h-11 inline-flex items-center px-1 font-medium text-[var(--color-blue-600)] hover:underline">Reset</a>
            </div>
        @endif
    </div>

    @if ($suratMasuks->isEmpty())
        <div class="bg-white rounded-2xl border border-[var(--color-slate-200)]/70
                shadow-[0_1px_2px_rgba(15,23,42,0.04),0_12px_28px_-10px_rgba(15,23,42,0.10)] xl:overflow-hidden px-5 py-14 text-center">
            <div class="w-12 h-12 rounded-2xl bg-[var(--color-slate-100)] flex items-center justify-center mx-auto mb-3">
                <x-icon name="inbox" class="w-5 h-5 text-[var(--color-slate-400)]" />
            </div>
            @if (request('search') || request('status'))
                <p class="text-sm font-medium text-[var(--color-slate-500)]">Tidak ada surat yang cocok.</p>
                <a href="{{ route('surat-masuk.index') }}" class="inline-block mt-2 text-sm font-medium text-[var(--color-blue-600)] hover:underline">Hapus pencarian dan filter</a>
            @else
                <p class="text-sm font-medium text-[var(--color-slate-500)]">{{ $canManage || $canViewAll ? 'Belum ada surat masuk.' : 'Belum ada surat yang didisposisikan.' }}</p>
                <p class="text-xs text-[var(--color-slate-500)] mt-0.5">
                    @if ($canManage)
                        Klik "Tambah Surat" untuk mencatat surat masuk pertama.
                    @elseif ($canViewAll)
                        Surat akan muncul setelah agendaris mencatatnya.
                    @else
                        Surat akan muncul di sini setelah didisposisikan kepada Anda.
                    @endif
                </p>
            @endif
        </div>
    @else
        <div class="bg-white rounded-2xl border border-[var(--color-slate-200)]/70
                shadow-[0_1px_2px_rgba(15,23,42,0.04),0_12px_28px_-10px_rgba(15,23,42,0.10)] xl:overflow-hidden">

            {{-- Daftar: mobile --}}
            <ul class="xl:hidden divide-y divide-[var(--color-slate-100)]">
                @foreach ($suratMasuks as $s)
                    @php
                        $sifatMap = ['sangat_segera' => ['danger', 'Sangat Segera'], 'segera' => ['warning', 'Segera'], 'rahasia' => ['navy', 'Rahasia']];
                        $statusMap = ['selesai' => ['success', 'Selesai'], 'diproses' => ['info', 'Diproses']];
                        $sifat = $sifatMap[$s->sifat_surat] ?? null;
                        $status = $statusMap[$s->status_disposisi] ?? ['neutral', 'Baru'];
                        $pending = null;
                        $mine = collect();
                        if (! $canViewAll) {
                            $mine = $s->disposisi->filter(fn ($d) => $d->canBeActedBy($user));
                            $pending = $mine->first(fn ($d) => ! $d->isTerminal());
                        }
                        $labelDisp = ['menunggu' => 'Menunggu', 'diterima' => 'Diterima', 'diproses' => 'Diproses'];
                    @endphp
                    <li class="px-4 py-3.5">
                        <div class="flex items-start gap-2">
                            <a href="{{ route('disposisi.show', $s) }}" class="min-w-0 flex-1 block py-0.5">
                                <span class="block text-sm font-semibold text-[var(--color-navy-900)] truncate">{{ $s->nomor_surat }}</span>
                                <span class="block text-sm text-[var(--color-slate-600)] mt-0.5 truncate">{{ $s->pengirim }}</span>
                                <span class="block text-xs text-[var(--color-slate-500)] mt-1 line-clamp-2">{{ $s->isi_ringkasan }}</span>
                            </a>
                            <x-row-menu :up="$loop->remaining < 2 && $loop->count > 4">
                                <a href="{{ route('disposisi.show', $s) }}" role="menuitem" class="row-menu-item">
                                    <x-icon name="arrow-right-circle" class="w-4 h-4 text-[var(--color-slate-400)]" />
                                    {{ $canViewAll ? 'Disposisi' : 'Lihat rantai disposisi' }}
                                </a>
                                @if ($s->file_surat)
                                    <a href="{{ asset('storage/' . $s->file_surat) }}" target="_blank" role="menuitem" class="row-menu-item">
                                        <x-icon name="eye" class="w-4 h-4 text-[var(--color-slate-400)]" />
                                        Lihat file
                                    </a>
                                @endif
                                @if ($canManage)
                                    <a href="{{ route('surat-masuk.edit', $s) }}" role="menuitem" class="row-menu-item">
                                        <x-icon name="pencil-square" class="w-4 h-4 text-[var(--color-slate-400)]" />
                                        Edit
                                    </a>
                                    <button type="button" role="menuitem"
                                        @click="open = false; $dispatch('confirm-delete', {
                                        url: '{{ route('surat-masuk.destroy', $s) }}',
                                        name: '{{ addslashes($s->nomor_surat) }}'
                                    })"
                                        class="row-menu-item !text-[var(--color-danger)] hover:!bg-red-50">
                                        <x-icon name="trash" class="w-4 h-4" />
                                        Hapus
                                    </button>
                                @endif
                            </x-row-menu>
                        </div>
                        <div class="flex flex-wrap items-center gap-1.5 mt-2.5">
                            @if ($sifat)
                                <x-status-badge :tone="$sifat[0]">{{ $sifat[1] }}</x-status-badge>
                            @endif
                            <x-status-badge :tone="$status[0]">{{ $status[1] }}</x-status-badge>
                            @if ($pending)
                                <a href="{{ route('disposisi.show', $s) }}" class="rounded-full">
                                    <x-status-badge tone="warning">Disposisi {{ strtolower($labelDisp[$pending->status] ?? $pending->status) }}</x-status-badge>
                                </a>
                            @elseif ($mine->isNotEmpty())
                                <x-status-badge tone="success">Disposisi selesai</x-status-badge>
                            @endif
                            <span class="ml-auto text-xs text-[var(--color-slate-500)]">{{ $s->tanggal_diterima->format('d M Y') }}</span>
                        </div>
                    </li>
                @endforeach
            </ul>

            {{-- Tabel: tablet ke atas --}}
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
                                            <x-action-tip label="Buka untuk menindaklanjuti">
                                                <a href="{{ route('disposisi.show', $s) }}"
                                                    class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium bg-[var(--color-warning)]/10 text-[var(--color-warning)] hover:bg-[var(--color-warning)]/20 transition-colors">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-[var(--color-warning)]"></span>{{ $labelDisp[$pending->status] ?? $pending->status }}
                                                </a>
                                            </x-action-tip>
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
                                        <x-action-tip label="Lihat File">
                                            <a href="{{ asset('storage/' . $s->file_surat) }}" target="_blank"
                                                class="inline-flex p-1.5 rounded-lg text-[var(--color-slate-400)] hover:text-[var(--color-blue-500)] hover:bg-[var(--color-blue-50)] transition-colors"
                                                aria-label="Lihat File">
                                                <x-icon name="eye" class="w-4 h-4" />
                                            </a>
                                        </x-action-tip>
                                    @else
                                        <span class="text-xs text-[var(--color-slate-300)]">-</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <x-action-tip label="{{ $canViewAll ? 'Disposisi' : 'Lihat rantai disposisi' }}">
                                            <a href="{{ route('disposisi.show', $s) }}"
                                                class="p-1.5 rounded-lg text-[var(--color-slate-400)] hover:text-[var(--color-blue-500)] hover:bg-[var(--color-blue-50)] transition-colors"
                                                aria-label="{{ $canViewAll ? 'Disposisi' : 'Lihat rantai disposisi' }}">
                                                <x-icon name="arrow-right-circle" class="w-4 h-4" />
                                            </a>
                                        </x-action-tip>
                                        @if ($canManage)
                                        <x-action-tip label="Edit">
                                            <a href="{{ route('surat-masuk.edit', $s) }}"
                                                class="p-1.5 rounded-lg text-[var(--color-slate-400)] hover:text-[var(--color-blue-500)] hover:bg-[var(--color-blue-50)] transition-colors"
                                                aria-label="Edit">
                                                <x-icon name="pencil-square" class="w-4 h-4" />
                                            </a>
                                        </x-action-tip>
                                        <x-action-tip label="Hapus">
                                            <button
                                                @click="$dispatch('confirm-delete', {
                                                url: '{{ route('surat-masuk.destroy', $s) }}',
                                                name: '{{ addslashes($s->nomor_surat) }}'
                                            })"
                                                class="p-1.5 rounded-lg text-[var(--color-slate-400)] hover:text-[var(--color-danger)] hover:bg-red-50 transition-colors"
                                                aria-label="Hapus">
                                                <x-icon name="trash" class="w-4 h-4" />
                                            </button>
                                        </x-action-tip>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
        </div>

        {{ $suratMasuks->links('vendor.pagination.surat') }}
    @endif

</x-layouts.app>