<?php

namespace Database\Seeders;

use App\Models\ReferensiRetensi;
use Illuminate\Database\Seeder;

/**
 * Seeder Referensi Retensi Arsip — Data Testing
 *
 * Kode klasifikasi   → Perbup Kab. Pekalongan No. 10 Tahun 2023
 * Nilai retensi + ket → Perbup Kab. Pekalongan No. 63 Tahun 2024 (JRA)
 *
 * nasib_akhir_default:
 *   'musnah'          → dimusnahkan setelah masa retensi habis
 *   'permanen'        → disimpan selamanya sebagai arsip statis
 *   'dinilai_kembali' → nasib akhir campuran, lihat keterangan_nasib_akhir
 *
 * keterangan_nasib_akhir:
 *   Isi verbatim dari kolom "Keterangan" JRA bila ada pengecualian
 *   (contoh: "Musnah, Kecuali Notulen terkait Kebijakan Permanen")
 *   null jika nasib akhirnya tunggal tanpa pengecualian.
 *
 * Scope: data kecil untuk testing — 000, 500.16, 700.1, 800, 900
 */
class ReferensiRetensiSeeder extends Seeder
{
    public function run(): void
    {
        ReferensiRetensi::truncate();

        $data = [

            // ──────────────────────────────────────────────────────────────
            // I. UMUM — A. KETATAUSAHAAN (000.1)  |  JRA hal. 1–2
            // ──────────────────────────────────────────────────────────────
            [
                'kode_klasifikasi'         => '000.1.1',
                'nama_kegiatan'            => 'Telekomunikasi (Fasilitasi Telekomunikasi)',
                'masa_aktif'               => 1,
                'masa_inaktif'             => 1,
                'nasib_akhir_default'      => 'musnah',
                'keterangan_nasib_akhir'   => null,
                'default_batas_waktu_hari' => 14,
            ],
            [
                'kode_klasifikasi'         => '000.1.4',
                'nama_kegiatan'            => 'Penggunaan Fasilitas Kantor (Ruang Rapat, Kendaraan, Wisma, Rumah Dinas)',
                'masa_aktif'               => 1,
                'masa_inaktif'             => 1,
                'nasib_akhir_default'      => 'musnah',
                'keterangan_nasib_akhir'   => null,
                'default_batas_waktu_hari' => 14,
            ],
            [
                'kode_klasifikasi'         => '000.1.5',
                'nama_kegiatan'            => 'Rapat Pimpinan',
                'masa_aktif'               => 1,
                'masa_inaktif'             => 1,
                'nasib_akhir_default'      => 'dinilai_kembali',
                'keterangan_nasib_akhir'   => 'Musnah, Kecuali Notulen terkait Kebijakan Permanen',
                'default_batas_waktu_hari' => 14,
            ],
            [
                'kode_klasifikasi'         => '000.1.6',
                'nama_kegiatan'            => 'Penyediaan Konsumsi',
                'masa_aktif'               => 1,
                'masa_inaktif'             => 1,
                'nasib_akhir_default'      => 'musnah',
                'keterangan_nasib_akhir'   => null,
                'default_batas_waktu_hari' => 14,
            ],
            [
                'kode_klasifikasi'         => '000.1.8',
                'nama_kegiatan'            => 'Pemeliharaan Gedung, Taman, dan Peralatan Kantor',
                'masa_aktif'               => 1,
                'masa_inaktif'             => 1,
                'nasib_akhir_default'      => 'musnah',
                'keterangan_nasib_akhir'   => null,
                'default_batas_waktu_hari' => 14,
            ],

            // ──────────────────────────────────────────────────────────────
            // I. UMUM — B. PERLENGKAPAN (000.2)  |  JRA hal. 3
            // ──────────────────────────────────────────────────────────────
            [
                'kode_klasifikasi'         => '000.2.4',
                'nama_kegiatan'            => 'Penghapusan Barang Milik Daerah (BA, SK Panitia, Daftar BMD)',
                'masa_aktif'               => 3,
                'masa_inaktif'             => 7,
                'nasib_akhir_default'      => 'musnah',
                'keterangan_nasib_akhir'   => null,
                'default_batas_waktu_hari' => 14,
            ],
            [
                'kode_klasifikasi'         => '000.2.5',
                'nama_kegiatan'            => 'Pengelolaan Database Barang Milik Daerah',
                'masa_aktif'               => 2,
                'masa_inaktif'             => 3,
                'nasib_akhir_default'      => 'permanen',
                'keterangan_nasib_akhir'   => null,
                'default_batas_waktu_hari' => 14,
            ],

            // ──────────────────────────────────────────────────────────────
            // I. UMUM — C. PENGADAAN (000.3)  |  JRA hal. 4
            // ──────────────────────────────────────────────────────────────
            [
                'kode_klasifikasi'         => '000.3.2',
                'nama_kegiatan'            => 'Pengadaan Langsung (Kontrak, Berita Acara)',
                'masa_aktif'               => 2,
                'masa_inaktif'             => 3,
                'nasib_akhir_default'      => 'musnah',
                'keterangan_nasib_akhir'   => null,
                'default_batas_waktu_hari' => 14,
            ],
            [
                'kode_klasifikasi'         => '000.3.6',
                'nama_kegiatan'            => 'Monitoring dan Evaluasi Pengadaan',
                'masa_aktif'               => 2,
                'masa_inaktif'             => 3,
                'nasib_akhir_default'      => 'permanen',
                'keterangan_nasib_akhir'   => null,
                'default_batas_waktu_hari' => 14,
            ],

            // ──────────────────────────────────────────────────────────────
            // I. UMUM — E. KEARSIPAN (000.5)  |  JRA hal. 8–14
            // ──────────────────────────────────────────────────────────────
            [
                'kode_klasifikasi'         => '000.5.3.1',
                'nama_kegiatan'            => 'Penciptaan Arsip (Buku Registrasi Naskah Masuk/Keluar, Kartu Kendali)',
                'masa_aktif'               => 2,
                'masa_inaktif'             => 3,
                'nasib_akhir_default'      => 'musnah',
                'keterangan_nasib_akhir'   => null,
                'default_batas_waktu_hari' => 14,
            ],
            [
                'kode_klasifikasi'         => '000.5.3.4',
                'nama_kegiatan'            => 'Penggunaan Arsip Dinamis (Daftar Arsip, Bukti Peminjaman)',
                'masa_aktif'               => 1,
                'masa_inaktif'             => 1,
                'nasib_akhir_default'      => 'dinilai_kembali',
                'keterangan_nasib_akhir'   => 'Musnah, Kecuali Daftar Arsip Dinamis Berdasarkan SKKAAD Permanen',
                'default_batas_waktu_hari' => 14,
            ],

            // ──────────────────────────────────────────────────────────────
            // I. UMUM — G. PERENCANAAN PEMBANGUNAN (000.7)  |  JRA hal. 15–17
            // ──────────────────────────────────────────────────────────────
            [
                'kode_klasifikasi'         => '000.7.2.1',
                'nama_kegiatan'            => 'Rencana Pembangunan Jangka Panjang (RPJP)',
                'masa_aktif'               => 2,
                'masa_inaktif'             => 3,
                'nasib_akhir_default'      => 'permanen',
                'keterangan_nasib_akhir'   => null,
                'default_batas_waktu_hari' => 14,
            ],
            [
                'kode_klasifikasi'         => '000.7.2.8',
                'nama_kegiatan'            => 'Laporan Berkala',
                'masa_aktif'               => 1,
                'masa_inaktif'             => 1,
                'nasib_akhir_default'      => 'musnah',
                'keterangan_nasib_akhir'   => null,
                'default_batas_waktu_hari' => 7,
            ],

            // ──────────────────────────────────────────────────────────────
            // I. UMUM — H. ORGANISASI DAN TATA LAKSANA (000.8)  |  JRA hal. 17
            // ──────────────────────────────────────────────────────────────
            [
                'kode_klasifikasi'         => '000.8.3.2',
                'nama_kegiatan'            => 'Standar Pelayanan',
                'masa_aktif'               => 2,
                'masa_inaktif'             => 3,
                'nasib_akhir_default'      => 'permanen',
                'keterangan_nasib_akhir'   => null,
                'default_batas_waktu_hari' => 14,
            ],
            [
                'kode_klasifikasi'         => '000.8.3.3',
                'nama_kegiatan'            => 'Standar Operasional Prosedur (SOP)',
                'masa_aktif'               => 2,
                'masa_inaktif'             => 3,
                'nasib_akhir_default'      => 'permanen',
                'keterangan_nasib_akhir'   => null,
                'default_batas_waktu_hari' => 14,
            ],

            // ──────────────────────────────────────────────────────────────
            // V.P. PENANAMAN MODAL (500.16) — INTI DPMPTSP  |  JRA hal. 162–164
            // ──────────────────────────────────────────────────────────────
            [
                'kode_klasifikasi'         => '500.16.1',
                'nama_kegiatan'            => 'Kebijakan di Bidang Penanaman Modal',
                'masa_aktif'               => 2,
                'masa_inaktif'             => 3,
                'nasib_akhir_default'      => 'permanen',
                'keterangan_nasib_akhir'   => null,
                'default_batas_waktu_hari' => 14,
            ],
            [
                'kode_klasifikasi'         => '500.16.2',
                'nama_kegiatan'            => 'Perencanaan Penanaman Modal (Agribisnis, Manufaktur, Jasa, Infrastruktur)',
                'masa_aktif'               => 2,
                'masa_inaktif'             => 3,
                'nasib_akhir_default'      => 'musnah',
                'keterangan_nasib_akhir'   => null,
                'default_batas_waktu_hari' => 14,
            ],
            [
                'kode_klasifikasi'         => '500.16.3.1',
                'nama_kegiatan'            => 'Deregulasi Penanaman Modal',
                'masa_aktif'               => 3,
                'masa_inaktif'             => 7,
                'nasib_akhir_default'      => 'permanen',
                'keterangan_nasib_akhir'   => null,
                'default_batas_waktu_hari' => 14,
            ],
            [
                'kode_klasifikasi'         => '500.16.3.2',
                'nama_kegiatan'            => 'Pengembangan Potensi Daerah (Iklim Investasi)',
                'masa_aktif'               => 3,
                'masa_inaktif'             => 7,
                'nasib_akhir_default'      => 'permanen',
                'keterangan_nasib_akhir'   => null,
                'default_batas_waktu_hari' => 14,
            ],
            [
                'kode_klasifikasi'         => '500.16.3.3',
                'nama_kegiatan'            => 'Pemberdayaan Usaha',
                'masa_aktif'               => 2,
                'masa_inaktif'             => 3,
                'nasib_akhir_default'      => 'musnah',
                'keterangan_nasib_akhir'   => null,
                'default_batas_waktu_hari' => 14,
            ],
            [
                'kode_klasifikasi'         => '500.16.4.1',
                'nama_kegiatan'            => 'Pengembangan Promosi Penanaman Modal',
                'masa_aktif'               => 2,
                'masa_inaktif'             => 3,
                'nasib_akhir_default'      => 'permanen',
                'keterangan_nasib_akhir'   => null,
                'default_batas_waktu_hari' => 14,
            ],
            [
                'kode_klasifikasi'         => '500.16.5.1',
                'nama_kegiatan'            => 'Kerja Sama Bilateral dan Multilateral Penanaman Modal',
                'masa_aktif'               => 2,
                'masa_inaktif'             => 3,
                'nasib_akhir_default'      => 'permanen',
                'keterangan_nasib_akhir'   => null,
                'default_batas_waktu_hari' => 14,
            ],
            [
                'kode_klasifikasi'         => '500.16.6.1',
                'nama_kegiatan'            => 'Pemantauan Penanaman Modal',
                'masa_aktif'               => 2,
                'masa_inaktif'             => 3,
                'nasib_akhir_default'      => 'musnah',
                'keterangan_nasib_akhir'   => null,
                'default_batas_waktu_hari' => 14,
            ],
            [
                'kode_klasifikasi'         => '500.16.6.4',
                'nama_kegiatan'            => 'Fasilitasi Penyelesaian Masalah Penanaman Modal',
                'masa_aktif'               => 2,
                'masa_inaktif'             => 3,
                'nasib_akhir_default'      => 'permanen',
                'keterangan_nasib_akhir'   => null,
                'default_batas_waktu_hari' => 7,
            ],
            [
                'kode_klasifikasi'         => '500.16.6.6',
                'nama_kegiatan'            => 'Pencabutan/Pembatalan Perizinan Penanaman Modal',
                'masa_aktif'               => 2,
                'masa_inaktif'             => 3,
                'nasib_akhir_default'      => 'permanen',
                'keterangan_nasib_akhir'   => null,
                'default_batas_waktu_hari' => 7,
            ],
            // --- PELAYANAN PERIZINAN — paling sering dipakai DPMPTSP ---
            [
                'kode_klasifikasi'         => '500.16.7.1',
                'nama_kegiatan'            => 'Pelayanan Aplikasi (OSS, SIAP, dll)',
                'masa_aktif'               => 3,
                'masa_inaktif'             => 7,
                'nasib_akhir_default'      => 'permanen',
                'keterangan_nasib_akhir'   => null,
                'default_batas_waktu_hari' => 7,
            ],
            [
                'kode_klasifikasi'         => '500.16.7.2',
                'nama_kegiatan'            => 'Pelayanan Perizinan (Izin Usaha, IMB, SIUP, TDP, NIB, dll)',
                'masa_aktif'               => 3,
                'masa_inaktif'             => 7,
                'nasib_akhir_default'      => 'permanen',
                'keterangan_nasib_akhir'   => null,
                'default_batas_waktu_hari' => 7,
            ],
            [
                'kode_klasifikasi'         => '500.16.7.3',
                'nama_kegiatan'            => 'Pelayanan Konsultasi Perizinan',
                'masa_aktif'               => 3,
                'masa_inaktif'             => 7,
                'nasib_akhir_default'      => 'permanen',
                'keterangan_nasib_akhir'   => null,
                'default_batas_waktu_hari' => 7,
            ],
            [
                'kode_klasifikasi'         => '500.16.7.4',
                'nama_kegiatan'            => 'Pelayanan Nonperizinan (Rekomendasi, Informasi Investasi, dll)',
                'masa_aktif'               => 3,
                'masa_inaktif'             => 7,
                'nasib_akhir_default'      => 'permanen',
                'keterangan_nasib_akhir'   => null,
                'default_batas_waktu_hari' => 7,
            ],
            [
                'kode_klasifikasi'         => '500.16.7.5',
                'nama_kegiatan'            => 'Pelayanan Fasilitas Investasi',
                'masa_aktif'               => 3,
                'masa_inaktif'             => 7,
                'nasib_akhir_default'      => 'permanen',
                'keterangan_nasib_akhir'   => null,
                'default_batas_waktu_hari' => 7,
            ],

            // ──────────────────────────────────────────────────────────────
            // VIII. PENGAWASAN — A. PENGAWASAN INTERNAL (700.1)  |  JRA hal. 184–185
            // ──────────────────────────────────────────────────────────────
            [
                'kode_klasifikasi'         => '700.1.1.1',
                'nama_kegiatan'            => 'Rencana Strategis Pengawasan',
                'masa_aktif'               => 3,
                'masa_inaktif'             => 7,
                'nasib_akhir_default'      => 'permanen',
                'keterangan_nasib_akhir'   => null,
                'default_batas_waktu_hari' => 14,
            ],
            [
                'kode_klasifikasi'         => '700.1.2.1',
                'nama_kegiatan'            => 'Laporan Hasil Audit (LHA/LHP/LHPO/LHE) yang Memerlukan Tindak Lanjut',
                'masa_aktif'               => 2,
                'masa_inaktif'             => 8,
                'nasib_akhir_default'      => 'permanen',
                'keterangan_nasib_akhir'   => null,
                'default_batas_waktu_hari' => 7,
            ],
            [
                'kode_klasifikasi'         => '700.1.2.2',
                'nama_kegiatan'            => 'Laporan Hasil Audit Investigasi (LHAI) Mengandung Unsur TPK',
                'masa_aktif'               => 2,
                'masa_inaktif'             => 3,
                'nasib_akhir_default'      => 'permanen',
                'keterangan_nasib_akhir'   => null,
                'default_batas_waktu_hari' => 7,
            ],

            // ──────────────────────────────────────────────────────────────
            // IX. KEPEGAWAIAN — A. SDM (800.1)  |  JRA hal. 185–202
            // ──────────────────────────────────────────────────────────────
            [
                'kode_klasifikasi'         => '800.1.1.1',
                'nama_kegiatan'            => 'Perencanaan Kebutuhan ASN (Analisis, Pengolahan Data)',
                'masa_aktif'               => 2,
                'masa_inaktif'             => 3,
                'nasib_akhir_default'      => 'musnah',
                'keterangan_nasib_akhir'   => null,
                'default_batas_waktu_hari' => 14,
            ],
            [
                'kode_klasifikasi'         => '800.1.2.1',
                'nama_kegiatan'            => 'Formasi ASN (Usulan, Persetujuan, Penetapan)',
                'masa_aktif'               => 2,
                'masa_inaktif'             => 3,
                'nasib_akhir_default'      => 'dinilai_kembali',
                'keterangan_nasib_akhir'   => 'Musnah, Kecuali Penetapan Formasi Khusus Permanen',
                'default_batas_waktu_hari' => 14,
            ],
            [
                'kode_klasifikasi'         => '800.1.2.2',
                'nama_kegiatan'            => 'Proses Rekrutmen/Pengadaan ASN',
                'masa_aktif'               => 2,
                'masa_inaktif'             => 3,
                'nasib_akhir_default'      => 'musnah',
                'keterangan_nasib_akhir'   => null,
                'default_batas_waktu_hari' => 14,
            ],
            [
                'kode_klasifikasi'         => '800.1.3.2',
                'nama_kegiatan'            => 'Kenaikan Pangkat/Golongan/Jabatan',
                'masa_aktif'               => 1,
                'masa_inaktif'             => 1,
                'nasib_akhir_default'      => 'dinilai_kembali',
                'keterangan_nasib_akhir'   => 'Musnah, Kecuali Nota dan SK Masuk Berkas Perseorangan Permanen',
                'default_batas_waktu_hari' => 14,
            ],
            [
                'kode_klasifikasi'         => '800.1.3.3',
                'nama_kegiatan'            => 'Pengangkatan dan Pemberhentian Jabatan Struktural/Fungsional',
                'masa_aktif'               => 1,
                'masa_inaktif'             => 1,
                'nasib_akhir_default'      => 'dinilai_kembali',
                'keterangan_nasib_akhir'   => 'Musnah, Kecuali SK Masuk Berkas Perseorangan Permanen',
                'default_batas_waktu_hari' => 14,
            ],
            [
                'kode_klasifikasi'         => '800.1.4.4',
                'nama_kegiatan'            => 'Standar Kinerja Pegawai (SKP) dan Penilaian Prestasi Kerja',
                'masa_aktif'               => 1,
                'masa_inaktif'             => 1,
                'nasib_akhir_default'      => 'musnah',
                'keterangan_nasib_akhir'   => null,
                'default_batas_waktu_hari' => 14,
            ],
            [
                'kode_klasifikasi'         => '800.1.6.1',
                'nama_kegiatan'            => 'Kode Etik Pegawai',
                'masa_aktif'               => 2,
                'masa_inaktif'             => 3,
                'nasib_akhir_default'      => 'permanen',
                'keterangan_nasib_akhir'   => null,
                'default_batas_waktu_hari' => 7,
            ],
            [
                'kode_klasifikasi'         => '800.1.6.2',
                'nama_kegiatan'            => 'Disiplin Pegawai',
                'masa_aktif'               => 2,
                'masa_inaktif'             => 3,
                'nasib_akhir_default'      => 'musnah',
                'keterangan_nasib_akhir'   => null,
                'default_batas_waktu_hari' => 7,
            ],
            [
                'kode_klasifikasi'         => '800.1.6.3',
                'nama_kegiatan'            => 'Pemberhentian Dengan Hormat',
                'masa_aktif'               => 2,
                'masa_inaktif'             => 3,
                'nasib_akhir_default'      => 'dinilai_kembali',
                'keterangan_nasib_akhir'   => 'Musnah, Kecuali SK Masuk Berkas Perseorangan Permanen',
                'default_batas_waktu_hari' => 14,
            ],
            [
                'kode_klasifikasi'         => '800.1.6.6',
                'nama_kegiatan'            => 'Pensiun ASN (Administrasi, Penetapan, Pertimbangan Teknis)',
                'masa_aktif'               => 1,
                'masa_inaktif'             => 1,
                'nasib_akhir_default'      => 'dinilai_kembali',
                'keterangan_nasib_akhir'   => 'Musnah, Kecuali Pensiun Pejabat Tinggi Pratama/Madya/Utama, Pejabat Negara dan Janda/Dudanya Permanen',
                'default_batas_waktu_hari' => 14,
            ],
            [
                'kode_klasifikasi'         => '800.1.11.1',
                'nama_kegiatan'            => 'Surat Perintah Dinas / Surat Tugas',
                'masa_aktif'               => 1,
                'masa_inaktif'             => 1,
                'nasib_akhir_default'      => 'musnah',
                'keterangan_nasib_akhir'   => null,
                'default_batas_waktu_hari' => 14,
            ],
            [
                'kode_klasifikasi'         => '800.1.13.1',
                'nama_kegiatan'            => 'Berkas Perseorangan PNS',
                'masa_aktif'               => 3,
                'masa_inaktif'             => 7,
                'nasib_akhir_default'      => 'musnah',
                'keterangan_nasib_akhir'   => null,
                'default_batas_waktu_hari' => 14,
            ],
            [
                'kode_klasifikasi'         => '800.1.13.3',
                'nama_kegiatan'            => 'Berkas Perseorangan Pejabat Negara dan yang Disetarakan',
                'masa_aktif'               => 1,
                'masa_inaktif'             => 1,
                'nasib_akhir_default'      => 'permanen',
                'keterangan_nasib_akhir'   => null,
                'default_batas_waktu_hari' => 14,
            ],

            // ──────────────────────────────────────────────────────────────
            // IX. KEPEGAWAIAN — B. DIKLAT (800.2)  |  JRA hal. 196–198
            // ──────────────────────────────────────────────────────────────
            [
                'kode_klasifikasi'         => '800.2.1',
                'nama_kegiatan'            => 'Kebijakan di Bidang Diklat',
                'masa_aktif'               => 2,
                'masa_inaktif'             => 3,
                'nasib_akhir_default'      => 'permanen',
                'keterangan_nasib_akhir'   => null,
                'default_batas_waktu_hari' => 14,
            ],

            // ──────────────────────────────────────────────────────────────
            // X. KEUANGAN DAERAH (900.1)  |  JRA hal. 199–212
            // ──────────────────────────────────────────────────────────────
            [
                'kode_klasifikasi'         => '900.1.1.1',
                'nama_kegiatan'            => 'Penyusunan Prioritas Plafon Anggaran (PPA)',
                'masa_aktif'               => 2,
                'masa_inaktif'             => 3,
                'nasib_akhir_default'      => 'permanen',
                'keterangan_nasib_akhir'   => null,
                'default_batas_waktu_hari' => 14,
            ],
            [
                'kode_klasifikasi'         => '900.1.2.4',
                'nama_kegiatan'            => 'Dokumen Pelaksanaan Anggaran SKPD (DPA SKPD)',
                'masa_aktif'               => 2,
                'masa_inaktif'             => 3,
                'nasib_akhir_default'      => 'permanen',
                'keterangan_nasib_akhir'   => null,
                'default_batas_waktu_hari' => 14,
            ],
            [
                'kode_klasifikasi'         => '900.1.3.1',
                'nama_kegiatan'            => 'Surat Penyedia Dana (SPP, SPM, SP2D: UP, GU, TU, LS)',
                'masa_aktif'               => 2,
                'masa_inaktif'             => 8,
                'nasib_akhir_default'      => 'dinilai_kembali',
                'keterangan_nasib_akhir'   => 'Musnah, Kecuali Berkas terkait Fixed Asset Permanen',
                'default_batas_waktu_hari' => 7,
            ],
            [
                'kode_klasifikasi'         => '900.1.3.5',
                'nama_kegiatan'            => 'Dokumen Penatausahaan Keuangan',
                'masa_aktif'               => 2,
                'masa_inaktif'             => 8,
                'nasib_akhir_default'      => 'musnah',
                'keterangan_nasib_akhir'   => null,
                'default_batas_waktu_hari' => 7,
            ],
            [
                'kode_klasifikasi'         => '900.1.3.7',
                'nama_kegiatan'            => 'Daftar Gaji',
                'masa_aktif'               => 2,
                'masa_inaktif'             => 8,
                'nasib_akhir_default'      => 'musnah',
                'keterangan_nasib_akhir'   => null,
                'default_batas_waktu_hari' => 14,
            ],
            [
                'kode_klasifikasi'         => '900.1.3.10',
                'nama_kegiatan'            => 'Laporan Keuangan Tahunan',
                'masa_aktif'               => 2,
                'masa_inaktif'             => 8,
                'nasib_akhir_default'      => 'permanen',
                'keterangan_nasib_akhir'   => null,
                'default_batas_waktu_hari' => 7,
            ],
            [
                'kode_klasifikasi'         => '900.1.11.1',
                'nama_kegiatan'            => 'Laporan Hasil Pemeriksaan BPK atas Laporan Keuangan',
                'masa_aktif'               => 2,
                'masa_inaktif'             => 3,
                'nasib_akhir_default'      => 'permanen',
                'keterangan_nasib_akhir'   => null,
                'default_batas_waktu_hari' => 7,
            ],
            [
                'kode_klasifikasi'         => '900.1.13.1',
                'nama_kegiatan'            => 'Pajak Daerah dan Retribusi Daerah',
                'masa_aktif'               => 2,
                'masa_inaktif'             => 3,
                'nasib_akhir_default'      => 'permanen',
                'keterangan_nasib_akhir'   => null,
                'default_batas_waktu_hari' => 7,
            ],

        ];

        foreach ($data as $item) {
            ReferensiRetensi::create($item);
        }
    }
}
