<?php

namespace Modules\Product\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $table = 'carts';

    protected $fillable = ['user_id', 'guest_user_id', 'location_id', 'product_id', 'product_variation_id', 'qty'];

    protected $casts = [
        'user_id' => 'integer',
        'product_id' => 'integer',
        'product_variation_id' => 'integer',
        'qty' => 'integer',
    ];

    protected static function newFactory()
    {
        return \Modules\Product\Database\factories\CartFactory::new();
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function product_variation()
    {
        return $this->belongsTo(ProductVariation::class, 'product_variation_id')->with('product_variation_stock', 'combinations');
    }
}
