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
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('nama_unit');
            $table->string('singkatan')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Satu user cuma punya satu unit & satu peran (gak ada rangkap jabatan),
        // jadi ditaruh langsung di sini sebagai kolom, bukan pivot table.
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('unit_id')->nullable()->after('id')->constrained('units')->nullOnDelete();
            $table->enum('peran', ['agendaris', 'pimpinan', 'sekretariat', 'kabid', 'staf'])
                  ->default('staf')
                  ->after('unit_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('unit_id');
            $table->dropColumn('peran');
        });

        Schema::dropIfExists('units');
    }
};