<?php

namespace Modules\Product\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Modules\Product\Models\Brands;

class BrandsTableSeeder extends Seeder
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
                'name' => 'Lotuus',
                'feature_image' => public_path('/dummy-images/Brand/lotuus.png'),
            ],
            [
                'name' => 'Bela Veeta',
                'feature_image' => public_path('/dummy-images/Brand/bela_veeta.png'),
            ],
            [
                'name' => 'Goodrej',
                'feature_image' => public_path('/dummy-images/Brand/goodrej.png'),
            ],
            [
                'name' => 'mCaaffeine',
                'feature_image' => public_path('/dummy-images/Brand/mCaaffeine.png'),
            ],
            [
                'name' => 'Dott & Keey',
                'feature_image' => public_path('/dummy-images/Brand/dott_keey.png'),
            ],
            [
                'name' => 'Nykaaa',
                'feature_image' => public_path('/dummy-images/Brand/nykaaa.png'),
            ],
            [
                'name' => 'Pluum',
                'feature_image' => public_path('/dummy-images/Brand/pluum.png'),
            ],
            [
                'name' => 'Sweess Beauty',
                'feature_image' => public_path('/dummy-images/Brand/sweess_beauty.png'),
            ],
            [
                'name' => 'Lekme',
                'feature_image' => public_path('/dummy-images/Brand/lekme.png'),
            ],
            [
                'name' => 'Feces Caanada',
                'feature_image' => public_path('/dummy-images/Brand/feces_caanada.png'),
            ],
            [
                'name' => 'MyyGlamm',
                'feature_image' => public_path('/dummy-images/Brand/myy_glamm.png'),
            ],
            [
                'name' => 'Majjestique',
                'feature_image' => public_path('/dummy-images/Brand/majjestique.png'),
            ],
            [
                'name' => 'Feeama',
                'feature_image' => public_path('/dummy-images/Brand/feeama.png'),
            ],
            [
                'name' => 'Adiddas',
                'feature_image' => public_path('/dummy-images/Brand/adiddas.png'),
            ],
            [
                'name' => 'Ikoniq',
                'feature_image' => public_path('/dummy-images/Brand/ikoniq.png'),
            ],
            [
                'name' => 'Havellss',
                'feature_image' => public_path('/dummy-images/Brand/havellss.png'),
            ],
            [
                'name' => 'Pheelips',
                'feature_image' => public_path('/dummy-images/Brand/pheelips.png'),
            ],
        ];

        if (env('IS_DUMMY_DATA')) {
            foreach ($data as $key => $value) {
                $featureImage = $value['feature_image'] ?? null;
                $branddata = Arr::except($value, ['feature_image']);
                $brand = Brands::create($branddata);
                if (isset($featureImage)) {
                    $this->attachFeatureImage($brand, $featureImage);
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
