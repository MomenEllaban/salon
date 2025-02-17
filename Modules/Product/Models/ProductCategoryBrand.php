<?php

namespace Modules\Product\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCategoryBrand extends Model
{
    use HasFactory;

    protected $table = 'product_category_brands';

    protected $casts = [
        'category_id' => 'integer',
        'brand_id' => 'integer',
    ];

    protected static function newFactory()
    {
        return \Modules\Product\Database\factories\ProductCategoryBrandFactory::new();
    }
}
