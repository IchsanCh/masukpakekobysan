<?php

namespace App\Http\Controllers;

use App\Services\PhoneOtp;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Halaman Data Akun — dipakai SEMUA peran untuk mengubah datanya sendiri.
 * Yang diubah selalu akun yang sedang login ($request->user()), tidak pernah
 * lewat id dari request, jadi tidak ada celah mengubah akun orang lain.
 *
 * Tiap form punya error bag sendiri (name/username/email/password/phone/otp)
 * supaya pesan error tidak muncul di form yang salah.
 */
class ProfileController extends Controller
{
    public function show(PhoneOtp $otp): View
    {
        $user = auth()->user()->load('unit');

        return view('profile.index', [
            'user' => $user,
            'pendingPhone' => $otp->pending($user),
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
     * Ganti username -> user otomatis keluar, login ulang pakai username baru.
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

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Username berhasil diganti. Silakan login dengan username baru.');
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
     * Langkah 1 ganti no. WhatsApp: kirim kode OTP ke NOMOR BARU. no_wa di akun
     * belum berubah sampai kode diverifikasi. Endpoint ini juga dipakai tombol
     * "Kirim ulang" (nomor yang sama dikirim lagi).
     */
    public function requestPhoneOtp(Request $request, PhoneOtp $otp): RedirectResponse
    {
        $user = $request->user();

        $request->validateWithBag('phone', [
            'no_wa' => ['required', 'string', 'max:20'],
        ], [
            'no_wa.required' => 'Nomor WhatsApp wajib diisi.',
        ]);

        $phone = PhoneOtp::normalize($request->input('no_wa'));

        if ($phone === null) {
            return back()->withInput()->withErrors(
                ['no_wa' => 'Format nomor tidak valid. Contoh: 08123456789.'],
                'phone'
            );
        }

        if ($user->no_wa && PhoneOtp::normalize($user->no_wa) === $phone) {
            return back()->withInput()->withErrors(
                ['no_wa' => 'Nomor ini sudah terpasang di akunmu.'],
                'phone'
            );
        }

        $result = $otp->send($user, $phone);

        if (! $result['ok']) {
            return back()->withInput()->withErrors(['no_wa' => $result['message']], 'phone');
        }

        return redirect()->route('profile.show')
            ->with('success', 'Kode OTP dikirim ke WhatsApp ' . PhoneOtp::mask($phone) . '.');
    }

    /**
     * Langkah 2: cocokkan kode. Hanya kalau benar, no_wa akun diganti ke nomor
     * yang tadi dikirimi kode (diambil dari state server, bukan dari form).
     */
    public function verifyPhoneOtp(Request $request, PhoneOtp $otp): RedirectResponse
    {
        $request->validateWithBag('otp', [
            'kode' => ['required', 'digits:6'],
        ], [
            'kode.required' => 'Masukkan kode OTP.',
            'kode.digits' => 'Kode OTP terdiri dari 6 angka.',
        ]);

        $user = $request->user();
        $result = $otp->verify($user, $request->input('kode'));

        if (! $result['ok']) {
            return back()->withErrors(['kode' => $result['message']], 'otp');
        }

        $user->update(['no_wa' => $result['phone']]);

        return redirect()->route('profile.show')->with('success', 'Nomor WhatsApp berhasil diganti.');
    }

    public function cancelPhoneOtp(Request $request, PhoneOtp $otp): RedirectResponse
    {
        $otp->cancel($request->user());

        return redirect()->route('profile.show');
    }
}