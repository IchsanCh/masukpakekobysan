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
        Schema::create('surat_keluars', function (Blueprint $table) {
            $table->id();
            $table->string('kode_surat', 50);                       // kode klasifikasi, misal: 800, 060
            $table->unsignedInteger('nomor_urut');                   // integer untuk kalkulasi urutan
            $table->string('nomor_surat', 255);                     // hasil generate: "800/001/2026"
            $table->date('tanggal_surat');
            $table->text('isi_ringkasan');
            $table->string('kepada');
            $table->string('pengelola')->nullable();                 // nama pengelola / bidang pengirim
            $table->string('lampiran')->nullable();
            $table->string('file_surat')->nullable();

            // Retensi & Arsip
            $table->foreignId('referensi_retensi_id')
                  ->nullable()
                  ->constrained('referensi_retensi')
                  ->nullOnDelete();
            $table->unsignedInteger('retensi_tahun')->default(2);
            $table->enum('status_arsip', ['Aktif', 'Inaktif', 'Musnah', 'Permanen'])
                  ->default('Aktif');
            $table->enum('nasib_akhir', ['Musnah', 'Permanen', 'Statis'])->nullable();

            // Relasi ke operator yang input
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained()
                  ->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_keluars');
    }
};
