@props(['count' => 0, 'label' => null])

{{-- Lencana "perlu ditangani" di menu sidebar: lonceng + jumlah. Hanya tampil kalau count > 0.
     Lonceng bergoyang sekali saat halaman dibuka (dimatikan kalau user memilih reduced motion). --}}
@if ($count > 0)
    <span
        {{ $attributes->merge(['class' => 'ml-auto shrink-0 inline-flex items-center gap-1 h-5 min-w-5 px-1.5 rounded-full bg-warning text-warning-content text-[11px] font-semibold leading-none']) }}>
        <x-icon name="bell" :solid="true" class="bell-ring w-3 h-3" />
        <span class="tabular-nums" aria-hidden="true">{{ $count > 99 ? '99+' : $count }}</span>
        <span class="sr-only">{{ $count }} {{ $label }}</span>
    </span>
@endif