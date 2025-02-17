<?php

namespace Modules\Location\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Modules\Location\Models\Location;
use Modules\MenuBuilder\Models\MenuBuilder;

class LocationDatabaseSeeder extends Seeder
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
        foreach (config('location.ARRAY_MENU') as $key => $value) {
            // code...
            $arr[] = $this->generateMenuObject($value);
        }

        foreach ($arr as $key => $value) {
            $this->saveMenu($value);
        }

        Location::create([
            'name' => 'Default Location',
            'address_line_1' => 'Default Address',
            'is_default' => 1,
            'status' => 1,
        ]);
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
