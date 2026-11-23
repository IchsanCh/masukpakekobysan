{{--
    Satu baris pengaturan di halaman Profile: label, nilai sekarang, tombol Ubah.
    Form-nya ($slot) membuka inline di bawah baris. State `open` dan `toggle()` datang dari
    x-data di halaman profile (satu baris terbuka sekali waktu).
    Slot `value` = isi kolom nilai.
--}}
@props(['id', 'label'])

<div>
    <div
        class="grid grid-cols-[minmax(0,1fr)_auto] sm:grid-cols-[9.5rem_minmax(0,1fr)_auto] items-center gap-x-4 gap-y-0.5 px-5 py-3.5">
        <p class="col-span-2 sm:col-span-1 text-sm text-[var(--color-slate-500)]">{{ $label }}</p>

        <div class="min-w-0 text-sm font-medium text-[var(--color-navy-900)]">{{ $value }}</div>

        <button type="button" @click="toggle('{{ $id }}')" :aria-expanded="open === '{{ $id }}'"
            aria-controls="panel-{{ $id }}"
            class="shrink-0 inline-flex items-center justify-center min-h-11 sm:min-h-9 px-3.5 -mr-2 rounded-lg text-sm font-medium text-[var(--color-blue-600)] hover:bg-[var(--color-blue-50)] transition-colors cursor-pointer focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[color:var(--color-blue-500)]">
            <span x-text="open === '{{ $id }}' ? 'Tutup' : 'Ubah'">Ubah</span><span
                class="sr-only">&nbsp;{{ $label }}</span>
        </button>
    </div>

    {{-- Buka/tutup lewat grid-rows (halus, tanpa hitung tinggi). inert = isi yang tertutup tidak bisa difokus. --}}
    <div id="panel-{{ $id }}" x-cloak :inert="open !== '{{ $id }}'"
        :class="open === '{{ $id }}' ? 'grid-rows-[1fr]' : 'grid-rows-[0fr]'"
        class="grid transition-[grid-template-rows] duration-200 ease-out motion-reduce:transition-none">
        <div class="overflow-hidden">
            <div
                class="border-t border-[color:var(--color-slate-100)] bg-[var(--color-slate-50)]/60 px-5 pb-5 pt-4">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>