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
        Schema::create('tindak_lanjuts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('disposisi_id')
                  ->constrained('disposisis')
                  ->cascadeOnDelete();
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();
            $table->text('keterangan');
            $table->string('file_bukti')->nullable();               // path file bukti upload
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tindak_lanjuts');
    }
};
