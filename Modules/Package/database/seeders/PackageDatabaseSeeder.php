<?php

namespace Modules\Package\database\seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Modules\Package\Models\Package;

class PackageDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'name' => 'Premium Package',
                'description' => 'Full package including haircut, shave, and massage.',
                // 'feature_image' => public_path('/dummy-images/staffs/1.webp'),
                'start_date' => '2024-07-11',
                'end_date' => '2024-07-17',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'is_featured' => 1,
                'branch_id' => 1,
                'status'=>1,
                'package_price'=>230,
                'employee_id' => [43],
                'services' => [
                    [
                        'package_id' => 1,
                        'service_id' => 1,
                        'service_price' => 50,
                        'discounted_price'=>30,
                        'qty' => 2,
                        'service_name' => 'Beard Trim',
                    ],
                    [
                        'package_id' => 1,
                        'service_id' => 11,
                        'service_price' => 400,
                        'discounted_price'=>200,
                        'qty' => 2,
                        'service_name' => 'Layered Cut',
                    ],
                ],
            ],
            [
                'name' => 'Standard Package',
                'description' => 'Basic package including haircut and shave.',
                // 'feature_image' => public_path('/dummy-images/staffs/1.webp'),
                'start_date' => '2024-07-10',
                'end_date' => '2024-08-18',
               // 'package_validity'=>'30',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'is_featured' => 0,
                'branch_id' => 1,
                'status'=>1,
                'package_price'=>2500,
                'employee_id' => [3],
                'services' => [
                    [
                        'package_id' => 2,
                        'service_id' => 2,
                        'service_price' => 400,
                        'discounted_price'=>300,
                        'qty' => 2,
                        'service_name' => 'men-haircut',
                    ],
                    [
                        'package_id' => 2,
                        'service_id' => 3,
                        'service_price' => 2000,
                        'discounted_price'=>1900,
                        'qty' => 1,
                        'service_name' => 'Buzz Cut',
                    ],
                ],
            ],
            [
                'name' => 'Teen Spa Package',
                'description' => 'Special spa package for teenagers including facial, manicure, and pedicure.',
                // 'feature_image' => public_path('/dummy-images/staffs/1.webp'),
                'start_date' => '2024-07-10',
                'end_date' => '2024-10-18',
                //'package_validity'=>'30',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'is_featured' => 0,
                'branch_id' => 1,
                'status'=>1,
                'package_price'=>800,
                'employee_id' => [3],
                'services' => [
                    [
                        'package_id' => 3,
                        'service_id' => 4,
                        'service_price' => 100,
                        'discounted_price'=>80,
                        'qty' => 10,
                        'service_name' => 'Fade Cut',

                    ],
                ],
            ],
            [
                'name' => 'Weekday Wellness Package',
                'description' => 'Stay refreshed during the week with this wellness package including meditation, facial, and acupuncture.',
                // 'feature_image' => public_path('/dummy-images/staffs/1.webp'),
                'start_date' => '2024-02-13',
                'end_date' => '2024-07-18',
                //'package_validity'=>'30',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'is_featured' => 1,
                'branch_id' => 1,
                'status'=>1,
                'package_price'=>200,
                'employee_id' => [3],
                'services' => [
                    [
                        'package_id' => 4,
                        'service_id' => 5,
                        'service_price' => 50,
                        'discounted_price'=>40,
                        'qty' => 5,
                        'service_name' => 'Basic Trim',

                    ],
                ],
            ],
            [
                'name' => 'Ultimate Glam Package',
                'description' => 'The ultimate package for glamour, including hair styling, makeup, and photoshoot.',
                // 'feature_image' => public_path('/dummy-images/staffs/1.webp'),
                'start_date' => '2024-02-13',
                'end_date' => '2024-07-18',
               // 'package_validity'=>'30',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'is_featured' => 0,
                'branch_id' => 1,
                'status'=>1,
                'package_price'=>550,
                'employee_id' => [3],
                'services' => [
                    [
                        'package_id' => 5,
                        'service_id' => 6,
                        'service_price' => 200,
                        'discounted_price'=>150,
                        'qty' => 1,
                        'service_name' => 'Curling',

                    ],
                    [
                        'package_id' => 5,
                        'service_id' => 7,
                        'service_price' => 200,
                        'discounted_price'=>200,
                        'qty' => 1,
                        'service_name' => 'Updo',

                    ],
                    [
                        'package_id' => 5,
                        'service_id' => 8,
                        'service_price' => 200,
                        'discounted_price'=>100,
                        'qty' => 1,
                        'service_name' => 'Blowout',

                    ],
                ],
            ],
        ];

        if (env('IS_DUMMY_DATA')) {
            foreach ($data as $key => $value) {
                $employee_id = $value['employee_id'];
                $services = $value['services'];
                $feature_image = $value['feature_image'] ?? null;
                $packageData = Arr::except($value, ['feature_image', 'employee_id', 'services']);
                $package = Package::create($packageData);

                $package->employees()->sync($employee_id);

                $service = [];

                foreach ($services as $skey => $svalue) {
                    $service[] = (array) $svalue;
                }

                $package->services()->sync($service);
                if (isset($feature_image)) {
                    $this->attachFeatureImage($package, $feature_image);
                }
            }

        }
    }

    private function attachFeatureImage($model, $publicPath)
    {
        if (! env('IS_DUMMY_DATA_IMAGE')) {
            return false;
        }

        $file = new \Illuminate\Http\File($publicPath);

        $media = $model->addMedia($file)->preservingOriginal()->toMediaCollection('feature_image');

        return $media;
    }
}
