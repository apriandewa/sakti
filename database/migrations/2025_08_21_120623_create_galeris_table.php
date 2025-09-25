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
        Schema::create('galeris', function (Blueprint $table) {
            $table->uuid("id")->primary();
			$table->string("nama")->nullable();
			$table->string("slug")->nullable();
			$table->text("desc")->nullable();
			$table->string("kategori")->nullable();
			$table->string("keterangan")->nullable();
			$table->string("status")->nullable();
			$table->foreignUuid("user_id")->nullable()->constrained();
			$table->timestamps();
			$table->softDeletes();
        });

        Schema::table('galeris', function (Blueprint $table) {
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('galeris');
    }
};
