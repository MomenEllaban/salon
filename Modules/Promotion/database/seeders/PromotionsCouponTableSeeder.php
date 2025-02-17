<?php

namespace Modules\Promotion\database\seeders;

use Illuminate\Database\Seeder;

class PromotionsCouponTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('promotions_coupon')->delete();

        \DB::table('promotions_coupon')->insert(array (
            0 =>
            array (
                'id' => 1,
                'coupon_code' => 'SPRING50',
                'coupon_type' => 'seasonal',
                'start_date_time' => '2024-04-01',
                'end_date_time' => '2024-04-30',
                'is_expired' => 1,
                'discount_type' => 'percent',
                'discount_percentage' => 50.0,
                'discount_amount' => 0,
                'use_limit' => 1,
                'used_by' => NULL,
                'promotion_id' => 1,
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
                'coupon_code' => 'SUMMER20',
                'coupon_type' => 'seasonal',
                'start_date_time' => '2024-06-01',
                'end_date_time' => '2024-06-30',
                'is_expired' => 1,
                'discount_type' => 'percent',
                'discount_percentage' => 20.0,
                'discount_amount' => 0,
                'use_limit' => 1,
                'used_by' => NULL,
                'promotion_id' => 2,
                'created_by' => 1,
                'updated_by' => 1,
                'deleted_by' => NULL,
                'created_at' => '2024-05-28 07:21:46',
                'updated_at' => '2024-05-28 07:21:46',
                'deleted_at' => NULL,
            ),
            2 =>
            array (
                'id' => 3,
                'coupon_code' => 'SCHOOL15',
                'coupon_type' => 'seasonal',
                'start_date_time' => '2024-08-01',
                'end_date_time' => '2024-08-31',
                'is_expired' => 1,
                'discount_type' => 'percent',
                'discount_percentage' => 15.0,
                'discount_amount' => 0,
                'use_limit' => 1,
                'used_by' => NULL,
                'promotion_id' => 3,
                'created_by' => 1,
                'updated_by' => 1,
                'deleted_by' => NULL,
                'created_at' => '2024-05-28 07:21:46',
                'updated_at' => '2024-05-28 07:21:46',
                'deleted_at' => NULL,
            ),
            3 =>
            array (
                'id' => 4,
                'coupon_code' => 'HOLIDAY30',
                'coupon_type' => 'seasonal',
                'start_date_time' => '2024-12-01',
                'end_date_time' => '2024-12-31',
                'is_expired' => 0,
                'discount_type' => 'fixed',
                'discount_percentage' => 0,
                'discount_amount' => 30.0,
                'use_limit' => 1,
                'used_by' => NULL,
                'promotion_id' => 4,
                'created_by' => 1,
                'updated_by' => 1,
                'deleted_by' => NULL,
                'created_at' => '2024-05-28 07:21:46',
                'updated_at' => '2024-05-28 07:21:46',
                'deleted_at' => NULL,
            ),
            4 =>
            array (
                'id' => 5,
                'coupon_code' => 'BLACKFRIDAY10',
                'coupon_type' => 'event',
                'start_date_time' => '2024-11-25',
                'end_date_time' => '2024-11-30',
                'is_expired' => 0,
                'discount_type' => 'percent',
                'discount_percentage' => 10.0,
                'discount_amount' => 0,
                'use_limit' => 1,
                'used_by' => NULL,
                'promotion_id' => 5,
                'created_by' => 1,
                'updated_by' => 1,
                'deleted_by' => NULL,
                'created_at' => '2024-05-28 07:21:46',
                'updated_at' => '2024-05-28 07:21:46',
                'deleted_at' => NULL,
            ),
        ));
    }
}
