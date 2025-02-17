<?php

namespace Modules\Earning\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Earning\Models\Earning;

class EarningDatabaseSeeder extends Seeder
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
         * Earnings Seed
         * ------------------
         */

        // DB::table('earnings')->truncate();
        // echo "Truncate: earnings \n";

        Earning::factory()->count(20)->create();
        $rows = Earning::all();
        echo " Insert: earnings \n\n";

        // Enable foreign key checks!
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
