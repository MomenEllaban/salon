<?php

namespace Modules\CustomField\database\seeders;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Modules\Category\Models\Category;
use Modules\CustomField\Models\CustomFieldGroup;
use Modules\Service\Models\Service;
use Modules\Subscriptions\Models\Plan;

class CustomFieldGroupTableSeeder extends Seeder
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

        $field_group = [
            [
                'name' => 'Branch',
                'model' => Branch::CUSTOM_FIELD_MODEL,
            ],
            [
                'name' => 'Customer / Staff',
                'model' => User::CUSTOM_FIELD_MODEL,
            ],
            [
                'name' => 'Category / Subcategory',
                'model' => Category::CUSTOM_FIELD_MODEL,

            ],
            [
                'name' => 'Service',
                'model' => Service::CUSTOM_FIELD_MODEL,

            ],
            [
                'name' => 'Plan',
                'model' => Plan::CUSTOM_FIELD_MODEL,

            ],
        ];

        foreach ($field_group as $field_group_data) {
            $field_group = CustomFieldGroup::create($field_group_data);
        }

        Schema::enableForeignKeyConstraints();
    }
}
