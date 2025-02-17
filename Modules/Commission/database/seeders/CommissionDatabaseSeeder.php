<?php

namespace Modules\Commission\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Commission\Models\Commission;

class CommissionDatabaseSeeder extends Seeder
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
         * Commissions Seed
         * ------------------
         */

        // DB::table('commissions')->truncate();
        // echo "Truncate: commissions \n";
        if (env('IS_DUMMY_DATA')) {
            $data = [
                [
                    'title' => 'Booking Commission',
                    'commission_type' => 'percentage',
                    'commission_value' => 10,
                    'status' => 1,
                ],
                [
                    'title' => 'Haircut Commission',
                    'commission_type' => 'fixed',
                    'commission_value' => 5.00,
                    'status' => 1,
                ],
                [
                    'title' => 'Makeup Commission',
                    'commission_type' => 'percentage',
                    'commission_value' => 15,
                    'status' => 1,
                ],
                [
                    'title' => 'Massage Commission',
                    'commission_type' => 'fixed',
                    'commission_value' => 7.50,
                    'status' => 1,
                ],
                [
                    'title' => 'Nail Art Commission',
                    'commission_type' => 'percentage',
                    'commission_value' => 12,
                    'status' => 1,
                ],
            ];

            foreach ($data as $key => $value) {
                Commission::create($value);
            }
        }

        // Enable foreign key checks!
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
