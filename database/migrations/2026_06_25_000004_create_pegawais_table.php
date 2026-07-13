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
        Schema::create('pegawais', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->foreignUuid("user_id")->nullable()->constrained('users')->nullOnDelete();
            $table->string("nama");
            $table->string("gelar_depan")->nullable();
            $table->string("gelar_belakang")->nullable();
            $table->string("nip")->nullable()->unique();
            $table->string("nik")->nullable()->unique();
            $table->foreignUuid("status_id")->nullable()->constrained('statuses')->nullOnDelete();
            $table->foreignUuid("pangkat_id")->nullable()->constrained('pangkats')->nullOnDelete();
            $table->foreignUuid("jabatan_jenis_id")->nullable()->constrained('jabatans')->nullOnDelete();
            $table->foreignUuid("jabatan_nama_id")->nullable()->constrained('jabatans')->nullOnDelete();
            $table->foreignUuid("bidang_id")->nullable()->constrained('pages')->nullOnDelete();
            $table->string("jenis_kelamin");
            $table->string("agama");
            $table->string("pendidikan_terakhir");
            $table->text("alamat");
            $table->string("telpon");
            $table->string("status")->default('aktif');
            $table->string("periode");
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
        Schema::dropIfExists('pegawais');
    }
};
