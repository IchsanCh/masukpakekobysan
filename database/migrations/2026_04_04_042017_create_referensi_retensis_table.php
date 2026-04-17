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
        Schema::create('referensi_retensi', function (Blueprint $table) {
            $table->id();
            $table->string('kode_klasifikasi', 50)->unique();
            $table->string('nama_kegiatan');
            $table->unsignedInteger('masa_aktif')->default(2);    // tahun
            $table->unsignedInteger('masa_inaktif')->default(3);  // tahun
            $table->enum('nasib_akhir_default', ['Musnah', 'Permanen', 'Statis'])->default('Musnah');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referensi_retensis');
    }
};
