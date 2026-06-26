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
        Schema::create('rapat_pesertas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('agenda_rapat_id')->constrained('agenda_rapats')->onDelete('cascade');
            $table->string('nama');
            $table->string('nip')->nullable();
            $table->string('jabatan')->nullable();
            $table->string('instansi')->nullable();
            $table->string('no_hp')->nullable();
            $table->longText('tanda_tangan')->nullable(); // Base64 signature image
            $table->timestamp('waktu_hadir')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rapat_pesertas');
    }
};
