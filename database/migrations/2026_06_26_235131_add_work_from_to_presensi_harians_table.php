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
        Schema::table('presensi_harians', function (Blueprint $table) {
            $table->string('work_from_masuk', 10)->nullable()->after('jam_keluar');
            $table->string('work_from_keluar', 10)->nullable()->after('work_from_masuk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('presensi_harians', function (Blueprint $table) {
            $table->dropColumn(['work_from_masuk', 'work_from_keluar']);
        });
    }
};
