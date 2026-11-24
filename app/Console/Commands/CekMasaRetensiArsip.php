<?php

namespace App\Console\Commands;

use App\Models\SuratKeluar;
use App\Models\SuratMasuk;
use App\Services\WhatsAppNotifier;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Throwable;

class CekMasaRetensiArsip extends Command
{
    protected $signature = 'arsip:cek-retensi {--dry-run : Cuma tampilkan apa yang AKAN diproses, tanpa beneran eksekusi}';

    protected $description = 'Cek masa retensi surat masuk & keluar: pindahkan aktif -> inaktif saat masa_aktif habis, lalu tandai nasib akhir dan kabari agendaris via WhatsApp saat masa_aktif + masa_inaktif habis';

    public function handle(WhatsAppNotifier $notifier): int
    {
        $dryRun = $this->option('dry-run');

        // Urutan hidup surat: aktif -> inaktif (masa_aktif habis) -> nasib akhir (masa_aktif + masa_inaktif habis).
        $this->prosesRetensiHabis($notifier, $dryRun);
        $this->tandaiInaktif($dryRun);

        if ($dryRun) {
            $this->line('Dry-run: status tidak diubah dan notifikasi WA tidak dikirim.');
        }

        return self::SUCCESS;
    }

    /**
     * Total retensi (masa_aktif + masa_inaktif) sudah habis -> tandai nasib akhirnya
     * (menunggu_musnah / permanen / perlu_ditinjau) dan kabari agendaris.
     */
    private function prosesRetensiHabis(WhatsAppNotifier $notifier, bool $dryRun): void
    {
        $suratMasuk = $this->kandidatRetensiHabis(SuratMasuk::query());
        $suratKeluar = $this->kandidatRetensiHabis(SuratKeluar::query());

        $total = $suratMasuk->count() + $suratKeluar->count();

        if ($total === 0) {
            $this->info('Tidak ada surat yang masa retensinya habis saat ini.');

            return;
        }

        $this->info("Ditemukan {$total} surat yang masa retensinya sudah habis.");

        // Kumpulkan dulu yang perlu notif; kirimnya setelah semua status ter-update,
        // supaya keputusan "satuan atau ringkasan" dihitung dari total keseluruhan.
        $konfirmasiMusnah = collect();
        $dinilaiKembali = collect();

        $kelompok = [
            ['masuk', 'surat masuk', $suratMasuk],
            ['keluar', 'surat keluar', $suratKeluar],
        ];

        foreach ($kelompok as [$jenis, $label, $daftarSurat]) {
            foreach ($daftarSurat as $surat) {
                $nasib = $this->prosesNasibAkhir($surat, $label, $dryRun);

                if ($dryRun) {
                    continue;
                }

                $item = ['surat' => $surat, 'jenis' => $jenis];

                if ($nasib === 'musnah') {
                    $konfirmasiMusnah->push($item);
                } elseif ($nasib === 'dinilai_kembali') {
                    $dinilaiKembali->push($item);
                }
            }
        }

        if ($dryRun) {
            return;
        }

        $this->kirimNotifikasi($notifier, $konfirmasiMusnah, $dinilaiKembali);
    }

    /**
     * aktif -> inaktif: masa_aktif sudah lewat, tapi total retensi belum habis.
     * Surat yang total retensinya SUDAH habis sengaja tidak disentuh di sini —
     * itu langsung ditangani prosesRetensiHabis() (misal data lama yang baru
     * dimasukkan, atau masa_inaktif = 0 sehingga tidak ada fase inaktif).
     */
    private function tandaiInaktif(bool $dryRun): void
    {
        $jumlah = 0;

        foreach ([['surat masuk', SuratMasuk::query()], ['surat keluar', SuratKeluar::query()]] as [$label, $query]) {
            foreach ($this->kandidatInaktif($query) as $surat) {
                $jumlah++;

                $this->line("- [{$label}] {$surat->nomor_surat} -> aktif => inaktif");

                if (! $dryRun) {
                    $surat->update(['status_arsip' => 'inaktif']);
                }
            }
        }

        if ($jumlah === 0) {
            $this->info('Tidak ada surat yang beralih ke inaktif.');

            return;
        }

        $this->info($dryRun ? "{$jumlah} surat AKAN beralih ke inaktif." : "{$jumlah} surat beralih dari aktif ke inaktif.");
    }

