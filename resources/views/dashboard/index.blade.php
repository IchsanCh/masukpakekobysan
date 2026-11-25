<x-layouts.app title="Dashboard">
    {{-- Entry JS khusus dashboard; harus lebih dulu dari app.js supaya komponen Alpine-nya terdaftar sebelum Alpine.start() --}}
    @push('head')
        @vite('resources/js/dashboard.js')
    @endpush

    @php
        $user = auth()->user();

        // Sapaan mengikuti jam server (Asia/Jakarta)
        $jam = (int) now()->format('G');
        $sapaan = match (true) {
            $jam >= 4 && $jam < 11 => 'Selamat pagi',
            $jam >= 11 && $jam < 15 => 'Selamat siang',
            $jam >= 15 && $jam < 18 => 'Selamat sore',
            default => 'Selamat malam',
        };
        $namaDepan = explode(' ', trim($user->name))[0];
        $tanggalHariIni = now()->locale('id')->isoFormat('dddd, D MMMM YYYY');

        $periodeTeks = $periodeOptions[$periode]['teks'];
        $granularitas = $perBulan ? 'bulan' : 'hari';

        // URL filter (dipakai filter desktop & tile status): pertahankan filter lain, buang nilai kosong
        $url = fn(array $ubah) => route(
            'dashboard',
            array_filter(
                array_merge(['periode' => $periode, 'status' => $status], $ubah),
                fn($v) => $v !== null && $v !== '',
            ),
        );
        $jumlahFilterAktif = ($periode !== '30h' ? 1 : 0) + ($status ? 1 : 0);
        $ringkasFilter = ucfirst($periodeTeks) . ', ' . ($status ? mb_strtolower($statusOptions[$status]) : 'semua status');

        $card = 'bg-white rounded-2xl border border-[var(--color-slate-200)]/70 shadow-[0_1px_2px_rgba(15,23,42,0.04),0_12px_28px_-10px_rgba(15,23,42,0.10)]';
        $heading = 'font-[var(--font-heading)] text-base font-semibold text-[var(--color-navy-900)]';
        $aktifKelas = 'bg-[var(--color-navy-700)] text-white border-transparent';
        $pasifKelas = 'bg-white border-[var(--color-slate-200)] text-[var(--color-slate-700)]';

        // Warna status mengikuti tema daisyUI, sama dengan badge di tabel
        $warna = ['menunggu' => 'bg-warning', 'diterima' => 'bg-info', 'diproses' => 'bg-primary', 'selesai' => 'bg-success'];
        $badgeKelas = ['menunggu' => 'badge-warning', 'diterima' => 'badge-info', 'diproses' => 'badge-primary', 'selesai' => 'badge-success'];

        $totalSeri = array_sum(array_map('array_sum', $chart['series']));
        $totalDisposisiSeri = array_sum($chart['series']['disposisi']);
        $totalStatus = array_sum($chart['status']);
        $fmt = fn($n) => number_format($n, 0, ',', '.');
    @endphp

    <div class="max-w-7xl mx-auto space-y-5 sm:space-y-6">

        {{-- Sapaan --}}
        <div>
            <h1
                class="font-[var(--font-heading)] text-xl sm:text-2xl font-bold text-[var(--color-navy-900)] tracking-tight">
                {{ $sapaan }}, {{ $namaDepan }}
            </h1>
            <p class="text-sm text-[var(--color-slate-500)] mt-0.5">{{ $tanggalHariIni }}</p>
            <p class="text-sm text-[var(--color-slate-700)] mt-1.5">
                @if ($disposisiPending > 0)
                    <span class="font-semibold text-[var(--color-navy-900)]">{{ $fmt($disposisiPending) }}</span>
                    disposisi menunggu tindak lanjut.
                @else
                    Tidak ada disposisi yang menunggu.
                @endif
            </p>
        </div>

        {{-- Filter, mobile: satu bar ringkas yang membuka bottom sheet (tanpa geser-geser) --}}
        <div class="sm:hidden flex items-stretch gap-2"
            x-data="{
                p: '{{ $periode }}',
                s: '{{ $status ?? '' }}',
                buka() {
                    this.p = '{{ $periode }}';
                    this.s = '{{ $status ?? '' }}';
                    this.$refs.sheet.showModal();
                },
            }">
            <button type="button" @click="buka()" aria-haspopup="dialog"
                class="flex-1 min-w-0 flex items-center gap-3 min-h-12 px-3.5 rounded-xl border border-[var(--color-slate-200)] bg-white text-left transition-colors active:bg-[var(--color-slate-50)]">
                <svg class="w-5 h-5 shrink-0 text-[var(--color-slate-500)]" fill="none" viewBox="0 0 24 24"
                    stroke-width="1.75" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" />
                </svg>
                <span class="min-w-0 flex-1">
                    <span class="block text-[11px] leading-none text-[var(--color-slate-500)]">Filter</span>
                    <span
                        class="mt-1 block truncate text-sm font-medium text-[var(--color-navy-900)]">{{ $ringkasFilter }}</span>
                </span>
                @if ($jumlahFilterAktif > 0)
                    <span
                        class="shrink-0 inline-flex items-center justify-center size-5 rounded-full bg-[var(--color-navy-700)] text-white text-xs font-semibold">{{ $jumlahFilterAktif }}</span>
                @endif
                <svg class="w-4 h-4 shrink-0 text-[var(--color-slate-400)]" fill="none" viewBox="0 0 24 24"
                    stroke-width="2" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                </svg>
            </button>

            @if ($jumlahFilterAktif > 0)
                <a href="{{ route('dashboard') }}" aria-label="Reset filter"
                    class="shrink-0 inline-flex items-center justify-center size-12 rounded-xl border border-[var(--color-slate-200)] bg-white text-[var(--color-slate-500)]">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                        aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </a>
            @endif

            <dialog x-ref="sheet" class="modal modal-bottom" @click.self="$refs.sheet.close()"
                aria-labelledby="judul-filter">
                <form method="GET" action="{{ route('dashboard') }}"
                    class="modal-box w-full max-w-none p-0 flex flex-col max-h-[88dvh] rounded-t-2xl rounded-b-none">
                    <input type="hidden" name="periode" :value="p">
                    <input type="hidden" name="status" :value="s" :disabled="!s">

                    <div class="flex items-center justify-between pl-5 pr-2 pt-2">
                        <h2 id="judul-filter" class="{{ $heading }}">Filter dashboard</h2>
                        <button type="button" @click="$refs.sheet.close()" aria-label="Tutup"
                            class="inline-flex items-center justify-center size-12 rounded-xl text-[var(--color-slate-500)]">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="flex-1 overflow-y-auto px-5 pb-4 space-y-5">
                        <fieldset>
                            <legend class="text-sm font-medium text-[var(--color-slate-500)] mb-2">Periode</legend>
                            <div class="grid grid-cols-2 gap-2">
                                @foreach ($periodeOptions as $key => $opt)
                                    <button type="button" @click="p = '{{ $key }}'"
                                        :aria-pressed="p === '{{ $key }}'"
                                        class="flex items-center justify-between gap-2 min-h-12 px-3.5 rounded-xl border text-sm font-medium transition-colors duration-150"
                                        :class="p === '{{ $key }}' ? '{{ $aktifKelas }}' : '{{ $pasifKelas }}'">
                                        <span>{{ ucfirst($opt['teks']) }}</span>
                                        <svg x-show="p === '{{ $key }}'" class="w-4 h-4 shrink-0" fill="none"
                                            viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"
                                            aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m4.5 12.75 6 6 9-13.5" />
                                        </svg>
                                    </button>
                                @endforeach
                            </div>
                        </fieldset>

                        <fieldset>
                            <legend class="text-sm font-medium text-[var(--color-slate-500)] mb-2">Status disposisi
                            </legend>
                            <div class="grid grid-cols-2 gap-2">
                                @foreach (['' => 'Semua status'] + $statusOptions as $key => $label)
                                    <button type="button" @click="s = '{{ $key }}'"
                                        :aria-pressed="s === '{{ $key }}'"
                                        class="flex items-center justify-between gap-2 min-h-12 px-3.5 rounded-xl border text-sm font-medium transition-colors duration-150"
                                        :class="s === '{{ $key }}' ? '{{ $aktifKelas }}' : '{{ $pasifKelas }}'">
                                        <span>{{ $label }}</span>
                                        <svg x-show="s === '{{ $key }}'" class="w-4 h-4 shrink-0" fill="none"
                                            viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"
                                            aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m4.5 12.75 6 6 9-13.5" />
                                        </svg>
                                    </button>
                                @endforeach
                            </div>
                        </fieldset>
                    </div>

                    <div
                        class="flex gap-3 px-5 pt-3 pb-[max(1rem,env(safe-area-inset-bottom))] border-t border-[var(--color-slate-100)]">
                        <a href="{{ route('dashboard') }}"
                            class="inline-flex items-center justify-center min-h-12 px-5 rounded-xl border border-[var(--color-slate-200)] text-sm font-medium text-[var(--color-slate-700)]">Reset</a>
                        <button type="submit"
                            class="flex-1 min-h-12 rounded-xl bg-[var(--color-navy-700)] text-white text-sm font-semibold">Terapkan</button>
                    </div>
                </form>
            </dialog>
        </div>

        {{-- Filter, tablet/desktop: muat satu layar, tanpa scroll (pindah baris kalau sempit) --}}
        <div class="hidden sm:flex flex-wrap items-center gap-x-4 gap-y-3">
            <div class="inline-grid grid-flow-col auto-cols-auto gap-1 p-1 rounded-xl bg-[var(--color-slate-100)]"
                role="group" aria-label="Periode">
                @foreach ($periodeOptions as $key => $opt)
                    <a href="{{ $url(['periode' => $key]) }}" @if ($periode === $key) aria-current="true" @endif
                        class="inline-flex items-center min-h-9 px-3 rounded-lg text-sm font-medium whitespace-nowrap transition-colors duration-200
                              {{ $periode === $key
                                  ? 'bg-white text-[var(--color-navy-900)] shadow-sm'
                                  : 'text-[var(--color-slate-500)] hover:text-[var(--color-navy-900)]' }}">
                        {{ $opt['chip'] }}
                    </a>
                @endforeach
            </div>

            <div class="flex flex-wrap items-center gap-2" role="group" aria-label="Status disposisi">
                <span class="text-sm text-[var(--color-slate-500)]">Disposisi</span>
                @foreach (['' => 'Semua'] + $statusOptions as $key => $label)
                    @php $aktif = ($status ?? '') === (string) $key; @endphp
                    <a href="{{ $url(['status' => $key === '' ? null : $key]) }}"
                        @if ($aktif) aria-current="true" @endif
                        class="inline-flex items-center min-h-9 px-3 rounded-full text-sm font-medium border whitespace-nowrap transition-colors duration-200
                              {{ $aktif
                                  ? 'bg-[var(--color-navy-700)] text-white border-transparent'
                                  : 'bg-white border-[var(--color-slate-200)] text-[var(--color-slate-600)] hover:border-[var(--color-slate-300)] hover:text-[var(--color-navy-900)]' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            @if ($jumlahFilterAktif > 0)
                <a href="{{ route('dashboard') }}"
                    class="text-sm font-medium text-[var(--color-slate-500)] hover:text-[var(--color-navy-900)] transition-colors">Reset
                    filter</a>
            @endif
        </div>

        {{-- Ringkasan --}}
        <div class="{{ $card }} grid grid-cols-2 lg:grid-cols-4 gap-px bg-[var(--color-slate-100)] overflow-hidden">
            @php
                // [ikon, nama, nilai, keterangan pendek (mobile), keterangan lengkap (sm ke atas)]
                $tiles = [
                    ['inbox', 'Surat masuk', $suratMasuk, '+' . $fmt($suratMasukPeriode) . ' baru', $fmt($suratMasukPeriode) . ' baru dalam ' . $periodeTeks],
                    ['paper-airplane', 'Surat keluar', $suratKeluar, '+' . $fmt($suratKeluarPeriode) . ' baru', $fmt($suratKeluarPeriode) . ' baru dalam ' . $periodeTeks],
                    ['arrow-right-circle', 'Disposisi menunggu', $disposisiPending, 'Belum ditindaklanjuti', 'Belum ditindaklanjuti'],
                    ['check-circle', 'Disposisi selesai', $disposisiSelesai, '+' . $fmt($disposisiSelesaiPeriode) . ' selesai', $fmt($disposisiSelesaiPeriode) . ' selesai dalam ' . $periodeTeks],
                ];
            @endphp
            @foreach ($tiles as [$ikon, $nama, $nilai, $pendek, $lengkap])
                <div class="bg-white p-4 sm:p-5 min-w-0">
                    <div class="flex items-center gap-2 text-[13px] sm:text-sm text-[var(--color-slate-500)]">
                        <x-icon name="{{ $ikon }}" class="hidden sm:block w-4 h-4 shrink-0" />
                        <span class="truncate">{{ $nama }}</span>
                    </div>
                    <p class="mt-1.5 sm:mt-2 font-[var(--font-heading)] text-2xl sm:text-3xl font-bold tabular-nums text-[var(--color-navy-900)]"
                        x-data="countUp({{ (int) $nilai }})" x-text="shown.toLocaleString('id-ID')">
                        {{ $fmt($nilai) }}</p>
                    <p class="mt-1 text-xs text-[var(--color-slate-500)]">
                        <span class="sm:hidden">{{ $pendek }}</span>
                        <span class="hidden sm:inline">{{ $lengkap }}</span>
                    </p>
                </div>
            @endforeach
        </div>

        {{-- Grafik --}}
        <div class="grid gap-5 sm:gap-6 lg:grid-cols-3" x-data="dashboardCharts(@js($chart))">

            {{-- Tren --}}
            <section class="{{ $card }} lg:col-span-2 p-4 sm:p-5 min-w-0" aria-labelledby="judul-tren">
                <h2 id="judul-tren" class="{{ $heading }}">Aktivitas surat dan disposisi</h2>
                <p class="text-sm text-[var(--color-slate-500)] mt-0.5">
                    {{ ucfirst($periodeTeks) }}, per {{ $granularitas }}.
                    @if ($status)
                        Disposisi hanya yang berstatus {{ mb_strtolower($statusOptions[$status]) }}.
                    @endif
                </p>

                @if ($totalSeri > 0)
                    {{-- Legenda sekaligus total per deret; ketuk untuk tampil/sembunyi --}}
                    <div class="mt-3 grid grid-cols-3 gap-2" role="group"
                        aria-label="Tampilkan atau sembunyikan deret">
                        @foreach ([['masuk', 'Surat masuk', $suratMasukPeriode], ['keluar', 'Surat keluar', $suratKeluarPeriode], ['disposisi', 'Disposisi', $totalDisposisiSeri]] as [$k, $l, $n])
                            <button type="button" @click="toggle('{{ $k }}')"
                                :aria-pressed="visible.{{ $k }}"
                                class="min-w-0 text-left rounded-xl border p-2.5 sm:p-3 transition-all duration-200"
                                :class="visible.{{ $k }} ?
                                    'border-[var(--color-slate-200)] bg-white' :
                                    'border-dashed border-[var(--color-slate-200)] bg-[var(--color-slate-50)] opacity-60'">
                                <span class="block w-5 h-0 border-t-2 mb-1.5"
                                    style="{{ $k === 'disposisi' ? 'border-top-style: dashed;' : '' }}"
                                    :style="{ borderColor: palette.series.{{ $k }} }"></span>
                                <span
                                    class="block truncate text-xs text-[var(--color-slate-500)]">{{ $l }}</span>
                                <span
                                    class="block mt-0.5 text-lg font-semibold tabular-nums text-[var(--color-navy-900)]">{{ $fmt($n) }}</span>
                            </button>
                        @endforeach
                    </div>

                    <div class="relative h-52 sm:h-72 mt-4">
                        <canvas x-ref="trend" role="img"
                            aria-label="Grafik garis jumlah surat masuk, surat keluar, dan disposisi per {{ $granularitas }}, {{ $periodeTeks }}. Data lengkap ada pada tabel tersembunyi di bawahnya."></canvas>
                    </div>
                    <table class="sr-only">
                        <caption>Jumlah per {{ $granularitas }}, {{ $periodeTeks }}</caption>
                        <thead>
                            <tr>
                                <th scope="col">{{ ucfirst($granularitas) }}</th>
                                <th scope="col">Surat masuk</th>
                                <th scope="col">Surat keluar</th>
                                <th scope="col">Disposisi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($chart['labels'] as $i => $lbl)
                                <tr>
                                    <th scope="row">{{ $lbl }}</th>
                                    <td>{{ $chart['series']['masuk'][$i] }}</td>
                                    <td>{{ $chart['series']['keluar'][$i] }}</td>
                                    <td>{{ $chart['series']['disposisi'][$i] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="h-48 sm:h-64 flex flex-col items-center justify-center text-center gap-1.5">
                        <p class="text-sm font-medium text-[var(--color-navy-900)]">Belum ada aktivitas dalam
                            {{ $periodeTeks }}</p>
                        <p class="text-sm text-[var(--color-slate-500)] max-w-xs">
                            @if ($status)
                                Coba pilih status lain atau perpanjang periode.
                            @else
                                Perpanjang periode untuk melihat tren yang lebih panjang.
                            @endif
                        </p>
                        @if ($periode !== 'tahun_ini')
                            <a href="{{ $url(['periode' => 'tahun_ini']) }}"
                                class="mt-1 inline-flex items-center min-h-11 text-sm font-semibold text-[var(--color-blue-600)] hover:underline">Lihat
                                tahun ini</a>
                        @endif
                    </div>
                @endif
            </section>

            {{-- Status disposisi: bar proporsi + 4 kotak status (ketuk untuk menyaring) --}}
            <section class="{{ $card }} p-4 sm:p-5 min-w-0" aria-labelledby="judul-status">
                <h2 id="judul-status" class="{{ $heading }}">Status disposisi</h2>
                <p class="text-sm text-[var(--color-slate-500)] mt-0.5">{{ ucfirst($periodeTeks) }}. Ketuk status untuk
                    menyaring.</p>

                <div class="mt-3 flex items-baseline gap-2">
                    <span
                        class="font-[var(--font-heading)] text-3xl font-bold tabular-nums text-[var(--color-navy-900)]"
                        x-data="countUp({{ (int) $totalStatus }})"
                        x-text="shown.toLocaleString('id-ID')">{{ $fmt($totalStatus) }}</span>
                    <span class="text-sm text-[var(--color-slate-500)]">disposisi</span>
                </div>

                <div class="mt-3 h-2.5 w-full rounded-full bg-[var(--color-slate-100)] overflow-hidden" role="img"
                    aria-label="Proporsi status disposisi: @foreach ($statusOptions as $key => $label){{ $label }} {{ $chart['status'][$key] }}{{ $loop->last ? '' : ', ' }}@endforeach">
                    @if ($totalStatus > 0)
                        <div class="flex h-full gap-0.5 origin-left transition-transform duration-700 ease-out motion-reduce:transition-none"
                            x-data="{ on: false }" x-init="$nextTick(() => on = true)"
                            :class="on ? 'scale-x-100' : 'scale-x-0'">
                            @foreach ($statusOptions as $key => $label)
                                @if ($chart['status'][$key] > 0)
                                    <span class="{{ $warna[$key] }} h-full basis-0 min-w-1"
                                        style="flex-grow: {{ $chart['status'][$key] }}"></span>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>

                <ul class="mt-4 grid grid-cols-2 gap-2">
                    @foreach ($statusOptions as $key => $label)
                        @php
                            $jumlah = $chart['status'][$key];
                            $persen = $totalStatus > 0 ? round(($jumlah / $totalStatus) * 100) : 0;
                            $dipilih = $status === $key;
                        @endphp
                        <li class="min-w-0">
                            <a href="{{ $url(['status' => $dipilih ? null : $key]) }}"
                                @if ($dipilih) aria-current="true" @endif
                                class="block min-h-14 rounded-xl border p-3 transition-colors duration-200
                                      {{ $dipilih
                                          ? 'border-[var(--color-blue-600)] bg-[var(--color-slate-100)]'
                                          : 'border-[var(--color-slate-200)] hover:bg-[var(--color-slate-50)]' }}">
                                <span class="flex items-center gap-2 text-[13px] text-[var(--color-slate-600)]">
                                    <span class="size-2 rounded-full shrink-0 {{ $warna[$key] }}"></span>
                                    <span class="truncate">{{ $label }}</span>
                                </span>
                                <span class="mt-1 flex items-baseline gap-1.5">
                                    <span
                                        class="text-xl font-semibold tabular-nums text-[var(--color-navy-900)]">{{ $fmt($jumlah) }}</span>
                                    <span
                                        class="text-xs tabular-nums text-[var(--color-slate-500)]">{{ $persen }}%</span>
                                </span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </section>
        </div>

        {{-- Disposisi terbaru: daftar kartu di mobile, tabel mulai md --}}
        <section class="{{ $card }} overflow-hidden" aria-labelledby="judul-terbaru">
            <div class="px-4 sm:px-5 py-4 border-b border-[var(--color-slate-100)]">
                <h2 id="judul-terbaru" class="{{ $heading }}">Disposisi terbaru</h2>
                @if ($status)
                    <p class="text-sm text-[var(--color-slate-500)] mt-0.5">Hanya yang berstatus
                        {{ mb_strtolower($statusOptions[$status]) }}.</p>
                @endif
            </div>

            @if ($recentDisposisi->isEmpty())
                <div class="px-5 py-10 text-center">
                    <p class="text-sm font-medium text-[var(--color-navy-900)]">
                        {{ $status ? 'Tidak ada disposisi berstatus ' . mb_strtolower($statusOptions[$status]) : 'Belum ada disposisi' }}
                    </p>
                    @if ($status)
                        <a href="{{ $url(['status' => null]) }}"
                            class="mt-1 inline-flex items-center min-h-11 text-sm font-semibold text-[var(--color-blue-600)] hover:underline">Tampilkan
                            semua status</a>
                    @else
                        <p class="text-sm text-[var(--color-slate-500)] mt-0.5">Disposisi yang ditujukan kepadamu akan
                            muncul di sini.</p>
                    @endif
                </div>
            @else
                {{-- Mobile --}}
                <ul class="md:hidden divide-y divide-[var(--color-slate-100)]">
                    @foreach ($recentDisposisi as $d)
                        <li class="px-4 py-3.5">
                            <div class="flex items-start justify-between gap-3">
                                <p class="min-w-0 text-sm font-semibold text-[var(--color-navy-900)] break-words">
                                    {{ $d->suratMasuk->nomor_surat ?? '-' }}</p>
                                <span class="badge {{ $badgeKelas[$d->status] ?? '' }} badge-sm shrink-0">
                                    {{ $statusOptions[$d->status] ?? ucfirst($d->status) }}</span>
                            </div>
                            @if ($d->instruksi)
                                <p class="mt-1 text-sm text-[var(--color-slate-700)] line-clamp-2">
                                    {{ $d->instruksi }}</p>
                            @endif
                            <p class="mt-1.5 text-xs text-[var(--color-slate-500)]">
                                Dari {{ $d->dariUser->name ?? '-' }} &middot; {{ $d->created_at->format('d/m/Y') }}
                            </p>
                        </li>
                    @endforeach
                </ul>

                {{-- md ke atas --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-[var(--color-slate-50)]/60 border-b border-[var(--color-slate-100)]">
                                @foreach (['Tanggal', 'Nomor Surat', 'Dari', 'Instruksi', 'Status'] as $th)
                                    <th
                                        class="px-5 py-3 text-left text-[11px] font-semibold text-[var(--color-slate-500)] uppercase tracking-wider">
                                        {{ $th }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[var(--color-slate-100)]">
                            @foreach ($recentDisposisi as $d)
                                <tr class="hover:bg-[var(--color-slate-50)]/60 transition-colors">
                                    <td
                                        class="px-5 py-3 text-sm tabular-nums whitespace-nowrap text-[var(--color-slate-700)]">
                                        {{ $d->created_at->format('d/m/Y') }}</td>
                                    <td class="px-5 py-3 text-sm whitespace-nowrap text-[var(--color-navy-900)]">
                                        {{ $d->suratMasuk->nomor_surat ?? '-' }}</td>
                                    <td class="px-5 py-3 text-sm whitespace-nowrap text-[var(--color-slate-700)]">
                                        {{ $d->dariUser->name ?? '-' }}</td>
                                    <td class="px-5 py-3 text-sm max-w-xs truncate text-[var(--color-slate-700)]"
                                        title="{{ $d->instruksi }}">{{ $d->instruksi }}</td>
                                    <td class="px-5 py-3">
                                        <span class="badge {{ $badgeKelas[$d->status] ?? '' }} badge-sm">
                                            {{ $statusOptions[$d->status] ?? ucfirst($d->status) }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    </div>
</x-layouts.app>