<?php

namespace Modules\Employee\database\seeders;

use App\Models\Branch;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Modules\Commission\Models\EmployeeCommission;
use Modules\Employee\Models\BranchEmployee;
use Modules\Employee\Models\EmployeeRating;

class EmployeeDatabaseSeeder extends Seeder
{
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
         * Employees Seed
         * ------------------
         */

        // DB::table('employees')->truncate();
        // echo "Truncate: employees \n";

        $employee = [
            [
                'first_name' => 'Manager',
                'last_name' => 'Salon',
                'email' => 'manager@salon.com',
                'feature_image' => public_path('/dummy-images/staffs/21.webp'),
                'password' => Hash::make('12345678'),
                'mobile' => '9999999999',
                'gender' => 'male',
                'email_verified_at' => Carbon::now(),
                'is_manager' => 1,
                'show_in_calender' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'first_name' => 'James',
                'last_name' => 'Anderson',
                'email' => 'james.anderson@glamourcuts.co.uk',
                'password' => Hash::make('12345678'),
                'mobile' => '20 3333 4444',
                'date_of_birth' => null,
                'avatar' => null,
                'gender' => 'male',
                'email_verified_at' => Carbon::now(),
                'is_manager' => 1,
                'feature_image' => public_path('/dummy-images/staffs/1.webp'),
                'show_in_calender' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'first_name' => 'Emma',
                'last_name' => 'Roberts',
                'email' => 'emma.roberts@glamourcuts.co.uk',
                'password' => Hash::make('12345678'),
                'mobile' => '20 1111 2222',
                'gender' => 'other',
                'email_verified_at' => Carbon::now(),
                'is_manager' => 1,
                'feature_image' => public_path('/dummy-images/staffs/2.webp'),
                'show_in_calender' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'first_name' => 'Joshua',
                'last_name' => 'Nelson',
                'email' => 'joshua.nelson@classycuts.com.au',
                'password' => Hash::make('12345678'),
                'mobile' => '20 5555 6666',
                'gender' => 'male',
                'email_verified_at' => Carbon::now(),
                'is_manager' => 1,
                'feature_image' => public_path('/dummy-images/staffs/3.webp'),
                'show_in_calender' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'first_name' => 'Samuel',
                'last_name' => 'Robinson',
                'email' => 'samuel.robinson@thebeautyspot.co.uk',
                'password' => Hash::make('12345678'),
                'mobile' => '20 7777 8888',
                'gender' => 'male',
                'email_verified_at' => Carbon::now(),
                'is_manager' => 1,
                'feature_image' => public_path('/dummy-images/staffs/4.webp'),
                'show_in_calender' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'first_name' => 'Ethan',
                'last_name' => 'Sullivan',
                'email' => 'ethan.sullivan@serenestyles.com.au',
                'password' => Hash::make('12345678'),
                'mobile' => '20 3333 4444',
                'gender' => 'male',
                'email_verified_at' => Carbon::now(),
                'is_manager' => 0,
                'feature_image' => public_path('/dummy-images/staffs/5.webp'),
                'show_in_calender' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'first_name' => 'Benjamin',
                'last_name' => 'Lee',
                'email' => 'benjamin.lee@trendytrims.com.au',
                'password' => Hash::make('12345678'),
                'mobile' => '20 1111 2222',
                'gender' => 'male',
                'email_verified_at' => Carbon::now(),
                'is_manager' => 0,
                'feature_image' => public_path('/dummy-images/staffs/6.webp'),
                'show_in_calender' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'first_name' => 'Matthew',
                'last_name' => 'Taylor',
                'email' => 'matthew.taylor@urbantrends.com.au',
                'password' => Hash::make('12345678'),
                'mobile' => '30 7777 8888',
                'gender' => 'male',
                'email_verified_at' => Carbon::now(),
                'is_manager' => 0,
                'feature_image' => public_path('/dummy-images/staffs/7.webp'),
                'show_in_calender' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'first_name' => 'Sophie',
                'last_name' => 'Turner',
                'email' => 'sophie.turner@urbantrends.com.au',
                'password' => Hash::make('12345678'),
                'mobile' => '30 5555 6666',
                'gender' => 'male',
                'email_verified_at' => Carbon::now(),
                'is_manager' => 0,
                'feature_image' => public_path('/dummy-images/staffs/8.webp'),
                'show_in_calender' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'first_name' => 'Sophia',
                'last_name' => 'Martin',
                'email' => 'sophia.martin@trendytrims.com.au',
                'password' => Hash::make('12345678'),
                'mobile' => '30 3333 4444',
                'gender' => 'male',
                'email_verified_at' => Carbon::now(),
                'is_manager' => 0,
                'feature_image' => public_path('/dummy-images/staffs/9.webp'),
                'show_in_calender' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'first_name' => 'Olivia',
                'last_name' => 'Wilson',
                'email' => 'olivia.wilson@serenestyles.com.au',
                'password' => Hash::make('12345678'),
                'mobile' => '30 1111 2222',
                'gender' => 'male',
                'email_verified_at' => Carbon::now(),
                'is_manager' => 0,
                'feature_image' => public_path('/dummy-images/staffs/10.webp'),
                'show_in_calender' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'first_name' => 'Davies',
                'last_name' => 'Miller',
                'email' => 'davies.miller@serenestyles.com.au',
                'password' => Hash::make('secret'),
                'mobile' => '30 2222 2222',
                'gender' => 'male',
                'email_verified_at' => Carbon::now(),
                'is_manager' => 0,
                'feature_image' => public_path('/dummy-images/staffs/11.webp'),
                'show_in_calender' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'first_name' => 'Lee',
                'last_name' => 'White',
                'email' => 'lee.white@serenestyles.com.au',
                'password' => Hash::make('secret'),
                'mobile' => '30 3333 2222',
                'gender' => 'male',
                'email_verified_at' => Carbon::now(),
                'is_manager' => 0,
                'feature_image' => public_path('/dummy-images/staffs/12.webp'),
                'show_in_calender' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'first_name' => 'Emily',
                'last_name' => 'Susan',
                'email' => 'emily.susan@serenestyles.com.au',
                'password' => Hash::make('secret'),
                'mobile' => '10 1111 2222',
                'gender' => 'male',
                'email_verified_at' => Carbon::now(),
                'is_manager' => 0,
                'feature_image' => public_path('/dummy-images/staffs/13.webp'),
                'show_in_calender' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'first_name' => 'Ava',
                'last_name' => 'Megan',
                'email' => 'ava.megan@serenestyles.com.au',
                'password' => Hash::make('secret'),
                'mobile' => '20 1111 3333',
                'gender' => 'male',
                'email_verified_at' => Carbon::now(),
                'is_manager' => 0,
                'feature_image' => public_path('/dummy-images/staffs/14.webp'),
                'show_in_calender' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'first_name' => 'Mia',
                'last_name' => 'Barbara',
                'email' => 'mia.barbara@serenestyles.com.au',
                'password' => Hash::make('secret'),
                'mobile' => '50 5555 2222',
                'gender' => 'male',
                'email_verified_at' => Carbon::now(),
                'is_manager' => 0,
                'feature_image' => public_path('/dummy-images/staffs/15.webp'),
                'show_in_calender' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'first_name' => 'Jacob',
                'last_name' => 'Callum',
                'email' => 'jacob.callum@serenestyles.com.au',
                'password' => Hash::make('secret'),
                'mobile' => '30 4444 4444',
                'gender' => 'male',
                'email_verified_at' => Carbon::now(),
                'is_manager' => 0,
                'feature_image' => public_path('/dummy-images/staffs/16.webp'),
                'show_in_calender' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'first_name' => 'Ethan',
                'last_name' => 'David',
                'email' => 'ethan.david@serenestyles.com.au',
                'password' => Hash::make('secret'),
                'mobile' => '70 4444 2222',
                'gender' => 'male',
                'email_verified_at' => Carbon::now(),
                'is_manager' => 0,
                'feature_image' => public_path('/dummy-images/staffs/17.webp'),
                'show_in_calender' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'first_name' => 'Damian',
                'last_name' => 'Daniel',
                'email' => 'damian.daniel@serenestyles.com.au',
                'password' => Hash::make('secret'),
                'mobile' => '10 1000 2222',
                'gender' => 'male',
                'email_verified_at' => Carbon::now(),
                'is_manager' => 0,
                'feature_image' => public_path('/dummy-images/staffs/18.webp'),
                'show_in_calender' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'first_name' => 'Joseph',
                'last_name' => 'Charles',
                'email' => 'joseph.charles@serenestyles.com.au',
                'password' => Hash::make('secret'),
                'mobile' => '30 1111 1111',
                'gender' => 'male',
                'email_verified_at' => Carbon::now(),
                'is_manager' => 0,
                'feature_image' => public_path('/dummy-images/staffs/19.webp'),
                'show_in_calender' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'first_name' => 'Oscar',
                'last_name' => 'Rhys',
                'email' => 'oscar.rhys@serenestyles.com.au',
                'password' => Hash::make('secret'),
                'mobile' => '80 0000 2222',
                'gender' => 'male',
                'email_verified_at' => Carbon::now(),
                'is_manager' => 0,
                'feature_image' => public_path('/dummy-images/staffs/20.webp'),
                'show_in_calender' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

        ];

        if (env('IS_DUMMY_DATA')) {
            foreach ($employee as $key => $employee_data) {
                $featureImage = $employee_data['feature_image'] ?? null;
                $empData = Arr::except($employee_data, ['feature_image']);
                $emp = User::create($empData);
                if (isset($featureImage)) {
                    $this->attachFeatureImage($emp, $featureImage);
                }
                if ($emp->is_manager) {
                    $branchId = $key + 1;
                } else {
                    $branchId = fake()->numberBetween(1, 5);
                }
                BranchEmployee::create([
                    'branch_id' => $branchId,
                    'employee_id' => $emp->id,
                ]);

                EmployeeCommission::create([
                    'employee_id' => $emp->id,
                    'commission_id' => fake()->numberBetween(1, 3),
                ]);
                $this->dummyReview($emp->id);

                if ($emp->is_manager) {
                    $emp->assignRole(['employee', 'manager']);
                    Branch::where('id', $branchId)->update(['manager_id' => $emp->id]);
                } else {
                    $emp->assignRole(['employee']);
                }
            }
        }

        if (env('IS_FAKE_DATA')) {
            User::factory()->count(19)->create()->each(function ($emp) {
                $img = public_path('/dummy-images/staffs/'.fake()->numberBetween(1, 20).'.webp');
                $emp['show_in_calender'] = 1;
                $emp->save();
                $emp->assignRole('employee');

                $this->attachFeatureImage($emp, $img);

                BranchEmployee::create([
                    'branch_id' => fake()->numberBetween(1, 5),
                    'employee_id' => $emp->id,
                ]);
                EmployeeCommission::create([
                    'employee_id' => $emp->id,
                    'commission_id' => fake()->numberBetween(1, 10),
                ]);
                $this->dummyReview($emp->id);
            });
        }

        // Enable foreign key checks!
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
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

    private function dummyReview($emp_id)
    {
        $employeerating = [
            [
                'employee_id' => $emp_id,
                'user_id' => fake()->numberBetween(2, 40),
                'review_msg' => 'Awesome service',
                'rating' => fake()->numberBetween(3, 5),
            ],
            [
                'employee_id' => $emp_id,
                'user_id' => fake()->numberBetween(2, 40),
                'review_msg' => 'Very nice',
                'rating' => fake()->numberBetween(3, 5),
            ],
            [
                'employee_id' => $emp_id,
                'user_id' => fake()->numberBetween(2, 40),
                'review_msg' => 'Very Good',
                'rating' => fake()->numberBetween(3, 5),
            ],
            [
                'employee_id' => $emp_id,
                'user_id' => fake()->numberBetween(2, 40),
                'review_msg' => 'Nice',
                'rating' => fake()->numberBetween(3, 5),
            ],
            [
                'employee_id' => $emp_id,
                'user_id' => fake()->numberBetween(2, 40),
                'review_msg' => 'Awesome service',
                'rating' => fake()->numberBetween(3, 5),
            ],
            [
                'employee_id' => $emp_id,
                'user_id' => fake()->numberBetween(2, 40),
                'review_msg' => 'Good service',
                'rating' => fake()->numberBetween(3, 5),
            ],
        ];
        foreach ($employeerating as $key => $employeeRating_data) {
            EmployeeRating::create($employeeRating_data);
        }
    }
}
