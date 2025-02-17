<?php

namespace Modules\Booking\database\factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class BookingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \Modules\Booking\Models\Booking::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $min = $this->faker->randomElement(['-00', '-60', '-120', '-200', '-260', '-340', '00', '60', '120', '200', '260', '340']);
        $startDate = Carbon::now()->subDays(rand(-7, 7))->addMinutes($min);

        return [
            'note' => $this->faker->realTextBetween($minNbChars = 160, $maxNbChars = 200, $indexSize = 2),
            'start_date_time' => $startDate,
            'user_id' => fake()->numberBetween(2, 43),
            'branch_id' => fake()->numberBetween(1, 5),
            'status' => $this->faker->randomElement(['pending', 'confirmed', 'check_in', 'checkout', 'completed']),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
