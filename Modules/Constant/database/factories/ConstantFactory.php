<?php

namespace Modules\Constant\database\factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ConstantFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \Modules\Constant\Models\Constant::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'type' => $this->faker->text(15),
            'value' => $this->faker->paragraph,
            'status' => 1,
            // 'created_at' => Carbon::now(),
            // 'updated_at' => Carbon::now(),
            // 'updated_by' => Carbon::now(),
        ];
    }
}
