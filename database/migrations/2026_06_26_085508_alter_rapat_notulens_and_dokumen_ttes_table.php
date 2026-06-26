<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::table('rapat_notulens', function (Blueprint $table) {
            $table->enum('status', ['DRAFT', 'REVISI', 'DISETUJUI', 'SELESAI'])->default('DRAFT')->after('hasil_rapat');
            $table->text('catatan_revisi')->nullable()->after('status');
            $table->foreignUuid('pimpinan_rapat_id')->nullable()->constrained('pegawais')->onDelete('set null')->after('isi_notulen');
            $table->foreignUuid('notulis_id')->nullable()->constrained('pegawais')->onDelete('set null')->after('pimpinan_rapat_id');
        });

        DB::statement("ALTER TABLE dokumen_ttes MODIFY COLUMN jenis_dokumen ENUM('undangan', 'daftar_hadir', 'notulen', 'notulen_notulis', 'notulen_pimpinan') NOT NULL");
    }

    public function down()
    {
        DB::statement("ALTER TABLE dokumen_ttes MODIFY COLUMN jenis_dokumen ENUM('undangan', 'daftar_hadir', 'notulen') NOT NULL");
        
        Schema::table('rapat_notulens', function (Blueprint $table) {
            $table->dropForeign(['pimpinan_rapat_id']);
            $table->dropForeign(['notulis_id']);
            $table->dropColumn(['status', 'catatan_revisi', 'pimpinan_rapat_id', 'notulis_id']);
        });
    }
};
