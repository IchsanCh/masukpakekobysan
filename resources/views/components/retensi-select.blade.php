{{--
    Pilih referensi retensi (bisa dicari lewat kode atau nama kegiatan) + ringkasan masa retensi.
    Semua info diambil dari tabel referensi_retensis, jadi surat cukup menyimpan referensi_retensi_id.

    Mobile : tombol membuka layar penuh berisi kolom cari + daftar (tidak ada dropdown kecil yang sulit disentuh).
    Desktop: dropdown di bawah tombol.
    Memancarkan event `retensi-changed` { id } saat pilihan berubah (dipakai form surat keluar untuk saran nomor).
--}}
@props(['retensis', 'selected' => null, 'name' => 'referensi_retensi_id'])

@php
    $items = $retensis
        ->map(
            fn($r) => [
                'id' => $r->id,
                'kode' => $r->kode_klasifikasi,
                'nama' => $r->nama_kegiatan,
                'aktif' => (int) $r->masa_aktif,
                'inaktif' => (int) $r->masa_inaktif,
                'nasib' => $r->nasib_akhir_default,
                'keterangan' => $r->keterangan_nasib_akhir,
            ],
        )
        ->values()
        ->all();
@endphp

@once
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('retensiPicker', (rawItems, selected) => ({
                uid: 'rp' + Math.random().toString(36).slice(2, 8),
                // `hay` = bahan pencarian: kode apa adanya, kode tanpa titik (ketik "0001" tetap ketemu 000.1), dan nama
                items: rawItems.map(i => ({
                    ...i,
                    hay: (i.kode + ' ' + i.kode.replace(/\./g, '') + ' ' + i.nama).toLowerCase(),
                })),
                id: selected ? String(selected) : '',
                open: false,
                query: '',
                active: 0,
                invalid: false,

                init() {
                    this.$watch('query', () => {
                        this.active = 0;
                        if (this.$refs.list) this.$refs.list.scrollTop = 0;
                    });
                    this.$watch('active', () => this.$nextTick(() => this.scrollActive()));
                },

                get info() {
                    return this.items.find(i => String(i.id) === this.id) ?? null;
                },

                // Semua kata yang diketik harus ada (urutan bebas) di kode atau nama kegiatan
                get results() {
                    const tokens = this.query.toLowerCase().trim().split(/\s+/).filter(Boolean);
                    if (!tokens.length) return this.items;
                    return this.items.filter(i => tokens.every(t => i.hay.includes(t)));
                },

                get activeId() {
                    const it = this.results[this.active];
                    return it ? this.uid + '-opt-' + it.id : '';
                },

                nasibOf(n) {
                    if (n === 'musnah') return {
                        label: 'Musnah',
                        cls: 'bg-[var(--color-danger)]/10 text-[var(--color-danger)]',
                        note: 'Setelah masa retensi habis, masuk antrean Menunggu Persetujuan pemusnahan.',
                    };
                    if (n === 'permanen') return {
                        label: 'Permanen',
                        cls: 'bg-[var(--color-success)]/10 text-[var(--color-success)]',
                        note: 'Setelah masa retensi habis, disimpan permanen sebagai arsip statis.',
                    };
                    return {
                        label: 'Dinilai kembali',
                        cls: 'bg-[var(--color-warning)]/10 text-[var(--color-warning)]',
                        note: 'Setelah masa retensi habis, masuk tab Perlu Ditinjau untuk ditentukan nasibnya.',
                    };
                },

                // Sorot potongan teks yang cocok dengan kata pencarian (teks di-escape dulu, aman untuk x-html)
                hl(text) {
                    const esc = s => String(s).replace(/[&<>"']/g, c => ({
                        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
                    }[c]));
                    const q = this.query.toLowerCase().trim();
                    if (!q) return esc(text);
                    const lower = String(text).toLowerCase();
                    const ranges = [];
                    q.split(/\s+/).forEach(t => {
                        if (!t) return;
                        let i = lower.indexOf(t);
                        while (i !== -1) {
                            ranges.push([i, i + t.length]);
                            i = lower.indexOf(t, i + t.length);
                        }
                    });
                    if (!ranges.length) return esc(text);
                    ranges.sort((a, b) => a[0] - b[0]);
                    const merged = [ranges[0].slice()];
                    ranges.slice(1).forEach(r => {
                        const last = merged[merged.length - 1];
                        if (r[0] <= last[1]) last[1] = Math.max(last[1], r[1]);
                        else merged.push(r.slice());
                    });
                    const src = String(text);
                    let out = '';
                    let pos = 0;
                    merged.forEach(([s, e]) => {
                        out += esc(src.slice(pos, s));
                        out += '<mark class="rounded-[3px] bg-[var(--color-blue-100)] px-px text-[var(--color-navy-900)]">' + esc(src.slice(s, e)) + '</mark>';
                        pos = e;
                    });
                    return out + esc(src.slice(pos));
                },

                toggle() {
                    this.open ? this.close() : this.show();
                },

                show() {
                    this.open = true;
                    const idx = this.results.findIndex(i => String(i.id) === this.id);
                    this.active = idx >= 0 ? idx : 0;
                    this.$nextTick(() => {
                        if (this.$refs.search) this.$refs.search.focus();
                        this.scrollActive();
                    });
                },

                close(refocus = true) {
                    this.open = false;
                    this.query = '';
                    if (refocus) this.$nextTick(() => this.$refs.trigger && this.$refs.trigger.focus());
                },

                move(step) {
                    const n = this.results.length;
                    if (!n) return;
                    this.active = (this.active + step + n) % n;
                },

                choose() {
                    const it = this.results[this.active];
                    if (it) this.pick(it);
                },

                pick(item) {
                    this.id = String(item.id);
                    this.invalid = false;
                    if (this.$refs.valid) this.$refs.valid.setCustomValidity('');
                    this.close();
                    this.$dispatch('retensi-changed', { id: this.id });
                },

                scrollActive() {
                    const el = this.$refs.list ? this.$refs.list.querySelector('[data-active="true"]') : null;
                    if (el) el.scrollIntoView({ block: 'nearest' });
                },
            }));
        });
    </script>
