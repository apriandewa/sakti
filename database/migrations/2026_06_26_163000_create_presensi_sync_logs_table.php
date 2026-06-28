<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('presensi_sync_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->integer('tahun');
            $table->integer('bulan');
            $table->string('triggered_by');
            $table->string('status', 20); // sukses, gagal
            $table->integer('total_pegawai_synced')->default(0);
            $table->integer('total_pegawai_skipped')->default(0);
            $table->text('message')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('presensi_sync_logs');
    }
};
