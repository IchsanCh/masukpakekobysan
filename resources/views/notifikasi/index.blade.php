<x-layouts.app title="Log Notifikasi">

    <div class="mb-6">
        <h1 class="font-[var(--font-heading)] text-2xl font-bold text-[var(--color-navy-900)] tracking-tight">Log
            Notifikasi</h1>
        <p class="text-sm text-[var(--color-slate-500)] mt-0.5">Riwayat notifikasi WhatsApp yang dikirim sistem</p>
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
                        placeholder="Cari nama, nomor HP, atau isi pesan..."
                        class="w-full pl-9 pr-4 py-2 text-sm rounded-xl border border-[var(--color-slate-200)] bg-white
                               placeholder:text-[var(--color-slate-400)] focus:outline-none focus:border-[var(--color-blue-500)] focus:ring-2 focus:ring-[var(--color-blue-500)]/10 transition-all" />
                </div>
                <select name="tipe" onchange="this.form.submit()"
                    class="px-3 py-2 text-sm rounded-xl border border-[var(--color-slate-200)] bg-white text-[var(--color-slate-600)]
                           focus:outline-none focus:border-[var(--color-blue-500)] focus:ring-2 focus:ring-[var(--color-blue-500)]/10 transition-all">
                    <option value="">Semua Tipe</option>
                    @foreach (['surat_masuk_baru' => 'Surat Masuk Baru', 'disposisi_masuk' => 'Disposisi Masuk', 'sub_disposisi' => 'Sub-Disposisi', 'tindaklanjut_selesai' => 'Tindak Lanjut Selesai', 'pengingat_deadline' => 'Pengingat Deadline', 'disposisi_ditolak' => 'Disposisi Ditolak'] as $value => $label)
                        <option value="{{ $value }}" @selected(request('tipe') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <select name="status_kirim" onchange="this.form.submit()"
                    class="px-3 py-2 text-sm rounded-xl border border-[var(--color-slate-200)] bg-white text-[var(--color-slate-600)]
                           focus:outline-none focus:border-[var(--color-blue-500)] focus:ring-2 focus:ring-[var(--color-blue-500)]/10 transition-all">
                    <option value="">Semua Status</option>
                    <option value="terkirim" @selected(request('status_kirim') === 'terkirim')>Terkirim</option>
                    <option value="gagal" @selected(request('status_kirim') === 'gagal')>Gagal</option>
                    <option value="pending" @selected(request('status_kirim') === 'pending')>Pending</option>
                </select>
                <button type="submit"
                    class="px-4 py-2 rounded-xl text-sm font-medium bg-white border border-[var(--color-slate-200)] text-[var(--color-slate-600)] hover:border-[var(--color-slate-300)] hover:text-[var(--color-navy-900)] transition-colors">Cari</button>
                @if (request('search') || request('tipe') || request('status_kirim'))
                    <a href="{{ route('notifikasi.index') }}"
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
                            Waktu</th>
                        <th
                            class="px-5 py-3 text-left text-[11px] font-semibold text-[var(--color-slate-500)] uppercase tracking-wider">
                            Tipe</th>
                        <th
                            class="px-5 py-3 text-left text-[11px] font-semibold text-[var(--color-slate-500)] uppercase tracking-wider">
                            Penerima</th>
                        <th
                            class="px-5 py-3 text-left text-[11px] font-semibold text-[var(--color-slate-500)] uppercase tracking-wider">
                            Pesan</th>
                        <th
                            class="px-5 py-3 text-left text-[11px] font-semibold text-[var(--color-slate-500)] uppercase tracking-wider">
                            Terkait</th>
                        <th
                            class="px-5 py-3 text-center text-[11px] font-semibold text-[var(--color-slate-500)] uppercase tracking-wider">
                            Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--color-slate-100)]">
                    @forelse ($notifications as $n)
                        @php
                            $tipeLabel = [
                                'surat_masuk_baru' => 'Surat Masuk Baru',
                                'disposisi_masuk' => 'Disposisi Masuk',
                                'sub_disposisi' => 'Sub-Disposisi',
                                'tindaklanjut_selesai' => 'Tindak Lanjut Selesai',
                                'pengingat_deadline' => 'Pengingat Deadline',
                                'disposisi_ditolak' => 'Disposisi Ditolak',
                            ][$n->tipe] ?? $n->tipe;

                            $suratMasukIdTerkait = $n->surat_masuk_id ?? $n->disposisi?->surat_masuk_id;
                        @endphp
                        <tr class="even:bg-[var(--color-slate-50)]/40 hover:bg-[var(--color-blue-50)]/40 transition-colors">
                            <td class="px-5 py-3.5 text-xs text-[var(--color-slate-500)] whitespace-nowrap">
                                {{ $n->created_at->format('d M Y, H:i') }}
                            </td>
                            <td class="px-5 py-3.5">
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-[var(--color-slate-100)] text-[var(--color-slate-600)] whitespace-nowrap">
                                    {{ $tipeLabel }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5">
                                <p class="text-sm text-[var(--color-navy-900)] font-medium">{{ $n->user->name ?? '—' }}</p>
                                <p class="text-xs text-[var(--color-slate-400)] font-mono">{{ $n->no_wa_tujuan }}</p>
                            </td>
                            <td class="px-5 py-3.5 max-w-xs">
                                <p class="text-xs text-[var(--color-slate-600)] line-clamp-2" title="{{ $n->pesan }}">
                                    {{ \Illuminate\Support\Str::limit($n->pesan, 80) }}
                                </p>
                            </td>
                            <td class="px-5 py-3.5">
                                @if ($suratMasukIdTerkait)
                                    <a href="{{ route('disposisi.show', $suratMasukIdTerkait) }}"
                                        class="text-xs font-medium text-[var(--color-blue-600)] hover:underline">
                                        {{ $n->suratMasuk->nomor_surat ?? $n->disposisi->suratMasuk->nomor_surat ?? 'Lihat' }}
                                    </a>
                                @else
                                    <span class="text-xs text-[var(--color-slate-300)]">-</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                @switch($n->status_kirim)
                                    @case('terkirim')
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium bg-[var(--color-success)]/10 text-[var(--color-success)]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[var(--color-success)]"></span>Terkirim
                                        </span>
                                    @break

                                    @case('gagal')
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium bg-[var(--color-danger)]/10 text-[var(--color-danger)]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[var(--color-danger)]"></span>Gagal
                                        </span>
                                    @break

                                    @default
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium bg-[var(--color-slate-100)] text-[var(--color-slate-500)]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[var(--color-slate-400)]"></span>Pending
                                        </span>
                                @endswitch
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-14 text-center">
                                <div
                                    class="w-12 h-12 rounded-2xl bg-[var(--color-slate-100)] flex items-center justify-center mx-auto mb-3">
                                    <x-icon name="bell" class="w-5 h-5 text-[var(--color-slate-400)]" />
                                </div>
                                <p class="text-sm font-medium text-[var(--color-slate-500)]">Belum ada notifikasi yang
                                    tercatat.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($notifications->hasPages())
            <div class="px-5 py-3 border-t border-[var(--color-slate-100)] bg-[var(--color-slate-50)]/50">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>

</x-layouts.app>