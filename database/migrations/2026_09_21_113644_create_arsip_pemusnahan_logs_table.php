<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('arsip_pemusnahan_logs', function (Blueprint $table) {
            $table->id();
            // 'surat_id' bukan foreign key beneran — surat aslinya udah dihapus permanen
            // begitu baris ini dibuat, jadi ini murni jejak referensi (bukan relasi aktif).
            $table->enum('jenis', ['masuk', 'keluar']);
            $table->unsignedBigInteger('surat_id');
            $table->string('nomor_surat');
            $table->date('tanggal_surat');
            $table->text('isi_ringkasan')->nullable();
            $table->string('kode_klasifikasi')->nullable();
            $table->foreignId('disetujui_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('dimusnahkan_at');
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->index(['jenis', 'surat_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('arsip_pemusnahan_logs');
    }
};