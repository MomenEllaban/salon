<?php

namespace Modules\Tag\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Modules\MenuBuilder\Models\MenuBuilder;

class TagDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $arr = [];
        foreach (config('tag.ARRAY_MENU') as $key => $value) {
            // code...
            $arr[] = $this->generateMenuObject($value);
        }

        foreach ($arr as $key => $value) {
            $this->saveMenu($value);
        }
    }

    protected function saveMenu($menu)
    {
        $menuChildren = $menu['children'] ?? null;
        $menu = Arr::except($menu, ['children']);
        $savedMenu = MenuBuilder::create($menu);
        if (isset($menuChildren) && count($menuChildren) > 0) {
            foreach ($menuChildren as $key => $value) {
                $value['parent_id'] = $savedMenu->id;
                $this->saveMenu($value);
            }
        }
    }

    protected function generateMenuObject($menu)
    {
        $menuArray = array_merge(config('menubuilder.MENU'), $menu);

        return $menuArray;
    }
}
