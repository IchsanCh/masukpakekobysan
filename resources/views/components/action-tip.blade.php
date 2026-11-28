{{-- Tooltip untuk tombol aksi (ikon). Muncul saat hover (tertunda sebentar) dan saat tombol difokus lewat keyboard.
     Pemakaian: <x-action-tip label="Edit"><a aria-label="Edit">...</a></x-action-tip> --}}
@props(['label'])
<span class="group/tip relative inline-flex">
    {{ $slot }}
    <span role="tooltip"
        class="pointer-events-none absolute left-1/2 -translate-x-1/2 bottom-full mb-1.5 z-30 whitespace-nowrap rounded-md px-2 py-1
               text-xs font-medium text-white bg-[#0f172a] border border-white/10 shadow-lg
               opacity-0 translate-y-0.5 transition duration-150
               group-hover/tip:opacity-100 group-hover/tip:translate-y-0 group-hover/tip:delay-300
               group-focus-within/tip:opacity-100 group-focus-within/tip:translate-y-0">{{ $label }}</span>
</span>