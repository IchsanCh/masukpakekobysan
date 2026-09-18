<?php

use App\Models\Configuration;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Jam kirim diambil dari Configuration (halaman Konfigurasi), bukan hardcode —
// makanya command-nya dicek tiap menit, tapi cuma beneran jalan begitu jamnya
// (HH:mm) cocok sama pengingat_jam_kirim. Butuh cron/scheduler server yang
// manggil `schedule:run` tiap menit biar ini kepicu (lihat catatan di chat).
Schedule::command('pengingat:deadline')
    ->everyMinute()
    ->when(fn () => now()->format('H:i') === Configuration::getValue('pengingat_jam_kirim', '07:00'));

// Cek retensi arsip sekali sehari — gak perlu presisi jam kayak pengingat di atas,
// jadi cukup ->daily() (jalan tengah malam) tanpa perlu setting jam terpisah.
Schedule::command('arsip:cek-retensi')->daily();