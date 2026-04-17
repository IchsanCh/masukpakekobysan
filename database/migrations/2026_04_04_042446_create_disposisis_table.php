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

            // Bisa dari surat masuk (disposisi umumnya dari surat masuk)
            $table->foreignId('surat_masuk_id')
                  ->nullable()
                  ->constrained('surat_masuks')
                  ->cascadeOnDelete();

            // Disposisi berantai: opsional parent disposisi
            $table->foreignId('parent_id')
                  ->nullable()
                  ->constrained('disposisis')
                  ->nullOnDelete();

            // Dari siapa
            $table->foreignId('dari_user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            // Tipe tujuan: ke unit (broadcast) atau personal
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
            $table->enum('status', ['pending', 'dibaca', 'diproses', 'selesai'])->default('pending');
            $table->timestamp('dibaca_at')->nullable();
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
