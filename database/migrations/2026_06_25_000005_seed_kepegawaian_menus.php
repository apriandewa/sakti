<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Menu;
use App\Models\AccessMenu;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $parent = Menu::where('code', 'mainmenu')->first();
        if (!$parent) {
            return;
        }

        $menus = [
            [
                'title' => 'Data Pegawai',
                'subtitle' => 'Manajemen Data Pegawai',
                'code' => 'pegawai',
                'model' => 'Pegawai',
                'url' => 'pegawai',
                'icon' => 'fa fa-id-badge',
                'type' => 'backend',
                'show' => true,
                'active' => true,
                'sort' => 9,
                'parent_id' => $parent->id,
            ],
            [
                'title' => 'Pangkat Pegawai',
                'subtitle' => 'Master Data Pangkat',
                'code' => 'pangkat',
                'model' => 'Pangkat',
                'url' => 'pangkat',
                'icon' => 'fa fa-level-up',
                'type' => 'backend',
                'show' => true,
                'active' => true,
                'sort' => 10,
                'parent_id' => $parent->id,
            ],
            [
                'title' => 'Status Pegawai',
                'subtitle' => 'Master Data Status Pegawai',
                'code' => 'status-pegawai',
                'model' => 'StatusPegawai',
                'url' => 'status-pegawai',
                'icon' => 'fa fa-toggle-on',
                'type' => 'backend',
                'show' => true,
                'active' => true,
                'sort' => 11,
                'parent_id' => $parent->id,
            ],
            [
                'title' => 'Jabatan Pegawai',
                'subtitle' => 'Master Data Jabatan',
                'code' => 'jabatan',
                'model' => 'Jabatan',
                'url' => 'jabatan',
                'icon' => 'fa fa-briefcase',
                'type' => 'backend',
                'show' => true,
                'active' => true,
                'sort' => 12,
                'parent_id' => $parent->id,
            ],
        ];

        foreach ($menus as $menuData) {
            // Check if already exists
            $menu = Menu::where('code', $menuData['code'])->first();
            if (!$menu) {
                $menu = Menu::create($menuData);
            } else {
                $menu->update($menuData);
            }

            // Grant Root (1) and Admin (2)
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
    public function down()
    {
        $codes = ['pegawai', 'pangkat', 'status-pegawai', 'jabatan'];
        $menus = Menu::whereIn('code', $codes)->get();
        foreach ($menus as $menu) {
            AccessMenu::where('menu_id', $menu->id)->delete();
            $menu->forceDelete();
        }
    }
};
