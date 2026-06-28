<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Menu;
use App\Models\AccessMenu;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        // 1. Create Table
        Schema::create('presensi_harians', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('pegawai_id');
            $table->date('tanggal');
            $table->time('jam_masuk')->nullable();
            $table->time('jam_keluar')->nullable();
            
            // Status Kehadiran: HN (Hadir Normal), TK (Tanpa Keterangan), CT (Cuti), DL (Dinas Luar), IZIN (Izin), LN (Libur Nasional), LJ/LS/LM (Weekend)
            $table->string('status_kehadiran', 10)->default('HN'); 
            
            // Kategori Keterlambatan: TM1, TM2, TM3, TM4, TMM
            $table->string('kategori_terlambat', 10)->nullable();
            $table->integer('menit_terlambat')->default(0);
            
            // Kategori Pulang Cepat: PC1, PC2, PC3, PC4, PC5, PCM
            $table->string('kategori_pulang_cepat', 10)->nullable();
            $table->integer('menit_pulang_cepat')->default(0);
            
            // Perhitungan Potongan
            $table->decimal('potongan_terlambat', 5, 2)->default(0.00);
            $table->decimal('potongan_pulang_cepat', 5, 2)->default(0.00);
            $table->decimal('total_potongan', 5, 2)->default(0.00);
            
            $table->text('keterangan')->nullable();
            $table->boolean('is_sync')->default(true);
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();

            // Relasi ke tabel pegawais
            $table->foreign('pegawai_id')->references('id')->on('pegawais')->onDelete('cascade');
            $table->unique(['pegawai_id', 'tanggal']);
        });

        // 2. Seed Menu
        $parent = Menu::where('code', 'mainmenu')->first();
        if ($parent) {
            $menu = Menu::updateOrCreate(
                ['code' => 'presensi'],
                [
                    'title' => 'Presensi Pegawai',
                    'subtitle' => 'Rekap Presensi & Potongan BKN',
                    'model' => 'PresensiHarian',
                    'url' => 'presensi',
                    'icon' => 'fa fa-calendar-check-o',
                    'type' => 'backend',
                    'show' => true,
                    'active' => true,
                    'sort' => 13,
                    'parent_id' => $parent->id,
                ]
            );

            // Beri Akses kepada Root (1) dan Admin (2)
            AccessMenu::updateOrCreate(
                ['access_group_id' => '1', 'menu_id' => $menu->id],
                ['access' => ['read', 'create', 'update', 'delete']]
            );
            AccessMenu::updateOrCreate(
                ['access_group_id' => '2', 'menu_id' => $menu->id],
                ['access' => ['read', 'create', 'update', 'delete']]
            );
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        // 1. Hapus Menu
        $menu = Menu::where('code', 'presensi')->first();
        if ($menu) {
            AccessMenu::where('menu_id', $menu->id)->delete();
            $menu->forceDelete();
        }

        // 2. Drop Table
        Schema::dropIfExists('presensi_harians');
    }
};
