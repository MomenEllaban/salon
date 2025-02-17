<?php

namespace Modules\Tip\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Tip\Models\Tip;

class TipDatabaseSeeder extends Seeder
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
         * Tips Seed
         * ------------------
         */

        // DB::table('tips')->truncate();
        // echo "Truncate: tips \n";

        Tip::factory()->count(20)->create();
        $rows = Tip::all();
        echo " Insert: tips \n\n";

        // Enable foreign key checks!
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
