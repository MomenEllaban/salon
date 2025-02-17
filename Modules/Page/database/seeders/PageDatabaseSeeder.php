<?php

namespace Modules\Page\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Page\Models\Page;

class PageDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $pages = [
            [
                'name' => 'Privacy Policy',
                'sequence' => 1,
                'description' => 'Privacy Policy',
            ],
            [
                'name' => 'Term & Condition',
                'sequence' => 2,
                'description' => 'Term & Condition',
            ],
        ];
        if (env('IS_DUMMY_DATA')) {
            foreach ($pages as $key => $pages_data) {
                $pages = Page::create($pages_data);
            }
        }
    }
}
