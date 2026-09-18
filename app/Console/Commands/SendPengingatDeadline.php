<?php

namespace App\Console\Commands;

use App\Models\Configuration;
use App\Models\Disposisi;
use App\Services\WhatsAppNotifier;
use Illuminate\Console\Command;

class SendPengingatDeadline extends Command
{
    protected $signature = 'pengingat:deadline';

    protected $description = 'Kirim pengingat WhatsApp untuk disposisi yang sudah lewat batas waktu dan belum selesai/ditolak';

    public function handle(WhatsAppNotifier $notifier): int
    {
        if (Configuration::getValue('pengingat_aktif', '0') !== '1') {
            $this->info('Pengingat harian sedang dinonaktifkan di halaman Konfigurasi, dilewati.');

            return self::SUCCESS;
        }

        $overdue = Disposisi::with(['suratMasuk', 'dariUser', 'unit', 'kepadaUser'])
            ->whereNotNull('batas_waktu')
            ->where('batas_waktu', '<', now())
            ->whereNotIn('status', ['selesai', 'ditolak'])
            ->get();

        if ($overdue->isEmpty()) {
            $this->info('Tidak ada disposisi yang lewat batas waktu saat ini.');

            return self::SUCCESS;
        }

        foreach ($overdue as $disposisi) {
            $notifier->notifyPengingatDeadline($disposisi);
        }

        $this->info("Pengingat dikirim untuk {$overdue->count()} disposisi yang lewat batas waktu.");

        return self::SUCCESS;
    }
}