    private function kandidatInaktif(Builder $query): Collection
    {
        return $query->with('referensiRetensi')
            ->where('status_arsip', 'aktif')
            ->whereNotNull('referensi_retensi_id')
            ->get()
            ->filter(function (Model $surat) {
                $retensi = $surat->referensiRetensi;

                if (! $retensi) {
                    return false;
                }

                $masaAktifHabis = $surat->tanggal_surat->copy()->addYears($retensi->masa_aktif)->isPast();
                $retensiHabis = $surat->tanggal_surat->copy()->addYears($retensi->masa_aktif + $retensi->masa_inaktif)->isPast();

                return $masaAktifHabis && ! $retensiHabis;
            });
    }

    /**
     * Ambil surat yang: masih aktif/inaktif (belum final), punya referensi retensi
     * (butuh masa_aktif+masa_inaktif+nasib_akhir_default buat diproses), dan
     * tanggal_surat + (masa_aktif + masa_inaktif) tahun sudah lewat hari ini.
     */
    private function kandidatRetensiHabis(\Illuminate\Database\Eloquent\Builder $query): Collection
    {
        return $query->with('referensiRetensi')
            ->whereIn('status_arsip', ['aktif', 'inaktif'])
            ->whereNotNull('referensi_retensi_id')
            ->get()
            ->filter(function (Model $surat) {
                $retensi = $surat->referensiRetensi;

                if (! $retensi) {
                    return false;
                }

                $totalTahun = $retensi->masa_aktif + $retensi->masa_inaktif;

                return $surat->tanggal_surat->copy()->addYears($totalTahun)->isPast();
            });
    }

    /**
     * PENTING: 'musnah' di sini CUMA nandain status jadi 'menunggu_musnah' —
     * gak langsung eksekusi hapus. Eksekusi beneran (file + DB + catat log)
     * cuma terjadi lewat approval manual agendaris di halaman Arsip
     * (lihat ArsipKeputusanService::musnahkan()).
     *
     * Mengembalikan nasib_akhir_default surat ini, dipakai untuk menentukan notif WA.
     */
    private function prosesNasibAkhir(Model $surat, string $label, bool $dryRun): ?string
    {
        $nasibDefault = $surat->referensiRetensi->nasib_akhir_default;

        $this->line("- [{$label}] {$surat->nomor_surat} -> nasib_akhir_default: {$nasibDefault}");

        if ($dryRun) {
            return $nasibDefault;
        }

        match ($nasibDefault) {
            'musnah' => $surat->update(['status_arsip' => 'menunggu_musnah']),
            'permanen' => $surat->update(['status_arsip' => 'permanen']),
            'dinilai_kembali' => $surat->update(['status_arsip' => 'perlu_ditinjau']),
            default => null,
        };

        return $nasibDefault;
    }

    /**
     * Surat bernasib 'permanen' tidak perlu notif (langsung final, tidak ada yang
     * harus diputuskan agendaris). Kalau pengiriman gagal, status arsip yang sudah
     * ter-update tetap aman — command tidak dibatalkan.
     */
    private function kirimNotifikasi(WhatsAppNotifier $notifier, Collection $konfirmasiMusnah, Collection $dinilaiKembali): void
    {
        try {
            if ($konfirmasiMusnah->isNotEmpty()) {
                $notifier->notifyRetensiHabis('konfirmasi_musnah', $konfirmasiMusnah);
                $this->info("Notifikasi konfirmasi pemusnahan diproses untuk {$konfirmasiMusnah->count()} surat.");
            }

            if ($dinilaiKembali->isNotEmpty()) {
                $notifier->notifyRetensiHabis('dinilai_kembali', $dinilaiKembali);
                $this->info("Notifikasi penilaian kembali diproses untuk {$dinilaiKembali->count()} surat.");
            }
        } catch (Throwable $e) {
            report($e);
            $this->error('Status arsip sudah diperbarui, tapi notifikasi WA gagal diproses: ' . $e->getMessage());
        }
    }
}