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
        Schema::create('kategoris', function (Blueprint $table) {
            $table->uuid("id")->primary();
			$table->string("nama")->nullable();
			$table->string("slug")->nullable();
			$table->text("desc")->nullable();
			$table->string("ikon")->nullable();
			$table->string("status")->nullable();
			$table->foreignUuid("user_id")->nullable()->constrained();
            $table->foreignUuid("parent_id")->nullable()->constrained('kategoris')->nullOnDelete();
			$table->timestamps();
			$table->softDeletes();
        });

        Schema::table('kategoris', function (Blueprint $table) {
            $table->foreign("parent_id")->references("id")->on("kategoris")->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kategoris');
    }
};
