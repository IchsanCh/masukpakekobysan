<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Halaman Profile — dipakai SEMUA peran untuk mengubah datanya sendiri.
 * Yang diubah selalu akun yang sedang login ($request->user()), tidak pernah
 * lewat id dari request, jadi tidak ada celah mengubah akun orang lain.
 *
 * Tiap form punya error bag sendiri (name/username/email/password/phone)
 * supaya pesan error tidak muncul di form yang salah.
 */
class ProfileController extends Controller
{
    public function show(): View
    {
        return view('profile.index', [
            'user' => auth()->user()->load('unit'),
        ]);
    }

    public function updateName(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('name', [
            'name' => ['required', 'string', 'max:255'],
        ]);

        $request->user()->update(['name' => $validated['name']]);

        return redirect()->route('profile.show')->with('success', 'Nama berhasil diperbarui.');
    }

    /**
     * Ganti username. Sesi TIDAK diputus: sesi login menyimpan id user (bukan username),
     * username hanya dibaca saat login, dan password sudah diminta ulang di form ini.
     * Memaksa logout tidak menambah keamanan (sesi perangkat lain tetap hidup) dan hanya
     * membuat user login ulang, apalagi kalau username barunya salah ketik.
     */
    public function updateUsername(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validateWithBag('username', [
            'current_password' => ['required', 'current_password'],
            'username' => [
                'required',
                'string',
                'max:50',
                'regex:/^\S+$/',
                Rule::unique('users', 'username')->ignore($user->id),
                Rule::notIn([$user->username]),
            ],
        ], [
            'current_password.current_password' => 'Password saat ini salah.',
            'username.regex' => 'Username tidak boleh mengandung spasi.',
            'username.not_in' => 'Username baru sama dengan yang sekarang.',
            'username.unique' => 'Username sudah dipakai pengguna lain.',
        ]);

        $user->update(['username' => $validated['username']]);

        return redirect()->route('profile.show')
            ->with('success', 'Username diganti menjadi ' . $validated['username'] . '. Pakai username baru saat login berikutnya.');
    }

    public function updateEmail(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validateWithBag('email', [
            'current_password' => ['required', 'current_password'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
        ], [
            'current_password.current_password' => 'Password saat ini salah.',
            'email.unique' => 'Email sudah dipakai pengguna lain.',
        ]);

        $user->update(['email' => $validated['email']]);

        return redirect()->route('profile.show')->with('success', 'Email berhasil diperbarui.');
    }

    /**
     * Ganti password. Sesi ini tetap login, tapi perangkat lain (sesi database +
     * token "ingat saya") dikeluarkan supaya password lama tidak berguna lagi.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validateWithBag('password', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:6', 'confirmed', 'different:current_password'],
        ], [
            'current_password.current_password' => 'Password saat ini salah.',
            'password.confirmed' => 'Konfirmasi password tidak sama.',
            'password.different' => 'Password baru harus berbeda dari password saat ini.',
        ]);

        $user->forceFill([
            'password' => Hash::make($validated['password']),
            'remember_token' => Str::random(60),
        ])->save();

        if (config('session.driver') === 'database') {
            DB::table(config('session.table', 'sessions'))
                ->where('user_id', $user->id)
                ->where('id', '!=', $request->session()->getId())
                ->delete();
        }

        return redirect()->route('profile.show')->with('success', 'Password berhasil diganti.');
    }

    /**
     * Ganti no. WhatsApp. Pola sama dengan username & email: cukup konfirmasi
     * password saat ini, tanpa OTP. Input dirapikan jadi format lokal 08xxxxxxxxxx.
     */
    public function updatePhone(Request $request): RedirectResponse
    {
        $user = $request->user();

        $request->validateWithBag('phone', [
            'current_password' => ['required', 'current_password'],
            'no_wa' => ['required', 'string', 'max:20'],
        ], [
            'current_password.current_password' => 'Password saat ini salah.',
            'no_wa.required' => 'Nomor WhatsApp wajib diisi.',
        ]);

        $phone = self::normalizePhone($request->input('no_wa'));

        if ($phone === null) {
            return back()->withInput()->withErrors(
                ['no_wa' => 'Format nomor tidak valid. Contoh: 08123456789.'],
                'phone'
            );
        }

        if ($user->no_wa && self::normalizePhone($user->no_wa) === $phone) {
            return back()->withInput()->withErrors(
                ['no_wa' => 'Nomor ini sudah terpasang di akunmu.'],
                'phone'
            );
        }

        $user->update(['no_wa' => $phone]);

        return redirect()->route('profile.show')->with('success', 'Nomor WhatsApp berhasil diperbarui.');
    }

    /**
     * Rapikan input nomor jadi format lokal 08xxxxxxxxxx.
     * Menerima 08..., 628..., atau +628... (spasi/strip diabaikan).
     * Mengembalikan null kalau bukan nomor seluler Indonesia yang masuk akal.
     */
    private static function normalizePhone(string $raw): ?string
    {
        $digits = preg_replace('/\D/', '', $raw);

        if (str_starts_with($digits, '62')) {
            $digits = '0' . substr($digits, 2);
        }

        return preg_match('/^08\d{7,12}$/', $digits) ? $digits : null;
    }
}