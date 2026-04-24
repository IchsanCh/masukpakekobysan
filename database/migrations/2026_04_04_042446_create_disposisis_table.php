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
        Schema::create('disposisis', function (Blueprint $table) {
            $table->id();

            $table->foreignId('surat_masuk_id')
                  ->constrained('surat_masuks')
                  ->cascadeOnDelete();

            // Disposisi berantai
            $table->foreignId('parent_id')
                  ->nullable()
                  ->constrained('disposisis')
                  ->nullOnDelete();

            // Dari siapa
            $table->foreignId('dari_user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            // Tujuan
            $table->enum('tipe_tujuan', ['unit', 'personal']);
            $table->foreignId('unit_id')
                  ->nullable()
                  ->constrained('units')
                  ->nullOnDelete();
            $table->foreignId('kepada_user_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->text('instruksi');
            $table->date('batas_waktu')->nullable();
            $table->enum('status', ['menunggu', 'diterima', 'diproses', 'selesai', 'ditolak'])->default('menunggu');
            $table->text('alasan_ditolak')->nullable();
            $table->timestamp('dibaca_at')->nullable();
            $table->timestamp('diselesaikan_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disposisis');
    }
};