@endonce

<div class="relative" x-data="retensiPicker(@js($items), @js((string) $selected))" @click.outside="if (open) close(false)">
    <label class="block text-sm font-medium text-[var(--color-navy-700)] mb-1.5" @click.prevent="show()">Referensi Retensi
        *</label>

    {{-- Tombol pemicu: terlihat seperti input, menampilkan pilihan saat ini --}}
    <button type="button" x-ref="trigger" @click="toggle()" aria-haspopup="listbox" :aria-expanded="open"
        :aria-controls="uid + '-list'"
        :class="invalid ? 'border-[var(--color-danger)]' : (open ? 'border-[var(--color-blue-500)] ring-2 ring-[var(--color-blue-500)]/10' :
            'border-[var(--color-slate-200)]')"
        class="w-full min-h-12 sm:min-h-11 px-3.5 py-2 rounded-xl border bg-white text-left flex items-center gap-2.5
               focus:outline-none focus-visible:border-[var(--color-blue-500)] focus-visible:ring-2 focus-visible:ring-[var(--color-blue-500)]/10 transition-all">
        <span x-show="!info" class="flex-1 min-w-0 flex items-center gap-2.5 text-base sm:text-sm text-[var(--color-slate-500)]">
            <x-icon name="magnifying-glass" class="w-4 h-4 shrink-0" />
            <span class="truncate">Cari kode atau nama kegiatan</span>
        </span>
        <span x-show="info" x-cloak class="flex-1 min-w-0 py-0.5">
            <span x-text="info?.kode"
                class="inline-block font-mono text-xs font-semibold text-[var(--color-blue-600)] bg-[var(--color-blue-50)] px-2 py-0.5 rounded-md"></span>
            <span x-text="info?.nama"
                class="mt-0.5 block text-base sm:text-sm leading-snug text-[var(--color-navy-900)] line-clamp-2"></span>
        </span>
        <x-icon name="chevron-down" class="w-4 h-4 shrink-0 text-[var(--color-slate-400)]" />
    </button>

    {{-- Input tersembunyi: membawa nilai ke form dan memicu pesan "wajib diisi" bawaan browser --}}
    <input type="text" name="{{ $name }}" x-ref="valid" :value="id" required tabindex="-1" aria-hidden="true"
        @invalid="$event.target.setCustomValidity('Pilih referensi retensi dulu.'); invalid = true"
        class="absolute left-4 bottom-0 w-px h-px opacity-0 pointer-events-none" />

    <p x-show="invalid" x-cloak class="text-xs text-[var(--color-danger)] mt-1" role="alert">Pilih referensi retensi dulu.</p>
    @error($name)
        <p class="text-xs text-[var(--color-danger)] mt-1">{{ $message }}</p>
    @enderror

    {{-- Panel pencarian: layar penuh di mobile, dropdown di sm ke atas --}}
    <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-150 motion-reduce:transition-none"
        x-transition:enter-start="opacity-0 translate-y-3 sm:-translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-100 motion-reduce:transition-none"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" role="dialog"
        aria-label="Pilih referensi retensi" @keydown.escape.stop="close()"
        class="fixed inset-0 z-50 flex flex-col bg-white
               sm:absolute sm:inset-auto sm:left-0 sm:right-0 sm:top-full sm:z-40 sm:mt-2 sm:max-h-[24rem] sm:rounded-xl sm:border sm:border-[var(--color-slate-200)]
               sm:shadow-[0_16px_40px_-12px_rgba(11,25,44,0.28)]">

        {{-- Judul + tutup (mobile saja) --}}
        <div class="sm:hidden flex items-center justify-between gap-2 h-14 pl-4 pr-2 border-b border-[var(--color-slate-200)] shrink-0">
            <p class="font-[var(--font-heading)] text-base font-bold text-[var(--color-navy-900)]">Pilih referensi retensi</p>
            <button type="button" @click="close()" aria-label="Tutup"
                class="size-11 inline-flex items-center justify-center rounded-xl text-[var(--color-slate-500)] hover:bg-[var(--color-slate-100)]">
                <x-icon name="x-mark" class="w-5 h-5" />
            </button>
        </div>

        {{-- Kolom cari --}}
        <div class="p-3 sm:p-2.5 border-b border-[var(--color-slate-100)] shrink-0">
            <div class="relative">
                <x-icon name="magnifying-glass"
                    class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-[var(--color-slate-400)] pointer-events-none" />
                <input type="text" x-ref="search" x-model="query" role="combobox" aria-autocomplete="list"
                    aria-expanded="true" :aria-controls="uid + '-list'" :aria-activedescendant="activeId"
                    aria-label="Cari kode atau nama kegiatan" placeholder="Cari kode atau nama kegiatan"
                    autocomplete="off" autocapitalize="off" spellcheck="false" enterkeyhint="search"
                    @keydown.arrow-down.prevent="move(1)" @keydown.arrow-up.prevent="move(-1)"
                    @keydown.enter.prevent="choose()"
                    class="w-full pl-10 pr-11 py-3 sm:py-2 min-h-12 sm:min-h-0 text-base sm:text-sm rounded-xl border border-[var(--color-slate-200)]
                           focus:outline-none focus:border-[var(--color-blue-500)] focus:ring-2 focus:ring-[var(--color-blue-500)]/10 transition-all" />
                <button type="button" x-show="query" x-cloak @click="query = ''; $refs.search.focus()"
                    aria-label="Hapus pencarian"
                    class="absolute right-1 top-1/2 -translate-y-1/2 size-10 sm:size-8 inline-flex items-center justify-center rounded-lg text-[var(--color-slate-400)] hover:text-[var(--color-navy-900)]">
                    <x-icon name="x-mark" class="w-4 h-4" />
                </button>
            </div>
            <p class="mt-2 px-1 text-xs text-[var(--color-slate-500)]" aria-live="polite">
                <span x-show="!query.trim()"><span x-text="items.length"></span> kegiatan, urut kode klasifikasi</span>
                <span x-show="query.trim()" x-cloak><span x-text="results.length"></span> dari <span
                        x-text="items.length"></span> kegiatan cocok</span>
            </p>
        </div>

        {{-- Daftar hasil --}}
        <div x-ref="list" class="flex-1 min-h-0 overflow-y-auto overscroll-contain pb-[env(safe-area-inset-bottom)]">
            <ul role="listbox" :id="uid + '-list'" aria-label="Referensi retensi">
                <template x-for="(item, i) in results" :key="item.id">
                    <li role="option" :id="uid + '-opt-' + item.id" :aria-selected="String(item.id) === id"
                        :data-active="i === active" @click="pick(item)" @mousemove="active = i"
                        :class="i === active ? 'bg-[var(--color-slate-100)]' : ''"
                        class="flex items-start gap-3 px-4 sm:px-3 py-3 sm:py-2.5 cursor-pointer border-b border-[var(--color-slate-100)] last:border-b-0 active:bg-[var(--color-slate-100)]">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span x-html="hl(item.kode)"
                                    class="font-mono text-xs font-semibold text-[var(--color-blue-600)] bg-[var(--color-blue-50)] px-2 py-0.5 rounded-md"></span>
                                <span x-text="nasibOf(item.nasib).label" :class="nasibOf(item.nasib).cls"
                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium"></span>
                                <span class="text-xs text-[var(--color-slate-500)]">Total <span
                                        x-text="item.aktif + item.inaktif"></span> th</span>
                            </div>
                            <p x-html="hl(item.nama)"
                                class="mt-1 text-sm leading-snug text-[var(--color-navy-900)]"></p>
                        </div>
                        <svg x-show="String(item.id) === id" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                            stroke-linejoin="round" aria-hidden="true"
                            class="w-5 h-5 shrink-0 mt-0.5 text-[var(--color-blue-600)]">
                            <path d="m4.5 12.75 6 6 9-13.5" />
                        </svg>
                    </li>
                </template>
            </ul>

            <div x-show="results.length === 0" x-cloak class="px-6 py-12 text-center">
                <div
                    class="w-12 h-12 rounded-2xl bg-[var(--color-slate-100)] flex items-center justify-center mx-auto mb-3">
                    <x-icon name="magnifying-glass" class="w-5 h-5 text-[var(--color-slate-400)]" />
                </div>
                <p class="text-sm font-medium text-[var(--color-slate-600)]">Tidak ada yang cocok dengan <span
                        x-text="'“' + query.trim() + '”'"></span></p>
                <p class="text-xs text-[var(--color-slate-500)] mt-1">Coba tulis kodenya saja, atau satu kata dari nama
                    kegiatan.</p>
            </div>
        </div>
    </div>

    {{-- Ringkasan masa retensi untuk pilihan saat ini --}}
    <div x-show="info" x-cloak x-transition.opacity.duration.150ms
        class="mt-3 rounded-xl border border-[var(--color-slate-200)] bg-[var(--color-slate-50)] overflow-hidden">
        <dl class="grid grid-cols-3 divide-x divide-[var(--color-slate-200)] text-center">
            <div class="px-2 py-3">
                <dt class="text-xs text-[var(--color-slate-500)]">Masa aktif</dt>
                <dd class="mt-0.5 font-[var(--font-heading)] text-lg font-bold text-[var(--color-navy-900)]"><span
                        x-text="info?.aktif"></span> <span
                        class="text-xs font-medium text-[var(--color-slate-500)]">th</span></dd>
            </div>
            <div class="px-2 py-3">
                <dt class="text-xs text-[var(--color-slate-500)]">Masa inaktif</dt>
                <dd class="mt-0.5 font-[var(--font-heading)] text-lg font-bold text-[var(--color-navy-900)]"><span
                        x-text="info?.inaktif"></span> <span
                        class="text-xs font-medium text-[var(--color-slate-500)]">th</span></dd>
            </div>
            <div class="px-2 py-3">
                <dt class="text-xs text-[var(--color-slate-500)]">Total</dt>
                <dd class="mt-0.5 font-[var(--font-heading)] text-lg font-bold text-[var(--color-navy-900)]"><span
                        x-text="info ? info.aktif + info.inaktif : 0"></span> <span
                        class="text-xs font-medium text-[var(--color-slate-500)]">th</span></dd>
            </div>
        </dl>
        <div class="border-t border-[var(--color-slate-200)] px-3.5 py-3 space-y-1.5">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="text-xs text-[var(--color-slate-500)]">Nasib akhir</span>
                <span x-text="nasibOf(info?.nasib).label" :class="nasibOf(info?.nasib).cls"
                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"></span>
            </div>
            <p x-show="info?.keterangan" x-text="info?.keterangan"
                class="text-xs italic text-[var(--color-warning)]"></p>
            <p x-text="nasibOf(info?.nasib).note" class="text-xs leading-relaxed text-[var(--color-slate-500)]"></p>
        </div>
    </div>
</div>