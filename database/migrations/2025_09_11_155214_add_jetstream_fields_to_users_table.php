<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// filepath: database/migrations/xxxx_xx_xx_xxxxxx_add_jetstream_fields_to_users_table.php
class AddJetstreamFieldsToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'email_verified_at')) {
                $table->timestamp('email_verified_at')->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'remember_token')) {
                $table->rememberToken()->after('password');
            }
            if (!Schema::hasColumn('users', 'current_team_id')) {
                $table->foreignId('current_team_id')->nullable()->after('remember_token');
            }
            if (!Schema::hasColumn('users', 'profile_photo_path')) {
                $table->string('profile_photo_path', 2048)->nullable()->after('current_team_id');
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['email_verified_at', 'remember_token', 'current_team_id', 'profile_photo_path']);
        });
    }
}