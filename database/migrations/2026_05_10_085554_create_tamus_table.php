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
        Schema::create('tamus', function (Blueprint $table) {
            $table->uuid("id")->primary();
			$table->string("nama")->nullable();
			$table->text("alamat")->nullable();
			$table->string("no_hp")->nullable();
			$table->string("email")->nullable();
			$table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan']);
			$table->string("pekerjaan")->nullable();
			$table->string("asal")->nullable();
			$table->string("keperluan")->nullable();
			$table->text("pesan")->nullable();
			$table->enum('status', ['TERKIRIM', 'DISETUJUI', 'DITOLAK'])->default('TERKIRIM');
			$table->dateTime("tanggal_kunjungan")->nullable();
			$table->string('ip_address', 45)->nullable();
            $table->foreignUuid("user_id")->nullable()->constrained();
            $table->foreignUuid("verifikator_id")->nullable()->constrained('users')->nullOnDelete();
			$table->timestamps();
			$table->softDeletes();
        });

        Schema::table('tamus', function (Blueprint $table) {
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tamus');
    }
};
