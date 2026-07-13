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
        Schema::create('jabatans', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->foreignUuid("parent_id")->nullable()->constrained('jabatans')->cascadeOnDelete();
            $table->string("nama");
            $table->text("desc")->nullable();
            $table->text("keterangan")->nullable();
            $table->string("status")->default('aktif');
            $table->foreignUuid("user_id")->nullable()->constrained('users')->nullOnDelete();
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
        Schema::dropIfExists('jabatans');
    }
};
