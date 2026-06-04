<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('visitor_logs', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address', 45)->nullable();
            $table->string('browser', 150)->nullable();
            $table->string('os', 100)->nullable();
            $table->string('device', 50)->nullable();
            $table->string('url', 500)->nullable();
            $table->string('referer', 500)->nullable();
            $table->string('session_id', 100)->nullable();
            $table->timestamps();

            // Index untuk query statistik yang cepat
            $table->index('ip_address');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitor_logs');
    }
};
