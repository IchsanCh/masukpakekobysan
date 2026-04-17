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
            $table->string('kode_surat', 50)->nullable();           // kode klasifikasi arsip
            $table->string('nomor_surat', 100);                     // isi manual dari pengirim
            $table->date('tanggal_surat');
            $table->date('tanggal_diterima');
            $table->text('isi_ringkasan');
            $table->string('pengirim');
            $table->string('sifat_surat', 50)->nullable();          // Biasa / Penting / Rahasia / Segera
            $table->string('file_surat')->nullable();               // path file upload

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
        Schema::dropIfExists('surat_masuks');
    }
};
