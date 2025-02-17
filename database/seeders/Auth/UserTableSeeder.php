<?php

namespace Database\Seeders\Auth;

use App\Events\Backend\UserCreated;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

/**
 * Class UserTableSeeder.
 */
class UserTableSeeder extends Seeder
{
    /**
     * Run the database seed.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();

        // Add the master administrator, user id of 1
        $avatarPath = config('app.avatar_base_path');
        $admin = User::create([
            'first_name' => 'Salon',
            'last_name' => 'Admin',
            'email' => 'admin@salon.com',
            'password' => Hash::make('12345678'),
            'mobile' => fake()->phoneNumber,
            'date_of_birth' => fake()->date,
            'avatar' => $avatarPath.'male.webp',
            'gender' => 'male',
            'email_verified_at' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        $admin->assignRole('admin');
        $users = [
            [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john@gmail.com',
                'password' => Hash::make('12345678'),
                'mobile' => fake()->phoneNumber,
                'date_of_birth' => fake()->date,
                'profile_image' => public_path('/dummy-images/customers/13.webp'),
                'avatar' => $avatarPath.'male.webp',
                'gender' => 'male',
                'email_verified_at' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'first_name' => 'John',
                'last_name' => 'Richards',
                'email' => 'john.richards@hotmail.com',
                'password' => Hash::make('12345678'),
                'mobile' => fake()->phoneNumber,
                'date_of_birth' => fake()->date,
                'avatar' => $avatarPath.'male.webp',
                'profile_image' => public_path('/dummy-images/customers/8.webp'),
                'gender' => 'male',
                'email_verified_at' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'first_name' => 'Alice',
                'last_name' => 'Thompson',
                'email' => 'alice.thompson@gmail.com',
                'password' => Hash::make('12345678'),
                'mobile' => fake()->phoneNumber,
                'date_of_birth' => fake()->date,
                'avatar' => null,
                'profile_image' => public_path('/dummy-images/customers/11.webp'),
                'gender' => 'other',
                'email_verified_at' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'first_name' => 'Noah',
                'last_name' => 'Thompson',
                'email' => 'noah.thompson@yahoo.com',
                'password' => Hash::make('12345678'),
                'mobile' => fake()->phoneNumber,
                'date_of_birth' => fake()->date,
                'avatar' => null,
                'profile_image' => public_path('/dummy-images/customers/2.webp'),
                'gender' => 'female',
                'email_verified_at' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'first_name' => 'Benjamin',
                'last_name' => 'Robinson',
                'email' => 'benjamin.robinson@yahoo.com',
                'password' => Hash::make('12345678'),
                'mobile' => fake()->phoneNumber,
                'date_of_birth' => fake()->date,
                'avatar' => null,
                'profile_image' => public_path('/dummy-images/customers/4.webp'),
                'gender' => 'male',
                'email_verified_at' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'first_name' => 'Liam',
                'last_name' => 'Wilson',
                'email' => 'liam.wilson@yahoo.com',
                'password' => Hash::make('12345678'),
                'mobile' => fake()->phoneNumber,
                'date_of_birth' => fake()->date,
                'avatar' => null,
                'profile_image' => public_path('/dummy-images/customers/3.webp'),
                'gender' => 'male',
                'email_verified_at' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'first_name' => 'Ethan',
                'last_name' => 'Brown',
                'email' => 'ethan.brown@hotmail.com',
                'password' => Hash::make('12345678'),
                'mobile' => fake()->phoneNumber,
                'date_of_birth' => fake()->date,
                'avatar' => null,
                'profile_image' => public_path('/dummy-images/customers/5.webp'),
                'gender' => 'male',
                'email_verified_at' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'first_name' => 'William',
                'last_name' => 'Turner',
                'email' => 'william.turner@hotmail.com',
                'password' => Hash::make('12345678'),
                'mobile' => fake()->phoneNumber,
                'date_of_birth' => fake()->date,
                'avatar' => null,
                'profile_image' => public_path('/dummy-images/customers/6.webp'),
                'gender' => 'male',
                'email_verified_at' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'first_name' => 'Isabella',
                'last_name' => 'Martin',
                'email' => 'isabella.martin@gmail.com',
                'password' => Hash::make('12345678'),
                'mobile' => fake()->phoneNumber,
                'date_of_birth' => fake()->date,
                'avatar' => null,
                'profile_image' => public_path('/dummy-images/customers/10.webp'),
                'gender' => 'male',
                'email_verified_at' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'first_name' => 'Emma',
                'last_name' => 'Johnson',
                'email' => 'emma.johnson@gmail.com',
                'password' => Hash::make('12345678'),
                'mobile' => fake()->phoneNumber,
                'date_of_birth' => fake()->date,
                'avatar' => null,
                'profile_image' => public_path('/dummy-images/customers/11.webp'),
                'gender' => 'male',
                'email_verified_at' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'first_name' => 'Olivia',
                'last_name' => 'Davis',
                'email' => 'olivia.davis@gmail.com',
                'password' => Hash::make('12345678'),
                'mobile' => fake()->phoneNumber,
                'date_of_birth' => fake()->date,
                'avatar' => null,
                'profile_image' => public_path('/dummy-images/customers/9.webp'),
                'gender' => 'male',
                'email_verified_at' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

        ];

        if (env('IS_DUMMY_DATA')) {
            foreach ($users as $key => $user_data) {
                $featureImage = $user_data['profile_image'] ?? null;
                $userData = Arr::except($user_data, ['profile_image']);
                $user = User::create($userData);
                $user->assignRole('user');
                event(new UserCreated($user));
                if (isset($featureImage)) {
                    $this->attachFeatureImage($user, $featureImage);
                }
            }
            if (env('IS_FAKE_DATA')) {
                User::factory()->count(30)->create()->each(function ($user) {
                    $user->assignRole('user');
                    $img = public_path('/dummy-images/customers/'.fake()->numberBetween(1, 13).'.webp');
                    $this->attachFeatureImage($user, $img);
                });
            }
        }

        Schema::enableForeignKeyConstraints();
    }

    private function attachFeatureImage($model, $publicPath)
    {
        if (! env('IS_DUMMY_DATA_IMAGE')) {
            return false;
        }

        $file = new \Illuminate\Http\File($publicPath);

        $media = $model->addMedia($file)->preservingOriginal()->toMediaCollection('profile_image');

        return $media;
    }
}
