<x-layouts.app title="Surat Keluar">

    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <div>
            <h1 class="font-[var(--font-heading)] text-2xl font-bold text-[var(--color-navy-900)] tracking-tight">Surat
                Keluar</h1>
            <p class="text-sm text-[var(--color-slate-500)] mt-0.5">{{ $canCreate ? 'Daftar surat keluar yang tercatat' : 'Surat keluar (hanya lihat)' }}</p>
        </div>
        @if ($canCreate)
        <a href="{{ route('surat-keluar.create') }}"
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
    <div class="mb-4 flex flex-col gap-3 md:flex-row md:items-center">
        <form method="GET" action="{{ route('surat-keluar.index') }}" role="search" class="relative md:w-80">
            @if (request('status_arsip'))
                <input type="hidden" name="status_arsip" value="{{ request('status_arsip') }}">
            @endif
            <x-icon name="magnifying-glass"
                class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-[var(--color-slate-400)] pointer-events-none" />
            <input type="search" name="search" value="{{ request('search') }}" enterkeyhint="search"
                placeholder="Cari no. surat, tujuan, atau isi..." aria-label="Cari surat"
                class="w-full min-h-11 pl-10 pr-11 text-base md:text-sm rounded-xl border border-[var(--color-slate-200)] bg-white
                       placeholder:text-[var(--color-slate-400)] focus:outline-none focus:border-[var(--color-blue-500)] focus:ring-2 focus:ring-[var(--color-blue-500)]/10 transition-all" />
            @if (request('search'))
                <a href="{{ route('surat-keluar.index', array_filter(['status_arsip' => request('status_arsip')])) }}" aria-label="Hapus pencarian"
                    class="absolute right-1 top-1/2 -translate-y-1/2 w-9 h-9 flex items-center justify-center rounded-lg text-[var(--color-slate-400)] hover:text-[var(--color-slate-600)]">
                    <x-icon name="x-mark" class="w-4 h-4" />
                </a>
            @endif
        </form>

        <nav aria-label="Filter" class="-mx-4 px-4 sm:-mx-6 sm:px-6 md:mx-0 md:px-0 overflow-x-auto flex gap-2 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
            @foreach ([
            '' => 'Semua',
            'aktif' => 'Aktif',
            'inaktif' => 'Inaktif',
            'permanen' => 'Permanen'
            ] as $val => $label)
                @php $active = (string) request('status_arsip') === (string) $val; @endphp
                <a href="{{ route('surat-keluar.index', array_filter(['search' => request('search'), 'status_arsip' => $val])) }}"
                    @if ($active) aria-current="true" @endif
                    class="shrink-0 inline-flex items-center min-h-10 px-4 rounded-full text-sm font-medium border transition-colors
                           {{ $active
                               ? 'bg-[var(--color-navy-700)] text-white border-transparent'
                               : 'bg-white border-[var(--color-slate-200)] text-[var(--color-slate-600)] hover:border-[var(--color-slate-300)] hover:text-[var(--color-navy-900)]' }}">
                    {{ $label }}
                </a>
            @endforeach
        </nav>
    </div>

    @if ($suratKeluars->isEmpty())
        <div class="bg-white rounded-2xl border border-[var(--color-slate-200)]/70
                shadow-[0_1px_2px_rgba(15,23,42,0.04),0_12px_28px_-10px_rgba(15,23,42,0.10)] md:overflow-hidden px-5 py-14 text-center">
            <div class="w-12 h-12 rounded-2xl bg-[var(--color-slate-100)] flex items-center justify-center mx-auto mb-3">
                <x-icon name="paper-airplane" class="w-5 h-5 text-[var(--color-slate-400)]" />
            </div>
            @if (request('search') || request('status_arsip'))
                <p class="text-sm font-medium text-[var(--color-slate-500)]">Tidak ada surat yang cocok.</p>
                <a href="{{ route('surat-keluar.index') }}" class="inline-block mt-2 text-sm font-medium text-[var(--color-blue-600)] hover:underline">Hapus pencarian dan filter</a>
            @else
                <p class="text-sm font-medium text-[var(--color-slate-500)]">Belum ada surat keluar.</p>
                <p class="text-xs text-[var(--color-slate-500)] mt-0.5">{{ $canCreate ? 'Klik "Tambah Surat" untuk mencatat surat keluar pertama.' : 'Belum ada surat keluar yang dicatat.' }}</p>
            @endif
        </div>
    @else
        <div class="bg-white rounded-2xl border border-[var(--color-slate-200)]/70
                shadow-[0_1px_2px_rgba(15,23,42,0.04),0_12px_28px_-10px_rgba(15,23,42,0.10)] md:overflow-hidden">

            {{-- Daftar: mobile --}}
            <ul class="md:hidden divide-y divide-[var(--color-slate-100)]">
                @foreach ($suratKeluars as $s)
                    @php
                        $arsipMap = [
                            'permanen' => ['navy', 'Permanen'],
                            'perlu_ditinjau' => ['warning', 'Perlu Ditinjau'],
                            'menunggu_musnah' => ['warning', 'Menunggu Persetujuan'],
                            'inaktif' => ['neutral', 'Inaktif'],
                        ];
                        $arsip = $arsipMap[$s->status_arsip] ?? ['success', 'Aktif'];
                        $bisaKelola = $s->canBeManagedBy($user);
                    @endphp
                    <li class="px-4 py-3.5">
                        <div class="flex items-start gap-2">
                            <div class="min-w-0 flex-1 py-0.5">
                                <p class="text-sm font-semibold text-[var(--color-navy-900)] truncate">{{ $s->nomor_surat }}</p>
                                <p class="text-sm text-[var(--color-slate-600)] mt-0.5 truncate">Kepada {{ $s->kepada }}</p>
                                <p class="text-xs text-[var(--color-slate-500)] mt-1 line-clamp-2">{{ $s->isi_ringkasan }}</p>
                            </div>
                            @if ($s->file_surat || $bisaKelola)
                                <x-row-menu>
                                    @if ($s->file_surat)
                                        <a href="{{ asset('storage/' . $s->file_surat) }}" target="_blank" role="menuitem" class="row-menu-item">
                                            <x-icon name="eye" class="w-4 h-4 text-[var(--color-slate-400)]" />
                                            Lihat file
                                        </a>
                                    @endif
                                    @if ($bisaKelola)
                                        <a href="{{ route('surat-keluar.edit', $s) }}" role="menuitem" class="row-menu-item">
                                            <x-icon name="pencil-square" class="w-4 h-4 text-[var(--color-slate-400)]" />
                                            Edit
                                        </a>
                                        <button type="button" role="menuitem"
                                            @click="open = false; $dispatch('confirm-delete', {
                                            url: '{{ route('surat-keluar.destroy', $s) }}',
                                            name: '{{ addslashes($s->nomor_surat) }}'
                                        })"
                                            class="row-menu-item !text-[var(--color-danger)] hover:!bg-red-50">
                                            <x-icon name="trash" class="w-4 h-4" />
                                            Hapus
                                        </button>
                                    @endif
                                </x-row-menu>
                            @endif
                        </div>
                        <div class="flex flex-wrap items-center gap-x-2 gap-y-1.5 mt-2.5">
                            <x-status-badge :tone="$arsip[0]">{{ $arsip[1] }}</x-status-badge>
                            @if ($s->pembuat)
                                <span class="text-xs text-[var(--color-slate-500)] truncate max-w-[10rem]">Oleh {{ $s->pembuat->name }}</span>
                            @endif
                            <span class="ml-auto text-xs text-[var(--color-slate-500)]">{{ $s->tanggal_surat->format('d M Y') }}</span>
                        </div>
                    </li>
                @endforeach
            </ul>

            {{-- Tabel: tablet ke atas --}}
            <div class="hidden md:block overflow-x-auto">
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
                        @foreach ($suratKeluars as $s)
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
    
                                        @case('menunggu_musnah')
                                            <span
                                                class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium bg-[var(--color-warning)]/10 text-[var(--color-warning)]">
                                                <span class="w-1.5 h-1.5 rounded-full bg-[var(--color-warning)]"></span>Menunggu
                                                Persetujuan
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
                                        @if ($s->canBeManagedBy($user))
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
                                        @else
                                            <span class="text-xs text-[var(--color-slate-300)]">-</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
        </div>

        {{ $suratKeluars->links('vendor.pagination.surat') }}
    @endif

</x-layouts.app>