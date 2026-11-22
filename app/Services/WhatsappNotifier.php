<?php

namespace App\Services;

use App\Jobs\SendWhatsAppNotification;
use App\Models\Configuration;
use App\Models\Disposisi;
use App\Models\SuratMasuk;
use App\Models\User;
use App\Models\WaNotification;
use Illuminate\Support\Collection;

class WhatsAppNotifier
{
    /** Lebih dari ini (per jenis notif) -> kirim satu pesan ringkasan, bukan satu per surat. */
    private const RETENSI_BATAS_RINGKAS = 3;

    /** Maksimal nomor surat yang ditampilkan di pesan ringkasan. */
    private const RETENSI_MAKS_DAFTAR = 5;

    /**
     * Surat masuk baru diinput agendaris -> kirim ke SEMUA pimpinan (semua unit).
     */
    public function notifySuratMasukBaru(SuratMasuk $suratMasuk): void
    {
        $template = Configuration::waTemplate('surat_masuk_baru');

        if (empty($template)) {
            return;
        }

        $pimpinanList = User::where('peran', 'pimpinan')->where('is_active', true)->get();

        foreach ($pimpinanList as $pimpinan) {
            $pesan = $this->render($template, [
                'nama' => $pimpinan->name,
                'nomor_surat' => $suratMasuk->nomor_surat,
                'pengirim' => $suratMasuk->pengirim,
                'sifat_surat' => $suratMasuk->sifat_surat,
            ]);

            $this->dispatch($pimpinan, 'surat_masuk_baru', $pesan, suratMasukId: $suratMasuk->id);
        }
    }

    /**
     * Disposisi baru dibuat (root ATAU sub/hasil forward) -> kirim ke target
     * (personal, atau semua user aktif di unit tujuan). Tipe template otomatis
     * dipilih: 'disposisi_masuk' untuk root, 'sub_disposisi' kalau ini hasil
     * forward (punya parent_id).
     */
    public function notifyDisposisi(Disposisi $disposisi): void
    {
        $tipe = $disposisi->parent_id ? 'sub_disposisi' : 'disposisi_masuk';
        $template = Configuration::waTemplate($tipe);

        if (empty($template)) {
            return;
        }

        foreach ($this->resolveTargets($disposisi) as $target) {
            $pesan = $this->render($template, [
                'nama' => $target->name,
                'dari' => $disposisi->dariUser->name,
                'nomor_surat' => $disposisi->suratMasuk->nomor_surat ?? '-',
                'instruksi' => $disposisi->instruksi,
                'batas_waktu' => $disposisi->batas_waktu?->format('d M Y H:i') ?? 'Tidak ada',
            ]);

            $this->dispatch($target, $tipe, $pesan, disposisiId: $disposisi->id);
        }
    }

    /**
     * Target penerima notif disposisi: kalau personal, ya orangnya itu sendiri.
     * Kalau unit, semua user aktif yang ada di unit tujuan itu.
     */
    private function resolveTargets(Disposisi $disposisi): Collection
    {
        if ($disposisi->tipe_tujuan === 'personal') {
            return $disposisi->kepadaUser ? collect([$disposisi->kepadaUser]) : collect();
        }

        return User::where('unit_id', $disposisi->unit_id)->where('is_active', true)->get();
    }

    /**
     * Disposisi ditandai selesai -> kabari orang yang ngirim/assign disposisi
     * ini (dariUser). Dipake dari 2 jalur: tindak lanjut staf yang otomatis
     * nyelesaiin (dari TindakLanjutController), ATAU klik manual tombol
     * "Tandai Selesai" oleh pimpinan/kabid/sekretariat/agendaris (dari
     * DisposisiController::updateStatus) — makanya $tindakLanjutId opsional.
     */
    public function notifyDisposisiSelesai(
        Disposisi $disposisi,
        User $pelaksana,
        string $keterangan,
        ?int $tindakLanjutId = null,
    ): void {
        $template = Configuration::waTemplate('tindaklanjut_selesai');

        if (empty($template) || ! $disposisi->dariUser) {
            return;
        }

        $pesan = $this->render($template, [
            'nama' => $disposisi->dariUser->name,
            'nomor_surat' => $disposisi->suratMasuk->nomor_surat ?? '-',
            'pelaksana' => $pelaksana->name,
            'keterangan' => $keterangan,
        ]);

        $this->dispatch(
            $disposisi->dariUser,
            'tindaklanjut_selesai',
            $pesan,
            disposisiId: $disposisi->id,
            tindakLanjutId: $tindakLanjutId,
        );
    }

    /**
     * Disposisi yang udah lewat batas_waktu dan belum selesai -> kirim
     * pengingat ke target yang sama kayak notifyDisposisi() (personal/unit).
     * Dipanggil terjadwal harian lewat command `pengingat:deadline`.
     */
    public function notifyPengingatDeadline(Disposisi $disposisi): void
    {
        $template = Configuration::waTemplate('pengingat_deadline');

        if (empty($template)) {
            return;
        }

        foreach ($this->resolveTargets($disposisi) as $target) {
            $pesan = $this->render($template, [
                'nama' => $target->name,
                'nomor_surat' => $disposisi->suratMasuk->nomor_surat ?? '-',
                'batas_waktu' => $disposisi->batas_waktu?->format('d M Y H:i') ?? '-',
                'dari' => $disposisi->dariUser->name,
            ]);

            $this->dispatch($target, 'pengingat_deadline', $pesan, disposisiId: $disposisi->id);
        }
    }

