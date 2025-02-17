<?php

namespace Modules\Product\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariationCombination extends Model
{
    use HasFactory;

    protected $fillable = [];

    protected $casts = [
        'product_id' => 'integer',
        'product_variation_id' => 'integer',
        'variation_id' => 'integer',
        'variation_value_id' => 'integer',
    ];

    protected static function newFactory()
    {
        return \Modules\Product\Database\factories\ProductVariationCombinationFactory::new();
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variation()
    {
        return $this->belongsTo(ProductVariation::class, 'product_variation_id');
    }

    public function variation_combination_data()
    {
        return $this->belongsTo(Variations::class, 'variation_id', 'id');
    }

    public function variation_combination_value()
    {
        return $this->belongsTo(VariationValue::class, 'variation_value_id', 'id');
    }
}
