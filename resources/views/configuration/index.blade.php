@php
    // Nama pendek + kapan dikirim, supaya admin paham fungsi tiap template tanpa buka kodenya.
    $labels = [
        'surat_masuk_baru' => 'Surat masuk baru',
        'disposisi_masuk' => 'Disposisi masuk',
        'sub_disposisi' => 'Sub-disposisi',
        'tindaklanjut_selesai' => 'Tindak lanjut selesai',
        'pengingat_deadline' => 'Pengingat deadline',
        'konfirmasi_musnah' => 'Konfirmasi pemusnahan',
        'konfirmasi_musnah_ringkasan' => 'Konfirmasi pemusnahan (ringkasan)',
        'dinilai_kembali' => 'Perlu dinilai kembali',
        'dinilai_kembali_ringkasan' => 'Perlu dinilai kembali (ringkasan)',
    ];
    $when = [
        'surat_masuk_baru' => 'Dikirim ke semua pimpinan aktif saat surat masuk baru dicatat.',
        'disposisi_masuk' => 'Dikirim ke penerima saat disposisi baru dibuat.',
        'sub_disposisi' => 'Dikirim ke penerima saat disposisi diteruskan ke bidang atau orang lain.',
        'tindaklanjut_selesai' => 'Dikirim ke pemberi disposisi saat penerima menandai tindak lanjut selesai.',
        'pengingat_deadline' => 'Dikirim tiap hari pada jam yang diatur di Jadwal pengingat, selama disposisi lewat batas waktu dan belum selesai.',
        'konfirmasi_musnah' => 'Dikirim ke agendaris saat masa retensi surat habis dan menunggu persetujuan pemusnahan. Dipakai untuk 3 surat atau kurang.',
        'konfirmasi_musnah_ringkasan' => 'Sama seperti konfirmasi pemusnahan, tapi dipakai kalau lebih dari 3 surat sekaligus.',
        'dinilai_kembali' => 'Dikirim ke agendaris saat masa retensi surat habis dan nasib akhirnya perlu ditinjau. Dipakai untuk 3 surat atau kurang.',
        'dinilai_kembali_ringkasan' => 'Sama seperti notifikasi dinilai kembali, tapi dipakai kalau lebih dari 3 surat sekaligus.',
    ];
    // Data yang tersedia di tiap template (sesuai yang dikirim WhatsAppNotifier).
    $placeholderMap = [
        'surat_masuk_baru' => ['nama', 'nomor_surat', 'pengirim', 'sifat_surat'],
        'disposisi_masuk' => ['nama', 'dari', 'nomor_surat', 'instruksi', 'batas_waktu'],
        'sub_disposisi' => ['nama', 'dari', 'nomor_surat', 'instruksi', 'batas_waktu'],
        'tindaklanjut_selesai' => ['nama', 'nomor_surat', 'pelaksana', 'keterangan'],
        'pengingat_deadline' => ['nama', 'nomor_surat', 'batas_waktu', 'dari'],
        'konfirmasi_musnah' => ['nama', 'jenis', 'nomor_surat', 'tanggal_surat', 'kode_klasifikasi', 'link'],
        'konfirmasi_musnah_ringkasan' => ['nama', 'jumlah', 'daftar_surat', 'link'],
        'dinilai_kembali' => ['nama', 'jenis', 'nomor_surat', 'tanggal_surat', 'kode_klasifikasi', 'link'],
        'dinilai_kembali_ringkasan' => ['nama', 'jumlah', 'daftar_surat', 'link'],
    ];
    $groupDefs = [
        'Surat dan disposisi' => ['surat_masuk_baru', 'disposisi_masuk', 'sub_disposisi', 'tindaklanjut_selesai'],
        'Pengingat' => ['pengingat_deadline'],
        'Arsip dan retensi' => [
            'konfirmasi_musnah',
            'konfirmasi_musnah_ringkasan',
            'dinilai_kembali',
            'dinilai_kembali_ringkasan',
        ],
    ];

    $groups = [];
    $dipakai = [];
    foreach ($groupDefs as $judul => $akhiran) {
        foreach ($akhiran as $a) {
            $key = 'wa_template_' . $a;
            if ($waTemplates->has($key)) {
                $groups[$judul][] = $key;
                $dipakai[] = $key;
            }
        }
    }
    $sisa = $waTemplates->keys()->diff($dipakai)->values()->all();
    if ($sisa) {
        $groups['Lainnya'] = $sisa; // template yang belum dikenal tetap muncul
    }

    $meta = $waTemplates->mapWithKeys(function ($c, $key) use ($labels, $when, $placeholderMap) {
        $a = \Illuminate\Support\Str::after($key, 'wa_template_');
        $ph =
            $placeholderMap[$a] ??
            (preg_match_all('/\{(\w+)\}/', (string) $c->value, $m) ? array_values(array_unique($m[1])) : []);

        return [
            $key => [
                'label' => $labels[$a] ?? ucfirst(str_replace(['Template notifikasi ', 'Template '], '', $c->description ?? $a)),
                'when' => $when[$a] ?? ($c->description ?? ''),
                'placeholders' => $ph,
            ],
        ];
    });

    $jamKirim = old('values.pengingat_jam_kirim', $scheduler['pengingat_jam_kirim']->value ?? '07:00');
    $pengingatAktif = old('values.pengingat_aktif', $scheduler['pengingat_aktif']->value ?? '0') === '1';

    $panel =
        'bg-white rounded-2xl border border-[var(--color-slate-200)]/70 shadow-[0_1px_2px_rgba(15,23,42,0.04),0_12px_28px_-10px_rgba(15,23,42,0.10)]';
    $judul = 'font-[var(--font-heading)] text-base font-semibold text-[var(--color-navy-900)]';
    $label = 'block text-sm font-medium text-[var(--color-navy-700)] mb-1.5';
    $input =
        'w-full px-3.5 py-2.5 text-base sm:text-sm rounded-xl border border-[var(--color-slate-200)] bg-white text-[var(--color-navy-900)] focus:outline-none focus:border-[var(--color-blue-500)] focus:ring-2 focus:ring-[var(--color-blue-500)]/15 transition-colors';
    $fokus =
        'focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[color:var(--color-blue-500)]';
