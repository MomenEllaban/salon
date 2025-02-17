<?php

namespace Modules\Product\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WishList extends Model
{
    use HasFactory;

    protected $table = 'wishlist';

    protected $fillable = ['user_id', 'guest_user_id', 'product_id'];

    protected $casts = [
        'user_id' => 'integer',
        'product_id' => 'integer',
    ];

    protected static function newFactory()
    {
        return \Modules\Product\Database\factories\WishListFactory::new();
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id')->with('product_variations');
    }
}
