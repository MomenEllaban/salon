<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;
use Modules\BussinessHour\Models\BussinessHour;

class BusinessHourSeeder extends Seeder
{
    public function run()
    {
        // if (env('IS_DUMMY_DATA')) {
        //     $businessHours = [
        //         [
        //             'day' => 'Monday',
        //             'start_time' => '09:00:00',
        //             'end_time' => '18:00:00',
        //             'is_holiday' => 0,
        //             'breaks' => '',
        //         ],
        //         // Add more business hour data here
        //     ];

        //     $branches = Branch::pluck('id'); // Get an array of all branch IDs

        //     foreach ($businessHours as $businessHour) {
        //         $businessHour['branch_id'] = $branches->random(); // Randomly select a branch ID

        //         BussinessHour::create($businessHour);
        //     }
        // }
    }
}
