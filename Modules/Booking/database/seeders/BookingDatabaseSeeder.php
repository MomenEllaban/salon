<?php

namespace Modules\Booking\database\seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Booking\Models\Booking;
use Modules\Booking\Models\BookingTransaction;
use Modules\Booking\Trait\BookingTrait;
use Modules\Commission\Models\CommissionEarning;
use Modules\Service\Models\ServiceBranches;

class BookingDatabaseSeeder extends Seeder
{
    use BookingTrait;

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
         * Bookings Seed
         * ------------------
         */

        DB::table('bookings')->truncate();
        if (env('IS_FAKE_DATA')) {
            $bookings = [
                [
                    'branch_id' => 1,
                    'start_date_time' => Carbon::now(),
                    'note' => '',
                    'user_id' => 2,
                    'status' => 'confirmed',
                    'services' => [
                        [
                            'employee_id' => 43,
                            'service_id' => 1,
                            'service_price' => 300,
                            'duration_min' => 30,
                            'start_date_time' => Carbon::now(),
                        ],
                    ],
                ],
            ];

            Booking::factory(120)->create()->each(function ($bk) {
                $time = $bk->start_date_time;
                $service = ServiceBranches::where(['service_id' => fake()->numberBetween(1, 22), 'branch_id' => $bk->branch_id])->first();
                $emp = fake()->numberBetween(43, 73);
                $services = [
                    [
                        'employee_id' => $emp,
                        'service_id' => $service->service_id,
                        'service_price' => $service->service_price,
                        'duration_min' => $service->duration_min,
                        'start_date_time' => $time,
                    ],
                ];
                $this->updateBookingService($services, $bk->id);

                switch ($bk->status) {
                    case 'completed':
                        $booking_transaction = BookingTransaction::where('booking_id', $bk->id)->update([
                            'booking_id' => $bk->id,
                            'external_transaction_id' => null,
                            'transaction_type' => 'cash',
                            'discount_percentage' => 0,
                            'discount_amount' => 10,
                            'tip_amount' => fake()->numberBetween(1, 35),
                            'payment_status' => 1,
                        ]);

                        $bk->commission()->save(new CommissionEarning([
                            'employee_id' => $emp,
                            'commission_amount' => 100,
                            'commission_status' => 'unpaid',
                            'payment_date' => date('Y-m-d'),
                        ]));
                        break;
                }
            });

            // Enable foreign key checks!
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
    }
}
