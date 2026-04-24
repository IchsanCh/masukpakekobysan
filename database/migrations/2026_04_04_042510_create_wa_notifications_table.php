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
        Schema::create('wa_notifications', function (Blueprint $table) {
            $table->id();

            // Penerima
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();
            $table->string('no_wa_tujuan', 20);

            // Sumber notifikasi (nullable, salah satu terisi)
            $table->foreignId('surat_masuk_id')
                  ->nullable()
                  ->constrained('surat_masuks')
                  ->cascadeOnDelete();
            $table->foreignId('disposisi_id')
                  ->nullable()
                  ->constrained('disposisis')
                  ->cascadeOnDelete();
            $table->foreignId('tindak_lanjut_id')
                  ->nullable()
                  ->constrained('tindak_lanjuts')
                  ->cascadeOnDelete();

            $table->enum('tipe', [
                'surat_masuk_baru',
                'disposisi_masuk',
                'sub_disposisi',
                'tindaklanjut_selesai',
                'pengingat_deadline',
                'disposisi_ditolak',
            ]);
            $table->text('pesan');
            $table->enum('status_kirim', ['pending', 'terkirim', 'gagal'])->default('pending');
            $table->timestamp('terkirim_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wa_notifications');
    }
};