@endphp

<x-layouts.app title="Konfigurasi">

    <div class="mb-6">
        <h1 class="font-[var(--font-heading)] text-2xl font-bold text-[var(--color-navy-900)] tracking-tight">
            Konfigurasi</h1>
        <p class="text-sm text-[var(--color-slate-500)] mt-0.5">Koneksi Fonnte, jadwal pengingat, dan template pesan
            WhatsApp.</p>
    </div>

    <div class="max-w-6xl" x-data="{
        submitting: false,
        dirty: false,
        showToken: false,
        pane: 'edit',
        jam: @js($jamKirim),
        aktif: @js($pengingatAktif),
        activeTemplate: @js($waTemplates->keys()->first()),
        templates: @js($waTemplates->mapWithKeys(fn ($c) => [$c->key => old("values.{$c->key}", $c->value)])),
        original: @js($waTemplates->mapWithKeys(fn ($c) => [$c->key => (string) $c->value])),
        meta: @js($meta),
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
            jenis: 'surat masuk',
            tanggal_surat: '12 Mar 2019',
            kode_klasifikasi: '000.1.1',
            jumlah: '7',
            daftar_surat: '- [Masuk] 045/DPMPTSP/2019\n- [Keluar] 112/KS/2018\n- [Masuk] 078/DPMPTSP/2019\n- [Masuk] 091/DPMPTSP/2019\n- [Keluar] 130/KS/2018\n- ...dan 2 surat lainnya',
            link: 'https://masukpakeko.example/arsip/nasib-akhir?tab=approval',
            alasan: 'Bukan kewenangan bidang ini, silakan disposisikan ke bidang yang sesuai.',
        },
        fonnteStatus: 'loading',
        fonnteInfo: null,

        norm(s) { return (s ?? '').replace(/\r\n?/g, '\n'); },
        isDirty(key) { return this.norm(this.templates[key]) !== this.norm(this.original[key]); },
        resetTemplate(key) { this.templates[key] = this.original[key] ?? ''; },

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

        
        insertAtCursor(before, after, fallback) {
            const key = this.activeTemplate;
            const el = this.$refs['ta_' + key];
            if (!el) return;
            const start = el.selectionStart, end = el.selectionEnd;
            const text = this.templates[key] ?? '';
            const selected = text.slice(start, end) || fallback;
            this.templates[key] = text.slice(0, start) + before + selected + after + text.slice(end);
            this.dirty = true;
            this.$nextTick(() => {
                el.focus();
                el.selectionStart = start + before.length;
                el.selectionEnd = start + before.length + selected.length;
            });
        },
        wrapSelection(marker) { this.insertAtCursor(marker, marker, 'teks'); },
        insertPlaceholder(name) { this.insertAtCursor('{' + name + '}', '', ''); },

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
    }" x-init="checkFonnte()"
        @beforeunload.window="if (dirty && !submitting) { $event.preventDefault(); $event.returnValue = ''; }">

        <form method="POST" action="{{ route('configuration.update') }}" @submit="submitting = true"
            @pageshow.window="submitting = false" @input="dirty = true" @change="dirty = true">
            @csrf
            @method('PUT')

            <div class="grid gap-6 lg:grid-cols-2 lg:items-start">

                {{-- Koneksi Fonnte --}}
                <section class="{{ $panel }} p-5" aria-labelledby="judul-fonnte">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <h2 id="judul-fonnte" class="{{ $judul }}">Koneksi WhatsApp</h2>
                            <p class="text-sm text-[var(--color-slate-500)] mt-0.5">Layanan Fonnte yang mengirim semua
                                notifikasi.</p>
                        </div>

                        <div class="flex items-center gap-1 shrink-0" aria-live="polite">
                            <template x-if="fonnteStatus === 'loading'">
                                <span
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-[var(--color-slate-100)] text-[var(--color-slate-500)]">
                                    <span
                                        class="w-1.5 h-1.5 rounded-full bg-[var(--color-slate-400)] animate-pulse"></span>
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
                            <button type="button" @click="checkFonnte()" aria-label="Cek ulang status koneksi"
                                title="Cek ulang"
                                class="w-9 h-9 -mr-1 inline-flex items-center justify-center rounded-lg text-[var(--color-slate-500)] hover:text-[var(--color-blue-600)] hover:bg-[var(--color-blue-50)] transition-colors cursor-pointer {{ $fokus }}">
                                <x-icon name="arrow-path" class="w-4 h-4" />
                            </button>
                        </div>
                    </div>

                    <p x-show="fonnteStatus === 'disconnected'" x-cloak
                        class="text-sm text-[var(--color-warning)] mt-3">Device belum tersambung. Scan ulang QR di
                        dashboard Fonnte.</p>
                    <p x-show="fonnteStatus === 'error'" x-cloak class="text-sm text-[var(--color-danger)] mt-3"
                        x-text="fonnteInfo?.reason ?? 'Gagal memuat status.'"></p>

                    <dl x-show="fonnteStatus === 'connected' && fonnteInfo" x-cloak
                        class="grid grid-cols-2 gap-x-4 gap-y-3 mt-4 p-4 rounded-xl bg-[var(--color-slate-50)] text-sm">
                        <div class="min-w-0">
                            <dt class="text-xs text-[var(--color-slate-500)]">Nomor device</dt>
                            <dd class="font-medium text-[var(--color-navy-900)] mt-0.5 truncate"
                                x-text="fonnteInfo?.device ?? '-'"></dd>
                        </div>
                        <div class="min-w-0">
                            <dt class="text-xs text-[var(--color-slate-500)]">Paket</dt>
                            <dd class="font-medium text-[var(--color-navy-900)] mt-0.5 truncate"
                                x-text="fonnteInfo?.package ?? '-'"></dd>
                        </div>
                        <div class="min-w-0">
                            <dt class="text-xs text-[var(--color-slate-500)]">Sisa kuota</dt>
                            <dd class="font-medium text-[var(--color-navy-900)] mt-0.5 truncate"
                                x-text="fonnteInfo?.quota ?? '-'"></dd>
                        </div>
                        <div class="min-w-0">
                            <dt class="text-xs text-[var(--color-slate-500)]">Berlaku sampai</dt>
                            <dd class="font-medium text-[var(--color-navy-900)] mt-0.5 truncate"
                                x-text="fonnteInfo?.expired ?? '-'"></dd>
                        </div>
                    </dl>

                    <div class="mt-5">
                        <label for="fonnte_token" class="{{ $label }}">API token</label>
                        <div class="relative">
                            <input id="fonnte_token" :type="showToken ? 'text' : 'password'"
                                name="values[fonnte_token]" autocomplete="off" spellcheck="false"
                                value="{{ old('values.fonnte_token', $fonnte['fonnte_token']->value ?? '') }}"
                                class="{{ $input }} pr-12 font-mono" />
                            <button type="button" @click="showToken = !showToken" :aria-pressed="showToken"
                                :aria-label="showToken ? 'Sembunyikan token' : 'Tampilkan token'"
                                class="absolute inset-y-0 right-0 w-11 flex items-center justify-center rounded-r-xl text-[var(--color-slate-500)] hover:text-[var(--color-navy-900)] transition-colors cursor-pointer focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-[color:var(--color-blue-500)]">
                                <svg x-show="!showToken" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"
                                    aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>
                                <svg x-show="showToken" style="display: none;" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                    class="w-5 h-5" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                                </svg>
                            </button>
                        </div>
                        <p class="text-xs text-[var(--color-slate-500)] mt-1.5">Nomor pengirim mengikuti device yang
                            terpasang di token ini.</p>
                    </div>
                </section>

                {{-- Jadwal pengingat --}}
                <section class="{{ $panel }} p-5" aria-labelledby="judul-jadwal">
                    <h2 id="judul-jadwal" class="{{ $judul }}">Jadwal pengingat</h2>
                    <p class="text-sm text-[var(--color-slate-500)] mt-0.5">Pengingat harian untuk disposisi yang lewat
                        batas waktu dan belum selesai.</p>

                    <div class="mt-5 space-y-5">
                        <label class="flex items-center justify-between gap-4 cursor-pointer select-none">
                            <span class="text-sm font-medium text-[var(--color-navy-700)]">Aktifkan pengingat
                                harian</span>
                            <input type="hidden" name="values[pengingat_aktif]" value="0">
                            <input type="checkbox" name="values[pengingat_aktif]" value="1" x-model="aktif"
                                class="toggle toggle-primary" />
                        </label>

                        <div>
                            <label for="pengingat_jam_kirim" class="{{ $label }}">Jam kirim</label>
                            <input id="pengingat_jam_kirim" type="time" name="values[pengingat_jam_kirim]"
                                x-model="jam" class="{{ $input }} sm:max-w-[10rem]" />
                        </div>

                        <p class="text-sm text-[var(--color-slate-500)]"
                            x-text="aktif ? 'Pengingat dikirim setiap hari pukul ' + jam + '.' : 'Pengingat dimatikan, tidak ada pesan yang dikirim.'">
                        </p>
                    </div>
                </section>
            </div>

            {{-- Template pesan WhatsApp --}}
            @if ($waTemplates->isNotEmpty())
                <section class="{{ $panel }} mt-6 overflow-hidden" aria-labelledby="judul-template">
                    <div class="px-5 pt-5 pb-4">
                        <h2 id="judul-template" class="{{ $judul }}">Template pesan WhatsApp</h2>
                        <p class="text-sm text-[var(--color-slate-500)] mt-0.5">Pilih jenis notifikasi, ubah isinya,
                            lalu cek pratinjau dengan data contoh.</p>
                    </div>

                    <div class="lg:grid lg:grid-cols-[15rem_minmax(0,1fr)] border-t border-[color:var(--color-slate-100)]">

                        {{-- Layar sempit: pilih template lewat dropdown --}}
                        <div class="px-5 pt-4 lg:hidden">
                            <label for="pilih-template" class="{{ $label }}">Template</label>
                            <select id="pilih-template" x-model="activeTemplate" @change.stop @input.stop
                                class="{{ $input }}">
                                @foreach ($groups as $judulGrup => $keys)
                                    <optgroup label="{{ $judulGrup }}">
                                        @foreach ($keys as $key)
                                            <option value="{{ $key }}">{{ $meta[$key]['label'] }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>

                        {{-- Layar lebar: daftar template per kelompok --}}
                        <nav aria-label="Daftar template"
                            class="hidden lg:block border-r border-[color:var(--color-slate-100)] p-3 space-y-4">
                            @foreach ($groups as $judulGrup => $keys)
                                <div>
                                    <p class="px-2.5 pb-1 text-xs font-semibold text-[var(--color-slate-500)]">
                                        {{ $judulGrup }}</p>
                                    <ul class="space-y-0.5">
                                        @foreach ($keys as $key)
                                            <li>
                                                <button type="button" @click="activeTemplate = '{{ $key }}'"
                                                    :aria-current="activeTemplate === '{{ $key }}' ? 'true' : null"
                                                    :class="activeTemplate === '{{ $key }}' ?
                                                        'bg-[var(--color-blue-50)] text-[var(--color-blue-600)] font-semibold' :
                                                        'text-[var(--color-slate-600)] hover:bg-[var(--color-slate-100)] font-medium'"
                                                    class="w-full flex items-center justify-between gap-2 px-2.5 min-h-9 rounded-lg text-sm text-left transition-colors cursor-pointer {{ $fokus }}">
                                                    <span>{{ $meta[$key]['label'] }}</span>
                                                    <span x-show="isDirty('{{ $key }}')" style="display: none;"
                                                        title="Belum disimpan"
                                                        class="w-1.5 h-1.5 rounded-full bg-[var(--color-warning)] shrink-0"></span>
                                                </button>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endforeach
                        </nav>

                        {{-- Editor + pratinjau --}}
                        <div class="min-w-0 p-5 space-y-4">
                            <div>
                                <h3 class="text-base font-semibold text-[var(--color-navy-900)]"
                                    x-text="meta[activeTemplate]?.label"></h3>
                                <p class="text-sm text-[var(--color-slate-500)] mt-0.5"
                                    x-text="meta[activeTemplate]?.when"></p>
                            </div>

                            {{-- Di bawah 2xl, editor dan pratinjau bergantian --}}
                            <div class="2xl:hidden inline-flex p-1 rounded-xl bg-[var(--color-slate-100)]"
                                role="group" aria-label="Tampilan">
                                <button type="button" @click="pane = 'edit'" :aria-pressed="pane === 'edit'"
                                    :class="pane === 'edit' ?
                                        'bg-white text-[var(--color-navy-900)] shadow-sm' :
                                        'text-[var(--color-slate-500)]'"
                                    class="min-h-9 px-4 rounded-lg text-sm font-medium transition-colors cursor-pointer {{ $fokus }}">Edit</button>
                                <button type="button" @click="pane = 'preview'" :aria-pressed="pane === 'preview'"
                                    :class="pane === 'preview' ?
                                        'bg-white text-[var(--color-navy-900)] shadow-sm' :
                                        'text-[var(--color-slate-500)]'"
                                    class="min-h-9 px-4 rounded-lg text-sm font-medium transition-colors cursor-pointer {{ $fokus }}">Pratinjau</button>
                            </div>

                            <div class="2xl:grid 2xl:grid-cols-2 2xl:gap-6">

                                {{-- Editor --}}
                                <div :class="pane === 'edit' ? '' : 'hidden 2xl:block'">
                                    <div class="flex flex-wrap items-center gap-1 mb-2">
                                        <button type="button" @click="wrapSelection('*')" title="Tebal (*teks*)"
                                            aria-label="Tebal"
                                            class="w-9 h-9 rounded-lg text-sm font-bold text-[var(--color-slate-600)] hover:bg-[var(--color-slate-100)] transition-colors cursor-pointer {{ $fokus }}">B</button>
                                        <button type="button" @click="wrapSelection('_')" title="Miring (_teks_)"
                                            aria-label="Miring"
                                            class="w-9 h-9 rounded-lg text-sm italic font-semibold text-[var(--color-slate-600)] hover:bg-[var(--color-slate-100)] transition-colors cursor-pointer {{ $fokus }}">I</button>
                                        <button type="button" @click="wrapSelection('~')" title="Coret (~teks~)"
                                            aria-label="Coret"
                                            class="w-9 h-9 rounded-lg text-sm line-through font-semibold text-[var(--color-slate-600)] hover:bg-[var(--color-slate-100)] transition-colors cursor-pointer {{ $fokus }}">S</button>
                                    </div>

                                    @foreach ($meta as $key => $m)
                                        <div x-show="activeTemplate === '{{ $key }}'" x-cloak>
                                            <label for="ta-{{ $key }}" class="sr-only">Isi template
                                                {{ $m['label'] }}</label>
                                            <textarea id="ta-{{ $key }}" name="values[{{ $key }}]"
                                                x-ref="ta_{{ $key }}" x-model="templates['{{ $key }}']"
                                                rows="11" spellcheck="false"
                                                class="{{ $input }} font-mono leading-relaxed resize-y"></textarea>
                                        </div>
                                    @endforeach

                                    <div class="mt-3" x-show="meta[activeTemplate]?.placeholders?.length" x-cloak>
                                        <p class="text-xs text-[var(--color-slate-500)] mb-1.5">Klik untuk menyisipkan
                                            data ke posisi kursor</p>
                                        <div class="flex flex-wrap gap-1.5">
                                            <template x-for="p in (meta[activeTemplate]?.placeholders ?? [])"
                                                :key="p">
                                                <button type="button" @click="insertPlaceholder(p)"
                                                    :title="'Contoh: ' + (sampleData[p] ?? '')"
                                                    x-text="'{' + p + '}'"
                                                    class="inline-flex items-center min-h-9 px-2.5 rounded-lg text-xs font-mono bg-[var(--color-slate-100)] text-[var(--color-slate-600)] hover:bg-[var(--color-blue-50)] hover:text-[var(--color-blue-600)] transition-colors cursor-pointer {{ $fokus }}"></button>
                                            </template>
                                        </div>
                                    </div>

                                    <div class="mt-3 flex flex-wrap items-center justify-between gap-2">
                                        <p class="text-xs text-[var(--color-slate-500)]">Format WhatsApp:
                                            <span class="font-mono">*tebal*</span>, <span
                                                class="font-mono">_miring_</span>, <span
                                                class="font-mono">~coret~</span>. Enter membuat baris baru.</p>
                                        <button type="button" x-show="isDirty(activeTemplate)"
                                            style="display: none;" @click="resetTemplate(activeTemplate)"
                                            class="min-h-9 px-3 rounded-lg text-sm font-medium text-[var(--color-blue-600)] hover:bg-[var(--color-blue-50)] transition-colors cursor-pointer {{ $fokus }}">Kembalikan
                                            ke versi tersimpan</button>
                                    </div>
                                </div>

                                {{-- Pratinjau WhatsApp (warna sengaja meniru aplikasi WhatsApp) --}}
                                <div :class="pane === 'preview' ? '' : 'hidden 2xl:block'" class="2xl:mt-0">
                                    <p class="text-xs text-[var(--color-slate-500)] mb-2">Pratinjau dengan data contoh
                                    </p>
                                    <div
                                        class="w-full max-w-[320px] mx-auto 2xl:mx-0 rounded-2xl overflow-hidden border border-[var(--color-slate-200)] shadow-sm">
                                        <div class="bg-[#075E54] px-3 py-2.5 flex items-center gap-2.5">
                                            <div
                                                class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center text-white text-xs font-semibold shrink-0">
                                                DI</div>
                                            <div class="min-w-0">
                                                <p class="text-white text-[13px] font-medium leading-tight truncate">
                                                    DPMPTSP Info</p>
                                                <p class="text-white/70 text-[10px] leading-tight">online</p>
                                            </div>
                                        </div>
                                        <div class="bg-[#E5DDD5] px-3 py-4 min-h-[260px] flex items-start">
                                            <div
                                                class="bg-[#D9FDD3] rounded-lg rounded-tl-none px-2.5 py-2 max-w-[85%] shadow-sm">
                                                <p class="text-[13px] leading-snug text-[#111B21] break-words"
                                                    style="font-family: system-ui, -apple-system, sans-serif;"
                                                    x-html="formatWA(templates[activeTemplate])"></p>
                                                <div class="flex items-center justify-end gap-1 mt-1">
                                                    <span class="text-[10px] text-[#667781]">09:41</span>
                                                    <svg class="w-3.5 h-3.5 text-[#53BDEB]" viewBox="0 0 16 15"
                                                        fill="currentColor" aria-hidden="true">
                                                        <path
                                                            d="M15.01 3.316l-.478-.372a.365.365 0 00-.51.063L8.61 9.98a.32.32 0 01-.484.033l-.358-.325a.319.319 0 00-.484.032l-.378.483a.418.418 0 00.036.541l1.32 1.266c.143.14.361.125.484-.033l6.272-8.048a.366.366 0 00-.064-.512zm-4.1 0l-.478-.372a.365.365 0 00-.51.063l-5.415 6.973a.32.32 0 01-.484.033L2.706 8.71a.319.319 0 00-.484.032l-.378.483a.418.418 0 00.036.541l2.643 2.534c.143.14.361.125.484-.033l6.272-8.048a.366.366 0 00-.064-.512l-.964-.75-.001-.001z" />
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            @endif

            {{-- Bar simpan: melayang di bawah supaya tetap terjangkau di halaman panjang --}}
            <div
                class="sticky bottom-3 sm:bottom-4 z-20 mt-6 px-4 py-3 flex items-center justify-between gap-3 rounded-2xl bg-white/90 backdrop-blur-md border border-[var(--color-slate-200)] shadow-[0_8px_24px_-8px_rgba(15,23,42,0.18)]">
                <p class="text-sm text-[var(--color-slate-500)] min-w-0"
                    x-text="dirty ? 'Ada perubahan yang belum disimpan' : 'Semua perubahan sudah tersimpan'"></p>
                <button type="submit" :disabled="submitting"
                    class="shrink-0 inline-flex items-center justify-center gap-2 min-h-11 sm:min-h-10 px-5 rounded-xl text-sm font-semibold text-white bg-gradient-to-br from-[var(--color-blue-500)] to-[var(--color-blue-600)] shadow-[0_4px_10px_-2px_rgba(37,99,235,0.35)] hover:shadow-[0_8px_16px_-2px_rgba(37,99,235,0.45)] transition-all disabled:opacity-60 disabled:cursor-not-allowed cursor-pointer {{ $fokus }}">
                    <span x-show="submitting" style="display: none;"
                        class="loading loading-spinner loading-xs"></span>
                    <span x-text="submitting ? 'Menyimpan...' : 'Simpan konfigurasi'">Simpan konfigurasi</span>
                </button>
            </div>
        </form>
    </div>

</x-layouts.app>