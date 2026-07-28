<x-layouts.app title="Edit Surat Masuk">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('surat-masuk.index') }}"
            class="p-2 rounded-xl text-[var(--color-slate-400)] hover:bg-[var(--color-slate-100)] hover:text-[var(--color-navy-900)] transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <div>
            <h1 class="font-[var(--font-heading)] text-2xl font-bold text-[var(--color-navy-900)] tracking-tight">Edit
                Surat Masuk</h1>
            <p class="text-sm text-[var(--color-slate-500)] mt-0.5">{{ $suratMasuk->nomor_agenda }} —
                {{ $suratMasuk->nomor_surat }}</p>
        </div>
    </div>

    @include('surat-masuk._form')

</x-layouts.app>
