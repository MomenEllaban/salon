<?php

namespace Modules\Product\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariation extends Model
{
    use HasFactory;

    protected $fillable = [];

    protected $casts = [
        'product_id' => 'integer',
        'price' => 'double',
    ];

    protected static function newFactory()
    {
        return \Modules\Product\Database\factories\ProductVariationFactory::new();
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function combinations()
    {
        return $this->hasMany(ProductVariationCombination::class)->with('variation_combination_data', 'variation_combination_value');
    }

    public function combination()
    {
        return $this->hasOne(ProductVariationCombination::class)->with('variation_combination_data', 'variation_combination_value');
    }

    // public function product_variation_stock()
    // {
    //     return $this->hasOne(ProductVariationStock::class)->where('location_id', session('stock_location_id'));
    // }

    public function product_variation_stock()
    {
        return $this->hasOne(ProductVariationStock::class, 'product_variation_id');
    }

    public function product_variation_stock_without_location()
    {
        return $this->hasOne(ProductVariationStock::class);
    }
}
