<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Menu;
use App\Models\AccessMenu;
use App\Models\AccessGroup;

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

        $verifikatorGroup = AccessGroup::where('code', 'verifikator')->first();
        $verifikatorId = $verifikatorGroup ? $verifikatorGroup->id : null;

        $menus = [
            [
                'title' => 'Agenda Rapat',
                'subtitle' => 'Manajemen Agenda & Undangan Rapat',
                'code' => 'agenda-rapat',
                'model' => 'AgendaRapat',
                'url' => 'agenda-rapat',
                'icon' => 'fa fa-calendar',
                'type' => 'backend',
                'show' => true,
                'active' => true,
                'sort' => 13,
                'parent_id' => $parent->id,
            ],
            [
                'title' => 'Verifikasi Rapat',
                'subtitle' => 'Persetujuan & TTE Undangan',
                'code' => 'verifikasi-rapat',
                'model' => 'AgendaRapat',
                'url' => 'verifikasi-rapat',
                'icon' => 'fa fa-check-square-o',
                'type' => 'backend',
                'show' => true,
                'active' => true,
                'sort' => 14,
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

            if ($menuData['code'] === 'agenda-rapat') {
                $groups = ['1', '2', '3'];
                if ($verifikatorId) {
                    $groups[] = $verifikatorId;
                }
                foreach ($groups as $groupId) {
                    AccessMenu::updateOrCreate(
                        ['access_group_id' => $groupId, 'menu_id' => $menu->id],
                        ['access' => ['read', 'create', 'update', 'delete']]
                    );
                }
            } else if ($menuData['code'] === 'verifikasi-rapat') {
                $groups = ['1', '2'];
                if ($verifikatorId) {
                    $groups[] = $verifikatorId;
                }
                foreach ($groups as $groupId) {
                    AccessMenu::updateOrCreate(
                        ['access_group_id' => $groupId, 'menu_id' => $menu->id],
                        ['access' => ['read', 'create', 'update', 'delete']]
                    );
                }
            }
        }
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $codes = ['agenda-rapat', 'verifikasi-rapat'];
        $menus = Menu::whereIn('code', $codes)->get();
        foreach ($menus as $menu) {
            AccessMenu::where('menu_id', $menu->id)->delete();
            $menu->forceDelete();
        }
    }
};
