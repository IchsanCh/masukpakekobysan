<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('arsip_keputusan_logs', function (Blueprint $table) {
            $table->id();
            // Jejak keputusan akhir arsip. Murni snapshot, bukan relasi: untuk 'musnah' baris
            // suratnya sudah dihapus permanen begitu log ini dibuat. Identitas surat = jenis + nomor_surat.
            $table->enum('jenis', ['masuk', 'keluar']);
            // 'musnah'   = dihapus permanen setelah approval
            // 'permanen' = surat tetap ada, ditetapkan kekal dari tab Perlu Ditinjau
            $table->enum('keputusan', ['musnah', 'permanen']);
            $table->string('nomor_surat');
            $table->date('tanggal_surat');
            $table->text('isi_ringkasan')->nullable();
            $table->string('kode_klasifikasi')->nullable();
            $table->foreignId('diputuskan_oleh')->nullable()->constrained('users')->nullOnDelete();
            // created_at = waktu keputusan dieksekusi.
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('arsip_keputusan_logs');
    }
};