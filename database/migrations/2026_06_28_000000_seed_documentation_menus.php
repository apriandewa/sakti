<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Menu;
use App\Models\AccessMenu;
use App\Models\AccessGroup;

return new class extends Migration
{
    public function up()
    {
        $parent = Menu::where('code', 'mainmenu')->first();
        if (!$parent) {
            return;
        }

        // Create Documentation Parent Menu
        $docMenuData = [
            'title' => 'Dokumentasi & PRD',
            'subtitle' => 'PRD & Implementation Plan',
            'code' => 'documentation',
            'model' => 'Documentation',
            'url' => '#',
            'icon' => 'fa fa-book',
            'type' => 'backend',
            'show' => true,
            'active' => true,
            'sort' => 99,
            'parent_id' => $parent->id,
        ];
        
        $docMenu = Menu::where('code', $docMenuData['code'])->first();
        if (!$docMenu) {
            $docMenu = Menu::create($docMenuData);
        } else {
            $docMenu->update($docMenuData);
        }

        // Submenus
        $submenus = [
            [
                'title' => 'Konsep & Presentasi',
                'subtitle' => 'Konsep / Rancangan Aplikasi',
                'code' => 'doc-slides',
                'model' => 'Documentation',
                'url' => 'documentation/slides',
                'icon' => 'fa fa-tv',
                'type' => 'backend',
                'show' => true,
                'active' => true,
                'sort' => 1,
                'parent_id' => $docMenu->id,
            ],
            [
                'title' => 'PRD Portal',
                'subtitle' => 'Product Requirement Document',
                'code' => 'doc-prd-portal',
                'model' => 'Documentation',
                'url' => 'documentation/prd-portal',
                'icon' => 'fa fa-file-text-o',
                'type' => 'backend',
                'show' => true,
                'active' => true,
                'sort' => 2,
                'parent_id' => $docMenu->id,
            ],
            [
                'title' => 'PRD Presensi',
                'subtitle' => 'Product Requirement Document',
                'code' => 'doc-prd-presensi',
                'model' => 'Documentation',
                'url' => 'documentation/prd-presensi',
                'icon' => 'fa fa-file-text-o',
                'type' => 'backend',
                'show' => true,
                'active' => true,
                'sort' => 3,
                'parent_id' => $docMenu->id,
            ],
            [
                'title' => 'Plan Portal',
                'subtitle' => 'Implementation Plan',
                'code' => 'doc-plan-portal',
                'model' => 'Documentation',
                'url' => 'documentation/plan-portal',
                'icon' => 'fa fa-cogs',
                'type' => 'backend',
                'show' => true,
                'active' => true,
                'sort' => 4,
                'parent_id' => $docMenu->id,
            ],
            [
                'title' => 'Plan Presensi',
                'subtitle' => 'Implementation Plan',
                'code' => 'doc-plan-presensi',
                'model' => 'Documentation',
                'url' => 'documentation/plan-presensi',
                'icon' => 'fa fa-cogs',
                'type' => 'backend',
                'show' => true,
                'active' => true,
                'sort' => 5,
                'parent_id' => $docMenu->id,
            ],
        ];

        foreach ($submenus as $menuData) {
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
        
        // Give access to parent menu as well
        $groups = ['1', '2'];
        foreach ($groups as $groupId) {
            AccessMenu::updateOrCreate(
                ['access_group_id' => $groupId, 'menu_id' => $docMenu->id],
                ['access' => ['read']]
            );
        }
    }

    public function down()
    {
        $codes = ['documentation', 'doc-slides', 'doc-prd-portal', 'doc-prd-presensi', 'doc-plan-portal', 'doc-plan-presensi'];
        $menus = Menu::whereIn('code', $codes)->get();
        foreach ($menus as $menu) {
            AccessMenu::where('menu_id', $menu->id)->delete();
            $menu->forceDelete();
        }
    }
};
