<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ekinerja_master_unor', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('unor_id', 64)->index();
            $table->string('nama_unor', 150);
            $table->string('unor_induk', 150)->nullable();
            $table->uuid('opd_id')->nullable(); // relasi opsional ke tabel OPD internal jika ada
            $table->boolean('is_active')->default(true);
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ekinerja_master_unor');
    }
};
