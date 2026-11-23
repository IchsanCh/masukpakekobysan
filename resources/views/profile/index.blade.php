@php
    $input = 'w-full px-3.5 py-2.5 text-base sm:text-sm rounded-xl border border-[var(--color-slate-200)] bg-white text-[var(--color-navy-900)] placeholder:text-[var(--color-slate-400)] focus:outline-none focus:border-[var(--color-blue-500)] focus:ring-2 focus:ring-[var(--color-blue-500)]/10 transition-all';
    $label = 'block text-sm font-medium text-[var(--color-navy-700)] mb-1.5';
    $error = 'text-xs text-[var(--color-danger)] mt-1';
    $hint = 'text-xs text-[var(--color-slate-500)] mt-1.5';
    $panel = 'bg-white rounded-2xl border border-[var(--color-slate-200)]/70 shadow-[0_1px_2px_rgba(15,23,42,0.04),0_12px_28px_-10px_rgba(15,23,42,0.10)] overflow-hidden';
    $toggle = 'shrink-0 inline-flex items-center justify-center min-h-11 sm:min-h-9 px-4 rounded-xl text-sm font-medium bg-white border border-[var(--color-slate-200)] text-[var(--color-slate-600)] hover:border-[var(--color-slate-300)] hover:text-[var(--color-navy-900)] transition-colors cursor-pointer';
    $primary = 'inline-flex items-center justify-center min-h-11 sm:min-h-10 px-5 rounded-xl text-sm font-semibold text-white bg-gradient-to-br from-[var(--color-blue-500)] to-[var(--color-blue-600)] shadow-[0_4px_10px_-2px_rgba(37,99,235,0.35)] hover:shadow-[0_8px_16px_-2px_rgba(37,99,235,0.45)] transition-all disabled:opacity-60 disabled:cursor-not-allowed cursor-pointer';
    $ghost = 'inline-flex items-center justify-center min-h-11 sm:min-h-10 px-4 rounded-xl text-sm font-medium text-[var(--color-slate-500)] hover:bg-[var(--color-slate-100)] transition-colors disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer';
    $lockSubmit = "\$el.querySelector('button[type=submit]').disabled = true";
@endphp

