{{--
    Pilih referensi retensi + kotak info read-only (kode, masa aktif/inaktif, total, nasib akhir).
    Semua info diambil dari tabel referensi_retensis, jadi surat cukup menyimpan referensi_retensi_id.
--}}
@props(['retensis', 'selected' => null])

@php
    $retensiMap = $retensis
        ->mapWithKeys(
            fn($r) => [
                $r->id => [
                    'kode' => $r->kode_klasifikasi,
                    'nama' => $r->nama_kegiatan,
                    'aktif' => (int) $r->masa_aktif,
                    'inaktif' => (int) $r->masa_inaktif,
                    'nasib' => $r->nasib_akhir_default,
                    'keterangan' => $r->keterangan_nasib_akhir,
                ],
            ],
        )
        ->all();
@endphp

<div class="space-y-3" x-data="{
    map: @js($retensiMap),
    id: @js((string) $selected),
    get info() { return this.map[this.id] ?? null; },
    get total() { return this.info ? this.info.aktif + this.info.inaktif : 0; },
    get nasib() {
        const n = this.info?.nasib;
        if (n === 'musnah') return {
            label: 'Musnah',
            cls: 'bg-[var(--color-danger)]/10 text-[var(--color-danger)]',
            note: 'Setelah masa retensi habis, masuk antrean Menunggu Persetujuan pemusnahan.'
        };
        if (n === 'permanen') return {
            label: 'Permanen',
            cls: 'bg-[var(--color-success)]/10 text-[var(--color-success)]',
            note: 'Setelah masa retensi habis, disimpan permanen sebagai arsip statis.'
        };
        return {
            label: 'Dinilai kembali',
            cls: 'bg-[var(--color-warning)]/10 text-[var(--color-warning)]',
            note: 'Setelah masa retensi habis, masuk tab Perlu Ditinjau untuk ditentukan nasibnya.'
        };
    }
}">
    <div>
        <label class="block text-sm font-medium text-[var(--color-navy-700)] mb-1.5">Referensi Retensi *</label>
        <select name="referensi_retensi_id" x-model="id" required
            class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-[var(--color-slate-200)] focus:outline-none focus:border-[var(--color-blue-500)] focus:ring-2 focus:ring-[var(--color-blue-500)]/10 transition-all">
            <option value="">Pilih referensi retensi...</option>
            @foreach ($retensis as $r)
                <option value="{{ $r->id }}">
                    {{ $r->kode_klasifikasi }} — {{ \Illuminate\Support\Str::limit($r->nama_kegiatan, 60) }}
                </option>
            @endforeach
        </select>
        @error('referensi_retensi_id')
            <p class="text-xs text-[var(--color-danger)] mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div x-show="info" x-cloak
        class="rounded-xl border border-[var(--color-slate-200)] bg-[var(--color-slate-50)] p-4 space-y-3">
        <div>
            <p class="text-[11px] font-medium uppercase tracking-wider text-[var(--color-slate-400)] mb-1">Kegiatan</p>
            <p class="text-sm text-[var(--color-navy-900)]">
                <span x-text="info?.kode"
                    class="font-mono text-xs font-semibold text-[var(--color-blue-600)] bg-[var(--color-blue-50)] px-2 py-0.5 rounded-md mr-1.5"></span>
                <span x-text="info?.nama" class="font-medium"></span>
            </p>
        </div>

        <div class="grid grid-cols-3 gap-3">
            <div>
                <p class="text-[11px] font-medium uppercase tracking-wider text-[var(--color-slate-400)]">Masa Aktif</p>
                <p class="text-sm font-semibold text-[var(--color-navy-900)]"><span x-text="info?.aktif"></span> tahun</p>
            </div>
            <div>
                <p class="text-[11px] font-medium uppercase tracking-wider text-[var(--color-slate-400)]">Masa Inaktif</p>
                <p class="text-sm font-semibold text-[var(--color-navy-900)]"><span x-text="info?.inaktif"></span> tahun</p>
            </div>
            <div>
                <p class="text-[11px] font-medium uppercase tracking-wider text-[var(--color-slate-400)]">Total Retensi</p>
                <p class="text-sm font-semibold text-[var(--color-navy-900)]"><span x-text="total"></span> tahun</p>
            </div>
        </div>

        <div>
            <p class="text-[11px] font-medium uppercase tracking-wider text-[var(--color-slate-400)] mb-1">Nasib Akhir</p>
            <span x-text="nasib.label" :class="nasib.cls"
                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"></span>
            <p x-show="info?.keterangan" x-text="info?.keterangan"
                class="text-xs italic text-[var(--color-warning)] mt-1.5"></p>
            <p x-text="nasib.note" class="text-[11px] text-[var(--color-slate-500)] mt-1.5"></p>
        </div>
    </div>
</div>