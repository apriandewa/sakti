<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ekinerja_log_pencarian', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nip', 18);
            $table->string('nama_input', 150)->nullable();
            $table->string('periode_id', 64);
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->enum('status', ['success', 'not_found', 'error'])->default('success');
            $table->string('response_message', 255)->nullable();
            $table->timestamp('created_at')->nullable();

            $table->index(['nip', 'created_at']);
            $table->index('ip_address');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ekinerja_log_pencarian');
    }
};
