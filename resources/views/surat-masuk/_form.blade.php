@php
    $isEdit = isset($suratMasuk);
    $existingFileUrl = $isEdit && $suratMasuk->file_surat ? asset('storage/' . $suratMasuk->file_surat) : null;
    $existingFileExt =
        $isEdit && $suratMasuk->file_surat ? strtolower(pathinfo($suratMasuk->file_surat, PATHINFO_EXTENSION)) : null;
    $existingFileName = $isEdit && $suratMasuk->file_surat ? basename($suratMasuk->file_surat) : null;

    // Ukuran font 16px di mobile supaya iOS tidak zoom otomatis saat input difokuskan
    $label = 'block text-sm font-medium text-[var(--color-navy-700)] mb-1.5';
    $input =
        'w-full px-3.5 py-3 sm:py-2.5 min-h-12 sm:min-h-0 text-base sm:text-sm rounded-xl border border-[var(--color-slate-200)] focus:outline-none focus:border-[var(--color-blue-500)] focus:ring-2 focus:ring-[var(--color-blue-500)]/10 transition-all';
    $card =
        'bg-white rounded-2xl border border-[var(--color-slate-200)]/70 shadow-[0_1px_2px_rgba(15,23,42,0.04),0_12px_28px_-10px_rgba(15,23,42,0.10)] p-4 sm:p-5 space-y-4';
    $h3 = 'font-[var(--font-heading)] text-sm font-bold text-[var(--color-navy-900)] uppercase tracking-wider';
    $err = 'text-xs text-[var(--color-danger)] mt-1';
    $currentSifat = old('sifat_surat', $suratMasuk->sifat_surat ?? 'biasa');
    $sifatOptions = [
        'biasa' => 'Biasa',
        'segera' => 'Segera',
        'sangat_segera' => 'Sangat Segera',
        'rahasia' => 'Rahasia',
    ];
@endphp

