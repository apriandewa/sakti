<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabel log sinkronisasi kinerja (PRD Bab 8.4).
 * Format disamakan dengan presensi_sync_logs.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ekinerja_sync_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('unor_id', 64)->nullable()->comment('Relasi ke ekinerja_master_unor.unor_id');
            $table->string('nama_unor', 150)->nullable();
            $table->string('periode_id', 64)->nullable()->comment('Relasi ke ekinerja_referensi_periode.periode_id');
            $table->string('sync_by', 255)->default('Sistem (Otomatis)')->comment('"Sistem (Otomatis)" atau nama/ID admin');
            $table->string('status', 50)->default('berjalan')->comment('berjalan, sukses, gagal');
            $table->timestamp('waktu_mulai')->nullable();
            $table->timestamp('waktu_selesai')->nullable();
            $table->integer('jumlah_data_ditarik')->nullable();
            $table->integer('jumlah_gagal')->nullable();
            $table->text('catatan_pesan')->nullable();
            $table->timestamps();

            $table->index('unor_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ekinerja_sync_logs');
    }
};
