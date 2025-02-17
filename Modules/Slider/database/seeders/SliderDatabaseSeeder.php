<?php

namespace Modules\Slider\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Modules\Category\Models\Category;
use Modules\Slider\Models\Slider;

class SliderDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = \Faker\Factory::create();

        $categoryIds = Category::whereNull('parent_id')->pluck('id')->toArray();

        $sliders = [
            [
                'name' => 'Hair cutting',
                'type' => 'category',
                'link_id' => 1,
                'feature_image' => public_path('/dummy-images/sliders/slider1.png'),
            ],
            [
                'name' => 'Hair Style',
                'type' => 'category',
                'link_id' => 1,
                'feature_image' => public_path('/dummy-images/sliders/slider2.png'),
            ],
            [
                'name' => 'Hair Wash',
                'type' => 'category',
                'link_id' => 1,
                'feature_image' => public_path('/dummy-images/sliders/slider3.png'),
            ],
            [
                'name' => 'Facial',
                'type' => 'category',
                'link_id' => 4,
                'feature_image' => public_path('/dummy-images/sliders/slider4.png'),
            ],
        ];

        if (env('IS_DUMMY_DATA')) {
            foreach ($sliders as $key => $sliders_data) {
                $featureImage = $sliders_data['feature_image'] ?? null;
                $slidersData = Arr::except($sliders_data, ['feature_image']);
                $slider = Slider::create($slidersData);

                $this->attachFeatureImage($slider, $featureImage);
            }
        }

        // Disable foreign key checks!
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
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