<form method="POST" action="{{ $isEdit ? route('surat-masuk.update', $suratMasuk) : route('surat-masuk.store') }}"
    enctype="multipart/form-data" x-data="{
        fileName: @js($existingFileName),
        fileSize: null,
        hasExistingFile: @js((bool) $existingFileUrl),
        previewUrl: null,
        previewType: null,
        fileSizeError: false,
        fileMissing: false,
        needsFile: @js(!$isEdit),
        dragging: false,
        submitting: false,
        typing: false,
        maxMB: 10,
        textFieldSelector: 'input:not([type=radio]):not([type=checkbox]):not([type=file]):not([type=date]):not([type=hidden]), textarea',

        formatSize(bytes) {
            return bytes >= 1048576 ? (bytes / 1048576).toFixed(1) + ' MB' : Math.max(1, Math.round(bytes / 1024)) + ' KB';
        },
        handleFile(file) {
            if (!file) return;
            this.fileSizeError = file.size > this.maxMB * 1024 * 1024;
            this.fileMissing = false;
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
        // File wajib saat tambah surat. Dicek di sini supaya user tidak kehilangan isian
        // (file tidak ikut kembali saat server menolak).
        onSubmit(e) {
            if (this.needsFile && !this.previewUrl) {
                e.preventDefault();
                this.fileMissing = true;
                this.$refs.fileCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
                return;
            }
            this.submitting = true;
        },
        onFocusIn(e) { this.typing = e.target.matches(this.textFieldSelector); },
        onFocusOut(e) {
            if (!e.relatedTarget || !e.relatedTarget.matches(this.textFieldSelector)) this.typing = false;
        }
    }" @submit="onSubmit($event)" @focusin="onFocusIn($event)" @focusout="onFocusOut($event)">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-5">

        {{-- Kiri: data surat + retensi --}}
        <div class="lg:col-span-2 space-y-4 sm:space-y-5">

            <div class="{{ $card }}">
                <h3 class="{{ $h3 }}">Data Surat</h3>

                <div>
                    <label for="nomor_surat" class="{{ $label }}">Nomor Surat *</label>
                    <input type="text" id="nomor_surat" name="nomor_surat" autocomplete="off" enterkeyhint="next"
                        value="{{ old('nomor_surat', $suratMasuk->nomor_surat ?? '') }}" required
                        class="{{ $input }}" />
                    @error('nomor_surat')
                        <p class="{{ $err }}">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="tanggal_surat" class="{{ $label }}">Tanggal Surat *</label>
                        <input type="date" id="tanggal_surat" name="tanggal_surat"
                            value="{{ old('tanggal_surat', isset($suratMasuk) ? $suratMasuk->tanggal_surat->format('Y-m-d') : '') }}"
                            required class="{{ $input }}" />
                        @error('tanggal_surat')
                            <p class="{{ $err }}">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="tanggal_diterima" class="{{ $label }}">Tanggal Diterima *</label>
                        <input type="date" id="tanggal_diterima" name="tanggal_diterima"
                            value="{{ old('tanggal_diterima', isset($suratMasuk) ? $suratMasuk->tanggal_diterima->format('Y-m-d') : now()->format('Y-m-d')) }}"
                            required class="{{ $input }}" />
                        @error('tanggal_diterima')
                            <p class="{{ $err }}">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="pengirim" class="{{ $label }}">Pengirim *</label>
                    <input type="text" id="pengirim" name="pengirim" autocomplete="off" autocapitalize="words"
                        enterkeyhint="next" value="{{ old('pengirim', $suratMasuk->pengirim ?? '') }}" required
                        class="{{ $input }}" placeholder="Instansi / perorangan pengirim" />
                    @error('pengirim')
                        <p class="{{ $err }}">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Sifat surat: 4 pilihan terlihat sekaligus, satu ketukan --}}
                <fieldset>
                    <legend class="{{ $label }}">Sifat Surat *</legend>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                        @foreach ($sifatOptions as $value => $text)
                            <label class="relative cursor-pointer">
                                <input type="radio" name="sifat_surat" value="{{ $value }}" required
                                    class="peer sr-only" @checked($currentSifat === $value) />
                                <span
                                    class="flex items-center justify-center min-h-12 sm:min-h-11 px-3 rounded-xl border border-[var(--color-slate-200)] text-sm font-medium text-[var(--color-slate-600)]
                                           transition-colors hover:bg-[var(--color-slate-50)]
                                           peer-checked:border-[var(--color-blue-500)] peer-checked:bg-[var(--color-blue-50)] peer-checked:text-[var(--color-blue-600)]
                                           peer-focus-visible:ring-2 peer-focus-visible:ring-[var(--color-blue-500)]/30">{{ $text }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('sifat_surat')
                        <p class="{{ $err }}">{{ $message }}</p>
                    @enderror
                </fieldset>

                <div>
                    <label for="isi_ringkasan" class="{{ $label }}">Isi Ringkasan *</label>
                    <textarea id="isi_ringkasan" name="isi_ringkasan" rows="3" required
                        class="{{ $input }} resize-none" placeholder="Ringkasan isi/perihal surat">{{ old('isi_ringkasan', $suratMasuk->isi_ringkasan ?? '') }}</textarea>
                    @error('isi_ringkasan')
                        <p class="{{ $err }}">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="{{ $card }}">
                <h3 class="{{ $h3 }}">Retensi &amp; Arsip</h3>
                <x-retensi-select :retensis="$retensis"
                    :selected="old('referensi_retensi_id', $suratMasuk->referensi_retensi_id ?? '')" />
            </div>
        </div>

        {{-- Kanan: file surat --}}
        <x-surat-file-card :is-edit="$isEdit" :existing-file-url="$existingFileUrl" :existing-file-ext="$existingFileExt"
            :required="!$isEdit" />
    </div>

    <x-surat-form-actions :cancel-url="route('surat-masuk.index')" :label="$isEdit ? 'Simpan Perubahan' : 'Simpan Surat'" />
</form>