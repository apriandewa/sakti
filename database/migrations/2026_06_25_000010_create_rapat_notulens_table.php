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
        Schema::create('rapat_notulens', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('agenda_rapat_id')->constrained('agenda_rapats')->onDelete('cascade');
            $table->text('isi_notulen');
            $table->string('pimpinan_rapat');
            $table->string('notulis');
            $table->text('hasil_rapat')->nullable();
            $table->foreignUuid('user_id')->nullable()->constrained('users')->nullOnDelete();
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
        Schema::dropIfExists('rapat_notulens');
    }
};
