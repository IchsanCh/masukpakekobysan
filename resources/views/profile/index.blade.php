@php
    $initials =
        collect(preg_split('/\s+/', trim($user->name ?? '')))
            ->filter()
            ->take(2)
            ->map(fn($kata) => mb_strtoupper(mb_substr($kata, 0, 1)))
            ->implode('') ?:
        'U';

    // Baris yang langsung terbuka: yang barusan gagal divalidasi.
    $openOnLoad = collect(['name', 'username', 'email', 'phone', 'password'])->first(
        fn($bag) => $errors->hasBag($bag),
    );

    // Input pertama tiap baris, untuk fokus otomatis saat baris dibuka.
    $focusIds = [
        'name' => 'name',
        'username' => 'username',
        'email' => 'email',
        'phone' => 'no_wa',
        'password' => 'current_password',
    ];

    $panel =
        'bg-white rounded-2xl border border-[var(--color-slate-200)]/70 shadow-[0_1px_2px_rgba(15,23,42,0.04),0_12px_28px_-10px_rgba(15,23,42,0.10)] overflow-hidden divide-y divide-[color:var(--color-slate-100)]';
    $groupTitle = 'font-[var(--font-heading)] text-base font-semibold text-[var(--color-navy-900)] mb-3';

    $btn =
        'inline-flex items-center justify-center min-h-11 sm:min-h-10 rounded-xl text-sm transition-colors cursor-pointer disabled:cursor-not-allowed focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[color:var(--color-blue-500)]';
    $primary =
        $btn .
        ' px-5 font-semibold text-white bg-gradient-to-br from-[var(--color-blue-500)] to-[var(--color-blue-600)] shadow-[0_4px_10px_-2px_rgba(37,99,235,0.35)] hover:shadow-[0_8px_16px_-2px_rgba(37,99,235,0.45)] disabled:opacity-60';
    $ghost =
        $btn .
        ' px-4 font-medium text-[var(--color-slate-600)] hover:bg-[var(--color-slate-100)] disabled:opacity-50';
@endphp

