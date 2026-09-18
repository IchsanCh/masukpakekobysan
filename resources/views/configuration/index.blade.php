<x-layouts.app title="Konfigurasi">

    <div class="mb-6">
        <h1 class="font-[var(--font-heading)] text-2xl font-bold text-[var(--color-navy-900)] tracking-tight">
            Konfigurasi</h1>
        <p class="text-sm text-[var(--color-slate-500)] mt-0.5">Pengaturan Fonnte, template notifikasi WhatsApp, dan
            jadwal pengingat</p>
    </div>

    <div x-data="{
        submitting: false,
        showToken: false,
        activeTemplate: @js($waTemplates->keys()->first()),
        templates: @js($waTemplates->mapWithKeys(fn ($c) => [$c->key => old("values.{$c->key}", $c->value)])),
        sampleData: {
            nama: 'Budi Santoso',
            nomor_surat: '005/415.4/2026',
            pengirim: 'Dinas Pendidikan Kab. Pekalongan',
            sifat_surat: 'Penting',
            dari: 'Kepala Dinas',
            instruksi: 'Mohon segera ditindaklanjuti dan laporkan hasilnya sebelum akhir pekan.',
            batas_waktu: '15 Sep 2026, 16:00',
            pelaksana: 'Siti Aminah',
            keterangan: 'Surat sudah diteruskan ke bagian terkait untuk diproses lebih lanjut.',
            penolak: 'Kepala Bidang',
            alasan: 'Bukan kewenangan unit ini, silakan disposisikan ke unit yang sesuai.',
        },
        fonnteStatus: 'loading',
        fonnteInfo: null,

        formatWA(text) {
            if (!text) return '<span class=\'opacity-40 italic\'>Kosong</span>';
            // Normalisasi semua jenis line ending jadi \n polos dulu, baru diproses —
            // biar gak ada \r sisa yang ikut dirender sebagai baris baru tambahan.
            let out = text.replace(/\r\n/g, '\n').replace(/\r/g, '\n');
            out = out.replace(/\{(\w+)\}/g, (m, k) => this.sampleData[k] ?? m);
            out = out.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
            out = out.replace(/\*([^\*\n]+)\*/g, '<b>$1</b>');
            out = out.replace(/_([^_\n]+)_/g, '<i>$1</i>');
            out = out.replace(/~([^~\n]+)~/g, '<s>$1</s>');
            out = out.replace(/```([^`]+)```/g, '<code class=\'px-1 py-0.5 rounded bg-black/10 text-[13px]\'>$1</code>');
            out = out.replace(/\n/g, '<br>');
            return out;
        },

        wrapSelection(marker) {
            const el = this.$refs['ta_' + this.activeTemplate];
            if (!el) return;
            const start = el.selectionStart, end = el.selectionEnd;
            const text = this.templates[this.activeTemplate];
            const selected = text.slice(start, end) || 'teks';
            this.templates[this.activeTemplate] = text.slice(0, start) + marker + selected + marker + text.slice(end);
            this.$nextTick(() => {
                el.focus();
                el.selectionStart = start + marker.length;
                el.selectionEnd = start + marker.length + selected.length;
            });
        },

        async checkFonnte() {
            this.fonnteStatus = 'loading';
            try {
                const res = await fetch('{{ route('configuration.fonnte-status') }}');
                const data = await res.json();
                this.fonnteInfo = data;
                if (data.device_status === 'connect') {
                    this.fonnteStatus = 'connected';
                } else if (data.status === false) {
                    this.fonnteStatus = 'error';
                } else {
                    this.fonnteStatus = 'disconnected';
                }
            } catch (e) {
                this.fonnteStatus = 'error';
                this.fonnteInfo = { reason: 'Gagal memuat status dari server.' };
            }
        },
    }" x-init="checkFonnte()">

        <form method="POST" action="{{ route('configuration.update') }}" @submit="submitting = true" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Fonnte --}}
            <div
                class="bg-white rounded-2xl border border-[var(--color-slate-200)]/70
                        shadow-[0_1px_2px_rgba(15,23,42,0.04),0_12px_28px_-10px_rgba(15,23,42,0.10)] p-5">
                <div class="flex items-start justify-between gap-3 mb-4">
                    <div>
                        <h2
                            class="font-[var(--font-heading)] text-sm font-bold text-[var(--color-navy-900)] uppercase tracking-wider">
                            Fonnte</h2>
                        <p class="text-xs text-[var(--color-slate-400)] mt-0.5">Kredensial layanan pengirim notifikasi
                            WhatsApp</p>
                    </div>

                    {{-- Status device --}}
                    <div class="flex items-center gap-2 shrink-0">
                        <template x-if="fonnteStatus === 'loading'">
                            <span
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-[var(--color-slate-100)] text-[var(--color-slate-500)]">
                                <span class="w-1.5 h-1.5 rounded-full bg-[var(--color-slate-400)] animate-pulse"></span>
                                Mengecek...
                            </span>
                        </template>
                        <template x-if="fonnteStatus === 'connected'">
                            <span
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-[var(--color-success)]/10 text-[var(--color-success)]">
                                <span class="w-1.5 h-1.5 rounded-full bg-[var(--color-success)]"></span>
                                Terhubung
                            </span>
                        </template>
                        <template x-if="fonnteStatus === 'disconnected'">
                            <span
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-[var(--color-warning)]/10 text-[var(--color-warning)]">
                                <span class="w-1.5 h-1.5 rounded-full bg-[var(--color-warning)]"></span>
                                Terputus
                            </span>
                        </template>
                        <template x-if="fonnteStatus === 'error'">
                            <span
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-[var(--color-danger)]/10 text-[var(--color-danger)]">
                                <span class="w-1.5 h-1.5 rounded-full bg-[var(--color-danger)]"></span>
                                Error
                            </span>
                        </template>
                        <button type="button" @click="checkFonnte()"
                            class="p-1.5 rounded-lg text-[var(--color-slate-400)] hover:text-[var(--color-blue-500)] hover:bg-[var(--color-blue-50)] transition-colors"
                            title="Cek ulang">
                            <x-icon name="arrow-path" class="w-3.5 h-3.5" />
                        </button>
                    </div>
                </div>

                {{-- Detail device kalau ada --}}
                <div x-show="fonnteStatus === 'connected' && fonnteInfo" x-cloak
                    class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4 p-3 rounded-xl bg-[var(--color-slate-50)] text-xs">
                    <div>
                        <p class="text-[var(--color-slate-400)]">Nomor Device</p>
                        <p class="font-medium text-[var(--color-navy-900)] mt-0.5" x-text="fonnteInfo?.device ?? '-'"></p>
                    </div>
                    <div>
                        <p class="text-[var(--color-slate-400)]">Paket</p>
                        <p class="font-medium text-[var(--color-navy-900)] mt-0.5" x-text="fonnteInfo?.package ?? '-'"></p>
                    </div>
                    <div>
                        <p class="text-[var(--color-slate-400)]">Sisa Kuota</p>
                        <p class="font-medium text-[var(--color-navy-900)] mt-0.5" x-text="fonnteInfo?.quota ?? '-'"></p>
                    </div>
                    <div>
                        <p class="text-[var(--color-slate-400)]">Berlaku s.d.</p>
                        <p class="font-medium text-[var(--color-navy-900)] mt-0.5" x-text="fonnteInfo?.expired ?? '-'"></p>
                    </div>
                </div>
                <p x-show="fonnteStatus === 'disconnected'" x-cloak
                    class="text-xs text-[var(--color-warning)] mb-4 -mt-1">Device belum tersambung — scan ulang QR
                    Fonnte di dashboard Fonnte kamu.</p>
                <p x-show="fonnteStatus === 'error'" x-cloak class="text-xs text-[var(--color-danger)] mb-4 -mt-1"
                    x-text="fonnteInfo?.reason ?? 'Gagal memuat status.'"></p>

                <div class="max-w-sm">
                    <label class="block text-sm font-medium text-[var(--color-navy-700)] mb-1.5">API Token</label>
                    <div class="relative">
                        <input :type="showToken ? 'text' : 'password'" name="values[fonnte_token]"
                            value="{{ old('values.fonnte_token', $fonnte['fonnte_token']->value ?? '') }}"
                            class="w-full px-3.5 py-2.5 pr-10 text-sm font-mono rounded-xl border border-[var(--color-slate-200)] focus:outline-none focus:border-[var(--color-blue-500)] focus:ring-2 focus:ring-[var(--color-blue-500)]/10 transition-all" />
                        <button type="button" @click="showToken = !showToken"
                            class="absolute right-2.5 top-1/2 -translate-y-1/2 p-1 text-[var(--color-slate-400)] hover:text-[var(--color-slate-600)]">
                            <x-icon name="eye" class="w-4 h-4" />
                        </button>
                    </div>
                    @if ($desc = $fonnte['fonnte_token']->description ?? null)
                        <p class="text-[11px] text-[var(--color-slate-400)] mt-1">{{ $desc }}</p>
                    @endif
                    <p class="text-[11px] text-[var(--color-slate-400)] mt-1">Nomor pengirim otomatis ngikutin device
                        yang terpasang di token ini — lihat di status koneksi di atas.</p>
                </div>
            </div>

            {{-- Template WhatsApp --}}
            <div
                class="bg-white rounded-2xl border border-[var(--color-slate-200)]/70
                        shadow-[0_1px_2px_rgba(15,23,42,0.04),0_12px_28px_-10px_rgba(15,23,42,0.10)] overflow-hidden">
                <div class="px-5 pt-5">
                    <h2
                        class="font-[var(--font-heading)] text-sm font-bold text-[var(--color-navy-900)] uppercase tracking-wider">
                        Template WhatsApp</h2>
                    <p class="text-xs text-[var(--color-slate-400)] mt-0.5">Preview di kanan nampilin persis gimana
                        pesannya bakal keliatan di WhatsApp</p>
                </div>

                {{-- Tabs --}}
                <div class="flex gap-1.5 px-5 mt-4 pb-4 overflow-x-auto border-b border-[var(--color-slate-100)]">
                    @foreach ($waTemplates as $key => $config)
                        @php
                            $label = str_replace(['Template notifikasi ', 'Template '], '', $config->description ?? $key);
                            $label = ucfirst($label);
                        @endphp
                        <button type="button" @click="activeTemplate = '{{ $key }}'"
                            :class="activeTemplate === '{{ $key }}' ?
                                'bg-[var(--color-blue-500)] text-white' :
                                'bg-[var(--color-slate-100)] text-[var(--color-slate-500)] hover:bg-[var(--color-slate-200)]'"
                            class="shrink-0 px-3 py-1.5 rounded-lg text-xs font-medium transition-colors whitespace-nowrap">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-0">
                    {{-- Editor --}}
                    <div class="p-5 border-r-0 lg:border-r border-[var(--color-slate-100)]">
                        @foreach ($waTemplates as $key => $config)
                            <div x-show="activeTemplate === '{{ $key }}'" x-cloak>
                                <div class="flex items-center gap-1 mb-2">
                                    <button type="button" @click="wrapSelection('*')"
                                        class="w-7 h-7 rounded-lg text-xs font-bold text-[var(--color-slate-500)] hover:bg-[var(--color-slate-100)] transition-colors"
                                        title="Tebal (*teks*)">B</button>
                                    <button type="button" @click="wrapSelection('_')"
                                        class="w-7 h-7 rounded-lg text-xs italic font-semibold text-[var(--color-slate-500)] hover:bg-[var(--color-slate-100)] transition-colors"
                                        title="Miring (_teks_)">I</button>
                                    <button type="button" @click="wrapSelection('~')"
                                        class="w-7 h-7 rounded-lg text-xs line-through font-semibold text-[var(--color-slate-500)] hover:bg-[var(--color-slate-100)] transition-colors"
                                        title="Coret (~teks~)">S</button>
                                </div>
                                <textarea name="values[{{ $key }}]" x-ref="ta_{{ $key }}" x-model="templates['{{ $key }}']"
                                    rows="7"
                                    class="w-full px-3.5 py-2.5 text-sm font-mono rounded-xl border border-[var(--color-slate-200)] focus:outline-none focus:border-[var(--color-blue-500)] focus:ring-2 focus:ring-[var(--color-blue-500)]/10 transition-all resize-y"></textarea>
                                <p class="text-[11px] text-[var(--color-slate-400)] mt-1.5">
                                    <span class="font-mono">*tebal*</span> · <span class="font-mono">_miring_</span> ·
                                    <span class="font-mono">~coret~</span> · Enter = baris baru
                                </p>
                            </div>
                        @endforeach
                    </div>

                    {{-- WhatsApp mockup preview --}}
                    <div class="p-5 flex items-start justify-center bg-[var(--color-slate-50)]/60">
                        <div class="w-full max-w-[300px] rounded-2xl overflow-hidden border border-[var(--color-slate-200)] shadow-sm">
                            {{-- WA header bar --}}
                            <div class="bg-[#075E54] px-3 py-2.5 flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center text-white text-xs font-semibold shrink-0">
                                    DI
                                </div>
                                <div class="min-w-0">
                                    <p class="text-white text-[13px] font-medium leading-tight truncate">DPMPTSP Info</p>
                                    <p class="text-white/70 text-[10px] leading-tight">online</p>
                                </div>
                            </div>
                            {{-- WA chat background --}}
                            <div class="bg-[#E5DDD5] px-3 py-4 min-h-[260px] flex items-start">
                                <div class="bg-[#D9FDD3] rounded-lg rounded-tl-none px-2.5 py-2 max-w-[85%] shadow-sm">
                                    <p class="text-[13px] leading-snug text-[#111B21] break-words"
                                        style="font-family: system-ui, -apple-system, sans-serif;"
                                        x-html="formatWA(templates[activeTemplate])"></p>
                                    <div class="flex items-center justify-end gap-1 mt-1">
                                        <span class="text-[10px] text-[#667781]">09:41</span>
                                        <svg class="w-3.5 h-3.5 text-[#53BDEB]" viewBox="0 0 16 15" fill="currentColor">
                                            <path d="M15.01 3.316l-.478-.372a.365.365 0 00-.51.063L8.61 9.98a.32.32 0 01-.484.033l-.358-.325a.319.319 0 00-.484.032l-.378.483a.418.418 0 00.036.541l1.32 1.266c.143.14.361.125.484-.033l6.272-8.048a.366.366 0 00-.064-.512zm-4.1 0l-.478-.372a.365.365 0 00-.51.063l-5.415 6.973a.32.32 0 01-.484.033L2.706 8.71a.319.319 0 00-.484.032l-.378.483a.418.418 0 00.036.541l2.643 2.534c.143.14.361.125.484-.033l6.272-8.048a.366.366 0 00-.064-.512l-.964-.75-.001-.001z" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Jadwal Pengingat --}}
            <div
                class="bg-white rounded-2xl border border-[var(--color-slate-200)]/70
                        shadow-[0_1px_2px_rgba(15,23,42,0.04),0_12px_28px_-10px_rgba(15,23,42,0.10)] p-5 space-y-4">
                <div>
                    <h2
                        class="font-[var(--font-heading)] text-sm font-bold text-[var(--color-navy-900)] uppercase tracking-wider">
                        Jadwal Pengingat</h2>
                    <p class="text-xs text-[var(--color-slate-400)] mt-0.5">Pengingat harian untuk disposisi yang
                        belum ditindaklanjuti</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-end">
                    <div>
                        <label class="block text-sm font-medium text-[var(--color-navy-700)] mb-1.5">Jam Kirim</label>
                        <input type="time" name="values[pengingat_jam_kirim]"
                            value="{{ old('values.pengingat_jam_kirim', $scheduler['pengingat_jam_kirim']->value ?? '07:00') }}"
                            class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-[var(--color-slate-200)] focus:outline-none focus:border-[var(--color-blue-500)] focus:ring-2 focus:ring-[var(--color-blue-500)]/10 transition-all" />
                    </div>
                    <div>
                        <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                            <input type="hidden" name="values[pengingat_aktif]" value="0">
                            <input type="checkbox" name="values[pengingat_aktif]" value="1"
                                @checked(old('values.pengingat_aktif', $scheduler['pengingat_aktif']->value ?? '0') === '1')
                                class="w-4 h-4 rounded border-[var(--color-slate-300)] text-[var(--color-blue-500)] focus:ring-[var(--color-blue-500)]/30" />
                            <span class="text-sm font-medium text-[var(--color-navy-700)]">Aktifkan pengingat
                                harian</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" :disabled="submitting"
                    class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-gradient-to-br from-[var(--color-blue-500)] to-[var(--color-blue-600)]
                           shadow-[0_4px_10px_-2px_rgba(37,99,235,0.35)] hover:shadow-[0_8px_16px_-2px_rgba(37,99,235,0.45)] transition-all
                           disabled:opacity-60 inline-flex items-center gap-2">
                    <span x-show="submitting" class="loading loading-spinner loading-xs"></span>
                    <span x-text="submitting ? 'Menyimpan...' : 'Simpan Konfigurasi'"></span>
                </button>
            </div>
        </form>
    </div>

</x-layouts.app>