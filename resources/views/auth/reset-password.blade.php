<x-layouts.guest title="Reset Password">
    <div class="min-h-screen flex flex-col lg:flex-row bg-[var(--color-slate-900)]">

        {{-- Left - Form --}}
        <div
            class="flex-1 flex items-center justify-center p-6 sm:p-10
                    bg-[var(--color-slate-50)] lg:rounded-r-[60px] lg:shadow-2xl relative z-10">
            <div
                class="w-full max-w-[380px] bg-white/80 backdrop-blur-xl p-6 sm:p-8 rounded-2xl shadow-xl border border-white/40">

                <div class="mb-9">
                    <h2
                        class="font-[var(--font-heading)] text-2xl font-bold text-[var(--color-navy-900)] mb-1 tracking-tight">
                        Reset password</h2>
                    <p class="text-sm text-[var(--color-slate-500)]">Buat password baru untuk akun anda</p>
                </div>

                <x-alert />

                <form method="POST" action="{{ route('password.update') }}" x-data="{ loading: false }"
                    @submit="loading = true">
                    @csrf

                    <input type="hidden" name="token" value="{{ $token }}">
                    <input type="hidden" name="email" value="{{ $email ?? old('email') }}">
                    <p class="text-sm text-[var(--color-navy-900)] mb-4 break-words">
                        Reset password untuk: <strong>{{ $email }}</strong>
                    </p>
                    <div class="mb-5">
                        <x-input label="Password baru" name="password" type="password"
                            placeholder="Masukkan password baru" icon="lock" :togglePassword="true" required />
                    </div>

                    <div class="mb-7">
                        <x-input label="Konfirmasi password" name="password_confirmation" type="password"
                            placeholder="Ulangi password baru" icon="lock" :togglePassword="true" required />
                    </div>

                    <x-auth.button-submit loadingText="Loading..."
                        class="w-full py-3 rounded-xl text-sm font-semibold text-white bg-gradient-to-r from-[var(--color-blue-500)] to-[var(--color-blue-600)] ...">
                        Reset Password
                    </x-auth.button-submit>
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
                    class="font-[var(--font-heading)] text-[44px] font-semibold text-white mb-3 tracking-wide leading-none">
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
