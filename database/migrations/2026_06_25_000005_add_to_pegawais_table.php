<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Tambahkan kolom kantor_id dan nama_kantor ke tabel pegawais yang sudah ada,
 * untuk mendukung integrasi presensi multi-kantor (Simpegnas BKN).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pegawais', function (Blueprint $table) {
            if (! Schema::hasColumn('pegawais', 'kantor_id')) {
                $table->string('kantor_id', 100)->nullable()->after('status')
                    ->comment('UUID kantor dari API Simpegnas');
            }
            if (! Schema::hasColumn('pegawais', 'nama_kantor')) {
                $table->string('nama_kantor')->nullable()->after('kantor_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pegawais', function (Blueprint $table) {
            $table->dropColumnIfExists('kantor_id');
            $table->dropColumnIfExists('nama_kantor');
        });
    }
};
