@php
    $isEdit = isset($suratKeluar);
    $existingFileUrl = $isEdit && $suratKeluar->file_surat ? asset('storage/' . $suratKeluar->file_surat) : null;
    $existingFileExt =
        $isEdit && $suratKeluar->file_surat ? strtolower(pathinfo($suratKeluar->file_surat, PATHINFO_EXTENSION)) : null;
    $existingFileName = $isEdit && $suratKeluar->file_surat ? basename($suratKeluar->file_surat) : null;
@endphp

<form method="POST" action="{{ $isEdit ? route('surat-keluar.update', $suratKeluar) : route('surat-keluar.store') }}"
    enctype="multipart/form-data" x-data="{
        fileName: @js($existingFileName),
        hasExistingFile: @js((bool) $existingFileUrl),
        previewUrl: null,
        previewType: null,
        fileSizeError: false,
        dragging: false,
        submitting: false,
        maxMB: 10,
        nomorSuratTouched: @js($isEdit || old('nomor_surat') !== null),
        retensiId: @js((string) old('referensi_retensi_id', $suratKeluar->referensi_retensi_id ?? '')),

        onRetensiChanged(id) {
            this.retensiId = id;
            this.refreshNomorSuggestion();
        },

        async refreshNomorSuggestion() {
            // Saran nomor (kode klasifikasi/tahun/no urut) cuma diisi otomatis selama
            // user belum mengetik nomor sendiri, dan tidak pernah dijalankan di halaman edit.
            if (this.nomorSuratTouched) return;
            const tahun = parseInt((this.$refs.tanggalInput.value || '').slice(0, 4), 10);
            if (!this.retensiId || !tahun) {
                this.$refs.nomorSuratInput.value = '';
                return;
            }
            try {
                const params = new URLSearchParams({ tahun: tahun, referensi_retensi_id: this.retensiId });
                const res = await fetch(`{{ route('surat-keluar.next-nomor') }}?${params.toString()}`);
                const data = await res.json();
                if (!this.nomorSuratTouched) this.$refs.nomorSuratInput.value = data.suggestion;
            } catch (e) {
                // Gagal ambil saran, biarkan nilai yang ada — bukan blocking error.
            }
        },

        handleFile(file) {
            if (!file) return;
            this.fileSizeError = file.size > this.maxMB * 1024 * 1024;
            this.fileName = file.name;
            if (this.previewUrl) URL.revokeObjectURL(this.previewUrl);
            this.previewUrl = URL.createObjectURL(file);
            this.previewType = file.type === 'application/pdf' ? 'pdf' : (file.type.startsWith('image/') ? 'image' : null);
            this.hasExistingFile = false;
        },
        onInputChange(e) { this.handleFile(e.target.files[0]); },
        onDrop(e) {
            this.dragging = false;
            const file = e.dataTransfer.files[0];
            if (file) { this.$refs.fileInput.files = e.dataTransfer.files;
                this.handleFile(file); }
        },
        clearFile() {
            this.$refs.fileInput.value = '';
            this.fileName = @js($existingFileName);
            this.previewUrl = null;
            this.previewType = null;
            this.fileSizeError = false;
            this.hasExistingFile = @js((bool) $existingFileUrl);
        }
    }" @retensi-changed="onRetensiChanged($event.detail.id)" @submit="submitting = true">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Left: Data Surat --}}
        <div
            class="lg:col-span-2 bg-white rounded-2xl border border-[var(--color-slate-200)]/70
                    shadow-[0_1px_2px_rgba(15,23,42,0.04),0_12px_28px_-10px_rgba(15,23,42,0.10)] p-5 space-y-4">
            <h3
                class="font-[var(--font-heading)] text-sm font-bold text-[var(--color-navy-900)] uppercase tracking-wider">
                Data Surat</h3>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-[var(--color-navy-700)] mb-1.5">Nomor Surat *</label>
                    <input type="text" name="nomor_surat" x-ref="nomorSuratInput" @input="nomorSuratTouched = true"
                        value="{{ old('nomor_surat', $suratKeluar->nomor_surat ?? ($suggestedNomor ?? '')) }}"
                        required
                        class="w-full px-3.5 py-2.5 text-sm font-mono rounded-xl border border-[var(--color-slate-200)] focus:outline-none focus:border-[var(--color-blue-500)] focus:ring-2 focus:ring-[var(--color-blue-500)]/10 transition-all"
                        placeholder="Contoh: 000.1.1/2026/0001" />
                    @if (!$isEdit)
                        <p class="text-[11px] text-[var(--color-slate-400)] mt-1">Format: kode klasifikasi/tahun/no.
                            urut (reset tiap ganti tahun). Terisi otomatis setelah referensi retensi dipilih — masih
                            bisa diedit bebas.</p>
                    @endif
                    @error('nomor_surat')
                        <p class="text-xs text-[var(--color-danger)] mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-[var(--color-navy-700)] mb-1.5">Tanggal Surat *</label>
                    <input type="date" name="tanggal_surat" x-ref="tanggalInput" @change="refreshNomorSuggestion()"
                        value="{{ old('tanggal_surat', isset($suratKeluar) ? $suratKeluar->tanggal_surat->format('Y-m-d') : now()->format('Y-m-d')) }}"
                        required
                        class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-[var(--color-slate-200)] focus:outline-none focus:border-[var(--color-blue-500)] focus:ring-2 focus:ring-[var(--color-blue-500)]/10 transition-all" />
                    @error('tanggal_surat')
                        <p class="text-xs text-[var(--color-danger)] mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-[var(--color-navy-700)] mb-1.5">Kepada *</label>
                    <input type="text" name="kepada" value="{{ old('kepada', $suratKeluar->kepada ?? '') }}" required
                        class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-[var(--color-slate-200)] focus:outline-none focus:border-[var(--color-blue-500)] focus:ring-2 focus:ring-[var(--color-blue-500)]/10 transition-all"
                        placeholder="Instansi / perorangan tujuan" />
                    @error('kepada')
                        <p class="text-xs text-[var(--color-danger)] mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-[var(--color-navy-700)] mb-1.5">Pengelola</label>
                    <input type="text" name="pengelola" value="{{ old('pengelola', $suratKeluar->pengelola ?? '') }}"
                        class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-[var(--color-slate-200)] focus:outline-none focus:border-[var(--color-blue-500)] focus:ring-2 focus:ring-[var(--color-blue-500)]/10 transition-all"
                        placeholder="Bidang/petugas yang mengelola" />
                    @error('pengelola')
                        <p class="text-xs text-[var(--color-danger)] mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-[var(--color-navy-700)] mb-1.5">Isi Ringkasan *</label>
                <textarea name="isi_ringkasan" rows="3" required
                    class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-[var(--color-slate-200)] focus:outline-none focus:border-[var(--color-blue-500)] focus:ring-2 focus:ring-[var(--color-blue-500)]/10 transition-all resize-none"
                    placeholder="Ringkasan isi/perihal surat">{{ old('isi_ringkasan', $suratKeluar->isi_ringkasan ?? '') }}</textarea>
                @error('isi_ringkasan')
                    <p class="text-xs text-[var(--color-danger)] mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-[var(--color-navy-700)] mb-1.5">Lampiran</label>
                <input type="text" name="lampiran" value="{{ old('lampiran', $suratKeluar->lampiran ?? '') }}"
                    class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-[var(--color-slate-200)] focus:outline-none focus:border-[var(--color-blue-500)] focus:ring-2 focus:ring-[var(--color-blue-500)]/10 transition-all"
                    placeholder="Contoh: 3 lembar" />
                <p class="text-[11px] text-[var(--color-slate-400)] mt-1">Keterangan lampiran fisik yang menyertai
                    surat (bukan file upload).</p>
                @error('lampiran')
                    <p class="text-xs text-[var(--color-danger)] mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="h-px bg-[var(--color-slate-100)]"></div>

            <h3
                class="font-[var(--font-heading)] text-sm font-bold text-[var(--color-navy-900)] uppercase tracking-wider">
                Retensi &amp; Arsip</h3>

            <x-retensi-select :retensis="$retensis"
                :selected="old('referensi_retensi_id', $suratKeluar->referensi_retensi_id ?? '')" />
        </div>

        {{-- Right: File Upload + Preview --}}
        <div
            class="bg-white rounded-2xl border border-[var(--color-slate-200)]/70
                    shadow-[0_1px_2px_rgba(15,23,42,0.04),0_12px_28px_-10px_rgba(15,23,42,0.10)] p-5 h-fit lg:sticky lg:top-5">
            <h3
                class="font-[var(--font-heading)] text-sm font-bold text-[var(--color-navy-900)] uppercase tracking-wider mb-3">
                File Surat
            </h3>

            {{-- Preview: new file just chosen --}}
            <template x-if="previewUrl && previewType === 'pdf'">
                <iframe :src="previewUrl"
                    class="w-full h-64 rounded-xl border border-[var(--color-slate-200)]"></iframe>
            </template>
            <template x-if="previewUrl && previewType === 'image'">
                <img :src="previewUrl"
                    class="w-full max-h-64 object-contain rounded-xl border border-[var(--color-slate-200)] bg-[var(--color-slate-50)]" />
            </template>

            {{-- Preview: existing file already on record (edit mode, not yet replaced) --}}
            @if ($existingFileUrl)
                <div x-show="hasExistingFile" x-cloak>
                    @if (in_array($existingFileExt, ['jpg', 'jpeg', 'png']))
                        <img src="{{ $existingFileUrl }}"
                            class="w-full max-h-64 object-contain rounded-xl border border-[var(--color-slate-200)] bg-[var(--color-slate-50)]" />
                    @else
                        <iframe src="{{ $existingFileUrl }}"
                            class="w-full h-64 rounded-xl border border-[var(--color-slate-200)]"></iframe>
                    @endif
                    <a href="{{ $existingFileUrl }}" target="_blank"
                        class="inline-flex items-center gap-1 mt-2 text-xs font-medium text-[var(--color-blue-600)] hover:underline">
                        <x-icon name="eye" class="w-3.5 h-3.5" /> Buka file di tab baru
                    </a>
                </div>
            @endif

            {{-- Dropzone (shown when nothing to preview yet) --}}
            <div x-show="!previewUrl && !hasExistingFile" @click="$refs.fileInput.click()"
                @dragover.prevent="dragging = true" @dragleave.prevent="dragging = false" @drop.prevent="onDrop($event)"
                :class="dragging ? 'border-[var(--color-blue-500)] bg-[var(--color-blue-50)]/50' :
                    'border-[var(--color-slate-300)] hover:border-[var(--color-blue-400)] hover:bg-[var(--color-slate-50)]'"
                class="cursor-pointer rounded-xl border-2 border-dashed p-6 text-center transition-colors">
                <x-icon name="arrow-up-tray" class="w-6 h-6 mx-auto text-[var(--color-slate-400)] mb-2" />
                <p class="text-sm font-medium text-[var(--color-slate-600)]">Klik atau seret file ke sini</p>
                <p class="text-xs text-[var(--color-slate-400)] mt-1">PDF, JPG, atau PNG — maks. 10 MB (opsional)</p>
            </div>

            <input type="file" name="file_surat" x-ref="fileInput" @change="onInputChange($event)"
                accept=".pdf,.jpg,.jpeg,.png" class="hidden" />

            {{-- File name row + actions, shown once something is loaded --}}
            <div x-show="previewUrl || hasExistingFile" x-cloak
                class="flex items-center justify-between gap-2 mt-3 px-3 py-2 rounded-lg bg-[var(--color-slate-50)]">
                <div class="flex items-center gap-2 min-w-0">
                    <x-icon name="document-text" class="w-4 h-4 text-[var(--color-slate-400)] shrink-0" />
                    <span class="text-xs text-[var(--color-slate-600)] truncate" x-text="fileName"></span>
                </div>
                <div class="flex items-center gap-1 shrink-0">
                    <button type="button" @click="$refs.fileInput.click()"
                        class="text-xs font-medium text-[var(--color-blue-600)] hover:underline">Ganti</button>
                    <template x-if="previewUrl">
                        <button type="button" @click="clearFile()"
                            class="p-1 rounded text-[var(--color-slate-400)] hover:text-[var(--color-danger)]">
                            <x-icon name="x-mark" class="w-3.5 h-3.5" />
                        </button>
                    </template>
                </div>
            </div>

            <p x-show="fileSizeError" x-cloak class="text-xs text-[var(--color-danger)] mt-2">
                Ukuran file melebihi 10 MB. Pilih file lain.
            </p>

            @error('file_surat')
                <p class="text-xs text-[var(--color-danger)] mt-2">{{ $message }}</p>
            @enderror

            @if ($isEdit)
                <p class="text-[11px] text-[var(--color-slate-400)] mt-2">Kosongkan / jangan ganti jika ingin tetap
                    pakai file yang sudah ada.</p>
            @endif
        </div>
    </div>

    <div class="flex gap-2 mt-5 justify-end">
        <a href="{{ route('surat-keluar.index') }}" :class="submitting ? 'pointer-events-none opacity-50' : ''"
            class="px-4 py-2.5 rounded-xl text-sm font-medium text-[var(--color-slate-500)] hover:bg-[var(--color-slate-100)] transition-colors">Batal</a>
        <button type="submit" :disabled="fileSizeError || submitting"
            class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-gradient-to-br from-[var(--color-blue-500)] to-[var(--color-blue-600)]
                   shadow-[0_4px_10px_-2px_rgba(37,99,235,0.35)] hover:shadow-[0_8px_16px_-2px_rgba(37,99,235,0.45)] transition-all
                   disabled:opacity-60 disabled:cursor-not-allowed disabled:shadow-none inline-flex items-center gap-2">
            <span x-show="submitting" class="loading loading-spinner loading-xs"></span>
            <span x-text="submitting ? 'Menyimpan...' : '{{ $isEdit ? 'Simpan Perubahan' : 'Simpan Surat' }}'"></span>
        </button>
    </div>
</form>