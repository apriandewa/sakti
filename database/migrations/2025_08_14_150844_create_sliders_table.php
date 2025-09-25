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
        Schema::create('sliders', function (Blueprint $table) {
            $table->uuid("id")->primary();
			$table->string("nama")->nullable();
			$table->text("desc")->nullable();
			$table->string("keterangan")->nullable();
			$table->string("status")->nullable();
			$table->foreignUuid("user_id")->nullable()->constrained();
			$table->timestamps();
			$table->softDeletes();
        });

        Schema::table('sliders', function (Blueprint $table) {
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sliders');
    }
};
