<?php

namespace App\Console\Commands;

use App\Models\SuratKeluar;
use App\Models\SuratMasuk;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;

class CekMasaRetensiArsip extends Command
{
    protected $signature = 'arsip:cek-retensi {--dry-run : Cuma tampilkan apa yang AKAN diproses, tanpa beneran eksekusi}';

    protected $description = 'Cek surat masuk & keluar yang masa retensinya (masa_aktif + masa_inaktif) sudah habis, lalu tandai nasib akhirnya';

    public function handle(): int
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

        foreach ($suratMasuk as $surat) {
            $this->prosesNasibAkhir($surat, 'surat masuk', $dryRun);
        }

        foreach ($suratKeluar as $surat) {
            $this->prosesNasibAkhir($surat, 'surat keluar', $dryRun);
        }

        return self::SUCCESS;
    }

    /**
     * Ambil surat yang: masih aktif/inaktif (belum final), punya referensi retensi
     * (butuh masa_aktif+masa_inaktif+nasib_akhir_default buat diproses), dan
     * tanggal_surat + (masa_aktif + masa_inaktif) tahun sudah lewat hari ini.
     */
    private function kandidatRetensiHabis(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Support\Collection
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
     */
    private function prosesNasibAkhir(Model $surat, string $label, bool $dryRun): void
    {
        $nasibDefault = $surat->referensiRetensi->nasib_akhir_default;

        $this->line("- [{$label}] {$surat->nomor_surat} -> nasib_akhir_default: {$nasibDefault}");

        if ($dryRun) {
            return;
        }

        match ($nasibDefault) {
            'musnah' => $surat->update(['status_arsip' => 'menunggu_musnah']),
            'permanen' => $surat->update(['status_arsip' => 'permanen', 'nasib_akhir' => 'permanen']),
            'dinilai_kembali' => $surat->update(['status_arsip' => 'perlu_ditinjau']),
            default => null,
        };
    }
}