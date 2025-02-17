<?php

namespace Modules\Expense\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Expense\Models\Expense;
use Illuminate\Support\Arr;

class ExpenseDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks!
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $expenses = [
            [
                'title' => 'Advertising',
                'feature_image' => public_path('/dummy-images/branches/1.png'),
                'amount' => 2200,
            ],
            [
                'title' => 'Rents',
                'feature_image' => public_path('/dummy-images/branches/2.png'),
                'amount' => 50000,
            ],
            [
                'title' => 'Utilities',
                'feature_image' => public_path('/dummy-images/branches/3.png'),
                'amount' => 2000,
            ],
        ];
        if (env('IS_DUMMY_DATA')) {
            foreach ($expenses as $key => $expenses_data) {
                $expenseData = Arr::except($expenses_data, ['feature_image']);

                $expense = Expense::create($expenseData);

                $featureImage = $expense['feature_image'] ?? null;

                if (isset($featureImage)) {
                    $this->attachFeatureImage($expense, $featureImage);
                }
            }
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

        $media = $model->addMedia($file)->preservingOriginal()->toMediaCollection('feature_image');

        return $media;
    }
}
