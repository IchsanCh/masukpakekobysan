<?php

namespace App\Jobs;

use App\Models\Configuration;
use App\Models\WaNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Throwable;

class SendWhatsAppNotification implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Total percobaan (1 percobaan awal + 2 retry) sebelum dianggap gagal permanen.
     */
    public int $tries = 3;

    public function __construct(public WaNotification $notification)
    {
    }

    /**
     * Jeda antar percobaan kalau gagal: 30 detik, lalu 2 menit, lalu 5 menit.
     */
    public function backoff(): array
    {
        return [30, 120, 300];
    }

    public function handle(): void
    {
        $token = Configuration::getValue('fonnte_token');

        if (empty($token)) {
            // Token kosong itu masalah konfigurasi, bukan gangguan sesaat — gak usah
            // di-retry berkali-kali percuma, langsung gagalkan job-nya.
            $this->fail(new RuntimeException('Token Fonnte belum diisi di halaman Konfigurasi.'));

            return;
        }

        $response = Http::withHeaders(['Authorization' => $token])
            ->timeout(15)
            ->post('https://api.fonnte.com/send', [
                'target' => $this->normalizePhone($this->notification->no_wa_tujuan),
                'message' => $this->notification->pesan,
            ]);

        if (! $response->successful() || $response->json('status') === false) {
            // Lempar exception biar job di-retry otomatis sesuai $tries & backoff().
            throw new RuntimeException('Fonnte gagal/menolak kirim: ' . $response->body());
        }

        $this->notification->update([
            'status_kirim' => 'terkirim',
            'terkirim_at' => now(),
        ]);
    }

    /**
     * Dipanggil Laravel otomatis begitu semua percobaan ($tries) udah habis dan
     * masih gagal juga — di sinilah status_kirim baru beneran ditandai 'gagal'
     * secara final (bukan di setiap percobaan yang gagal).
     */
    public function failed(?Throwable $exception): void
    {
        $this->notification->update(['status_kirim' => 'gagal']);
    }

    /**
     * Fonnte lebih reliable dikasih format 62xxx (tanpa 0 di depan).
     */
    private function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone);

        return str_starts_with($digits, '0') ? '62' . substr($digits, 1) : $digits;
    }
}