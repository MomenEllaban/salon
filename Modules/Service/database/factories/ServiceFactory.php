<?php

namespace Modules\Service\database\factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ServiceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \Modules\Service\Models\Service::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => substr($this->faker->text(20), 0, -1),
            'slug' => '',
            'description' => $this->faker->realTextBetween($minNbChars = 160, $maxNbChars = 200, $indexSize = 2),
            'duration_min' => $this->faker->randomElement([15, 30, 45, 60]),
            'default_price' => $this->faker->randomElement([350.00, 400.00, 1000.00, 1250.00]),
            'status' => $this->faker->numberBetween(0, 1),
            'category_id' => $this->faker->numberBetween(1, 20),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
