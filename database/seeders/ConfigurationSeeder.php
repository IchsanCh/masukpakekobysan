<?php

namespace Database\Seeders;

use App\Models\Configuration;
use Illuminate\Database\Seeder;

class ConfigurationSeeder extends Seeder
{
    public function run(): void
    {
        $configs = [
            // Fonnte
            [
                'key' => 'fonnte_token',
                'value' => '',
                'group' => 'fonnte',
                'description' => 'API token dari Fonnte untuk pengiriman WhatsApp',
            ],
            [
                'key' => 'fonnte_sender',
                'value' => '',
                'group' => 'fonnte',
                'description' => 'Nomor WhatsApp pengirim (terdaftar di Fonnte)',
            ],

            // WA Templates
            [
                'key' => 'wa_template_surat_masuk_baru',
                'value' => "Halo {nama},\n\nAda surat masuk baru dengan nomor *{nomor_surat}* dari *{pengirim}*.\nSifat: {sifat_surat}\n\nSilakan cek sistem untuk detail lebih lanjut.",
                'group' => 'wa_template',
                'description' => 'Template notifikasi surat masuk baru',
            ],
            [
                'key' => 'wa_template_disposisi_masuk',
                'value' => "Halo {nama},\n\nAnda menerima disposisi dari *{dari}* untuk surat nomor *{nomor_surat}*.\nInstruksi: {instruksi}\nBatas waktu: {batas_waktu}\n\nSegera ditindaklanjuti.",
                'group' => 'wa_template',
                'description' => 'Template notifikasi disposisi masuk',
            ],
            [
                'key' => 'wa_template_sub_disposisi',
                'value' => "Halo {nama},\n\nAnda menerima sub-disposisi dari *{dari}* untuk surat nomor *{nomor_surat}*.\nInstruksi: {instruksi}\nBatas waktu: {batas_waktu}",
                'group' => 'wa_template',
                'description' => 'Template notifikasi sub-disposisi',
            ],
            [
                'key' => 'wa_template_tindaklanjut_selesai',
                'value' => "Halo {nama},\n\nDisposisi untuk surat *{nomor_surat}* telah ditindaklanjuti oleh *{pelaksana}*.\nKeterangan: {keterangan}",
                'group' => 'wa_template',
                'description' => 'Template notifikasi tindak lanjut selesai',
            ],
            [
                'key' => 'wa_template_pengingat_deadline',
                'value' => "Halo {nama},\n\n⚠️ *Pengingat*: Disposisi untuk surat *{nomor_surat}* belum ditindaklanjuti.\nBatas waktu: *{batas_waktu}*\nDari: {dari}\n\nSegera selesaikan.",
                'group' => 'wa_template',
                'description' => 'Template pengingat deadline disposisi',
            ],
            [
                'key' => 'wa_template_disposisi_ditolak',
                'value' => "Halo {nama},\n\nDisposisi untuk surat *{nomor_surat}* ditolak oleh *{penolak}*.\nAlasan: {alasan}",
                'group' => 'wa_template',
                'description' => 'Template notifikasi disposisi ditolak',
            ],

            // Scheduler
            [
                'key' => 'pengingat_jam_kirim',
                'value' => '07:00',
                'group' => 'scheduler',
                'description' => 'Jam pengiriman pengingat harian (format HH:mm)',
            ],
            [
                'key' => 'pengingat_aktif',
                'value' => '1',
                'group' => 'scheduler',
                'description' => 'Aktifkan pengingat harian (1 = aktif, 0 = nonaktif)',
            ],
        ];

        foreach ($configs as $config) {
            Configuration::updateOrCreate(
                ['key' => $config['key']],
                $config,
            );
        }
    }
}
