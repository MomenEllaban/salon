<?php

namespace Modules\CustomField\database\seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class CustomFieldDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call(CustomFieldGroupTableSeeder::class);
        $this->call(CustomFieldTableSeeder::class);
    }
}
