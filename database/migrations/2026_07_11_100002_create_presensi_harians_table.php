<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Tabel presensi_harians — sudah ada, migration ini sebagai dokumentasi/referensi.
 * Akan berjalan sebagai no-op jika tabel sudah ada dengan kolom yang benar.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('presensi_harians')) {
            Schema::create('presensi_harians', function (Blueprint $table) {
                $table->uuid('id')->primary()->default(DB::raw('(UUID())'));
                $table->uuid('pegawai_id')->comment('Relasi ke tabel pegawais');
                $table->date('tanggal');
                $table->time('jam_masuk')->nullable();
                $table->time('jam_keluar')->nullable();
                $table->string('status_kehadiran', 20)->nullable()->comment('HN, TK, CT, DL, IZIN, LN, WFH');
                $table->string('kategori_terlambat', 10)->nullable();
                $table->integer('menit_terlambat')->default(0);
                $table->decimal('potongan_terlambat', 5, 2)->default(0);
                $table->string('kategori_pulang_cepat', 10)->nullable();
                $table->integer('menit_pulang_cepat')->default(0);
                $table->decimal('potongan_pulang_cepat', 5, 2)->default(0);
                $table->decimal('total_potongan', 5, 2)->default(0);
                $table->string('work_from_masuk', 20)->nullable();
                $table->string('work_from_keluar', 20)->nullable();
                $table->text('keterangan')->nullable();
                $table->boolean('is_sync')->default(false);
                $table->timestamp('synced_at')->nullable();
                $table->timestamps();

                $table->foreign('pegawai_id')->references('id')->on('pegawais')->onDelete('cascade');
                $table->unique(['pegawai_id', 'tanggal'], 'unique_presensi_per_hari');
                $table->index(['pegawai_id', 'tanggal']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('presensi_harians');
    }
};
