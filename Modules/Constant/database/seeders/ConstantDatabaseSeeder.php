<?php

namespace Modules\Constant\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Constant\Models\Constant;

class ConstantDatabaseSeeder extends Seeder
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
         * Constants Seed
         * ------------------
         */
        // Enable foreign key checks!
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $arr1 = [
            [
                'type' => 'PAYMENT_METHODS',
                'name' => 'cash',
                'value' => 'Cash',
            ],
            [
                'type' => 'PAYMENT_METHODS',
                'name' => 'upi',
                'value' => 'UPI',
            ],
            [
                'type' => 'PAYMENT_METHODS',
                'name' => 'razorpay',
                'value' => 'Razorpay',
            ],
            [
                'type' => 'PAYMENT_METHODS',
                'name' => 'stripe',
                'value' => 'Stripe',
            ],
            [
                'type' => 'BRANCH_SERVICE_GENDER',
                'name' => 'unisex',
                'value' => 'Unisex',
            ],
            [
                'type' => 'BRANCH_SERVICE_GENDER',
                'name' => 'female',
                'value' => 'Female',
            ],
            [
                'type' => 'BRANCH_SERVICE_GENDER',
                'name' => 'male',
                'value' => 'Male',
            ],

            [
                'type' => 'status',
                'name' => 1,
                'value' => 'Active',
            ],
            [
                'type' => 'status',
                'name' => 0,
                'value' => 'Deactive',
            ],
            [
                'type' => 'BOOKING_STATUS',
                'name' => 'pending',
                'value' => 'Pending',
                'sequence' => 0,
            ],
            [
                'type' => 'BOOKING_STATUS',
                'name' => 'confirmed',
                'value' => 'Confirmed',
                'sequence' => 1,
            ],
            [
                'type' => 'BOOKING_STATUS',
                'name' => 'check_in',
                'value' => 'Check In',
                'sequence' => 2,
            ],
            [
                'type' => 'BOOKING_STATUS',
                'name' => 'checkout',
                'value' => 'Checkout',
                'sequence' => 3,
            ],
            [
                'type' => 'BOOKING_STATUS',
                'name' => 'cancelled',
                'value' => 'Cancelled',
                'sequence' => 4,
            ],
            [
                'type' => 'BOOKING_STATUS',
                'name' => 'completed',
                'value' => 'Completed',
                'sequence' => 5,
            ],
            [
                'type' => 'BOOKING_STATUS_COLOR',
                'name' => '#e5a900',
                'value' => 'Pending Color',
                'sub_type' => 'pending',
                'sequence' => 0,
            ],
            [
                'type' => 'BOOKING_STATUS_COLOR',
                'name' => '#6E6EEF',
                'value' => 'Confirm Color',
                'sub_type' => 'confirmed',
                'sequence' => 1,
            ],
            [
                'type' => 'BOOKING_STATUS_COLOR',
                'name' => '#D68AF1',
                'value' => 'Check In Color',
                'sub_type' => 'check_in',
                'sequence' => 2,
            ],
            [
                'type' => 'BOOKING_STATUS_COLOR',
                'name' => '#E58282',
                'value' => 'Check Out Color',
                'sub_type' => 'checkout',
                'sequence' => 3,
            ],
            [
                'type' => 'BOOKING_STATUS_COLOR',
                'name' => '#D1D1D1',
                'value' => 'Cancelled Color',
                'sub_type' => 'cancelled',
                'sequence' => 4,
            ],
            [
                'type' => 'BOOKING_STATUS_COLOR',
                'name' => '#3ABA61',
                'value' => 'Completed Color',
                'sub_type' => 'completed',
                'sequence' => 4,
            ],

            [
                'type' => 'field_type',
                'value' => 'text',
                'name' => 'Text',
            ],

            [
                'type' => 'field_type',
                'value' => 'textarea',
                'name' => 'Textarea',
                'sequence' => 2,
            ],
            [
                'type' => 'field_type',
                'value' => 'select',
                'name' => 'Select',
                'sequence' => 3,
            ],
            [
                'type' => 'field_type',
                'value' => 'radio',
                'name' => 'Radio',
                'sequence' => 4,
            ],
            [
                'type' => 'field_type',
                'value' => 'checkbox',
                'name' => 'Checkbox',
                'sequence' => 5,
            ],
            [
                'type' => 'language',
                'value' => 'en',
                'name' => 'English',
                'sequence' => 1,
            ],
            [
                'type' => 'language',
                'value' => 'br',
                'name' => 'বাংলা',
                'sequence' => 2,
            ],
            [
                'type' => 'language',
                'value' => 'ar',
                'name' => 'العربی',
                'sequence' => 3,
            ],
            [
                'type' => 'language',
                'value' => 'vi',
                'name' => 'Vietnamese',
                'sequence' => 4,
            ],
            [
                'type' => 'SLIDER_TYPES',
                'value' => 'category',
                'name' => 'Category',
                'sequence' => 1,
            ],
            [
                'type' => 'SLIDER_TYPES',
                'value' => 'service',
                'name' => 'Service',
                'sequence' => 2,
            ],

            [
                'type' => 'EARNING_PAYMENT_TYPE',
                'value' => 'cash',
                'name' => 'Cash',
                'sequence' => 1,
            ],

            [
                'type' => 'EARNING_PAYMENT_TYPE',
                'value' => 'bank',
                'name' => 'Bank',
                'sequence' => 2,
            ],

            [
                'type' => 'EARNING_PAYMENT_TYPE',
                'value' => 'wallet',
                'name' => 'Wallet',
                'sequence' => 3,
            ],
            [
                'type' => 'additional_permissions',
                'value' => 'Gallery',
                'name' => 'gallery',
                'sequence' => 1,
            ],
            [
                'type' => 'additional_permissions',
                'value' => 'Password',
                'name' => 'password',
                'sequence' => 2,
            ],
            [
                'type' => 'additional_permissions',
                'value' => 'Tableview',
                'name' => 'tableview',
                'sequence' => 3,
            ],
            [
                'type' => 'additional_permissions',
                'value' => 'Review',
                'name' => 'review',
                'sequence' => 4,
            ],
            [
                'type' => 'PAYMENT_STATUS',
                'name' => 'Paid',
                'value' => '1',
            ],

            [
                'type' => 'PAYMENT_STATUS',
                'name' => 'Pending',
                'value' => '0',
            ],
        ];

        foreach ($arr1 as $key => $val) {
            Constant::create($val);
        }
    }
}
