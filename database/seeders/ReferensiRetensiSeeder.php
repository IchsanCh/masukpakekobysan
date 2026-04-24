<?php

namespace Database\Seeders;

use App\Models\ReferensiRetensi;
use Illuminate\Database\Seeder;

class ReferensiRetensiSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['kode_klasifikasi' => '000', 'nama_kegiatan' => 'Umum', 'masa_aktif' => 2, 'masa_inaktif' => 3, 'nasib_akhir_default' => 'musnah', 'default_batas_waktu_hari' => 14],
            ['kode_klasifikasi' => '010', 'nama_kegiatan' => 'Urusan Dalam', 'masa_aktif' => 2, 'masa_inaktif' => 3, 'nasib_akhir_default' => 'musnah', 'default_batas_waktu_hari' => 14],
            ['kode_klasifikasi' => '060', 'nama_kegiatan' => 'Perencanaan', 'masa_aktif' => 2, 'masa_inaktif' => 5, 'nasib_akhir_default' => 'dinilai_kembali', 'default_batas_waktu_hari' => 14],
            ['kode_klasifikasi' => '090', 'nama_kegiatan' => 'Pengawasan', 'masa_aktif' => 2, 'masa_inaktif' => 5, 'nasib_akhir_default' => 'dinilai_kembali', 'default_batas_waktu_hari' => 7],
            ['kode_klasifikasi' => '500', 'nama_kegiatan' => 'Penanaman Modal', 'masa_aktif' => 3, 'masa_inaktif' => 5, 'nasib_akhir_default' => 'permanen', 'default_batas_waktu_hari' => 14],
            ['kode_klasifikasi' => '510', 'nama_kegiatan' => 'Perizinan', 'masa_aktif' => 3, 'masa_inaktif' => 5, 'nasib_akhir_default' => 'permanen', 'default_batas_waktu_hari' => 7],
            ['kode_klasifikasi' => '800', 'nama_kegiatan' => 'Kepegawaian', 'masa_aktif' => 2, 'masa_inaktif' => 5, 'nasib_akhir_default' => 'dinilai_kembali', 'default_batas_waktu_hari' => 14],
            ['kode_klasifikasi' => '900', 'nama_kegiatan' => 'Keuangan', 'masa_aktif' => 2, 'masa_inaktif' => 10, 'nasib_akhir_default' => 'permanen', 'default_batas_waktu_hari' => 7],
        ];

        foreach ($data as $item) {
            ReferensiRetensi::create($item);
        }
    }
}
