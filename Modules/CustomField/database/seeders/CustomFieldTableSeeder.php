<?php

namespace Modules\CustomField\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Modules\CustomField\Models\CustomField;

class CustomFieldTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = \Faker\Factory::create();

        // Add the master administrator, user id of 1

        $custom_field = [
            // [
            //     'custom_field_group_id' => 1,
            //     'label' => 'Name',
            //     'name' => 'Name',
            //     'type' => 'text',
            //     'required' => 1,
            //     'values' => '',
            //     'is_export' => 1,
            //     'is_view' => 1,

            // ],
            // [
            //     'custom_field_group_id' => 1,
            //     'label' => 'Age',
            //     'name' => 'age',
            //     'type' => 'text',
            //     'required' => 1,
            //     'values' => '',
            //     'is_export' => 1,
            //     'is_view' => 1,
            // ],
        ];

        if (env('IS_DUMMY_DATA')) {
            foreach ($custom_field as $custom_field_data) {
                $custom_field = CustomField::create($custom_field_data);
            }
        }

        Schema::enableForeignKeyConstraints();
    }
}
