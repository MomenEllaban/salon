<?php

namespace Modules\Product\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Modules\MenuBuilder\Models\MenuBuilder;

class ProductDatabaseSeeder extends Seeder
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
        foreach (config('product.ARRAY_MENU') as $key => $value) {
            // code...
            $arr[] = $this->generateMenuObject($value);
        }

        foreach (config('product.H_ARRAY_MENU') as $key => $value) {
            // code...
            $arr[] = $this->generateMenuObject($value);
        }

        foreach ($arr as $key => $value) {
            $this->saveMenu($value);
        }

        $this->call(ProductCategoryTableSeeder::class);
        $this->call(BrandsTableSeeder::class);
        $this->call(UnitsTableSeeder::class);
        $this->call(VariationsTableSeeder::class);
        $this->call(ProductsTableSeeder::class);
        $this->call(ProductCategoryMappingsTableSeeder::class);
        $this->call(ProductTagsTableSeeder::class);
        $this->call(ProductVariationsTableSeeder::class);
        $this->call(ProductVariationCombinationsTableSeeder::class);
        $this->call(ProductVariationStocksTableSeeder::class);
        $this->call(TagsTableSeeder::class);
        $this->call(CartTableSeeder::class);
        $this->call(OrderTableSeeder::class);
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
