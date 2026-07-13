<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('presensi_sync_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('kantor_id')->nullable();
            $table->string('bulan');
            $table->string('tahun');
            $table->string('sync_by');
            $table->string('status');
            $table->timestamp('waktu_mulai')->nullable();
            $table->timestamp('waktu_selesai')->nullable();
            $table->integer('jumlah_data_ditarik')->nullable();
            $table->text('catatan_pesan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presensi_sync_logs');
    }
};