<x-layouts.app title="Profile">

    <div class="max-w-4xl" x-data="{
        open: @js($openOnLoad),
        focusIds: @js($focusIds),
        toggle(key) {
            this.open = this.open === key ? null : key;
            if (this.open) this.focusField();
        },
        focusField() {
            this.$nextTick(() => setTimeout(() => document.getElementById(this.focusIds[this.open])?.focus(), 80));
        },
        init() { if (this.open) this.focusField(); }
    }" @keydown.escape="open = null">

        <div class="mb-8">
            <h1 class="font-[var(--font-heading)] text-2xl font-bold text-[var(--color-navy-900)] tracking-tight">
                Profile</h1>
            <p class="text-sm text-[var(--color-slate-500)] mt-0.5">Kelola data akun dan keamanan login kamu.</p>
        </div>

        <div class="flex flex-col gap-8 lg:grid lg:grid-cols-[15rem_minmax(0,1fr)] lg:gap-12">

            {{-- Identitas. Peran dan bidang tidak bisa diubah sendiri. --}}
            <aside class="lg:sticky lg:top-6 lg:self-start">
                <div class="flex items-center gap-4 lg:block">
                    <span aria-hidden="true"
                        class="w-16 h-16 lg:w-20 lg:h-20 rounded-2xl bg-gradient-to-br from-[var(--color-blue-500)] to-[var(--color-blue-600)] flex items-center justify-center shrink-0">
                        <span
                            class="text-white text-2xl lg:text-3xl font-bold font-[var(--font-heading)]">{{ $initials }}</span>
                    </span>
                    <div class="min-w-0 lg:mt-4">
                        <p class="text-lg font-semibold text-[var(--color-navy-900)] break-words">{{ $user->name }}
                        </p>
                        <div class="mt-1.5 flex flex-wrap items-center gap-x-2 gap-y-1">
                            <span
                                class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium border border-[var(--color-blue-500)]/20 bg-[var(--color-blue-500)]/5 text-[var(--color-blue-600)]">{{ ucfirst($user->peran) }}</span>
                            @if ($user->unit)
                                <span
                                    class="text-sm text-[var(--color-slate-500)]">{{ $user->unit->nama_unit }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                <p class="mt-4 text-sm text-[var(--color-slate-500)] max-w-[16rem]">Peran dan bidang diatur oleh
                    agendaris.</p>
            </aside>

            <div class="min-w-0 space-y-8">

                {{-- Informasi akun --}}
                <section aria-labelledby="grup-akun">
                    <h2 id="grup-akun" class="{{ $groupTitle }}">Informasi akun</h2>

                    <div class="{{ $panel }}">

                        {{-- Nama --}}
                        <x-profile.row id="name" label="Nama lengkap">
                            <x-slot:value><span class="block truncate"
                                    title="{{ $user->name }}">{{ $user->name }}</span></x-slot:value>

                            <form method="POST" action="{{ route('profile.name') }}" class="max-w-md space-y-4"
                                x-data="{ busy: false }" @submit="busy = true" @pageshow.window="busy = false">
                                @csrf
                                @method('PUT')
                                <x-profile.field id="name" name="name" label="Nama lengkap" bag="name"
                                    :value="old('name', $user->name)" required maxlength="255" autocomplete="name" />
                                <div class="flex flex-wrap items-center gap-2">
                                    <button type="submit" :disabled="busy" class="{{ $primary }}"
                                        x-text="busy ? 'Menyimpan...' : 'Simpan nama'">Simpan nama</button>
                                    <button type="button" @click="open = null"
                                        class="{{ $ghost }}">Batal</button>
                                </div>
                            </form>
                        </x-profile.row>

                        {{-- Username --}}
                        <x-profile.row id="username" label="Username">
                            <x-slot:value><span class="block truncate"
                                    title="{{ $user->username }}">{{ $user->username }}</span></x-slot:value>

                            <form method="POST" action="{{ route('profile.username') }}" class="max-w-md space-y-4"
                                x-data="{ busy: false }" @submit="busy = true" @pageshow.window="busy = false">
                                @csrf
                                @method('PUT')
                                <x-profile.field id="username" name="username" label="Username baru" bag="username"
                                    :value="old('username', $user->username)" required maxlength="50" pattern="\S+"
                                    title="Username tidak boleh mengandung spasi" autocomplete="username"
                                    autocapitalize="none" spellcheck="false"
                                    hint="Dipakai untuk login. Sesi ini tetap aktif; pakai username baru saat login berikutnya." />
                                <x-profile.field id="username_password" name="current_password"
                                    label="Password saat ini" bag="username" type="password" required
                                    autocomplete="current-password" />
                                <div class="flex flex-wrap items-center gap-2">
                                    <button type="submit" :disabled="busy" class="{{ $primary }}"
                                        x-text="busy ? 'Menyimpan...' : 'Ganti username'">Ganti username</button>
                                    <button type="button" @click="open = null"
                                        class="{{ $ghost }}">Batal</button>
                                </div>
                            </form>
                        </x-profile.row>

                        {{-- Email --}}
                        <x-profile.row id="email" label="Email">
                            <x-slot:value>
                                @if ($user->email)
                                    <span class="block truncate"
                                        title="{{ $user->email }}">{{ $user->email }}</span>
                                @else
                                    <span class="font-normal text-[var(--color-slate-500)]">Belum diisi</span>
                                @endif
                            </x-slot:value>

                            <form method="POST" action="{{ route('profile.email') }}" class="max-w-md space-y-4"
                                x-data="{ busy: false }" @submit="busy = true" @pageshow.window="busy = false">
                                @csrf
                                @method('PUT')
                                <x-profile.field id="email" name="email" label="Email baru" bag="email"
                                    type="email" :value="old('email', $user->email)" required maxlength="255"
                                    autocomplete="email" hint="Link reset password dikirim ke email ini." />
                                <x-profile.field id="email_password" name="current_password"
                                    label="Password saat ini" bag="email" type="password" required
                                    autocomplete="current-password" />
                                <div class="flex flex-wrap items-center gap-2">
                                    <button type="submit" :disabled="busy" class="{{ $primary }}"
                                        x-text="busy ? 'Menyimpan...' : 'Simpan email'">Simpan email</button>
                                    <button type="button" @click="open = null"
                                        class="{{ $ghost }}">Batal</button>
                                </div>
                            </form>
                        </x-profile.row>

                        {{-- Nomor WhatsApp --}}
                        <x-profile.row id="phone" label="Nomor WhatsApp">
                            <x-slot:value>
                                @if ($user->no_wa)
                                    <span class="block truncate" title="{{ $user->no_wa }}">{{ $user->no_wa }}</span>
                                @else
                                    <span class="block font-normal text-[var(--color-slate-500)]">Belum diisi</span>
                                @endif
                            </x-slot:value>

                            <form method="POST" action="{{ route('profile.phone') }}" class="max-w-md space-y-4"
                                x-data="{ busy: false }" @submit="busy = true" @pageshow.window="busy = false">
                                @csrf
                                @method('PUT')
                                <x-profile.field id="no_wa" name="no_wa" label="Nomor WhatsApp baru" bag="phone"
                                    type="tel" :value="old('no_wa', $user->no_wa)" required maxlength="20"
                                    inputmode="tel" autocomplete="tel" placeholder="08123456789"
                                    hint="Notifikasi disposisi dan pengingat dikirim ke nomor ini." />
                                <x-profile.field id="phone_password" name="current_password"
                                    label="Password saat ini" bag="phone" type="password" required
                                    autocomplete="current-password" />
                                <div class="flex flex-wrap items-center gap-2">
                                    <button type="submit" :disabled="busy" class="{{ $primary }}"
                                        x-text="busy ? 'Menyimpan...' : 'Simpan nomor'">Simpan nomor</button>
                                    <button type="button" @click="open = null"
                                        class="{{ $ghost }}">Batal</button>
                                </div>
                            </form>
                        </x-profile.row>
                    </div>
                </section>

                {{-- Keamanan --}}
                <section aria-labelledby="grup-keamanan">
                    <h2 id="grup-keamanan" class="{{ $groupTitle }}">Keamanan</h2>

                    <div class="{{ $panel }}">
                        <x-profile.row id="password" label="Password">
                            <x-slot:value><span aria-hidden="true" class="tracking-widest">••••••••</span><span
                                    class="sr-only">Disembunyikan</span></x-slot:value>

                            <form method="POST" action="{{ route('profile.password') }}" class="max-w-md space-y-4"
                                x-data="{ busy: false, pw: '', pw2: '' }" @submit="busy = true"
                                @pageshow.window="busy = false">
                                @csrf
                                @method('PUT')
                                <x-profile.field id="current_password" name="current_password"
                                    label="Password saat ini" bag="password" type="password" required
                                    autocomplete="current-password" />

                                <x-profile.field id="password" name="password" label="Password baru" bag="password"
                                    type="password" required minlength="6" autocomplete="new-password"
                                    x-model="pw">
                                    <p class="flex items-center gap-1.5 text-xs mt-1.5"
                                        :class="pw.length >= 6 ? 'text-[var(--color-success)]' :
                                            'text-[var(--color-slate-500)]'">
                                        <svg x-show="pw.length >= 6" style="display: none;"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5"
                                            aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m4.5 12.75 6 6 9-13.5" />
                                        </svg>
                                        Minimal 6 karakter
                                    </p>
                                </x-profile.field>

                                <x-profile.field id="password_confirmation" name="password_confirmation"
                                    label="Ulangi password baru" bag="password" type="password" required
                                    autocomplete="new-password" x-model="pw2">
                                    <p x-show="pw2 && pw !== pw2" style="display: none;"
                                        class="text-xs text-[var(--color-danger)] mt-1.5">Konfirmasi belum sama dengan
                                        password baru.</p>
                                </x-profile.field>

                                <p class="text-xs text-[var(--color-slate-500)]">Perangkat lain yang sedang login
                                    dengan akun ini akan dikeluarkan.</p>

                                <div class="flex flex-wrap items-center gap-2">
                                    <button type="submit" :disabled="busy" class="{{ $primary }}"
                                        x-text="busy ? 'Menyimpan...' : 'Ganti password'">Ganti password</button>
                                    <button type="button" @click="open = null"
                                        class="{{ $ghost }}">Batal</button>
                                </div>
                            </form>
                        </x-profile.row>
                    </div>
                </section>
            </div>
        </div>
    </div>
</x-layouts.app>