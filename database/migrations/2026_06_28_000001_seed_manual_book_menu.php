<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Menu;
use App\Models\AccessMenu;

return new class extends Migration
{
    public function up()
    {
        $docMenu = Menu::where('code', 'documentation')->first();
        if (!$docMenu) {
            return;
        }

        $menuData = [
            'title' => 'Buku Petunjuk',
            'subtitle' => 'Manual Book Aplikasi',
            'code' => 'doc-manual-book',
            'model' => 'Documentation',
            'url' => 'documentation/manual-book',
            'icon' => 'fa fa-book',
            'type' => 'backend',
            'show' => true,
            'active' => true,
            'sort' => 6,
            'parent_id' => $docMenu->id,
        ];

        $menu = Menu::where('code', $menuData['code'])->first();
        if (!$menu) {
            $menu = Menu::create($menuData);
        } else {
            $menu->update($menuData);
        }

        // Assign access to level 1 and 2
        $groups = ['1', '2'];
        foreach ($groups as $groupId) {
            AccessMenu::updateOrCreate(
                ['access_group_id' => $groupId, 'menu_id' => $menu->id],
                ['access' => ['read']]
            );
        }
    }

    public function down()
    {
        $menu = Menu::where('code', 'doc-manual-book')->first();
        if ($menu) {
            AccessMenu::where('menu_id', $menu->id)->delete();
            $menu->forceDelete();
        }
    }
};
