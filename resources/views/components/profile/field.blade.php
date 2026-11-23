{{--
    Satu field form di halaman Profile: label, input, pesan error, hint.
    type="password" otomatis dapat tombol lihat/sembunyikan.
    Atribut lain (required, maxlength, autocomplete, x-model, dst.) diteruskan ke <input>.
    $slot dirender di bawah input (untuk bantuan live seperti syarat password).
--}}
@props(['id', 'name', 'label', 'bag', 'type' => 'text', 'value' => null, 'hint' => null])

@php
    $input =
        'w-full px-3.5 py-2.5 text-base sm:text-sm rounded-xl border border-[var(--color-slate-200)] bg-white text-[var(--color-navy-900)] placeholder:text-[var(--color-slate-400)] focus:outline-none focus:border-[var(--color-blue-500)] focus:ring-2 focus:ring-[var(--color-blue-500)]/15 transition-colors';
    $isPassword = $type === 'password';
@endphp

<div>
    <label for="{{ $id }}"
        class="block text-sm font-medium text-[var(--color-navy-700)] mb-1.5">{{ $label }}</label>

    @if ($isPassword)
        <div class="relative" x-data="{ show: false }">
            <input id="{{ $id }}" name="{{ $name }}" type="password" :type="show ? 'text' : 'password'"
                @error($name, $bag) aria-invalid="true" aria-describedby="{{ $id }}-error" @enderror
                {{ $attributes->merge(['class' => $input . ' pr-12']) }} />
            <button type="button" @click="show = !show" :aria-pressed="show"
                :aria-label="show ? 'Sembunyikan password' : 'Tampilkan password'"
                class="absolute inset-y-0 right-0 w-11 flex items-center justify-center rounded-r-xl text-[var(--color-slate-500)] hover:text-[var(--color-navy-900)] transition-colors cursor-pointer focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-[color:var(--color-blue-500)]">
                <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke-width="1.5" stroke="currentColor" class="w-5 h-5" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                </svg>
                <svg x-show="show" style="display: none;" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                </svg>
            </button>
        </div>
    @else
        <input id="{{ $id }}" name="{{ $name }}" type="{{ $type }}" value="{{ $value }}"
            @error($name, $bag) aria-invalid="true" aria-describedby="{{ $id }}-error" @enderror
            {{ $attributes->merge(['class' => $input]) }} />
    @endif

    @error($name, $bag)
        <p id="{{ $id }}-error" class="text-xs text-[var(--color-danger)] mt-1.5">{{ $message }}</p>
    @enderror

    @if ($hint)
        <p class="text-xs text-[var(--color-slate-500)] mt-1.5">{{ $hint }}</p>
    @endif

    {{ $slot }}
</div>