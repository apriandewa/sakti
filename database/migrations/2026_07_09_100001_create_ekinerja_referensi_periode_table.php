<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ekinerja_referensi_periode', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('periode_id', 64)->unique();
            $table->string('nama', 30);
            $table->string('tahun', 4)->nullable();
            $table->string('periode_awal', 5)->nullable();
            $table->string('periode_akhir', 5)->nullable();
            $table->string('batas_pengisian', 15)->nullable();
            $table->string('jenis_periode', 20)->nullable();
            $table->string('tipe_periodik', 20)->nullable();
            $table->string('angka_periodik', 2)->nullable();
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();

            $table->index('tahun');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ekinerja_referensi_periode');
    }
};
