<x-layouts.guest title="Lupa Password">
    <div class="min-h-screen flex flex-col lg:flex-row">

        {{-- Left - Form --}}
        <div class="flex-1 flex items-center justify-center p-6 sm:p-10 bg-[var(--color-slate-50)]">
            <div
                class="w-full max-w-[380px] bg-white/80 backdrop-blur-xl p-6 sm:p-8 rounded-2xl shadow-xl border border-white/40">

                <div class="mb-9">
                    <h2
                        class="font-[var(--font-heading)] text-2xl font-bold text-[var(--color-navy-900)] mb-1 tracking-tight">
                        Lupa password?</h2>
                    <p class="text-sm text-[var(--color-slate-500)] leading-relaxed">Masukkan email yang terdaftar. Kami
                        akan mengirimkan link untuk reset password.</p>
                </div>

                <x-alert />

                @if (session('status'))
                    <div class="mb-5 p-4 rounded-xl bg-[var(--color-blue-50)] border border-[var(--color-blue-100)]">
                        <p class="text-sm text-[var(--color-blue-600)]">{{ session('status') }}</p>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    <div class="mb-7">
                        <x-input label="Email" name="email" type="email" placeholder="Masukkan email"
                            icon="mail" required autofocus />
                    </div>

                    <button type="submit"
                        class="w-full py-3 rounded-xl text-sm font-semibold text-white
                            bg-gradient-to-r from-[var(--color-blue-500)] to-[var(--color-blue-600)]
                            transition-all duration-300
                            hover:scale-[1.02] hover:shadow-xl hover:shadow-[var(--color-blue-500)]/30">
                        Kirim Link Reset
                    </button>
                </form>

                <p class="text-center text-xs text-[var(--color-slate-500)] mt-7">
                    Sudah ingat? <a href="{{ route('login') }}"
                        class="text-[var(--color-blue-500)] hover:text-[var(--color-blue-600)] hover:underline">Kembali
                        ke login</a>
                </p>
            </div>
        </div>

        {{-- Right - Branding --}}
        <div
            class="hidden lg:flex flex-[1.4] bg-[var(--color-navy-900)] flex-col justify-between p-10 relative overflow-hidden">

            <div
                class="absolute -top-20 -left-20 w-[300px] h-[300px] border-[40px] border-[var(--color-blue-500)]/[0.07] rounded-full">
            </div>
            <div
                class="absolute -bottom-12 -right-12 w-[200px] h-[200px] border-[30px] border-[var(--color-blue-500)]/[0.05] rounded-full">
            </div>

            <div></div>

            <div class="relative z-10">
                <h1
                    class="font-[var(--font-heading)] text-[44px] font-bold text-white mb-3 tracking-tight leading-none">
                    MasukPakEko
                </h1>
                <p class="text-[15px] text-[var(--color-slate-400)] leading-relaxed max-w-[320px]">
                    Kelola surat masuk, surat keluar, dan disposisi dalam satu platform terpadu.
                </p>
            </div>

            <div class="relative z-10">
                <p class="text-xs text-[var(--color-slate-400)]">&copy; {{ date('Y') }} DPMPTSP Kabupaten Pekalongan
                </p>
            </div>
        </div>

        <div class="lg:hidden text-center py-6 bg-[var(--color-slate-50)]">
            <p class="text-xs text-[var(--color-slate-400)] mt-1">&copy; {{ date('Y') }} DPMPTSP Kabupaten
                Pekalongan
            </p>
        </div>
    </div>
</x-layouts.guest>
