<?php

namespace Modules\Promotion\database\seeders;

use Illuminate\Database\Seeder;

class PromotionDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('promotions')->delete();
        
        \DB::table('promotions')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Spring Sale',
                'description' => 'Up to 50% off on selected items.',
                'start_date_time' => '2024-04-01',
                'end_date_time' => '2024-04-30',
                'status' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'deleted_by' => NULL,
                'created_at' => '2024-05-28 07:21:46',
                'updated_at' => '2024-05-28 07:21:46',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Summer Blowout',
                'description' => 'Exclusive deals on summer products.',
                'start_date_time' => '2024-06-01',
                'end_date_time' => '2024-06-30',
                'status' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'deleted_by' => NULL,
                'created_at' => '2024-05-28 07:22:57',
                'updated_at' => '2024-05-28 07:22:57',
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'Back to School',
                'description' => 'Discounts on school supplies.',
                'start_date_time' => '2024-08-01',
                'end_date_time' => '2024-08-31',
                'status' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'deleted_by' => NULL,
                'created_at' => '2024-05-28 07:23:58',
                'updated_at' => '2024-05-28 07:23:58',
                'deleted_at' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'Holiday Specials',
                'description' => 'Special offers for the holiday season.',
                'start_date_time' => '2024-12-01',
                'end_date_time' => '2024-12-31',
                'status' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'deleted_by' => NULL,
                'created_at' => '2024-05-28 07:23:58',
                'updated_at' => '2024-05-28 07:23:58',
                'deleted_at' => NULL,
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'Black Friday Deals',
                'description' => 'Massive discounts on Black Friday.',
                'start_date_time' => '2024-11-25',
                'end_date_time' => '2024-11-30',
                'status' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'deleted_by' => NULL,
                'created_at' => '2024-05-28 07:23:58',
                'updated_at' => '2024-05-28 07:23:58',
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}


