@php
    $depth = $depth ?? 0;
    $canActOnThis = $disposisi->canBeActedBy(auth()->user());
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
    $sc = $statusColors[$disposisi->status];
    $statusLabel = [
        'menunggu' => 'Menunggu',
        'diterima' => 'Diterima',
        'diproses' => 'Diproses',
        'selesai' => 'Selesai',
        'ditolak' => 'Ditolak',
    ][$disposisi->status];
    $isOverdue = $disposisi->batas_waktu && $disposisi->batas_waktu->isPast() && !$disposisi->isTerminal();
@endphp

<div class="relative {{ $depth > 0 ? 'mt-3 ml-5 pl-5 border-l-2 border-[var(--color-slate-200)]' : '' }}">
    <span
        class="absolute -left-[7px] top-4 w-3 h-3 rounded-full {{ $sc['dot'] }} ring-4 ring-white {{ $depth === 0 ? 'hidden' : '' }}"></span>

    <div
        class="bg-white rounded-2xl border border-[var(--color-slate-200)]/70 shadow-[0_1px_2px_rgba(15,23,42,0.04),0_6px_16px_-8px_rgba(15,23,42,0.08)] p-4">
        <div class="flex items-start justify-between gap-3 flex-wrap">
            <div class="min-w-0">
                <p class="text-sm text-[var(--color-slate-500)]">
                    <span class="font-semibold text-[var(--color-navy-900)]">{{ $disposisi->dariUser->name }}</span>
                    →
                    @if ($disposisi->tipe_tujuan === 'unit')
                        <span
                            class="font-semibold text-[var(--color-navy-900)]">{{ $disposisi->unit->nama_unit ?? '—' }}</span>
                    @else
                        <span
                            class="font-semibold text-[var(--color-navy-900)]">{{ $disposisi->kepadaUser->name ?? '—' }}</span>
                        <span class="text-xs text-[var(--color-slate-400)]">(personal)</span>
                    @endif
                </p>
                <p class="text-sm text-[var(--color-slate-600)] mt-1.5">{{ $disposisi->instruksi }}</p>
            </div>
            <span
                class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium {{ $sc['bg'] }} {{ $sc['text'] }} shrink-0">
                <span class="w-1.5 h-1.5 rounded-full {{ $sc['dot'] }}"></span>{{ $statusLabel }}
            </span>
        </div>

        <div class="flex items-center gap-3 mt-2.5 text-xs text-[var(--color-slate-400)]">
            <span>{{ $disposisi->created_at->format('d M Y, H:i') }}</span>
            @if ($disposisi->batas_waktu)
                <span class="{{ $isOverdue ? 'text-[var(--color-danger)] font-medium' : '' }}">
                    Batas: {{ $disposisi->batas_waktu->format('d M Y') }}{{ $isOverdue ? ' (lewat)' : '' }}
                </span>
            @endif
        </div>

        @if ($disposisi->status === 'ditolak' && $disposisi->alasan_ditolak)
            <div class="mt-2.5 px-3 py-2 rounded-lg bg-[var(--color-danger)]/5 text-xs text-[var(--color-danger)]">
                <span class="font-medium">Alasan ditolak:</span> {{ $disposisi->alasan_ditolak }}
            </div>
        @endif

        @if ($canActOnThis && !$disposisi->isTerminal())
            <div x-data="{ showForward: false, showReject: false }" class="mt-3 pt-3 border-t border-[var(--color-slate-100)]">
                <div class="flex flex-wrap items-center gap-2">
                    @php $nextStatuses = ['menunggu' => 'diterima', 'diterima' => 'diproses', 'diproses' => 'selesai'][$disposisi->status] ?? null; @endphp
                    @php $nextLabel = ['diterima' => 'Tandai Diterima', 'diproses' => 'Proses', 'selesai' => 'Tandai Selesai'][$nextStatuses] ?? null; @endphp

                    @if ($nextStatuses)
                        <form method="POST" action="{{ route('disposisi.status', $disposisi) }}">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="{{ $nextStatuses }}">
                            <button type="submit"
                                class="px-3 py-1.5 rounded-lg text-xs font-semibold text-white bg-gradient-to-br from-[var(--color-blue-500)] to-[var(--color-blue-600)] shadow-sm hover:shadow transition-all">
                                {{ $nextLabel }}
                            </button>
                        </form>
                    @endif

                    <button type="button" @click="showForward = !showForward; showReject = false"
                        class="px-3 py-1.5 rounded-lg text-xs font-medium border border-[var(--color-slate-200)] text-[var(--color-slate-600)] hover:bg-[var(--color-slate-50)] transition-colors">
                        Teruskan Disposisi
                    </button>

                    <button type="button" @click="showReject = !showReject; showForward = false"
                        class="px-3 py-1.5 rounded-lg text-xs font-medium text-[var(--color-danger)] hover:bg-red-50 transition-colors">
                        Tolak
                    </button>
                </div>

                <div x-show="showForward" x-cloak class="mt-3 p-3 rounded-xl bg-[var(--color-slate-50)]">
                    @include('disposisi._target-form', [
                        'actionUrl' => route('disposisi.forward', $disposisi),
                        'units' => $units,
                        'usersForPersonal' => $usersForPersonal,
                        'submitLabel' => 'Teruskan Disposisi',
                    ])
                </div>

                <div x-show="showReject" x-cloak class="mt-3 p-3 rounded-xl bg-red-50/50">
                    <form method="POST" action="{{ route('disposisi.status', $disposisi) }}" x-data="{ submitting: false }"
                        @submit="submitting = true">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="ditolak">
                        <label class="block text-sm font-medium text-[var(--color-navy-700)] mb-1.5">Alasan Penolakan
                            *</label>
                        <textarea name="alasan_ditolak" rows="2" required
                            class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-[var(--color-slate-200)] focus:outline-none focus:border-[var(--color-danger)] focus:ring-2 focus:ring-[var(--color-danger)]/10 transition-all resize-none"></textarea>
                        <button type="submit" :disabled="submitting"
                            class="mt-2 px-4 py-2 rounded-xl text-sm font-semibold text-white bg-[var(--color-danger)] shadow-sm disabled:opacity-60 inline-flex items-center gap-2">
                            <span x-show="submitting" class="loading loading-spinner loading-xs"></span>
                            <span x-text="submitting ? 'Mengirim...' : 'Konfirmasi Tolak'"></span>
                        </button>
                    </form>
                </div>
            </div>
        @endif
    </div>

    @foreach ($disposisi->childrenRecursive as $child)
        @include('disposisi._node', [
            'disposisi' => $child,
            'depth' => $depth + 1,
            'units' => $units,
            'usersForPersonal' => $usersForPersonal,
        ])
    @endforeach
</div>
