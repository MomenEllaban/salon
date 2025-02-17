<?php

namespace Modules\Category\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Modules\Category\Models\Category;

class CategoryDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (env('IS_DUMMY_DATA')) {
            $data = [
                [
                    'slug' => 'hair',
                    'name' => 'Hair',
                    'status' => 1,
                    'feature_image' => public_path('/dummy-images/common/Service 8.webp'),
                    'sub_category' => [
                        [
                            'slug' => 'haircuts',
                            'name' => 'Haircuts',
                            'status' => 1,
                            'feature_image' => public_path('/dummy-images/common/Service 19.webp'),
                        ],
                        [
                            'slug' => 'hairstyling',
                            'name' => 'Hairstyling',
                            'status' => 1,
                            'feature_image' => public_path('/dummy-images/common/Service 14.webp'),
                        ],
                    ],
                ],
                [
                    'slug' => 'grooming',
                    'name' => 'Grooming',
                    'status' => 1,
                    'feature_image' => public_path('/dummy-images/common/Service 42.webp'),
                    'sub_category' => [
                        [
                            'slug' => 'facial',
                            'name' => 'Facial',
                            'status' => 1,
                            'feature_image' => public_path('/dummy-images/common/Service 44.webp'),
                        ],
                        [
                            'slug' => 'shaving',
                            'name' => 'Shaving',
                            'status' => 1,
                            'feature_image' => public_path('/dummy-images/common/Service 25.webp'),
                        ],
                    ],
                ],
                [
                    'slug' => 'skin',
                    'name' => 'Skin',
                    'status' => 1,
                    'feature_image' => public_path('/dummy-images/common/Service 43.webp'),
                    'sub_category' => [
                        [
                            'slug' => 'facials',
                            'name' => 'Facials',
                            'status' => 1,
                            'feature_image' => public_path('/dummy-images/common/Service 1.webp'),

                        ],
                        [
                            'slug' => 'waxing',
                            'name' => 'Waxing',
                            'status' => 1,
                            'feature_image' => public_path('/dummy-images/common/Service 5.webp'),
                        ],
                        [
                            'slug' => 'skin-care-consultation',
                            'name' => 'Skin Care Consultation',
                            'status' => 1,
                            'feature_image' => public_path('/dummy-images/common/Service 13.webp'),
                        ],
                        [
                            'slug' => 'facial-treatments',
                            'name' => 'Facial Treatments',
                            'status' => 1,
                            'feature_image' => public_path('/dummy-images/common/Service 1.webp'),
                        ],
                    ],
                ],
                [
                    'slug' => 'makeup',
                    'name' => 'Makeup',
                    'status' => 1,
                    'feature_image' => public_path('/dummy-images/common/Service 32.webp'),
                    'sub_category' => [
                        [
                            'slug' => 'bridal-makeup',
                            'name' => 'Bridal Makeup',
                            'status' => 1,
                            'feature_image' => public_path('/dummy-images/common/Service 43.webp'),

                        ],
                        [
                            'slug' => 'special-occasion-makeup',
                            'name' => 'Special Occasion Makeup',
                            'status' => 1,
                            'feature_image' => public_path('/dummy-images/common/Service 42.webp'),
                        ],
                    ],
                ],
                [
                    'slug' => 'coloring',
                    'name' => 'Coloring',
                    'status' => 1,
                    'feature_image' => public_path('/dummy-images/common/Service 45.png'),
                    'sub_category' => [
                        [
                            'slug' => 'hair-coloring',
                            'name' => 'Hair Coloring',
                            'status' => 1,
                            'feature_image' => public_path('/dummy-images/common/Service 8.webp'),
                        ],
                        [
                            'slug' => 'permanent-hair-coloring',
                            'name' => 'Permanent Hair Coloring',
                            'status' => 1,
                            'feature_image' => public_path('/dummy-images/common/Service 18.webp'),
                        ],
                        [
                            'slug' => 'highlights',
                            'name' => 'Highlights',
                            'status' => 1,
                            'feature_image' => public_path('/dummy-images/common/Service 18.webp'),
                        ],
                    ],
                ],
                [
                    'slug' => 'hair-removal',
                    'name' => 'Hair Removal',
                    'status' => 1,
                    'feature_image' => public_path('/dummy-images/common/Service 5.webp'),
                    'sub_category' => [
                        [
                            'slug' => 'threading',
                            'name' => 'Threading',
                            'status' => 1,
                            'feature_image' => public_path('/dummy-images/common/Service 16.webp'),

                        ],
                        [
                            'slug' => 'waxing',
                            'name' => 'Waxing',
                            'status' => 1,
                            'feature_image' => public_path('/dummy-images/common/Service 5.webp'),
                        ],
                    ],
                ],
                [
                    'slug' => 'massage',
                    'name' => 'Massage',
                    'status' => 1,
                    'feature_image' => public_path('/dummy-images/common/Service 21.webp'),
                    'sub_category' => [
                        [
                            'slug' => 'swedish-massage',
                            'name' => 'Swedish Massage',
                            'status' => 1,
                            'feature_image' => public_path('/dummy-images/common/Service 36.webp'),

                        ],
                        [
                            'slug' => 'sports-massage',
                            'name' => 'Sports Massage',
                            'status' => 1,
                            'feature_image' => public_path('/dummy-images/common/Service 29.webp'),
                        ],
                        [
                            'slug' => 'hot-stone-massage',
                            'name' => 'Hot Stone Massage',
                            'status' => 1,
                            'feature_image' => public_path('/dummy-images/common/Service 20.webp'),
                        ],
                        [
                            'slug' => 'relaxation',
                            'name' => 'Relaxation',
                            'status' => 1,
                            'feature_image' => public_path('/dummy-images/common/Service 11.webp'),
                        ],
                    ],
                ],
                [
                    'slug' => 'nail-care',
                    'name' => 'Nail Care',
                    'status' => 1,
                    'feature_image' => public_path('/dummy-images/common/Service 26.webp'),
                    'sub_category' => [
                        [
                            'slug' => 'pedicure',
                            'name' => 'Pedicure',
                            'status' => 1,
                            'feature_image' => public_path('/dummy-images/common/Service 31.webp'),

                        ],
                        [
                            'slug' => 'manicure',
                            'name' => 'Manicure',
                            'status' => 1,
                            'feature_image' => public_path('/dummy-images/common/Service 26.webp'),
                        ],
                    ],
                ],
                [
                    'slug' => 'spa-packages',
                    'name' => 'Spa Packages',
                    'status' => 1,
                    'feature_image' => public_path('/dummy-images/common/Service 36.webp'),
                    'sub_category' => [
                        [
                            'slug' => 'detox-package',
                            'name' => 'Detox Package',
                            'status' => 1,
                            'feature_image' => public_path('/dummy-images/common/Service 29.webp'),

                        ],
                        [
                            'slug' => 'relaxation-package',
                            'name' => 'Relaxation Package',
                            'status' => 1,
                            'feature_image' => public_path('/dummy-images/common/Service 36.webp'),
                        ],
                    ],
                ],
                [
                    'slug' => 'hair-treatments',
                    'name' => 'Hair Treatments',
                    'status' => 1,
                    'feature_image' => public_path('/dummy-images/common/Service 37.webp'),
                    'sub_category' => [
                        [
                            'slug' => 'hair-repair-treatments',
                            'name' => 'Hair Repair Treatments',
                            'status' => 1,
                            'feature_image' => public_path('/dummy-images/common/Service 39.webp'),

                        ],
                        [
                            'slug' => 'scalp-treatments',
                            'name' => 'Scalp Treatments',
                            'status' => 1,
                            'feature_image' => public_path('/dummy-images/common/Service 6.webp'),
                        ],
                    ],
                ],
                [
                    'slug' => 'hair-texture-services',
                    'name' => 'Hair Texture Services',
                    'status' => 1,
                    'feature_image' => public_path('/dummy-images/common/Service 18.webp'),
                    'sub_category' => [
                        [
                            'slug' => 'keratin smothings',
                            'name' => 'Keratin Smothings',
                            'status' => 1,
                            'feature_image' => public_path('/dummy-images/common/Service 17.webp'),
                        ],
                        [
                            'slug' => 'scalp-treatments',
                            'name' => 'Chemical Waving',
                            'status' => 1,
                            'feature_image' => public_path('/dummy-images/common/Service 6.webp'),
                        ],
                    ],
                ],
                [
                    'slug' => 'hair-extension',
                    'name' => 'Hair Extension',
                    'status' => 1,
                    'feature_image' => public_path('/dummy-images/common/Service 33.webp'),
                    'sub_category' => [
                        [
                            'slug' => 'temporary-extension',
                            'name' => 'Temporary Extension',
                            'status' => 1,
                            'feature_image' => public_path('/dummy-images/common/Service 15.webp'),
                        ],
                        [
                            'slug' => 'Tape-In Extension',
                            'name' => 'Tape-In Extension',
                            'status' => 1,
                            'feature_image' => public_path('/dummy-images/common/Service 22.webp'),
                        ],
                    ],
                ],
            ];
            foreach ($data as $key => $val) {
                $subCategorys = $val['sub_category'];
                $featureImage = $val['feature_image'] ?? null;
                $categoryData = Arr::except($val, ['sub_category', 'feature_image']);
                $category = Category::create($categoryData);
                if (isset($featureImage)) {
                    $this->attachFeatureImage($category, $featureImage);
                }
                foreach ($subCategorys as $subKey => $subCategory) {
                    $subCategory['parent_id'] = $category->id;
                    $featureImage = $subCategory['feature_image'] ?? null;
                    $sub_categoryData = Arr::except($subCategory, ['feature_image']);
                    $subcategoryData = Category::create($sub_categoryData);
                    if (isset($featureImage)) {
                        $this->attachFeatureImage($subcategoryData, $featureImage);
                    }
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
