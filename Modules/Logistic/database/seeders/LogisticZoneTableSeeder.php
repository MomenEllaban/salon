<?php

namespace Modules\Logistic\database\seeders;

use Illuminate\Database\Seeder;
use Modules\Logistic\Models\Logistic;
use Modules\Logistic\Models\LogisticZone;
use Modules\Logistic\Models\LogisticZoneCity;

class LogisticZoneTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (env('IS_DUMMY_DATA')) {
            Logistic::create([
                'name' => 'Fedex',
            ]);

            Logistic::create([
                'name' => 'Bluedart',
            ]);

            $this->createLogisticZone([
                'city_id' => [1],
                'description' => 'Near gokuldham Circle',
                'name' => 'Mumbai Zone',
                'mobile' => '9999999999',
                'logistic_id' => 1,
                'country_id' => 1,
                'state_id' => 1,
            ]);
        }
    }

    protected function createLogisticZone($request)
    {
        $data = collect($request)->except('city_id');
        $logisticZone = LogisticZone::create($data->toArray());
        foreach ($request['city_id'] as $city_id) {
            $logisticZoneCity = new LogisticZoneCity;
            $logisticZoneCity->logistic_id = $logisticZone->logistic_id;
            $logisticZoneCity->logistic_zone_id = $logisticZone->id;
            $logisticZoneCity->city_id = $city_id;
            $logisticZoneCity->save();
        }
    }
}
