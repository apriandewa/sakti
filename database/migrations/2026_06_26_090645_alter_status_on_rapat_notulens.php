<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::statement("ALTER TABLE rapat_notulens MODIFY COLUMN status ENUM('DRAFT', 'MENUNGGU_PERSETUJUAN', 'REVISI', 'DISETUJUI', 'SELESAI') NOT NULL DEFAULT 'DRAFT'");
        DB::table('rapat_notulens')->where('status', '')->update(['status' => 'MENUNGGU_PERSETUJUAN']);
    }

    public function down()
    {
        DB::table('rapat_notulens')->where('status', 'MENUNGGU_PERSETUJUAN')->update(['status' => 'DRAFT']);
        DB::statement("ALTER TABLE rapat_notulens MODIFY COLUMN status ENUM('DRAFT', 'REVISI', 'DISETUJUI', 'SELESAI') NOT NULL DEFAULT 'DRAFT'");
    }
};
