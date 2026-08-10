{{--
    Partial form target disposisi. Include dengan parameter:
    - $actionUrl   : URL tujuan submit
    - $units       : Collection Unit aktif
    - $usersForPersonal : Collection User aktif
    - $submitLabel : teks tombol submit (opsional, default "Kirim Disposisi")
--}}
@php $submitLabel = $submitLabel ?? 'Kirim Disposisi'; @endphp

<form method="POST" action="{{ $actionUrl }}" x-data="{ tipe: 'unit', submitting: false }" @submit="submitting = true" class="space-y-3">
    @csrf

    <div class="flex gap-2">
        <label class="flex-1 cursor-pointer">
            <input type="radio" name="tipe_tujuan" value="unit" x-model="tipe" class="peer sr-only" checked>
            <div
                class="text-center px-3 py-2 rounded-xl border text-sm font-medium transition-colors
                        border-[var(--color-slate-200)] text-[var(--color-slate-500)]
                        peer-checked:border-[var(--color-blue-500)] peer-checked:bg-[var(--color-blue-50)] peer-checked:text-[var(--color-blue-600)]">
                Ke Unit
            </div>
        </label>
        <label class="flex-1 cursor-pointer">
            <input type="radio" name="tipe_tujuan" value="personal" x-model="tipe" class="peer sr-only">
            <div
                class="text-center px-3 py-2 rounded-xl border text-sm font-medium transition-colors
                        border-[var(--color-slate-200)] text-[var(--color-slate-500)]
                        peer-checked:border-[var(--color-blue-500)] peer-checked:bg-[var(--color-blue-50)] peer-checked:text-[var(--color-blue-600)]">
                Ke Personal
            </div>
        </label>
    </div>

    <div x-show="tipe === 'unit'">
        <label class="block text-sm font-medium text-[var(--color-navy-700)] mb-1.5">Unit Tujuan</label>
        <select name="unit_id" :required="tipe === 'unit'"
            class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-[var(--color-slate-200)] focus:outline-none focus:border-[var(--color-blue-500)] focus:ring-2 focus:ring-[var(--color-blue-500)]/10 transition-all">
            <option value="">Pilih unit...</option>
            @foreach ($units as $unit)
                <option value="{{ $unit->id }}">{{ $unit->nama_unit }}</option>
            @endforeach
        </select>
        @error('unit_id')
            <p class="text-xs text-[var(--color-danger)] mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div x-show="tipe === 'personal'" x-cloak>
        <label class="block text-sm font-medium text-[var(--color-navy-700)] mb-1.5">Personal Tujuan</label>
        <select name="kepada_user_id" :required="tipe === 'personal'"
            class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-[var(--color-slate-200)] focus:outline-none focus:border-[var(--color-blue-500)] focus:ring-2 focus:ring-[var(--color-blue-500)]/10 transition-all">
            <option value="">Pilih orang...</option>
            @foreach ($usersForPersonal as $u)
                <option value="{{ $u->id }}">
                    {{ $u->name }}{{ $u->jabatan_struktural ? ' — ' . $u->jabatan_struktural : '' }}</option>
            @endforeach
        </select>
        @error('kepada_user_id')
            <p class="text-xs text-[var(--color-danger)] mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-[var(--color-navy-700)] mb-1.5">Instruksi *</label>
        <textarea name="instruksi" rows="2" required
            class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-[var(--color-slate-200)] focus:outline-none focus:border-[var(--color-blue-500)] focus:ring-2 focus:ring-[var(--color-blue-500)]/10 transition-all resize-none"
            placeholder="Contoh: Mohon ditindaklanjuti sesuai ketentuan"></textarea>
        @error('instruksi')
            <p class="text-xs text-[var(--color-danger)] mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-[var(--color-navy-700)] mb-1.5">Batas Waktu</label>
        <input type="date" name="batas_waktu"
            class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-[var(--color-slate-200)] focus:outline-none focus:border-[var(--color-blue-500)] focus:ring-2 focus:ring-[var(--color-blue-500)]/10 transition-all" />
        @error('batas_waktu')
            <p class="text-xs text-[var(--color-danger)] mt-1">{{ $message }}</p>
        @enderror
    </div>

    <button type="submit" :disabled="submitting"
        class="w-full px-4 py-2.5 rounded-xl text-sm font-semibold text-white bg-gradient-to-br from-[var(--color-blue-500)] to-[var(--color-blue-600)]
               shadow-[0_4px_10px_-2px_rgba(37,99,235,0.35)] hover:shadow-[0_8px_16px_-2px_rgba(37,99,235,0.45)] transition-all
               disabled:opacity-60 disabled:cursor-not-allowed inline-flex items-center justify-center gap-2">
        <span x-show="submitting" class="loading loading-spinner loading-xs"></span>
        <span x-text="submitting ? 'Mengirim...' : @js($submitLabel)"></span>
    </button>
</form>
