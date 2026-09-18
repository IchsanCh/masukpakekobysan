<x-layouts.app title="Tindak Lanjut">

    <div class="mb-6">
        <h1 class="font-[var(--font-heading)] text-2xl font-bold text-[var(--color-navy-900)] tracking-tight">Tindak
            Lanjut</h1>
        <p class="text-sm text-[var(--color-slate-500)] mt-0.5">Disposisi yang perlu kamu tindaklanjuti, dan riwayat
            yang sudah kamu tulis</p>
    </div>

    {{-- Perlu Ditindaklanjuti --}}
    <div
        class="bg-white rounded-2xl border border-[var(--color-slate-200)]/70
                shadow-[0_1px_2px_rgba(15,23,42,0.04),0_12px_28px_-10px_rgba(15,23,42,0.10)] overflow-hidden mb-6">
        <div class="px-5 py-4 border-b border-[var(--color-slate-100)] flex items-center justify-between">
            <h2 class="font-[var(--font-heading)] text-sm font-bold text-[var(--color-navy-900)] uppercase tracking-wider">
                Perlu Ditindaklanjuti</h2>
            <span
                class="inline-flex items-center justify-center min-w-[1.5rem] h-6 px-1.5 rounded-full text-xs font-semibold bg-[var(--color-blue-500)]/10 text-[var(--color-blue-600)]">
                {{ $pending->count() }}
            </span>
        </div>

        @forelse ($pending as $d)
            @php
                $isOverdue = $d->batas_waktu && $d->batas_waktu->isPast();
                $statusLabel = [
                    'menunggu' => 'Menunggu',
                    'diterima' => 'Diterima',
                    'diproses' => 'Diproses',
                ][$d->status];
                $statusColor = [
                    'menunggu' => ['bg' => 'bg-[var(--color-slate-100)]', 'text' => 'text-[var(--color-slate-500)]'],
                    'diterima' => ['bg' => 'bg-[var(--color-blue-500)]/10', 'text' => 'text-[var(--color-blue-600)]'],
                    'diproses' => ['bg' => 'bg-[var(--color-warning)]/10', 'text' => 'text-[var(--color-warning)]'],
                ][$d->status];
            @endphp
            <a href="{{ route('disposisi.show', $d->surat_masuk_id) }}"
                class="flex items-start gap-3 px-5 py-4 border-b border-[var(--color-slate-100)] last:border-0 hover:bg-[var(--color-blue-50)]/30 transition-colors">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-sm font-semibold text-[var(--color-navy-900)]">{{ $d->suratMasuk->nomor_surat ?? '—' }}</span>
                        <span
                            class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium {{ $statusColor['bg'] }} {{ $statusColor['text'] }}">
                            {{ $statusLabel }}
                        </span>
                        @if ($isOverdue)
                            <span
                                class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-[var(--color-danger)]/10 text-[var(--color-danger)]">
                                Lewat batas waktu
                            </span>
                        @endif
                    </div>
                    <p class="text-sm text-[var(--color-slate-600)] mt-1.5">{{ $d->instruksi }}</p>
                    <div class="flex items-center gap-3 mt-1.5 text-xs text-[var(--color-slate-400)]">
                        <span>Dari {{ $d->dariUser->name }}</span>
                        @if ($d->batas_waktu)
                            <span class="{{ $isOverdue ? 'text-[var(--color-danger)] font-medium' : '' }}">
                                Batas: {{ $d->batas_waktu->format('d M Y H:i') }}
                            </span>
                        @endif
                    </div>
                </div>
                <x-icon name="chevron-right" class="w-4 h-4 text-[var(--color-slate-300)] shrink-0 mt-1" />
            </a>
        @empty
            <div class="px-5 py-12 text-center">
                <div
                    class="w-12 h-12 rounded-2xl bg-[var(--color-success)]/10 flex items-center justify-center mx-auto mb-3">
                    <x-icon name="check-circle" class="w-5 h-5 text-[var(--color-success)]" />
                </div>
                <p class="text-sm font-medium text-[var(--color-slate-500)]">Semua disposisi kamu sudah ditindaklanjuti.
                </p>
            </div>
        @endforelse
    </div>

    {{-- Riwayat --}}
    <div
        class="bg-white rounded-2xl border border-[var(--color-slate-200)]/70
                shadow-[0_1px_2px_rgba(15,23,42,0.04),0_12px_28px_-10px_rgba(15,23,42,0.10)] overflow-hidden">
        <div class="px-5 py-4 border-b border-[var(--color-slate-100)]">
            <h2 class="font-[var(--font-heading)] text-sm font-bold text-[var(--color-navy-900)] uppercase tracking-wider">
                Riwayat Tindak Lanjut Saya</h2>
        </div>

        @forelse ($riwayat as $tl)
            <a href="{{ route('disposisi.show', $tl->disposisi->surat_masuk_id) }}"
                class="flex items-start gap-3 px-5 py-4 border-b border-[var(--color-slate-100)] last:border-0 hover:bg-[var(--color-slate-50)] transition-colors">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-sm font-semibold text-[var(--color-navy-900)]">
                            {{ $tl->disposisi->suratMasuk->nomor_surat ?? '—' }}
                        </span>
                        <span class="text-[11px] text-[var(--color-slate-400)] shrink-0">
                            {{ $tl->created_at->format('d M Y, H:i') }}
                        </span>
                    </div>
                    <p class="text-sm text-[var(--color-slate-600)] mt-1">{{ \Illuminate\Support\Str::limit($tl->keterangan, 120) }}</p>
                    @if ($tl->lampiran->isNotEmpty())
                        <p class="text-xs text-[var(--color-slate-400)] mt-1">
                            📎 {{ $tl->lampiran->count() }} lampiran
                        </p>
                    @endif
                </div>
            </a>
        @empty
            <div class="px-5 py-12 text-center">
                <p class="text-sm font-medium text-[var(--color-slate-500)]">Kamu belum pernah menulis tindak lanjut.</p>
            </div>
        @endforelse

        @if ($riwayat->hasPages())
            <div class="px-5 py-3 border-t border-[var(--color-slate-100)] bg-[var(--color-slate-50)]/50">
                {{ $riwayat->links() }}
            </div>
        @endif
    </div>

</x-layouts.app>