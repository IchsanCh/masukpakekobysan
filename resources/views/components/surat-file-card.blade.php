{{--
    Kartu upload file surat (dipakai surat masuk & surat keluar).
    Tidak punya state sendiri: memakai state dari x-data form induk
    (fileName, fileSize, hasExistingFile, previewUrl, previewType, fileSizeError, fileMissing, dragging, maxMB, handleFile, onInputChange, onDrop, clearFile).
--}}
@props(['isEdit' => false, 'existingFileUrl' => null, 'existingFileExt' => null, 'required' => false, 'optional' => false])

<div x-ref="fileCard"
    class="bg-white rounded-2xl border border-[var(--color-slate-200)]/70
           shadow-[0_1px_2px_rgba(15,23,42,0.04),0_12px_28px_-10px_rgba(15,23,42,0.10)] p-4 sm:p-5 h-fit lg:sticky lg:top-5 scroll-mt-4">
    <div class="flex items-baseline justify-between gap-2 mb-3">
        <h3 class="font-[var(--font-heading)] text-sm font-bold text-[var(--color-navy-900)] uppercase tracking-wider">
            File Surat{{ $required ? ' *' : '' }}
        </h3>
        @if ($optional)
            <span class="text-xs text-[var(--color-slate-500)]">Opsional</span>
        @endif
    </div>

    {{-- Preview: file yang baru dipilih. Iframe PDF disembunyikan di mobile (browser HP jarang bisa menampilkannya). --}}
    <template x-if="previewUrl && previewType === 'pdf'">
        <iframe :src="previewUrl"
            class="hidden sm:block w-full h-64 rounded-xl border border-[var(--color-slate-200)]"></iframe>
    </template>
    <template x-if="previewUrl && previewType === 'image'">
        <img :src="previewUrl" alt="Pratinjau file surat"
            class="w-full max-h-48 sm:max-h-64 object-contain rounded-xl border border-[var(--color-slate-200)] bg-[var(--color-slate-50)]" />
    </template>

    {{-- Preview: file lama (mode edit, belum diganti) --}}
    @if ($existingFileUrl)
        <div x-show="hasExistingFile" x-cloak>
            @if (in_array($existingFileExt, ['jpg', 'jpeg', 'png']))
                <img src="{{ $existingFileUrl }}" alt="File surat saat ini"
                    class="w-full max-h-48 sm:max-h-64 object-contain rounded-xl border border-[var(--color-slate-200)] bg-[var(--color-slate-50)]" />
            @else
                <iframe src="{{ $existingFileUrl }}"
                    class="hidden sm:block w-full h-64 rounded-xl border border-[var(--color-slate-200)]"></iframe>
            @endif
            <a href="{{ $existingFileUrl }}" target="_blank" rel="noopener"
                class="inline-flex items-center gap-1.5 mt-1 min-h-11 sm:min-h-0 sm:mt-2 text-sm sm:text-xs font-medium text-[var(--color-blue-600)] hover:underline">
                <x-icon name="eye" class="w-4 h-4 sm:w-3.5 sm:h-3.5" /> Buka file di tab baru
            </a>
        </div>
    @endif

    {{-- Area pilih file (tampil selama belum ada file) --}}
    <button type="button" x-show="!previewUrl && !hasExistingFile" @click="$refs.fileInput.click()"
        @dragover.prevent="dragging = true" @dragleave.prevent="dragging = false" @drop.prevent="onDrop($event)"
        :class="dragging ? 'border-[var(--color-blue-500)] bg-[var(--color-blue-50)]/50' :
            (fileMissing ? 'border-[var(--color-danger)]' :
                'border-[var(--color-slate-300)] hover:border-[var(--color-blue-400)] hover:bg-[var(--color-slate-50)]')"
        class="w-full cursor-pointer rounded-xl border-2 border-dashed px-4 py-8 sm:py-6 text-center transition-colors
               focus:outline-none focus-visible:ring-2 focus-visible:ring-[var(--color-blue-500)]/30">
        <x-icon name="arrow-up-tray" class="w-7 h-7 sm:w-6 sm:h-6 mx-auto text-[var(--color-slate-400)] mb-2" />
        <span class="block text-base sm:text-sm font-medium text-[var(--color-slate-600)]">
            <span class="sm:hidden">Ketuk untuk ambil foto atau pilih file</span>
            <span class="hidden sm:inline">Klik atau seret file ke sini</span>
        </span>
        <span class="block text-xs text-[var(--color-slate-500)] mt-1">PDF, JPG, atau PNG, maks. <span
                x-text="maxMB"></span> MB</span>
    </button>

    <input type="file" name="file_surat" x-ref="fileInput" @change="onInputChange($event)"
        accept="application/pdf,image/jpeg,image/png,.pdf,.jpg,.jpeg,.png" class="hidden" />

    {{-- Nama file + aksi, tampil setelah ada file --}}
    <div x-show="previewUrl || hasExistingFile" x-cloak
        class="flex items-center gap-2 mt-3 pl-3 pr-1 py-1 rounded-xl bg-[var(--color-slate-50)]">
        <x-icon name="document-text" class="w-5 h-5 text-[var(--color-slate-400)] shrink-0" />
        <div class="min-w-0 flex-1 py-1.5">
            <p class="text-sm text-[var(--color-slate-700)] truncate" x-text="fileName"></p>
            <p x-show="fileSize" x-cloak class="text-xs text-[var(--color-slate-500)]" x-text="fileSize"></p>
        </div>
        <div class="flex items-center shrink-0">
            <button type="button" @click="$refs.fileInput.click()"
                class="min-h-11 px-3 rounded-lg text-sm font-medium text-[var(--color-blue-600)] hover:bg-[var(--color-blue-50)] transition-colors">Ganti</button>
            <template x-if="previewUrl">
                <button type="button" @click="clearFile()" aria-label="Hapus file yang dipilih"
                    class="size-11 inline-flex items-center justify-center rounded-lg text-[var(--color-slate-500)] hover:text-[var(--color-danger)] transition-colors">
                    <x-icon name="x-mark" class="w-4 h-4" />
                </button>
            </template>
        </div>
    </div>

    <p x-show="fileSizeError" x-cloak class="text-xs text-[var(--color-danger)] mt-2" role="alert">
        Ukuran file melebihi <span x-text="maxMB"></span> MB. Pilih file lain.
    </p>
    <p x-show="fileMissing && !previewUrl" x-cloak class="text-xs text-[var(--color-danger)] mt-2" role="alert">
        File surat wajib diunggah sebelum disimpan.
    </p>

    @error('file_surat')
        <p class="text-xs text-[var(--color-danger)] mt-2">{{ $message }}</p>
    @enderror

    @if ($isEdit)
        <p class="text-xs text-[var(--color-slate-500)] mt-2">Kosongkan jika ingin tetap memakai file yang sudah ada.</p>
    @endif
</div>