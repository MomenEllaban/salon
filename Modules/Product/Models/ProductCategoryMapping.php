<?php

namespace Modules\Product\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCategoryMapping extends Model
{
    use HasFactory;

    protected $fillable = [];

    protected $casts = [
        'product_id' => 'integer',
        'category_id' => 'integer',
    ];

    protected static function newFactory()
    {
        return \Modules\Product\Database\factories\ProductCategoryMappingFactory::new();
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function category()
    {
        return $this->belongsTo(ProductCategory::class);
    }
}
