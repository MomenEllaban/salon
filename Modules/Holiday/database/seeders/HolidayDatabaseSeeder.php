<?php

namespace Modules\Holiday\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Holiday\Models\Holiday;

class HolidayDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Disable foreign key checks!
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        /*
         * Holidays Seed
         * ------------------
         */

        // DB::table('holidays')->truncate();
        // echo "Truncate: holidays \n";

        Holiday::factory()->count(20)->create();
        $rows = Holiday::all();
        echo " Insert: holidays \n\n";

        // Enable foreign key checks!
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
