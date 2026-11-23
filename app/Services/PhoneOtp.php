<?php

namespace App\Services;

use App\Models\Configuration;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Throwable;

/**
 * OTP verifikasi nomor WhatsApp baru saat user ganti no_wa di halaman Data Akun.
 *
 * Sengaja TIDAK lewat WhatsAppNotifier/queue: notifier mencatat isi pesan ke
 * wa_notifications (kode OTP bakal kelihatan di halaman Log Notifikasi) dan
 * retry-nya lambat (30 detik+). OTP harus langsung terkirim, jadi di sini
 * request ke Fonnte dibuat sinkron, pakai token yang sama dari Konfigurasi.
 *
 * State disimpan di cache (hash kodenya, bukan kode aslinya), jadi tidak perlu
 * tabel/migration baru.
 */
class PhoneOtp
{
    /** Masa berlaku kode (detik). */
    private const TTL_DETIK = 300;

    /** Jeda minimal sebelum boleh minta kode baru (detik). */
    private const COOLDOWN_DETIK = 60;

    /** Salah input sebanyak ini -> kode dihanguskan, harus minta ulang. */
    private const MAKS_SALAH = 5;

    /**
     * Rapikan input nomor jadi format lokal 08xxxxxxxxxx.
     * Menerima 08..., 628..., atau +628... (spasi/strip diabaikan).
     * Mengembalikan null kalau bukan nomor seluler Indonesia yang masuk akal.
     */
    public static function normalize(string $raw): ?string
    {
        $digits = preg_replace('/\D/', '', $raw);

        if (str_starts_with($digits, '62')) {
            $digits = '0' . substr($digits, 2);
        }

        return preg_match('/^08\d{7,12}$/', $digits) ? $digits : null;
    }

    /** 0812345678 -> 0812***678 (buat ditampilkan, bukan disimpan). */
    public static function mask(string $phone): string
    {
        $sisa = max(0, strlen($phone) - 7);

        return substr($phone, 0, 4) . str_repeat('*', $sisa) . substr($phone, -3);
    }

    /**
     * Permintaan ganti nomor yang masih menunggu kode, atau null kalau tidak ada
     * (belum pernah minta, sudah kedaluwarsa, atau sudah dibatalkan).
     *
     * @return array{phone: string, masked: string, expires_in: int, resend_in: int}|null
     */
    public function pending(User $user): ?array
    {
        $state = Cache::get($this->stateKey($user));

        if (! is_array($state) || ($state['expires_at'] ?? 0) <= time()) {
            return null;
        }

        return [
            'phone' => $state['phone'],
            'masked' => self::mask($state['phone']),
            'expires_in' => $state['expires_at'] - time(),
            'resend_in' => $this->resendIn($user),
        ];
    }

    /**
     * Kirim kode ke nomor baru. $phone harus sudah lewat normalize().
     *
     * @return array{ok: bool, message: string}
     */
    public function send(User $user, string $phone): array
    {
        $tunggu = $this->resendIn($user);

        if ($tunggu > 0) {
            return $this->gagal("Tunggu {$tunggu} detik sebelum minta kode baru.");
        }

        $token = Configuration::getValue('fonnte_token');

        if (empty($token)) {
            return $this->gagal('Layanan WhatsApp belum dikonfigurasi. Hubungi agendaris.');
        }

        $kode = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        try {
            $response = Http::withHeaders(['Authorization' => $token])
                ->timeout(10)
                ->post('https://api.fonnte.com/send', [
                    'target' => '62' . substr($phone, 1),
                    'message' => "*MasukPakEko*\nKode verifikasi nomor WhatsApp kamu: *{$kode}*\n"
                        . 'Berlaku ' . (self::TTL_DETIK / 60) . ' menit. Jangan bagikan kode ini ke siapa pun.',
                ]);
        } catch (Throwable $e) {
            return $this->gagal('Gagal menghubungi layanan WhatsApp. Coba lagi sebentar lagi.');
        }

        if (! $response->successful() || $response->json('status') === false) {
            return $this->gagal('Kode gagal dikirim. Pastikan nomor aktif dan terdaftar di WhatsApp, lalu coba lagi.');
        }

        // State baru ditulis SETELAH Fonnte menerima pesan, supaya kegagalan kirim
        // tidak meninggalkan kode "hantu" yang tidak pernah sampai ke user.
        Cache::put($this->stateKey($user), [
            'phone' => $phone,
            'hash' => $this->hash($kode),
            'attempts' => 0,
            'expires_at' => time() + self::TTL_DETIK,
        ], self::TTL_DETIK);

        Cache::put($this->cooldownKey($user), time() + self::COOLDOWN_DETIK, self::COOLDOWN_DETIK);

        return ['ok' => true, 'message' => 'Kode OTP dikirim.'];
    }

    /**
     * Cocokkan kode yang diketik user. Kalau benar, kode langsung dihanguskan
     * (sekali pakai) dan nomor yang diverifikasi dikembalikan.
     *
     * @return array{ok: bool, message: string, phone?: string}
     */
    public function verify(User $user, string $kode): array
    {
        $state = Cache::get($this->stateKey($user));

        if (! is_array($state) || ($state['expires_at'] ?? 0) <= time()) {
            $this->cancel($user);

            return $this->gagal('Kode sudah kedaluwarsa. Minta kode baru.');
        }

        if (! hash_equals($state['hash'], $this->hash($kode))) {
            $state['attempts']++;

            if ($state['attempts'] >= self::MAKS_SALAH) {
                $this->cancel($user);

                return $this->gagal('Terlalu banyak salah memasukkan kode. Minta kode baru.');
            }

            Cache::put($this->stateKey($user), $state, max(1, $state['expires_at'] - time()));

            $sisa = self::MAKS_SALAH - $state['attempts'];

            return $this->gagal("Kode salah. Sisa percobaan: {$sisa}.");
        }

        $this->cancel($user);

        return ['ok' => true, 'message' => 'Nomor terverifikasi.', 'phone' => $state['phone']];
    }

    /** Batalkan permintaan ganti nomor (cooldown kirim ulang tetap berlaku). */
    public function cancel(User $user): void
    {
        Cache::forget($this->stateKey($user));
    }

    private function resendIn(User $user): int
    {
        return max(0, (int) Cache::get($this->cooldownKey($user), 0) - time());
    }

    private function hash(string $kode): string
    {
        return hash_hmac('sha256', $kode, (string) config('app.key'));
    }

    private function stateKey(User $user): string
    {
        return "profile.phone_otp.{$user->id}";
    }

    private function cooldownKey(User $user): string
    {
        return "profile.phone_otp_cooldown.{$user->id}";
    }

    /** @return array{ok: bool, message: string} */
    private function gagal(string $message): array
    {
        return ['ok' => false, 'message' => $message];
    }
}