<x-layouts.app title="Disposisi Surat">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ url()->previous() }}"
            class="p-2 rounded-xl text-[var(--color-slate-400)] hover:bg-[var(--color-slate-100)] hover:text-[var(--color-navy-900)] transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <div>
            <h1 class="font-[var(--font-heading)] text-2xl font-bold text-[var(--color-navy-900)] tracking-tight">
                Disposisi Surat</h1>
            <p class="text-sm text-[var(--color-slate-500)] mt-0.5">{{ $suratMasuk->nomor_agenda }} —
                {{ $suratMasuk->nomor_surat }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-5">

        {{-- Left: Surat summary + timeline --}}
        <div class="xl:col-span-2 space-y-5">

            {{-- Surat Summary --}}
            <div
                class="bg-white rounded-2xl border border-[var(--color-slate-200)]/70
                        shadow-[0_1px_2px_rgba(15,23,42,0.04),0_12px_28px_-10px_rgba(15,23,42,0.10)] p-5">
                <div class="flex items-start justify-between gap-3 flex-wrap">
                    <div>
                        <p class="text-sm font-semibold text-[var(--color-navy-900)]">{{ $suratMasuk->pengirim }}</p>
                        <p class="text-sm text-[var(--color-slate-600)] mt-1">{{ $suratMasuk->isi_ringkasan }}</p>
                        <p class="text-xs text-[var(--color-slate-400)] mt-1.5">Diterima
                            {{ $suratMasuk->tanggal_diterima->format('d M Y') }}</p>
                    </div>
                    @if ($suratMasuk->file_surat)
                        <a href="{{ asset('storage/' . $suratMasuk->file_surat) }}" target="_blank"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium border border-[var(--color-slate-200)] text-[var(--color-slate-600)] hover:border-[var(--color-blue-300)] hover:text-[var(--color-blue-600)] transition-colors shrink-0">
                            <x-icon name="eye" class="w-3.5 h-3.5" /> Lihat File
                        </a>
                    @endif
                </div>
            </div>

            {{-- Timeline --}}
            <div>
                <h3
                    class="font-[var(--font-heading)] text-sm font-bold text-[var(--color-navy-900)] uppercase tracking-wider mb-3">
                    Riwayat Disposisi</h3>

                @forelse($tree as $root)
                    @include('disposisi._node', [
                        'disposisi' => $root,
                        'depth' => 0,
                        'units' => $units,
                        'usersForPersonal' => $usersForPersonal,
                    ])
                @empty
                    <div class="bg-white rounded-2xl border border-[var(--color-slate-200)]/70 p-10 text-center">
                        <div
                            class="w-12 h-12 rounded-2xl bg-[var(--color-slate-100)] flex items-center justify-center mx-auto mb-3">
                            <x-icon name="paper-airplane" class="w-5 h-5 text-[var(--color-slate-400)]" />
                        </div>
                        <p class="text-sm font-medium text-[var(--color-slate-500)]">Surat ini belum didisposisi.</p>
                        @if ($canCreateRoot)
                            <p class="text-xs text-[var(--color-slate-400)] mt-0.5">Buat disposisi pertama lewat form di
                                samping.</p>
                        @endif
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Right: Create root disposisi --}}
        @if ($canCreateRoot)
            <div
                class="bg-white rounded-2xl border border-[var(--color-slate-200)]/70
                        shadow-[0_1px_2px_rgba(15,23,42,0.04),0_12px_28px_-10px_rgba(15,23,42,0.10)] p-5 h-fit xl:sticky xl:top-5">
                <h3
                    class="font-[var(--font-heading)] text-sm font-bold text-[var(--color-navy-900)] uppercase tracking-wider mb-3">
                    {{ $tree->isEmpty() ? 'Buat Disposisi' : 'Tambah Disposisi Baru' }}
                </h3>
                @include('disposisi._target-form', [
                    'actionUrl' => route('disposisi.store', $suratMasuk),
                    'units' => $units,
                    'usersForPersonal' => $usersForPersonal,
                    'submitLabel' => 'Kirim Disposisi',
                    'pimpinanOptions' => $pimpinanUntukRoot->isNotEmpty() ? $pimpinanUntukRoot : null,
                ])
            </div>
        @endif
    </div>

</x-layouts.app>