<x-layouts.app title="Data Akun">

    <div class="mb-6">
        <h1 class="font-[var(--font-heading)] text-2xl font-bold text-[var(--color-navy-900)] tracking-tight">Data Akun
        </h1>
        <p class="text-sm text-[var(--color-slate-500)] mt-0.5">Ubah username, password, email, dan nomor WhatsApp kamu
        </p>
    </div>

    <div class="max-w-2xl space-y-4">

        {{-- Info akun (tidak bisa diubah sendiri) --}}
        <section class="{{ $panel }}">
            <div class="flex items-center gap-3 px-5 py-4">
                <span
                    class="w-11 h-11 rounded-full bg-gradient-to-br from-[var(--color-blue-500)] to-[var(--color-blue-600)] flex items-center justify-center shrink-0">
                    <span
                        class="text-white text-base font-bold">{{ mb_strtoupper(mb_substr($user->name ?? 'U', 0, 1)) }}</span>
                </span>
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-[var(--color-navy-900)] truncate">{{ $user->name }}</p>
                    <p class="text-xs text-[var(--color-slate-500)] truncate">
                        {{ ucfirst($user->peran) }}@if ($user->unit)
                            , {{ $user->unit->nama_unit }}
                        @endif
                    </p>
                </div>
            </div>
            <p class="px-5 py-3 text-xs text-[var(--color-slate-500)] border-t border-[var(--color-slate-100)] bg-[var(--color-slate-50)]/50">
                Peran dan bidang diatur oleh agendaris.
            </p>
        </section>

        {{-- Nama --}}
        <section x-data="{ open: {{ $errors->hasBag('name') ? 'true' : 'false' }} }" class="{{ $panel }}">
            <div class="flex items-center justify-between gap-3 px-5 py-4">
                <div class="min-w-0">
                    <h2 class="text-sm font-semibold text-[var(--color-navy-900)]">Nama lengkap</h2>
                    <p class="text-sm text-[var(--color-slate-500)] truncate">{{ $user->name }}</p>
                </div>
                <button type="button" @click="open = !open" :aria-expanded="open" class="{{ $toggle }}"
                    x-text="open ? 'Tutup' : 'Ubah'">Ubah</button>
            </div>
            <div x-show="open" style="display: none;" x-transition
                class="px-5 pb-5 pt-4 border-t border-[var(--color-slate-100)]">
                <form method="POST" action="{{ route('profile.name') }}" @submit="{{ $lockSubmit }}">
                    @csrf
                    @method('PUT')
                    <label for="name" class="{{ $label }}">Nama lengkap</label>
                    <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required
                        maxlength="255" autocomplete="name" class="{{ $input }}" />
                    @error('name', 'name')
                        <p class="{{ $error }}">{{ $message }}</p>
                    @enderror
                    <div class="flex justify-end gap-2 mt-4">
                        <button type="button" @click="open = false" class="{{ $ghost }}">Batal</button>
                        <button type="submit" class="{{ $primary }}">Simpan nama</button>
                    </div>
                </form>
            </div>
        </section>

        {{-- Username --}}
        <section x-data="{ open: {{ $errors->hasBag('username') ? 'true' : 'false' }} }" class="{{ $panel }}">
            <div class="flex items-center justify-between gap-3 px-5 py-4">
                <div class="min-w-0">
                    <h2 class="text-sm font-semibold text-[var(--color-navy-900)]">Username</h2>
                    <p class="text-sm text-[var(--color-slate-500)] truncate">{{ $user->username }}</p>
                </div>
                <button type="button" @click="open = !open" :aria-expanded="open" class="{{ $toggle }}"
                    x-text="open ? 'Tutup' : 'Ubah'">Ubah</button>
            </div>
            <div x-show="open" style="display: none;" x-transition
                class="px-5 pb-5 pt-4 border-t border-[var(--color-slate-100)]">
                <form method="POST" action="{{ route('profile.username') }}" @submit="{{ $lockSubmit }}"
                    class="space-y-3">
                    @csrf
                    @method('PUT')
                    <div>
                        <label for="username" class="{{ $label }}">Username baru</label>
                        <input id="username" type="text" name="username" value="{{ old('username', $user->username) }}"
                            required maxlength="50" autocomplete="username" autocapitalize="none" spellcheck="false"
                            class="{{ $input }}" />
                        @error('username', 'username')
                            <p class="{{ $error }}">{{ $message }}</p>
                        @enderror
                        <p class="{{ $hint }}">Setelah diganti, kamu otomatis keluar dan harus login lagi dengan
                            username baru.</p>
                    </div>
                    <div>
                        <label for="username_password" class="{{ $label }}">Password saat ini</label>
                        <input id="username_password" type="password" name="current_password" required
                            autocomplete="current-password" class="{{ $input }}" />
                        @error('current_password', 'username')
                            <p class="{{ $error }}">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex justify-end gap-2 pt-1">
                        <button type="button" @click="open = false" class="{{ $ghost }}">Batal</button>
                        <button type="submit" class="{{ $primary }}">Ganti username</button>
                    </div>
                </form>
            </div>
        </section>

        {{-- Email --}}
        <section x-data="{ open: {{ $errors->hasBag('email') ? 'true' : 'false' }} }" class="{{ $panel }}">
            <div class="flex items-center justify-between gap-3 px-5 py-4">
                <div class="min-w-0">
                    <h2 class="text-sm font-semibold text-[var(--color-navy-900)]">Email</h2>
                    <p class="text-sm text-[var(--color-slate-500)] truncate">{{ $user->email ?: 'Belum diisi' }}</p>
                </div>
                <button type="button" @click="open = !open" :aria-expanded="open" class="{{ $toggle }}"
                    x-text="open ? 'Tutup' : 'Ubah'">Ubah</button>
            </div>
            <div x-show="open" style="display: none;" x-transition
                class="px-5 pb-5 pt-4 border-t border-[var(--color-slate-100)]">
                <form method="POST" action="{{ route('profile.email') }}" @submit="{{ $lockSubmit }}"
                    class="space-y-3">
                    @csrf
                    @method('PUT')
                    <div>
                        <label for="email" class="{{ $label }}">Email baru</label>
                        <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required
                            maxlength="255" autocomplete="email" class="{{ $input }}" />
                        @error('email', 'email')
                            <p class="{{ $error }}">{{ $message }}</p>
                        @enderror
                        <p class="{{ $hint }}">Link reset password dikirim ke email ini.</p>
                    </div>
                    <div>
                        <label for="email_password" class="{{ $label }}">Password saat ini</label>
                        <input id="email_password" type="password" name="current_password" required
                            autocomplete="current-password" class="{{ $input }}" />
                        @error('current_password', 'email')
                            <p class="{{ $error }}">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex justify-end gap-2 pt-1">
                        <button type="button" @click="open = false" class="{{ $ghost }}">Batal</button>
                        <button type="submit" class="{{ $primary }}">Simpan email</button>
                    </div>
                </form>
            </div>
        </section>

        {{-- No. WhatsApp (verifikasi OTP ke nomor baru) --}}
        <section
            x-data="{
                open: {{ $pendingPhone || $errors->hasBag('phone') || $errors->hasBag('otp') ? 'true' : 'false' }},
                resend: {{ (int) ($pendingPhone['resend_in'] ?? 0) }},
                init() { setInterval(() => { if (this.resend > 0) this.resend--; }, 1000); }
            }"
            class="{{ $panel }}">
            <div class="flex items-center justify-between gap-3 px-5 py-4">
                <div class="min-w-0">
                    <h2 class="text-sm font-semibold text-[var(--color-navy-900)]">Nomor WhatsApp</h2>
                    <p class="text-sm text-[var(--color-slate-500)] truncate">{{ $user->no_wa ?: 'Belum diisi' }}</p>
                </div>
                <button type="button" @click="open = !open" :aria-expanded="open" class="{{ $toggle }}"
                    x-text="open ? 'Tutup' : 'Ubah'">Ubah</button>
            </div>
            <div x-show="open" style="display: none;" x-transition
                class="px-5 pb-5 pt-4 border-t border-[var(--color-slate-100)]">

                @if ($pendingPhone)
                    {{-- Langkah 2: masukkan kode --}}
                    <p class="text-sm text-[var(--color-slate-700)] mb-3">
                        Kode OTP 6 angka dikirim ke WhatsApp <span
                            class="font-semibold">{{ $pendingPhone['masked'] }}</span>. Berlaku 5 menit. Nomor di
                        akunmu baru berganti setelah kode benar.
                    </p>
                    <form method="POST" action="{{ route('profile.phone.verify') }}" @submit="{{ $lockSubmit }}">
                        @csrf
                        <label for="kode" class="{{ $label }}">Kode OTP</label>
                        <input id="kode" type="text" name="kode" inputmode="numeric" pattern="[0-9]*" maxlength="6"
                            autocomplete="one-time-code" required placeholder="123456"
                            class="{{ $input }} tracking-[0.3em] sm:max-w-[12rem]" />
                        @error('kode', 'otp')
                            <p class="{{ $error }}">{{ $message }}</p>
                        @enderror
                        <div class="mt-4">
                            <button type="submit" class="{{ $primary }} w-full sm:w-auto">Verifikasi nomor</button>
                        </div>
                    </form>

                    <div class="flex flex-wrap items-center gap-2 mt-3 pt-3 border-t border-[var(--color-slate-100)]">
                        <form method="POST" action="{{ route('profile.phone.otp') }}" @submit="{{ $lockSubmit }}">
                            @csrf
                            <input type="hidden" name="no_wa" value="{{ $pendingPhone['phone'] }}" />
                            <button type="submit" :disabled="resend > 0" class="{{ $ghost }}"
                                x-text="resend > 0 ? 'Kirim ulang kode (' + resend + ' dtk)' : 'Kirim ulang kode'">Kirim
                                ulang kode</button>
                        </form>
                        <form method="POST" action="{{ route('profile.phone.cancel') }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="{{ $ghost }}">Pakai nomor lain</button>
                        </form>
                    </div>
                    @error('no_wa', 'phone')
                        <p class="{{ $error }}">{{ $message }}</p>
                    @enderror
                @else
                    {{-- Langkah 1: nomor baru --}}
                    <form method="POST" action="{{ route('profile.phone.otp') }}" @submit="{{ $lockSubmit }}">
                        @csrf
                        <label for="no_wa" class="{{ $label }}">Nomor WhatsApp baru</label>
                        <input id="no_wa" type="tel" name="no_wa" value="{{ old('no_wa') }}" required maxlength="20"
                            inputmode="tel" autocomplete="tel" placeholder="08123456789" class="{{ $input }}" />
                        @error('no_wa', 'phone')
                            <p class="{{ $error }}">{{ $message }}</p>
                        @enderror
                        <p class="{{ $hint }}">Kami kirim kode OTP ke nomor ini lewat WhatsApp untuk memastikan nomornya
                            milikmu.</p>
                        <div class="flex justify-end gap-2 mt-4">
                            <button type="button" @click="open = false" class="{{ $ghost }}">Batal</button>
                            <button type="submit" class="{{ $primary }}">Kirim kode OTP</button>
                        </div>
                    </form>
                @endif
            </div>
        </section>

        {{-- Password --}}
        <section x-data="{ open: {{ $errors->hasBag('password') ? 'true' : 'false' }} }" class="{{ $panel }}">
            <div class="flex items-center justify-between gap-3 px-5 py-4">
                <div class="min-w-0">
                    <h2 class="text-sm font-semibold text-[var(--color-navy-900)]">Password</h2>
                    <p class="text-sm text-[var(--color-slate-500)]">Minimal 6 karakter</p>
                </div>
                <button type="button" @click="open = !open" :aria-expanded="open" class="{{ $toggle }}"
                    x-text="open ? 'Tutup' : 'Ubah'">Ubah</button>
            </div>
            <div x-show="open" style="display: none;" x-transition
                class="px-5 pb-5 pt-4 border-t border-[var(--color-slate-100)]">
                <form method="POST" action="{{ route('profile.password') }}" @submit="{{ $lockSubmit }}"
                    class="space-y-3">
                    @csrf
                    @method('PUT')
                    <div>
                        <label for="current_password" class="{{ $label }}">Password saat ini</label>
                        <input id="current_password" type="password" name="current_password" required
                            autocomplete="current-password" class="{{ $input }}" />
                        @error('current_password', 'password')
                            <p class="{{ $error }}">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="password" class="{{ $label }}">Password baru</label>
                        <input id="password" type="password" name="password" required minlength="6"
                            autocomplete="new-password" class="{{ $input }}" />
                        @error('password', 'password')
                            <p class="{{ $error }}">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="password_confirmation" class="{{ $label }}">Ulangi password baru</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required
                            autocomplete="new-password" class="{{ $input }}" />
                    </div>
                    <p class="{{ $hint }}">Perangkat lain yang sedang login dengan akun ini akan dikeluarkan.</p>
                    <div class="flex justify-end gap-2 pt-1">
                        <button type="button" @click="open = false" class="{{ $ghost }}">Batal</button>
                        <button type="submit" class="{{ $primary }}">Ganti password</button>
                    </div>
                </form>
            </div>
        </section>
    </div>
</x-layouts.app>