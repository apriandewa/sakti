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
    public function up()
    {
        Schema::table('agenda_rapats', function (Blueprint $table) {
            $table->string('tipe_rapat')->default('offline')->after('tempat');
            $table->string('zoom_meeting_id')->nullable()->after('tipe_rapat');
            $table->string('zoom_password')->nullable()->after('zoom_meeting_id');
            $table->string('jenis_tujuan_surat')->default('tunggal')->after('surat_tujuan');
            $table->text('surat_tujuan_lampiran')->nullable()->after('jenis_tujuan_surat');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('agenda_rapats', function (Blueprint $table) {
            $table->dropColumn([
                'tipe_rapat',
                'zoom_meeting_id',
                'zoom_password',
                'jenis_tujuan_surat',
                'surat_tujuan_lampiran'
            ]);
        });
    }
};
