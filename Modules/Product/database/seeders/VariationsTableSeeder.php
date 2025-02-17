<?php

namespace Modules\Product\database\seeders;

use Illuminate\Database\Seeder;
use Modules\Product\Models\Variations;

class VariationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $variations = [
            [
                'name' => 'Size',
                'type' => 'text',
                'values' => [
                    [
                        'name' => 'Small',
                        'value' => 'S',
                    ],
                    [
                        'name' => 'Mediam',
                        'value' => 'M',
                    ],
                    [
                        'name' => 'Large',
                        'value' => 'L',
                    ],
                ],
            ],
            [
                'name' => 'Colour',
                'type' => 'color',
                'values' => [
                    [
                        'name' => 'Red',
                        'value' => '#f81b1b',
                    ],
                    [
                        'name' => 'Blue',
                        'value' => '#3DB5FF',
                    ],
                    [
                        'name' => 'Green',
                        'value' => '#79DD36',
                    ],
                ],
            ],
            [
                'name' => 'Weight',
                'type' => 'ml',
                'values' => [
                    [
                        'name' => '5 ml',
                        'value' => '5 ml',
                    ],
                    [
                        'name' => '10 ml',
                        'value' => '10 ml',
                    ],
                    [
                        'name' => '25 ml',
                        'value' => '25 ml',
                    ],
                    [
                        'name' => '50 ml',
                        'value' => '50 ml',
                    ],
                    [
                        'name' => '100 ml',
                        'value' => '100 ml',
                    ],
                    [
                        'name' => '250 ml',
                        'value' => '250 ml',
                    ],
                    [
                        'name' => '500 ml',
                        'value' => '500 ml',
                    ],
                    [
                        'name' => '1000 ml',
                        'value' => '1000 ml',
                    ],
                ],
            ],
            [
                'name' => 'Weight',
                'type' => 'gm',
                'values' => [
                    [
                        'name' => '5 gm',
                        'value' => '5 gm',
                    ],
                    [
                        'name' => '10 gm',
                        'value' => '10gm',
                    ],
                    [
                        'name' => '25 gm',
                        'value' => '25 gm',
                    ],
                    [
                        'name' => '50 gm',
                        'value' => '50 gm',
                    ],
                    [
                        'name' => '100 gm',
                        'value' => '100 gm',
                    ],
                    [
                        'name' => '250 gm',
                        'value' => '250 gm',
                    ],
                    [
                        'name' => '500 gm',
                        'value' => '500 gm',
                    ],
                    [
                        'name' => '1000 gm',
                        'value' => '1000 gm',
                    ],
                ],
            ],
        ];

        foreach ($variations as $key => $value) {
            $values = $value['values'];

            $data = \Arr::except($value, ['values']);

            $variation = Variations::create($data);

            foreach ($values as $k => $v) {
                $variation->values()->create($v);
            }
        }
        //
    }
}
