@php
    $isEdit = isset($suratKeluar);
    $existingFileUrl = $isEdit && $suratKeluar->file_surat ? asset('storage/' . $suratKeluar->file_surat) : null;
    $existingFileExt =
        $isEdit && $suratKeluar->file_surat ? strtolower(pathinfo($suratKeluar->file_surat, PATHINFO_EXTENSION)) : null;
    $existingFileName = $isEdit && $suratKeluar->file_surat ? basename($suratKeluar->file_surat) : null;

    // Ukuran font 16px di mobile supaya iOS tidak zoom otomatis saat input difokuskan
    $label = 'block text-sm font-medium text-[var(--color-navy-700)] mb-1.5';
    $input =
        'w-full px-3.5 py-3 sm:py-2.5 min-h-12 sm:min-h-0 text-base sm:text-sm rounded-xl border border-[var(--color-slate-200)] focus:outline-none focus:border-[var(--color-blue-500)] focus:ring-2 focus:ring-[var(--color-blue-500)]/10 transition-all';
    $card =
        'bg-white rounded-2xl border border-[var(--color-slate-200)]/70 shadow-[0_1px_2px_rgba(15,23,42,0.04),0_12px_28px_-10px_rgba(15,23,42,0.10)] p-4 sm:p-5 space-y-4';
    $h3 = 'font-[var(--font-heading)] text-sm font-bold text-[var(--color-navy-900)] uppercase tracking-wider';
    $err = 'text-xs text-[var(--color-danger)] mt-1';
@endphp

<form method="POST" action="{{ $isEdit ? route('surat-keluar.update', $suratKeluar) : route('surat-keluar.store') }}"
    enctype="multipart/form-data" x-data="{
        fileName: @js($existingFileName),
        fileSize: null,
        hasExistingFile: @js((bool) $existingFileUrl),
        previewUrl: null,
        previewType: null,
        fileSizeError: false,
        fileMissing: false,
        dragging: false,
        submitting: false,
        typing: false,
        maxMB: 10,
        textFieldSelector: 'input:not([type=radio]):not([type=checkbox]):not([type=file]):not([type=date]):not([type=hidden]), textarea',
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

        formatSize(bytes) {
            return bytes >= 1048576 ? (bytes / 1048576).toFixed(1) + ' MB' : Math.max(1, Math.round(bytes / 1024)) + ' KB';
        },
        handleFile(file) {
            if (!file) return;
            this.fileSizeError = file.size > this.maxMB * 1024 * 1024;
            this.fileName = file.name;
            this.fileSize = this.formatSize(file.size);
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
            this.fileSize = null;
            this.previewUrl = null;
            this.previewType = null;
            this.fileSizeError = false;
            this.hasExistingFile = @js((bool) $existingFileUrl);
        },
        onFocusIn(e) { this.typing = e.target.matches(this.textFieldSelector); },
        onFocusOut(e) {
            if (!e.relatedTarget || !e.relatedTarget.matches(this.textFieldSelector)) this.typing = false;
        }
    }" @retensi-changed="onRetensiChanged($event.detail.id)" @submit="submitting = true"
    @focusin="onFocusIn($event)" @focusout="onFocusOut($event)">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-5">

        {{-- Kiri: retensi dulu (nomor surat disarankan dari klasifikasi), baru data surat --}}
        <div class="lg:col-span-2 space-y-4 sm:space-y-5">

            <div class="{{ $card }}">
                <h3 class="{{ $h3 }}">Retensi &amp; Arsip</h3>
                <x-retensi-select :retensis="$retensis"
                    :selected="old('referensi_retensi_id', $suratKeluar->referensi_retensi_id ?? '')" />
            </div>

            <div class="{{ $card }}">
                <h3 class="{{ $h3 }}">Data Surat</h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="nomor_surat" class="{{ $label }}">Nomor Surat *</label>
                        <input type="text" id="nomor_surat" name="nomor_surat" x-ref="nomorSuratInput"
                            @input="nomorSuratTouched = true" autocomplete="off" autocapitalize="off"
                            enterkeyhint="next"
                            value="{{ old('nomor_surat', $suratKeluar->nomor_surat ?? ($suggestedNomor ?? '')) }}"
                            required class="{{ $input }} font-mono" placeholder="Contoh: 000.1.1/2026/0001" />
                        @if (!$isEdit)
                            <p class="text-xs text-[var(--color-slate-500)] mt-1">Terisi otomatis setelah retensi
                                dipilih (kode klasifikasi/tahun/no. urut), tetap bisa diedit.</p>
                        @endif
                        @error('nomor_surat')
                            <p class="{{ $err }}">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="tanggal_surat" class="{{ $label }}">Tanggal Surat *</label>
                        <input type="date" id="tanggal_surat" name="tanggal_surat" x-ref="tanggalInput"
                            @change="refreshNomorSuggestion()"
                            value="{{ old('tanggal_surat', isset($suratKeluar) ? $suratKeluar->tanggal_surat->format('Y-m-d') : now()->format('Y-m-d')) }}"
                            required class="{{ $input }}" />
                        @error('tanggal_surat')
                            <p class="{{ $err }}">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="kepada" class="{{ $label }}">Kepada *</label>
                        <input type="text" id="kepada" name="kepada" autocomplete="off" autocapitalize="words"
                            enterkeyhint="next" value="{{ old('kepada', $suratKeluar->kepada ?? '') }}" required
                            class="{{ $input }}" placeholder="Instansi / perorangan tujuan" />
                        @error('kepada')
                            <p class="{{ $err }}">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="pengelola" class="{{ $label }}">Pengelola</label>
                        <input type="text" id="pengelola" name="pengelola" autocomplete="off" autocapitalize="words"
                            enterkeyhint="next" value="{{ old('pengelola', $suratKeluar->pengelola ?? '') }}"
                            class="{{ $input }}" placeholder="Bidang/petugas yang mengelola" />
                        @error('pengelola')
                            <p class="{{ $err }}">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="isi_ringkasan" class="{{ $label }}">Isi Ringkasan *</label>
                    <textarea id="isi_ringkasan" name="isi_ringkasan" rows="3" required
                        class="{{ $input }} resize-none" placeholder="Ringkasan isi/perihal surat">{{ old('isi_ringkasan', $suratKeluar->isi_ringkasan ?? '') }}</textarea>
                    @error('isi_ringkasan')
                        <p class="{{ $err }}">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="lampiran" class="{{ $label }}">Lampiran</label>
                    <input type="text" id="lampiran" name="lampiran" autocomplete="off" enterkeyhint="done"
                        value="{{ old('lampiran', $suratKeluar->lampiran ?? '') }}" class="{{ $input }}"
                        placeholder="Contoh: 3 lembar" />
                    <p class="text-xs text-[var(--color-slate-500)] mt-1">Keterangan lampiran fisik yang menyertai
                        surat (bukan file upload).</p>
                    @error('lampiran')
                        <p class="{{ $err }}">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Kanan: file surat --}}
        <x-surat-file-card :is-edit="$isEdit" :existing-file-url="$existingFileUrl" :existing-file-ext="$existingFileExt"
            optional />
    </div>

    <x-surat-form-actions :cancel-url="route('surat-keluar.index')" :label="$isEdit ? 'Simpan Perubahan' : 'Simpan Surat'" />
</form>