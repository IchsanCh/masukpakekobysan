@props(['tone' => 'neutral'])
@php
    $tones = [
        'danger' => ['bg-[var(--color-danger)]/10 text-[var(--color-danger)]', 'bg-[var(--color-danger)]'],
        'warning' => ['bg-[var(--color-warning)]/10 text-[var(--color-warning)]', 'bg-[var(--color-warning)]'],
        'success' => ['bg-[var(--color-success)]/10 text-[var(--color-success)]', 'bg-[var(--color-success)]'],
        'info' => ['bg-[var(--color-blue-500)]/10 text-[var(--color-blue-600)]', 'bg-[var(--color-blue-500)]'],
        'navy' => ['bg-[var(--color-navy-700)]/10 text-[var(--color-navy-700)]', 'bg-[var(--color-navy-700)]'],
        'neutral' => ['bg-[var(--color-slate-100)] text-[var(--color-slate-500)]', 'bg-[var(--color-slate-400)]'],
    ];
    [$cls, $dot] = $tones[$tone] ?? $tones['neutral'];
@endphp
<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium whitespace-nowrap $cls"]) }}>
    <span class="w-1.5 h-1.5 rounded-full {{ $dot }}"></span>{{ $slot }}
</span>