    /**
     * Surat yang masa retensinya (masa_aktif + masa_inaktif) sudah habis -> kabari
     * agendaris, karena merekalah yang memproses halaman Nasib Akhir Arsip.
     *
     * $tipe: 'konfirmasi_musnah' (menunggu persetujuan pemusnahan) atau
     *        'dinilai_kembali' (perlu ditinjau ulang nasib akhirnya).
     * $items: Collection berisi ['surat' => SuratMasuk|SuratKeluar, 'jenis' => 'masuk'|'keluar'].
     *
     * Sedikit surat (<= RETENSI_BATAS_RINGKAS) dikirim satu pesan per surat. Kalau
     * lebih banyak, digabung jadi satu pesan ringkasan per agendaris supaya WA
     * tidak kebanjiran.
     */
    public function notifyRetensiHabis(string $tipe, Collection $items): void
    {
        if ($items->isEmpty()) {
            return;
        }

        $agendarisList = User::where('peran', 'agendaris')->where('is_active', true)->get();

        if ($agendarisList->isEmpty()) {
            return;
        }

        $tab = $tipe === 'konfirmasi_musnah' ? 'approval' : 'tinjau';

        if ($items->count() > self::RETENSI_BATAS_RINGKAS) {
            $this->notifyRetensiRingkasan($tipe, $tab, $items, $agendarisList);

            return;
        }

        $template = Configuration::waTemplate($tipe);

        if (empty($template)) {
            return;
        }

        foreach ($items as $item) {
            $surat = $item['surat'];

            foreach ($agendarisList as $agendaris) {
                $pesan = $this->render($template, [
                    'nama' => $agendaris->name,
                    'jenis' => $item['jenis'] === 'keluar' ? 'surat keluar' : 'surat masuk',
                    'nomor_surat' => $surat->nomor_surat,
                    'tanggal_surat' => $surat->tanggal_surat?->format('d M Y') ?? '-',
                    'kode_klasifikasi' => $surat->referensiRetensi?->kode_klasifikasi ?? '-',
                    'link' => route('arsip.index', ['jenis' => $item['jenis'], 'tab' => $tab]),
                ]);

                $this->dispatch($agendaris, $tipe, $pesan);
            }
        }
    }

    private function notifyRetensiRingkasan(string $tipe, string $tab, Collection $items, Collection $agendarisList): void
    {
        $template = Configuration::waTemplate($tipe . '_ringkasan');

        if (empty($template)) {
            return;
        }

        $daftar = $items->take(self::RETENSI_MAKS_DAFTAR)
            ->map(fn (array $item) => '- [' . ($item['jenis'] === 'keluar' ? 'Keluar' : 'Masuk') . '] ' . $item['surat']->nomor_surat)
            ->implode("\n");

        $sisa = $items->count() - self::RETENSI_MAKS_DAFTAR;

        if ($sisa > 0) {
            $daftar .= "\n- ...dan {$sisa} surat lainnya";
        }

        // Halaman Arsip menampilkan satu jenis surat per tab; arahkan ke jenis yang ada.
        $jenisLink = $items->contains(fn (array $item) => $item['jenis'] === 'masuk') ? 'masuk' : 'keluar';

        foreach ($agendarisList as $agendaris) {
            $pesan = $this->render($template, [
                'nama' => $agendaris->name,
                'jumlah' => $items->count(),
                'daftar_surat' => $daftar,
                'link' => route('arsip.index', ['jenis' => $jenisLink, 'tab' => $tab]),
            ]);

            $this->dispatch($agendaris, $tipe, $pesan);
        }
    }

    /**
     * Ganti placeholder {xxx} di template dengan data asli. Placeholder yang gak
     * dikenal dibiarkan apa adanya (gak bikin error).
     */
    private function render(string $template, array $data): string
    {
        return preg_replace_callback('/\{(\w+)\}/', function ($m) use ($data) {
            return $data[$m[1]] ?? $m[0];
        }, $template);
    }

    /**
     * Catat ke wa_notifications (buat riwayat/audit) lalu dispatch job buat
     * beneran ngirim via Fonnte di background (queue) — biar gak nge-block
     * request utama (bikin surat/disposisi) nunggu Fonnte respon.
     */
    private function dispatch(
        User $user,
        string $tipe,
        string $pesan,
        ?int $suratMasukId = null,
        ?int $disposisiId = null,
        ?int $tindakLanjutId = null,
    ): void {
        if (empty($user->no_wa)) {
            return;
        }

        $log = WaNotification::create([
            'user_id' => $user->id,
            'no_wa_tujuan' => $user->no_wa,
            'surat_masuk_id' => $suratMasukId,
            'disposisi_id' => $disposisiId,
            'tindak_lanjut_id' => $tindakLanjutId,
            'tipe' => $tipe,
            'pesan' => $pesan,
            'status_kirim' => 'pending',
        ]);

        // Kirim beneran + retry-nya ditangani job di queue (SendWhatsAppNotification),
        // biar request utama (bikin surat/disposisi) gak nunggu Fonnte respon.
        SendWhatsAppNotification::dispatch($log);
    }
}