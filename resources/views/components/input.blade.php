@props([
    'label',
    'name',
    'type' => 'text',
    'placeholder' => '',
    'required' => false,
    'value' => '',
    'icon' => null,
    'togglePassword' => false,
])

<div>
    <label class="block text-[13px] font-medium text-[var(--color-navy-700)] mb-1.5">{{ $label }}</label>
    <div class="relative">
        @if ($icon)
            @switch($icon)
                @case('users')
                    <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none w-4 h-4" viewBox="0 0 24 24"
                        fill="none" stroke="var(--color-slate-400)" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2" />
                        <circle cx="12" cy="7" r="4" />
                    </svg>
                @break

                @case('lock')
                    <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none w-4 h-4" viewBox="0 0 24 24"
                        fill="none" stroke="var(--color-slate-400)" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                        <path d="M7 11V7a5 5 0 0110 0v4" />
                    </svg>
                @break

                @case('inbox')
                    <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none w-4 h-4" viewBox="0 0 24 24"
                        fill="none" stroke="var(--color-slate-400)" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path
                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                @break

                @case('search')
                    <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none w-4 h-4" viewBox="0 0 24 24"
                        fill="none" stroke="var(--color-slate-400)" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8" />
                        <path d="M21 21l-4.35-4.35" />
                    </svg>
                @break

                @case('mail')
                    <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none w-4 h-4" viewBox="0 0 24 24"
                        fill="none" stroke="var(--color-slate-400)" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                        <polyline points="22,6 12,13 2,6" />
                    </svg>
                @break

                @default
                    <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none w-4 h-4" viewBox="0 0 24 24"
                        fill="none" stroke="var(--color-slate-400)" stroke-width="2">
                        <circle cx="12" cy="12" r="10" />
                    </svg>
            @endswitch
        @endif

        <input @if ($togglePassword) id="input-{{ $name }}" @endif type="{{ $type }}"
            name="{{ $name }}" value="{{ old($name, $value) }}" placeholder="{{ $placeholder }}"
            {{ $required ? 'required' : '' }}
            {{ $attributes->merge([
                'class' =>
                    'w-full ' .
                    ($icon ? 'pl-10 ' : 'pl-4 ') .
                    ($togglePassword ? 'pr-11 ' : 'pr-4 ') .
                    'py-2.5 border-[1.5px] border-[var(--color-slate-200)] rounded-[10px] text-sm bg-white text-[var(--color-navy-900)] placeholder:text-[var(--color-slate-400)] outline-none transition-all focus:border-[var(--color-blue-500)] focus:ring-[3px] focus:ring-[var(--color-blue-500)]/10 focus:shadow-md focus:shadow-[var(--color-blue-500)]/10' .
                    ($errors->has($name) ? ' border-[var(--color-danger)]' : ''),
            ]) }} />

        @if ($togglePassword)
            <button type="button" onclick="togglePw_{{ $name }}()"
                class="absolute right-3 top-1/2 -translate-y-1/2 p-0.5 cursor-pointer">
                <svg id="eye-open-{{ $name }}" width="18" height="18" viewBox="0 0 24 24" fill="none"
                    stroke="var(--color-slate-400)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                    <circle cx="12" cy="12" r="3" />
                </svg>
                <svg id="eye-closed-{{ $name }}" width="18" height="18" viewBox="0 0 24 24"
                    fill="none" stroke="var(--color-slate-400)" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round" class="hidden">
                    <path
                        d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24" />
                    <line x1="1" y1="1" x2="23" y2="23" />
                </svg>
            </button>
            <script>
                function togglePw_{{ $name }}() {
                    const pw = document.getElementById('input-{{ $name }}');
                    const open = document.getElementById('eye-open-{{ $name }}');
                    const closed = document.getElementById('eye-closed-{{ $name }}');
                    if (pw.type === 'password') {
                        pw.type = 'text';
                        open.classList.add('hidden');
                        closed.classList.remove('hidden');
                    } else {
                        pw.type = 'password';
                        open.classList.remove('hidden');
                        closed.classList.add('hidden');
                    }
                }
            </script>
        @endif
    </div>
    @error($name)
        <p class="text-xs text-[var(--color-danger)] mt-1">{{ $message }}</p>
    @enderror
</div>
