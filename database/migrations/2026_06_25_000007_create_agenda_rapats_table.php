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
        Schema::create('agenda_rapats', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama');
            $table->date('tanggal');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->string('tempat');
            $table->text('acara');
            $table->text('deskripsi')->nullable();
            $table->text('catatan')->nullable();
            $table->string('status')->default('DRAFT');
            
            // Surat Undangan fields
            $table->string('surat_nomor')->nullable();
            $table->string('surat_sifat')->nullable();
            $table->string('surat_lampiran')->nullable();
            $table->string('surat_hal')->nullable();
            $table->text('surat_tujuan')->nullable();

            // Signatory & TTE
            $table->foreignUuid('pegawai_id')->nullable()->constrained('pegawais')->nullOnDelete();
            $table->enum('jenis_tanda_tangan', ['manual', 'elektronik'])->default('manual');

            // Dasar Surat fields
            $table->string('dasar_dari')->nullable();
            $table->string('dasar_no')->nullable();
            $table->date('dasar_tgl')->nullable();
            $table->string('dasar_hal')->nullable();

            // QR & Creator
            $table->string('barcode_token')->unique()->nullable();
            $table->foreignUuid('user_id')->nullable()->constrained('users')->nullOnDelete();

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
        Schema::dropIfExists('agenda_rapats');
    }
};
