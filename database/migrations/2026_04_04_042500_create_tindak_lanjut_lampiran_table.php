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
        Schema::create('tindak_lanjut_lampirans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tindak_lanjut_id')
                  ->constrained('tindak_lanjuts')
                  ->cascadeOnDelete();
            $table->string('nama_file');
            $table->string('file_path');
            $table->string('mime_type')->nullable();
            $table->unsignedInteger('ukuran_bytes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tindak_lanjut_lampirans');
    }
};
