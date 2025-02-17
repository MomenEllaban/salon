<?php

namespace Modules\Expense\database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class ExpenseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = \Modules\Expense\Models\Expense::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => substr($this->faker->text(15), 0, -1),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}

