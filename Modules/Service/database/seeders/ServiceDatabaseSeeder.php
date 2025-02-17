<?php

namespace Modules\Service\database\seeders;

use Illuminate\Database\Seeder;

class ServiceDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(ServicesTableSeeder::class);
    }
}
