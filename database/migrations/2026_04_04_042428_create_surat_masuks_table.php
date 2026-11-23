<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('surat_masuks', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_agenda', 50)->unique();
            $table->string('nomor_surat', 100);
            $table->date('tanggal_surat');
            $table->date('tanggal_diterima');
            $table->text('isi_ringkasan');
            $table->string('pengirim');
            $table->enum('sifat_surat', ['biasa', 'segera', 'sangat_segera', 'rahasia'])->default('biasa');
            $table->string('file_surat')->nullable();

            // Retensi & Arsip
            $table->foreignId('referensi_retensi_id')
                  ->constrained('referensi_retensis')
                  ->restrictOnDelete();
            $table->enum('status_disposisi', ['baru', 'diproses', 'selesai'])->default('baru');
            $table->enum('status_arsip', ['aktif', 'inaktif', 'perlu_ditinjau', 'menunggu_musnah', 'musnah', 'permanen'])->default('aktif');

            // Relasi ke user yang input
            $table->foreignId('dibuat_oleh')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_masuks');
    }
};