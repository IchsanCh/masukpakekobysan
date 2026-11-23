<?php

namespace App\Console\Commands;

use App\Models\SuratKeluar;
use App\Models\SuratMasuk;
use App\Services\WhatsAppNotifier;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Throwable;

class CekMasaRetensiArsip extends Command
{
    protected $signature = 'arsip:cek-retensi {--dry-run : Cuma tampilkan apa yang AKAN diproses, tanpa beneran eksekusi}';

    protected $description = 'Cek surat masuk & keluar yang masa retensinya (masa_aktif + masa_inaktif) sudah habis, lalu tandai nasib akhirnya dan kabari agendaris via WhatsApp';

    public function handle(WhatsAppNotifier $notifier): int
    {
        $dryRun = $this->option('dry-run');

        $suratMasuk = $this->kandidatRetensiHabis(SuratMasuk::query());
        $suratKeluar = $this->kandidatRetensiHabis(SuratKeluar::query());

        $total = $suratMasuk->count() + $suratKeluar->count();

        if ($total === 0) {
            $this->info('Tidak ada surat yang masa retensinya habis saat ini.');

            return self::SUCCESS;
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
            $this->line('Dry-run: status tidak diubah dan notifikasi WA tidak dikirim.');

            return self::SUCCESS;
        }

        $this->kirimNotifikasi($notifier, $konfirmasiMusnah, $dinilaiKembali);

        return self::SUCCESS;
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
     * (lihat ArsipPemusnahanService::musnahkan()).
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