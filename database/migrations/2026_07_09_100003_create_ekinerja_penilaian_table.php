<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ekinerja_penilaian', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('bkn_id', 64)->nullable();
            $table->string('jenis', 10)->nullable();

            $table->string('nip', 18);
            $table->string('nama', 150)->nullable();

            $table->date('periode_awal_skp')->nullable();
            $table->date('periode_akhir_skp')->nullable();

            $table->string('skp_unor_id', 64)->nullable();
            $table->string('skp_unor', 150)->nullable();
            $table->string('skp_unor_induk', 150)->nullable();
            $table->string('skp_jabatan', 150)->nullable();
            $table->string('skp_jenis_jabatan', 5)->nullable();
            $table->boolean('is_skp_plt_plh_pjb')->default(false);

            $table->string('hasil_kerja', 30)->nullable();
            $table->string('perilaku_kerja', 30)->nullable();
            $table->string('hasil_akhir', 30)->nullable();

            $table->string('pegawai_atasan_id', 64)->nullable();
            $table->string('pegawai_atasan_nip', 18)->nullable();
            $table->string('pegawai_atasan_nama', 150)->nullable();
            $table->string('pegawai_atasan_unor_id', 64)->nullable();
            $table->string('pegawai_atasan_unor', 150)->nullable();
            $table->string('pegawai_atasan_jabatan', 150)->nullable();
            $table->string('pegawai_atasan_golru', 10)->nullable();

            $table->dateTime('waktu_dinilai')->nullable();
            $table->string('pegawai_penilai_id', 64)->nullable();
            $table->unsignedSmallInteger('tahun_skp')->nullable();
            $table->string('skp_id', 64)->nullable();
            $table->string('periode_id', 64);
            $table->string('skp_penilaian_id', 64)->nullable();
            $table->string('golru', 10)->nullable();
            $table->string('jenis_pegawai', 10)->nullable();

            $table->json('raw_response')->nullable();
            $table->enum('source', ['frontend_search', 'backend_sync', 'import'])->default('frontend_search');
            $table->timestamp('synced_at')->nullable();

            $table->timestamps();

            $table->unique(['nip', 'periode_id']);
            $table->index('skp_unor_id');
            $table->index('periode_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ekinerja_penilaian');
    }
};
