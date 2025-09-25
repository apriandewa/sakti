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
        Schema::create('verifikasis', function (Blueprint $table) {
            $table->uuid("id")->primary();

            // ubah jadi verifiable
            $table->string("verifiable_type")->nullable();
            $table->foreignUuid("verifiable_id")->nullable();

            $table->text("catatan")->nullable();
            $table->string("status")->nullable();

            // user yang memverifikasi
            $table->foreignUuid("user_id")->nullable()->constrained();

            $table->timestamps();
            $table->softDeletes();
        });


        Schema::table('verifikasis', function (Blueprint $table) {
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('verifikasis');
    }
};
