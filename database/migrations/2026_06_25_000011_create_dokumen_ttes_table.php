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
        Schema::create('dokumen_ttes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('agenda_rapat_id')->constrained('agenda_rapats')->onDelete('cascade');
            $table->enum('jenis_dokumen', ['undangan', 'daftar_hadir', 'notulen']);
            $table->foreignUuid('pegawai_id')->constrained('pegawais')->onDelete('cascade');
            $table->string('signed_file')->nullable();
            $table->string('original_file')->nullable();
            $table->enum('status', ['pending', 'signed', 'failed'])->default('pending');
            $table->text('bsre_response')->nullable();
            $table->timestamp('signed_at')->nullable();
            $table->timestamps();

            $table->unique(['agenda_rapat_id', 'jenis_dokumen'], 'unique_dokumen_tte');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dokumen_ttes');
    }
};
