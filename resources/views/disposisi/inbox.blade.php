<x-layouts.app title="Disposisi Saya">

    <div class="mb-6">
        <h1 class="font-[var(--font-heading)] text-2xl font-bold text-[var(--color-navy-900)] tracking-tight">Disposisi
            Saya</h1>
        <p class="text-sm text-[var(--color-slate-500)] mt-0.5">Disposisi yang ditujukan ke kamu / unit kamu</p>
    </div>

    <div
        class="bg-white rounded-2xl border border-[var(--color-slate-200)]/70
                shadow-[0_1px_2px_rgba(15,23,42,0.04),0_12px_28px_-10px_rgba(15,23,42,0.10)] overflow-hidden">

        {{-- Toolbar --}}
        <div
            class="px-5 py-4 border-b border-[var(--color-slate-100)] bg-[var(--color-slate-50)]/50 flex flex-wrap items-center justify-between gap-2">
            <div class="flex gap-1 p-1 rounded-xl bg-[var(--color-slate-100)]">
                <a href="{{ route('disposisi.inbox', ['view' => 'diterima']) }}"
                    class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors {{ $view === 'diterima' ? 'bg-white text-[var(--color-navy-900)] shadow-sm' : 'text-[var(--color-slate-500)]' }}">
                    Diterima
                </a>
                <a href="{{ route('disposisi.inbox', ['view' => 'dikirim']) }}"
                    class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors {{ $view === 'dikirim' ? 'bg-white text-[var(--color-navy-900)] shadow-sm' : 'text-[var(--color-slate-500)]' }}">
                    Dikirim
                </a>
            </div>

            <form method="GET" class="flex items-center gap-2">
                <input type="hidden" name="view" value="{{ $view }}">
                <select name="status" onchange="this.form.submit()"
                    class="px-3 py-2 text-sm rounded-xl border border-[var(--color-slate-200)] bg-white text-[var(--color-slate-600)]
                           focus:outline-none focus:border-[var(--color-blue-500)] focus:ring-2 focus:ring-[var(--color-blue-500)]/10 transition-all">
                    <option value="">Semua Status</option>
                    <option value="menunggu" @selected(request('status') === 'menunggu')>Menunggu</option>
                    <option value="diterima" @selected(request('status') === 'diterima')>Diterima</option>
                    <option value="diproses" @selected(request('status') === 'diproses')>Diproses</option>
                    <option value="selesai" @selected(request('status') === 'selesai')>Selesai</option>
                    <option value="ditolak" @selected(request('status') === 'ditolak')>Ditolak</option>
                </select>
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
                            {{ $view === 'dikirim' ? 'Tujuan' : 'Dari' }}</th>
                        <th
                            class="px-5 py-3 text-left text-[11px] font-semibold text-[var(--color-slate-500)] uppercase tracking-wider">
                            Instruksi</th>
                        <th
                            class="px-5 py-3 text-center text-[11px] font-semibold text-[var(--color-slate-500)] uppercase tracking-wider">
                            Batas</th>
                        <th
                            class="px-5 py-3 text-center text-[11px] font-semibold text-[var(--color-slate-500)] uppercase tracking-wider">
                            Status</th>
                        <th
                            class="px-5 py-3 text-right text-[11px] font-semibold text-[var(--color-slate-500)] uppercase tracking-wider">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--color-slate-100)]">
                    @forelse ($disposisis as $d)
                        @php
                            $statusColors = [
                                'menunggu' => [
                                    'bg' => 'bg-[var(--color-slate-100)]',
                                    'text' => 'text-[var(--color-slate-500)]',
                                    'dot' => 'bg-[var(--color-slate-400)]',
                                ],
                                'diterima' => [
                                    'bg' => 'bg-[var(--color-blue-500)]/10',
                                    'text' => 'text-[var(--color-blue-600)]',
                                    'dot' => 'bg-[var(--color-blue-500)]',
                                ],
                                'diproses' => [
                                    'bg' => 'bg-[var(--color-warning)]/10',
                                    'text' => 'text-[var(--color-warning)]',
                                    'dot' => 'bg-[var(--color-warning)]',
                                ],
                                'selesai' => [
                                    'bg' => 'bg-[var(--color-success)]/10',
                                    'text' => 'text-[var(--color-success)]',
                                    'dot' => 'bg-[var(--color-success)]',
                                ],
                                'ditolak' => [
                                    'bg' => 'bg-[var(--color-danger)]/10',
                                    'text' => 'text-[var(--color-danger)]',
                                    'dot' => 'bg-[var(--color-danger)]',
                                ],
                            ];
                            $sc = $statusColors[$d->status];
                            $statusLabel = [
                                'menunggu' => 'Menunggu',
                                'diterima' => 'Diterima',
                                'diproses' => 'Diproses',
                                'selesai' => 'Selesai',
                                'ditolak' => 'Ditolak',
                            ][$d->status];
                        @endphp
                        <tr
                            class="even:bg-[var(--color-slate-50)]/40 hover:bg-[var(--color-blue-50)]/40 transition-colors">
                            <td class="px-5 py-3.5">
                                <span
                                    class="font-mono text-xs font-semibold text-[var(--color-blue-600)] bg-[var(--color-blue-50)] px-2 py-0.5 rounded-md">{{ $d->suratMasuk->nomor_agenda }}</span>
                                <p class="text-xs text-[var(--color-slate-500)] mt-1 truncate max-w-[180px]">
                                    {{ $d->suratMasuk->nomor_surat }}</p>
                            </td>
                            <td class="px-5 py-3.5 text-sm text-[var(--color-navy-900)]">
                                @if ($view === 'dikirim')
                                    {{ $d->tipe_tujuan === 'unit' ? $d->unit->nama_unit ?? '—' : $d->kepadaUser->name ?? '—' }}
                                @else
                                    {{ $d->dariUser->name }}
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-sm text-[var(--color-slate-500)] truncate max-w-xs">
                                {{ \Illuminate\Support\Str::limit($d->instruksi, 50) }}</td>
                            <td class="px-5 py-3.5 text-sm text-center text-[var(--color-slate-500)]">
                                {{ $d->batas_waktu?->format('d M Y') ?? '-' }}
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <span
                                    class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium {{ $sc['bg'] }} {{ $sc['text'] }}">
                                    <span
                                        class="w-1.5 h-1.5 rounded-full {{ $sc['dot'] }}"></span>{{ $statusLabel }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <a href="{{ route('disposisi.show', $d->suratMasuk) }}"
                                    class="inline-flex p-1.5 rounded-lg text-[var(--color-slate-400)] hover:text-[var(--color-blue-500)] hover:bg-[var(--color-blue-50)] transition-colors"
                                    title="Lihat Disposisi">
                                    <x-icon name="eye" class="w-4 h-4" />
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-14 text-center">
                                <div
                                    class="w-12 h-12 rounded-2xl bg-[var(--color-slate-100)] flex items-center justify-center mx-auto mb-3">
                                    <x-icon name="paper-airplane" class="w-5 h-5 text-[var(--color-slate-400)]" />
                                </div>
                                <p class="text-sm font-medium text-[var(--color-slate-500)]">
                                    {{ $view === 'dikirim' ? 'Belum ada disposisi yang kamu kirim.' : 'Belum ada disposisi untukmu.' }}
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($disposisis->hasPages())
            <div class="px-5 py-3 border-t border-[var(--color-slate-100)] bg-[var(--color-slate-50)]/50">
                {{ $disposisis->links() }}</div>
        @endif
    </div>

</x-layouts.app>
