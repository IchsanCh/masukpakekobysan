<x-layouts.guest title="Login">
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
                        Selamat datang</h2>
                    <p class="text-sm text-[var(--color-slate-500)]">Masuk dengan akun yang terdaftar</p>
                </div>

                <x-alert />

                <form method="POST" action="{{ route('login') }}" x-data="{ loading: false }" @submit="loading = true">
                    @csrf

                    {{-- Username --}}
                    <div class="mb-5">
                        <x-input label="Username" name="username" placeholder="Masukkan username" icon="users"
                            required autofocus />
                    </div>

                    {{-- Password --}}
                    <div class="mb-4">
                        <x-input label="Password" name="password" type="password" placeholder="Masukkan password"
                            icon="lock" :togglePassword="true" required />
                    </div>

                    {{-- Remember + Forgot --}}
                    <div class="flex items-center justify-between mb-7">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="remember" class="w-[15px] h-[15px] checkbox checkbox-info" />
                            <span class="text-[13px] text-[var(--color-slate-500)]">Ingat saya</span>
                        </label>
                        <a href="{{ route('password.request') }}"
                            class="text-[13px] text-[var(--color-blue-500)] font-medium hover:underline">Lupa
                            password?</a>
                    </div>

                    {{-- Submit --}}
                    <form x-data="{ loading: false }" @submit="loading = true">
                        <x-auth.button-submit loadingText="Loading..."
                            class="w-full py-3 rounded-xl text-sm font-semibold text-white bg-gradient-to-r from-[var(--color-blue-500)] to-[var(--color-blue-600)] ...">
                            Masuk
                        </x-auth.button-submit>
                    </form>
                </form>

                <p class="text-center text-xs text-[var(--color-slate-500)] mt-7">
                    Belum punya akun? <a href="wa.me/6281"
                        class="text-[var(--color-blue-500)] hover:text-[var(--color-blue-600)] hover:underline">Hubungi
                        agendaris</a>
                </p>
            </div>
        </div>

        {{-- Right - Branding (hidden on mobile, shown on lg+) --}}
        <div
            class="hidden lg:flex flex-[1.4] bg-[var(--color-navy-900)] flex-col justify-between p-10 relative overflow-hidden">

            {{-- Geometric accents --}}
            <div
                class="absolute -top-20 -left-20 w-[300px] h-[300px] border-[40px] border-[var(--color-blue-500)]/[0.07] rounded-full">
            </div>
            <div
                class="absolute -bottom-12 -right-12 w-[200px] h-[200px] border-[30px] border-[var(--color-blue-500)]/[0.05] rounded-full">
            </div>

            {{-- Top spacer --}}
            <div></div>

            {{-- Center brand --}}
            <div class="relative z-10">
                <h1
                    class="font-[var(--font-heading)]  text-[44px] font-semibold text-white mb-3 tracking-wide leading-none">
                    MasukPakEko
                </h1>
                <p class="text-[15px] text-[var(--color-slate-400)] leading-relaxed max-w-[320px]">
                    Kelola surat masuk, surat keluar, dan disposisi dalam satu platform terpadu.
                </p>
            </div>

            {{-- Bottom --}}
            <div class="relative z-10">
                <p class="text-xs text-[var(--color-slate-400)]">&copy; {{ date('Y') }} DPMPTSP Kabupaten Pekalongan
                </p>
            </div>
        </div>

        {{-- Mobile branding (shown only on mobile) --}}
        <div class="lg:hidden text-center py-6 bg-[var(--color-slate-50)]">
            <p class="text-xs text-[var(--color-slate-400)] mt-1">&copy; {{ date('Y') }} DPMPTSP Kabupaten
                Pekalongan</p>
        </div>
    </div>
</x-layouts.guest>
