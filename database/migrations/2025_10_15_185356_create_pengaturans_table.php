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
        Schema::create('pengaturans', function (Blueprint $table) {
            $table->uuid("id")->primary();
			$table->string("judul")->nullable();
			$table->string("subjudul")->nullable();
			$table->text("deskripsi")->nullable();
			$table->text("alamat")->nullable();
			$table->string("telepon")->nullable();
			$table->string("email")->nullable();
			$table->text("peta")->nullable();
			$table->string("facebook")->nullable();
			$table->string("instagram")->nullable();
			$table->string("twiter")->nullable();
			$table->string("tiktok")->nullable();
			$table->string("youtube")->nullable();
			$table->string("call_center")->nullable();
			$table->foreignUuid("user_id")->nullable()->constrained();
			$table->timestamps();
			$table->softDeletes();
        });

        Schema::table('pengaturans', function (Blueprint $table) {
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pengaturans');
    }
};
