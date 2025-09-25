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
        Schema::create('statistiks', function (Blueprint $table) {
            $table->uuid("id")->primary();
			$table->year("tahun")->nullable();
			$table->bigInteger("pemohon")->nullable();
			$table->bigInteger("diminta")->nullable();
			$table->bigInteger("diberikan")->nullable();
			$table->bigInteger("ditolak")->nullable();
			$table->string("keterangan")->nullable();
			$table->foreignUuid("user_id")->nullable()->constrained();
			$table->timestamps();
			$table->softDeletes();
        });

        Schema::table('statistiks', function (Blueprint $table) {
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('statistiks');
    }
};
