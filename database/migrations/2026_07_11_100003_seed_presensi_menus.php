<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Seed menu Presensi ke tabel menus.
 * Kolom menus: title, subtitle, code, url, model, icon, type, show, active, sort, maintenance, coming_soon
 */
return new class extends Migration
{
    public function up(): void
    {
        // Cek apakah menu presensi sudah ada
        $existing = DB::table('menus')->whereIn('code', ['presensi', 'rekap-presensi'])->count();
        if ($existing > 0) return;

        $parentId = (string) \Illuminate\Support\Str::uuid();

        DB::table('menus')->insert([
            [
                'id'          => $parentId,
                'parent_id'   => null,
                'title'       => 'Presensi Pegawai',
                'subtitle'    => 'Rekap Absensi ASN',
                'code'        => 'presensi',
                'url'         => '/admin/presensi',
                'model'       => null,
                'icon'        => 'mdi mdi-calendar-clock',
                'type'        => 'backend',
                'show'        => true,
                'active'      => true,
                'sort'        => 50,
                'maintenance' => false,
                'coming_soon' => false,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);
    }

    public function down(): void
    {
        DB::table('menus')->whereIn('code', ['presensi', 'rekap-presensi'])->delete();
    }
};
