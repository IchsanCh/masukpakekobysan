<x-layouts.guest title="Login">
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <div class="text-center mb-6">
                <h1 class="text-2xl font-bold text-primary">DPMPTSP</h1>
                <p class="text-base-content/60 text-sm mt-1">Sistem Informasi Persuratan</p>
            </div>

            <x-alert />

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <x-input label="Username" name="username" placeholder="Masukkan username" required autofocus />

                <x-input label="Password" name="password" type="password" placeholder="Masukkan password" required
                    class="mt-2" />

                <div class="flex items-center justify-between mt-4">
                    <label class="label cursor-pointer gap-2">
                        <input type="checkbox" name="remember" class="checkbox checkbox-sm checkbox-primary" />
                        <span class="label-text">Ingat saya</span>
                    </label>
                </div>

                <button type="submit" class="btn btn-primary w-full mt-6">
                    Login
                </button>
            </form>
        </div>
    </div>
</x-layouts.guest>
