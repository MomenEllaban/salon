<?php

namespace Modules\Employee\database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class EmployeeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \Modules\Employee\Models\User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $first_name = $this->faker->firstName;
        $last_name = $this->faker->lastName;
        $gender = $this->faker->randomElement(['male', 'female', 'other']);
        $avatar = config('app.avatar_base_path')."$gender.png";

        return [
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $this->faker->unique()->safeEmail(),
            'password' => Hash::make('secret'),
            'remember_token' => \Str::random(10),
            'mobile' => $this->faker->phoneNumber,
            'date_of_birth' => $this->faker->date,
            'avatar' => $avatar,
            'gender' => $gender,
            'show_in_calender' => 1,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
